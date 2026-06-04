<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlideHeroController;
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

Route::get('/slide-heroes', [SlideHeroController::class, 'indexApi']);
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

// Legacy order endpoint (keep for backward compatibility)
Route::post('/orders', [OrderController::class, 'store']);
