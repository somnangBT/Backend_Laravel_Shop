<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $cartItems = Cart::with(['product:pro_id,pro_code,pro_name,price,discount,image,qty'])
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => $item->product ? [
                        'id' => $item->product->pro_id,
                        'code' => $item->product->pro_code,
                        'name' => $item->product->pro_name,
                        'price' => $item->product->price,
                        'discount' => $item->product->discount,
                        'discounted_price' => $item->product->discounted_price,
                        'image' => $item->product->image,
                        'stock' => $item->product->qty,
                    ] : null,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->product ? $item->product->discounted_price * $item->quantity : 0,
                ];
            });

        $total = $cartItems->sum('subtotal');

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $cartItems,
                'total_items' => $cartItems->sum('quantity'),
                'subtotal' => round($total, 2),
            ],
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,pro_id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Check stock
        $product = Product::find($productId);
        if ($product->qty < $quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Available: ' . $product->qty,
            ], 400);
        }

        // Check if item already in cart
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->qty < $newQuantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient stock. Available: ' . $product->qty,
                ], 400);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart',
            'data' => $cartItem,
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->user()->id;
        $cartItem = Cart::where('id', $id)->where('user_id', $userId)->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found',
            ], 404);
        }

        // Check stock
        $product = Product::find($cartItem->product_id);
        if ($product->qty < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient stock. Available: ' . $product->qty,
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated',
            'data' => $cartItem,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;
        $cartItem = Cart::where('id', $id)->where('user_id', $userId)->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found',
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart',
        ]);
    }

    /**
     * Clear all items from cart
     */
    public function clear(Request $request)
    {
        $userId = $request->user()->id;
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared',
        ]);
    }
}
