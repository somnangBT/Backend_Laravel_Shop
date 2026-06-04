<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\OrderController;
<<<<<<< HEAD
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Http\Request;
=======
use App\Http\Controllers\SlideHeroController;
>>>>>>> 03f53ac22f0f9f31d93e44a9965a36e6b4e1674f
use Illuminate\Support\Facades\Route;

// =============================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================

// Auth Routes
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [AuthController::class, 'apiRegister']);

// Category API Routes (Public - Read Only)
Route::get('/categories', [CategoryController::class, 'indexApi'])->name('api.categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showApi'])->name('api.categories.show');

// Product API Routes (Public - Read Only)
Route::get('/products', [ProductController::class, 'indexApi'])->name('api.products.index');
Route::get('/products/{id}', [ProductController::class, 'showApi'])->name('api.products.show');


// =============================================
// PROTECTED ROUTES (Authentication Required)
// =============================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    });

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Checkout Routes
    Route::get('/checkout/summary', [CheckoutController::class, 'summary']);
    Route::post('/checkout', [CheckoutController::class, 'process']);

    // Order History Routes
    Route::get('/orders', [CheckoutController::class, 'orderHistory']);
    Route::get('/orders/{id}', [CheckoutController::class, 'orderDetail']);

    // Category Management (Admin)
    Route::post('/categories', [CategoryController::class, 'storeApi'])->name('api.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'updateApi'])->name('api.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroyApi'])->name('api.categories.destroy');

    // Product Management (Admin)
    Route::put('/products/{id}', [ProductController::class, 'updateApi'])->name('api.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroyApi'])->name('api.products.destroy');
});


// =============================================
// ADMIN ONLY ROUTES
// =============================================

// User Management API Routes (Admin)
Route::get('/users', [AuthController::class, 'getAllUsers']);
Route::post('/add-user', [AuthController::class, 'addUser']);
Route::delete('/delete-user/{id}', [AuthController::class, 'deleteUser']);
Route::put('/update-user/{id}', [AuthController::class, 'updateUser']);

<<<<<<< HEAD
// Legacy order endpoint (keep for backward compatibility)
Route::post('/orders', [OrderController::class, 'store']);
=======
// Category API Routes
Route::get('/categories', [CategoryController::class, 'indexApi'])->name('api.categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showApi'])->name('api.categories.show');
Route::put('/categories/{id}', [CategoryController::class, 'updateApi'])->name('api.categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroyApi'])->name('api.categories.destroy');

// Product API Routes
Route::get('/products', [ProductController::class, 'indexApi'])->name('api.products.index');
Route::get('/products/{id}', [ProductController::class, 'showApi'])->name('api.products.show');
Route::put('/products/{id}', [ProductController::class, 'updateApi'])->name('api.products.update');
Route::delete('/products/{id}', [ProductController::class, 'destroyApi'])->name('api.products.destroy');

// SlideHero API Routes - Fixed and Complete
Route::get('/slide-heroes', [SlideHeroController::class, 'indexApi'])->name('api.slide-heroes.index');
Route::post('/slide-heroes', [SlideHeroController::class, 'storeApi'])->name('api.slide-heroes.store');
Route::get('/slide-heroes/active/list', [SlideHeroController::class, 'activeApi'])->name('api.slide-heroes.active');
Route::post('/slide-heroes/bulk-delete', [SlideHeroController::class, 'bulkDeleteApi'])->name('api.slide-heroes.bulk-delete');
Route::get('/slide-heroes/{id}', [SlideHeroController::class, 'showApi'])->name('api.slide-heroes.show');
Route::put('/slide-heroes/{id}', [SlideHeroController::class, 'updateApi'])->name('api.slide-heroes.update');
Route::delete('/slide-heroes/{id}', [SlideHeroController::class, 'destroyApi'])->name('api.slide-heroes.destroy');
Route::patch('/slide-heroes/{id}/toggle-status', [SlideHeroController::class, 'toggleStatusApi'])->name('api.slide-heroes.toggle-status');
>>>>>>> 03f53ac22f0f9f31d93e44a9965a36e6b4e1674f
