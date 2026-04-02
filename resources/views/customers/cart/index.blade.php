@extends('customers.layouts.layout')

@section('title', 'Giỏ Hàng - TTM SHOP')

@section('content')
<div class="min-h-screen bg-white pt-12">
    <div class="max-w-7xl mx-auto px-4 md:px-12">

        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter mb-4 text-slate-900">
                Giỏ Hàng Của Bạn
            </h1>
            <p class="text-slate-500 text-lg">
                Kiểm tra và quản lý các sản phẩm trong giỏ hàng
            </p>
        </div>

        <!-- Alert -->
        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-600 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600 text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if (empty($cart))
            <!-- Empty -->
            <div class="text-center py-24">
                <h2 class="text-2xl font-bold mb-2 text-slate-900">Giỏ hàng trống</h2>
                <p class="text-slate-500 mb-8">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="{{ route('products') }}"
                   class="inline-block px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                    Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">

                        <div class="p-6 border-b border-slate-200">
                            <h2 class="text-xl font-bold text-slate-900">
                                Sản phẩm ({{ $itemCount }})
                            </h2>
                        </div>

                        <div class="divide-y divide-slate-200">
                            @foreach ($cart as $item)
                                <div class="p-6 hover:bg-slate-50 transition">
                                    <div class="flex gap-4">

                                        <!-- Image -->
                                        <div class="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden">
                                            @if ($item['product_thumbnail'])
                                                <img src="{{ asset('storage/' . $item['product_thumbnail']) }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400">
                                                    Không có hình
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg mb-1 text-slate-900">
                                                {{ $item['product_name'] }}
                                            </h3>

                                            <p class="text-orange-500 font-bold mb-3">
                                                {{ number_format($item['unit_price'], 0, ',', '.') }} ₫
                                            </p>

                                            <!-- Quantity -->
                                            <form action="{{ route('cart.update', $item['product_id']) }}"
                                                  method="POST"
                                                  class="flex items-center gap-2 mb-3">
                                                @csrf
                                                @method('PUT')

                                                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden">
                                                    <button type="button"
                                                        onclick="var q=this.closest('form').querySelector('input[name=quantity]'); if(q.value>1)q.stepDown();"
                                                        class="text-gray-600 px-3 py-2 hover:bg-slate-100">-</button>

                                                    <input type="number" name="quantity"
                                                           value="{{ $item['quantity'] }}" min="1"
                                                           class="text-gray-600 w-12 text-center bg-transparent">

                                                    <button type="button"
                                                        onclick="var q=this.closest('form').querySelector('input[name=quantity]'); q.stepUp();"
                                                        class="text-gray-600 px-3 py-2 hover:bg-slate-100">+</button>
                                                </div>

                                                <button type="submit"
                                                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm transition">
                                                    Cập nhật
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Price + Remove -->
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-slate-900 mb-3">
                                                {{ number_format($item['subtotal'], 0, ',', '.') }} ₫
                                            </div>

                                            <form action="{{ route('cart.remove', $item['product_id']) }}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm transition">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('products') }}"
                           class="inline-block px-6 py-2 text-orange-500 hover:text-orange-400 font-medium transition">
                            ← Tiếp tục mua sắm
                        </a>
                    </div>
                </div>

                <!-- Summary -->
                <div>
                    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-6 text-slate-900">Chi tiết đơn hàng</h2>

                        <div class="space-y-3 mb-6 pb-6 border-b border-slate-200">
                            <div class="flex justify-between text-slate-600">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                            </div>

                            <div class="flex justify-between text-slate-600">
                                <span>Phí vận chuyển:</span>
                                <span class="text-orange-500">Miễn phí</span>
                            </div>

                            <div class="flex justify-between text-slate-600">
                                <span>Giảm giá:</span>
                                <span>0 ₫</span>
                            </div>
                        </div>

                        <div class="flex justify-between text-xl font-bold mb-6 text-slate-900">
                            <span>Tổng cộng:</span>
                            <span class="text-orange-500">
                                {{ number_format($total, 0, ',', '.') }} ₫
                            </span>
                        </div>

                        @auth
                            <a href="{{ route('checkout') }}"
                               class="w-full block text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg mb-3">
                                Tiến hành thanh toán
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full block text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg mb-3">
                                Đăng nhập để thanh toán
                            </a>
                        @endauth

                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full px-6 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg transition">
                                Xóa giỏ hàng
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection