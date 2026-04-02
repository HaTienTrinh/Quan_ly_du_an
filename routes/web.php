<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'product'])->name('products');
Route::get('/products/{product}', [HomeController::class, 'show'])->name('products.show');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/posts', [HomeController::class, 'post'])->name('posts');


// Đăng ký / Đăng nhập / Đăng xuất
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// ROUTE ADMIN (phải đăng nhập + role = admin)
// ============================================================

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Sprint 2 — Quản lý danh mục
        Route::get('categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
        Route::patch('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDestroy'])->name('categories.force-destroy');
        Route::resource('categories', CategoryController::class);

        // Sprint 3 — Quản lý sản phẩm
        Route::get('products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
        Route::patch('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDestroy'])->name('products.force-destroy');
        Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Sprint 4 — Quản lý tài khoản
        // Route::resource('users', Admin\UserController::class);

        // Sprint 5 — Quản lý bài viết
        // Route::resource('posts', Admin\PostController::class);

        // Sprint 5 — Quản lý đơn hàng
        // Route::resource('orders', Admin\OrderController::class);
    });

// ============================================================
// ROUTE KHÁCH HÀNG (phải đăng nhập + role = customer)
// ============================================================

// ============================================================
// ROUTE GIỎ HÀNG + ĐẶT HÀNG (phải đăng nhập + role = customer)
// ============================================================

Route::middleware(['auth', 'customer'])->group(function () {

    // Giỏ hàng
    Route::prefix('cart')
        ->name('cart.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Customer\CartController::class, 'index'])->name('index');
            Route::post('/add', [\App\Http\Controllers\Customer\CartController::class, 'add'])->name('add');
            Route::put('/{product_id}', [\App\Http\Controllers\Customer\CartController::class, 'update'])->name('update');
            Route::delete('/{product_id}', [\App\Http\Controllers\Customer\CartController::class, 'remove'])->name('remove');
            Route::post('/clear', [\App\Http\Controllers\Customer\CartController::class, 'clear'])->name('clear');
        });

    // Sprint 4 — Hồ sơ cá nhân
    // Route::get('/profile', [Customer\ProfileController::class, 'index'])->name('profile');
    // Route::put('/profile', [Customer\ProfileController::class, 'update'])->name('profile.update');

    // Sprint 5 — Đặt hàng
    Route::get('/checkout', [\App\Http\Controllers\Customer\OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [\App\Http\Controllers\Customer\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [\App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/confirmation', [\App\Http\Controllers\Customer\OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/orders/{order}', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
});
