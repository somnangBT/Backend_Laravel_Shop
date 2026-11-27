<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\SlideHeroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =============================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================
Route::middleware(['api'])->group(function () {
    Route::get('/products/search', [ProductController::class, 'searchApi']);
});

// Auth & Recovery Routes
Route::get('/auth/verification-channels', [AuthController::class, 'apiVerificationChannels']);
Route::post('/login', [AuthController::class, 'apiLogin'])->middleware('throttle:15,1');
Route::post('/register/send-code', [AuthController::class, 'apiSendRegisterCode'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'apiRegister'])->middleware('throttle:10,1');
Route::post('/forgot-password', [AuthController::class, 'apiForgotPassword'])->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'apiResetPassword'])->middleware('throttle:10,1');

// Contact & Newsletter APIs
Route::post('/contact', [MessageController::class, 'storeContactApi'])->middleware('throttle:10,1');
Route::post('/newsletter', [MessageController::class, 'subscribeNewsletterApi'])->middleware('throttle:10,1');

// Category API Routes (Public - Read Only)
Route::get('/categories', [CategoryController::class, 'indexApi'])->name('api.categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'showApi'])->name('api.categories.show');

// Product API Routes (Public - Read Only)
Route::get('/products', [ProductController::class, 'indexApi'])->name('api.products.index');
Route::get('/products/{id}', [ProductController::class, 'showApi'])->name('api.products.show');

// Slide Hero API Route
Route::get('/slide-heroes', [SlideHeroController::class, 'indexApi']);

// Order Creation + Bakong Real-Time Check & Telegram Bot Webhook
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/bakong/check-payment', [OrderController::class, 'checkBakongPayment']);
Route::post('/telegram/webhook', [OrderController::class, 'telegramWebhook']);


// =============================================
// PROTECTED ROUTES (Authentication Required)
// =============================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Password
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::put('/change-password', [AuthController::class, 'apiChangePassword']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    });

    // Customer Orders & Payment Slip Proxy Upload
    Route::get('/orders', [OrderController::class, 'apiIndex']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelApi']);
    Route::post('/upload-slip', [OrderController::class, 'uploadSlipApi']);

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Checkout Routes
    Route::get('/checkout/summary', [CheckoutController::class, 'summary']);
    Route::post('/checkout', [CheckoutController::class, 'process']);

    // Category Management (Admin Only enforced in controller)
    Route::post('/categories', [CategoryController::class, 'storeApi'])->name('api.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'updateApi'])->name('api.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroyApi'])->name('api.categories.destroy');

    // Product Management (Admin Only enforced in controller)
    Route::put('/products/{id}', [ProductController::class, 'updateApi'])->name('api.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroyApi'])->name('api.products.destroy');

    // User Management API Routes (Admin-only protected via controller)
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    Route::post('/add-user', [AuthController::class, 'addUser']);
    Route::delete('/delete-user/{id}', [AuthController::class, 'deleteUser']);
    Route::put('/update-user/{id}', [AuthController::class, 'updateUser']);
});