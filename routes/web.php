<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PostCommentController;
use App\Http\Controllers\Customer\ProductReviewController;
use App\Http\Controllers\Customer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'product'])->name('products');
Route::get('/products/{product}', [HomeController::class, 'show'])->name('products.show');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/posts', [HomeController::class, 'post'])->name('posts');
Route::get('/posts/{slug}', [HomeController::class, 'postShow'])->name('posts.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
        Route::patch('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDestroy'])->name('categories.force-destroy');
        Route::resource('categories', CategoryController::class);

        Route::get('products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
        Route::patch('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDestroy'])->name('products.force-destroy');
        Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::get('users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
        Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDestroy'])->name('users.force-destroy');
        Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

        Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::get('posts/trashed', [PostController::class, 'trashed'])->name('posts.trashed');
        Route::patch('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
        Route::delete('posts/{id}/force-delete', [PostController::class, 'forceDestroy'])->name('posts.force-destroy');
        Route::resource('posts', PostController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::resource('orders', AdminOrderController::class)->only(['index']);
    });

Route::middleware(['auth', 'customer'])->group(function () {
    Route::prefix('cart')
        ->name('cart.')
        ->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [CartController::class, 'add'])->name('add');
            Route::put('/{product_id}', [CartController::class, 'update'])->name('update');
            Route::delete('/{product_id}', [CartController::class, 'remove'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::patch('/profile/addresses/{address}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.default');
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');

    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::post('/posts/{slug}/comments', [PostCommentController::class, 'store'])->name('posts.comments.store');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/orders/{order}/receive', [OrderController::class, 'receive'])->name('orders.receive');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});
