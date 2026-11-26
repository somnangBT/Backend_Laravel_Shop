<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Fetch products with search & pagination (only needed columns)
        $productsQuery = Product::with('category:cat_id,cat_name');
        if ($request->filled('product_search')) {
            $search = trim($request->product_search);
            $productsQuery->where(function ($q) use ($search) {
                $q->where('pro_name', 'like', "%{$search}%")
                  ->orWhere('pro_code', 'like', "%{$search}%");
            });
        }
        $products = $productsQuery->latest()->paginate(10, ['*'], 'products_page')->withQueryString();

        // When live-searching products via AJAX, skip heavy dashboard stats & customer lists
        if ($request->ajax()) {
            $totalProducts = 0;
            $productsInStock = 0;
            $lowStockProducts = 0;
            $customers = User::whereRaw('1 = 0')->paginate(5, ['*'], 'customers_page');
            $categories = collect();

            return view('dashboard', compact('products', 'totalProducts', 'productsInStock', 'lowStockProducts', 'customers', 'categories'));
        }

        // Single aggregated query instead of 3 separate COUNT(*) round-trips
        $prodStats = DB::table('products')->selectRaw("
            COUNT(*) as total_count,
            SUM(CASE WHEN qty > 0 THEN 1 ELSE 0 END) as in_stock_count,
            SUM(CASE WHEN qty <= 10 THEN 1 ELSE 0 END) as low_stock_count
        ")->first();

        $totalProducts = (int) ($prodStats->total_count ?? 0);
        $productsInStock = (int) ($prodStats->in_stock_count ?? 0);
        $lowStockProducts = (int) ($prodStats->low_stock_count ?? 0);

        // Fetch customers with their orders and order items, paginated
        $customers = User::where('role', 'user')
            ->with(['orders' => function ($query) {
                $query->latest();
            }, 'orders.orderItems.product:pro_id,pro_name'])
            ->latest()
            ->paginate(5, ['*'], 'customers_page')
            ->withQueryString();

        $categories = Cache::remember('all_categories', 300, function () {
            return Category::all();
        });

        return view('dashboard', compact('products', 'totalProducts', 'productsInStock', 'lowStockProducts', 'customers', 'categories'));
    }
}