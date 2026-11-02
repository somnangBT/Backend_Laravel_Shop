<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Fetch products with search & pagination
        $productsQuery = Product::with('category');
        if ($request->filled('product_search')) {
            $search = $request->product_search;
            $productsQuery->where(function ($q) use ($search) {
                $q->where('pro_name', 'like', "%$search%")
                  ->orWhere('pro_code', 'like', "%$search%");
            });
        }
        $products = $productsQuery->latest()->paginate(10, ['*'], 'products_page')->withQueryString();

        // Calculate product statistics
        $totalProducts = Product::count();
        $productsInStock = Product::where('qty', '>', 0)->count();
        $lowStockProducts = Product::where('qty', '<=', 10)->count();

        // Fetch customers with their orders and order items, paginated
        $customers = User::where('role', 'user')
            ->with(['orders' => function ($query) {
                $query->latest();
            }, 'orders.orderItems.product'])
            ->latest()
            ->paginate(5, ['*'], 'customers_page')
            ->withQueryString();

        $categories = Category::all();

        return view('dashboard', compact('products', 'totalProducts', 'productsInStock', 'lowStockProducts', 'customers', 'categories'));
    }
}