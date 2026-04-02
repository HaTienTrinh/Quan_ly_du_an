@extends('customers.layouts.layout')

@section('title', 'Giỏ Hàng - TTM SHOP')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#0f1419] to-[#05070a] pt-12">
        <div class="max-w-7xl mx-auto px-4 md:px-12">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter mb-4">Giỏ Hàng Của Bạn</h1>
                <p class="text-gray-400 text-lg">Kiểm tra và quản lý các sản phẩm trong giỏ hàng</p>
            </div>

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="mb-8 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                    <ul class="text-red-200 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-8 p-4 bg-green-500/20 border border-green-500/50 rounded-lg">
                    <p class="text-green-200 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-8 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                    <p class="text-red-200 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if (empty($cart))
                <!-- Empty Cart -->
                <div class="text-center py-24">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-600 mb-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-2xl font-bold mb-2">Giỏ hàng trống</h2>
                    <p class="text-gray-400 mb-8">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                    <a href="{{ route('products') }}"
                        class="inline-block px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                        Tiếp tục mua sắm
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="glass rounded-2xl overflow-hidden">
                            <div class="p-6 border-b border-white/10">
                                <h2 class="text-xl font-bold">Sản phẩm ({{ $itemCount }})</h2>
                            </div>

                            <div class="divide-y divide-white/10">
                                @foreach ($cart as $item)
                                    <div class="p-6 hover:bg-white/5 transition">
                                        <div class="flex gap-4">
                                            <!-- Product Image -->
                                            <div class="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden">
                                                @if ($item['product_thumbnail'])
                                                    <img src="{{ asset('storage/' . $item['product_thumbnail']) }}"
                                                        alt="{{ $item['product_name'] }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-500">
                                                        Không có hình
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg mb-1">{{ $item['product_name'] }}</h3>
                                                <p class="text-orange-500 font-bold mb-3">
                                                    {{ number_format($item['unit_price'], 0, ',', '.') }} ₫
                                                </p>

                                                <!-- Quantity Control -->
                                                <form action="{{ route('cart.update', $item['product_id']) }}"
                                                    method="POST" class="flex items-center gap-2 mb-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <div
                                                        class="flex items-center border border-gray-600 rounded-lg overflow-hidden">
                                                        <button type="button"
                                                            onclick="var qty=this.closest('form').querySelector('input[name=quantity]'); if(qty.value>1)qty.stepDown();"
                                                            class="px-3 py-2 hover:bg-white/10">-</button>
                                                        <input type="number" name="quantity"
                                                            value="{{ $item['quantity'] }}" min="1"
                                                            class="w-12 text-center bg-transparent">
                                                        <button type="button"
                                                            onclick="var qty=this.closest('form').querySelector('input[name=quantity]'); qty.stepUp();"
                                                            class="px-3 py-2 hover:bg-white/10">+</button>
                                                    </div>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 rounded-lg text-sm font-medium transition">
                                                        Cập nhật
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-white mb-3">
                                                    {{ number_format($item['subtotal'], 0, ',', '.') }} ₫
                                                </div>
                                                <form action="{{ route('cart.remove', $item['product_id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-block px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm transition">
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

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="glass rounded-2xl p-6 sticky top-24">
                            <h2 class="text-xl font-bold mb-6">Chi tiết đơn hàng</h2>

                            <div class="space-y-3 mb-6 pb-6 border-b border-white/10">
                                <div class="flex justify-between text-gray-300">
                                    <span>Tạm tính:</span>
                                    <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Phí vận chuyển:</span>
                                    <span class="text-orange-500">Miễn phí</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Giảm giá:</span>
                                    <span>0 ₫</span>
                                </div>
                            </div>

                            <div class="flex justify-between text-xl font-bold mb-6">
                                <span>Tổng cộng:</span>
                                <span class="text-orange-500">{{ number_format($total, 0, ',', '.') }} ₫</span>
                            </div>

                            @auth
                                <a href="{{ route('checkout') }}"
                                    class="w-full block text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition mb-3">
                                    Tiến hành thanh toán
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full block text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition mb-3">
                                    Đăng nhập để thanh toán
                                </a>
                            @endauth

                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full px-6 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-400 font-semibold rounded-lg transition">
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
