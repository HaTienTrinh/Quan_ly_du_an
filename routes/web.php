<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'product'])->name('products');
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
    Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'edit', 'update']);

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

Route::middleware(['auth', 'customer'])->group(function () {

    // Sprint 4 — Hồ sơ cá nhân
    // Route::get('/profile', [Customer\ProfileController::class, 'index'])->name('profile');
    // Route::put('/profile', [Customer\ProfileController::class, 'update'])->name('profile.update');

    // Sprint 5 — Đặt hàng
    // Route::get('/checkout', [Customer\OrderController::class, 'checkout'])->name('checkout');
    // Route::post('/orders', [Customer\OrderController::class, 'store'])->name('orders.store');
    // Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/{order}', [Customer\OrderController::class, 'show'])->name('orders.show');
});