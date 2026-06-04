<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Get checkout summary (requires authentication)
     */
    public function summary(Request $request)
    {
        $userId = $request->user()->id;
        
        $cartItems = Cart::with(['product:pro_id,pro_code,pro_name,price,discount,image,qty'])
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty',
            ], 400);
        }

        $items = [];
        $subtotal = 0;

        foreach ($cartItems as $item) {
            if (!$item->product) {
                continue;
            }

            $price = $item->product->discounted_price;
            $itemTotal = $price * $item->quantity;
            $subtotal += $itemTotal;

            $items[] = [
                'product_id' => $item->product->pro_id,
                'name' => $item->product->pro_name,
                'price' => round($price, 2),
                'quantity' => $item->quantity,
                'subtotal' => round($itemTotal, 2),
                'image' => $item->product->image,
            ];
        }

        // Calculate totals
        $tax = round($subtotal * 0.10, 2); // 10% tax
        $shipping = $subtotal >= 50 ? 0 : 5.00; // Free shipping over $50
        $total = round($subtotal + $tax + $shipping, 2);

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $items,
                'subtotal' => round($subtotal, 2),
                'tax' => $tax,
                'shipping' => $shipping,
                'discount' => 0,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Process checkout/payment (requires authentication)
     */
    public function process(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,card,paypal,bank_transfer,bakong_aba',
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
            'promo_code' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get cart items
        $cartItems = Cart::with(['product'])
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty. Please add items before checkout.',
            ], 400);
        }

        // Verify stock and calculate totals
        $items = [];
        $subtotal = 0;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found in cart',
                ], 400);
            }

            if ($product->qty < $cartItem->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient stock for {$product->pro_name}. Available: {$product->qty}",
                ], 400);
            }

            $price = $product->discounted_price;
            $itemTotal = $price * $cartItem->quantity;
            $subtotal += $itemTotal;

            $items[] = [
                'product_id' => $product->pro_id,
                'quantity' => $cartItem->quantity,
                'price' => round($price, 2),
                'discount' => $product->discount ?? 0,
            ];
        }

        // Calculate totals
        $tax = round($subtotal * 0.10, 2);
        $shipping = $subtotal >= 50 ? 0 : 5.00;
        $discount = $this->calculatePromoDiscount($request->promo_code, $subtotal);
        $total = round($subtotal + $tax + $shipping - $discount, 2);

        try {
            $order = DB::transaction(function () use ($user, $request, $items, $subtotal, $tax, $shipping, $discount, $total) {
                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'subtotal' => round($subtotal, 2),
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'discount' => $discount,
                    'total' => $total,
                    'promo_code' => $request->promo_code,
                    'notes' => $request->notes,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'shipping_address' => $request->shipping_address,
                    'phone' => $request->phone,
                ]);

                // Create order items and update stock
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                    ]);

                    // Decrease product stock
                    Product::where('pro_id', $item['product_id'])
                        ->decrement('qty', $item['quantity']);
                }

                // Clear user's cart
                Cart::where('user_id', $user->id)->delete();

                return $order;
            });

            // Send confirmation email
            try {
                Mail::to($user->email)->queue(new OrderConfirmation($order));
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully! Contact us on Telegram for order confirmation.',
                'data' => [
                    'order_id' => $order->id,
                    'total' => $order->total,
                    'status' => $order->status,
                    'telegram_link' => 'https://t.me/yorn_somsang',
                    'telegram_username' => '@yorn_somsang',
                    'contact_message' => 'Please contact us on Telegram to confirm your order and arrange payment.',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's order history
     */
    public function orderHistory(Request $request)
    {
        $user = $request->user();

        $orders = Order::with(['items.product:pro_id,pro_name,image'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Get single order details
     */
    public function orderDetail(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::with(['items.product:pro_id,pro_name,image,price'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }

    /**
     * Calculate promo code discount
     */
    private function calculatePromoDiscount($promoCode, $subtotal)
    {
        if (!$promoCode) {
            return 0;
        }

        // Example promo codes - you can extend this with a database table
        $promoCodes = [
            'SAVE10' => ['type' => 'percentage', 'value' => 10],
            'SAVE20' => ['type' => 'percentage', 'value' => 20],
            'FLAT5' => ['type' => 'fixed', 'value' => 5],
            'FLAT10' => ['type' => 'fixed', 'value' => 10],
        ];

        $promoCode = strtoupper($promoCode);
        
        if (!isset($promoCodes[$promoCode])) {
            return 0;
        }

        $promo = $promoCodes[$promoCode];
        
        if ($promo['type'] === 'percentage') {
            return round($subtotal * ($promo['value'] / 100), 2);
        }
        
        return min($promo['value'], $subtotal); // Don't let discount exceed subtotal
    }
}
