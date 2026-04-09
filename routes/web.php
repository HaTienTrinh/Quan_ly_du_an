<?php

use App\Http\Controllers\Admin\AdminInspectionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReturnRequestController as AdminReturnRequestController;
use App\Http\Controllers\Admin\ReshipController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PostCommentController;
use App\Http\Controllers\Customer\ProductReviewController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\ReturnRequestController;
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

        Route::patch('orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
        Route::patch('orders/{order}/prepare', [AdminOrderController::class, 'prepare'])->name('orders.prepare');
        Route::patch('orders/{order}/ship', [AdminOrderController::class, 'ship'])->name('orders.ship');
        Route::patch('orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
        Route::patch('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);

        Route::get('return-requests', [AdminReturnRequestController::class, 'index'])->name('return-requests.index');
        Route::get('return-requests/{returnRequest}', [AdminReturnRequestController::class, 'show'])->name('return-requests.show');
        Route::patch('return-requests/{returnRequest}/approve', [AdminReturnRequestController::class, 'approve'])->name('return-requests.approve');
        Route::patch('return-requests/{returnRequest}/reject', [AdminReturnRequestController::class, 'reject'])->name('return-requests.reject');
        Route::patch('return-requests/{returnRequest}/shipping-back', [AdminReturnRequestController::class, 'shippingBack'])->name('return-requests.shipping-back');
        Route::patch('return-requests/{returnRequest}/receive', [AdminReturnRequestController::class, 'receive'])->name('return-requests.receive');
        Route::patch('return-requests/{returnRequest}/inspect', [AdminReturnRequestController::class, 'inspect'])->name('return-requests.inspect');
        // Xử lý kết quả kiểm tra: 2 nút "Hàng hợp lệ" / "Hàng gian lận"
        Route::post('return-requests/{returnRequest}/process-inspection', [AdminReturnRequestController::class, 'processInspection'])->name('return-requests.process-inspection');
        Route::patch('return-requests/{returnRequest}/refund', [AdminReturnRequestController::class, 'refund'])->name('return-requests.refund');
        Route::patch('return-requests/{returnRequest}/exchange', [AdminReturnRequestController::class, 'exchange'])->name('return-requests.exchange');
        Route::patch('return-requests/{returnRequest}/complete', [AdminReturnRequestController::class, 'complete'])->name('return-requests.complete');

        // ===== INSPECTION (Kiểm tra hàng) =====
        // Bước bắt buộc trong quy trình xử lý yêu cầu hoàn/đổi
        Route::get('inspections', [AdminInspectionController::class, 'index'])->name('inspections.index');
        Route::get('inspections/{returnRequest}', [AdminInspectionController::class, 'show'])->name('inspections.show');
        Route::post('inspections/{returnRequest}', [AdminInspectionController::class, 'process'])->name('inspections.process');

        // ===== RESHIP (Gửi lại hàng) =====
        Route::get('reships', [ReshipController::class, 'index'])->name('reships.index');
        Route::patch('reships/{reship}', [ReshipController::class, 'update'])->name('reships.update');
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
    Route::get('/orders/{order}/returns/create', [ReturnRequestController::class, 'create'])->name('orders.returns.create');
    Route::post('/orders/{order}/returns', [ReturnRequestController::class, 'store'])->name('orders.returns.store');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});
