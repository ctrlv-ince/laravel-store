<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController as UserOrderController;
use App\Http\Controllers\ReviewController as UserReviewController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Test route for email
Route::get('/test-email', function () {
    try {
        Mail::raw('This is a test email from Tech Store', function($message) {
            $message->to('vinceerolborja@gmail.com')
                   ->subject('Test Email');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Test route for account
Route::get('/check-account', function () {
    if (!Auth::check()) {
        return 'Not logged in';
    }
    
    $user = Auth::user();
    $account = $user->account;
    
    if ($account) {
        return 'Account exists: ' . $account->account_id . ', Role: ' . $account->role . ', Status: ' . $account->account_status;
    } else {
        // Try to create an account
        $account = \App\Models\Account::create([
            'user_id' => $user->user_id,
            'username' => $user->email,
            'password' => $user->password,
            'role' => 'user',
            'profile_img' => '',
            'account_status' => 'active'
        ]);
        
        return 'Created account: ' . $account->account_id;
    }
});

// Protected routes (require authentication and email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Item routes
    Route::resource('items', ItemController::class);
    
    // Product routes
    Route::get('/products', [ItemController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ItemController::class, 'show'])->name('products.show');

    // User Orders route
    Route::get('/orders', [UserOrderController::class, 'userOrders'])->name('user.orders');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [UserOrderController::class, 'generateReceipt'])->name('orders.generateReceipt');

    // Cart and checkout routes
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::post('/buy-now', [UserOrderController::class, 'buyNow'])->name('order.buyNow');
    Route::post('/items/{item}/reviews', [UserReviewController::class, 'store'])->name('reviews.store');

    // Cart routes with middleware
    Route::middleware('auth')->group(function () {
        Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
        Route::get('/cart/items', [\App\Http\Controllers\CartController::class, 'getItems'])->name('cart.getItems');
        Route::put('/cart/{cart}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [\App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('/cart', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
        
        // Workaround for item-based cart updates
        Route::put('/cart/item/{item_id}', [\App\Http\Controllers\CartController::class, 'updateByItemId'])->name('cart.updateByItemId');
        Route::delete('/cart/item/{item_id}', [\App\Http\Controllers\CartController::class, 'destroyByItemId'])->name('cart.destroyByItemId');
        
        // Checkout routes
        Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    });
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::post('users/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
    
    // Create a very specific route for image deletion that won't conflict
    Route::delete('item-images/{id}', [AdminItemController::class, 'deleteImage'])->name('items.delete-image');
    
    // Add a special route for deleting images from the edit page
    Route::delete('edit-item-images/{id}', [AdminItemController::class, 'deleteImage'])->name('items.edit-delete-image');
    
    // Product Management - Custom routes first
    Route::get('items/trash', [AdminItemController::class, 'trash'])->name('items.trash');
    Route::post('items/{id}/restore', [AdminItemController::class, 'restore'])->name('items.restore');
    Route::delete('items/{id}/force-delete', [AdminItemController::class, 'forceDelete'])->name('items.force-delete');
    Route::post('items/import', [AdminItemController::class, 'import'])->name('items.import');
    
    // Then the resource routes
    Route::resource('items', AdminItemController::class);
    Route::resource('groups', GroupController::class);
    
    // Order Management
    Route::resource('orders', OrderController::class);
    
    // Review Management
    Route::resource('reviews', ReviewController::class)->only(['index', 'destroy']);
});

require __DIR__.'/auth.php';
