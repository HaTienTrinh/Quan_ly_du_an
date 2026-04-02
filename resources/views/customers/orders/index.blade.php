@extends('customers.layouts.layout')

@section('title', 'Đơn Hàng Của Tôi - TTM SHOP')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#0f1419] to-[#05070a] pt-12">
        <div class="max-w-7xl mx-auto px-4 md:px-12 pb-12">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter mb-4">Đơn Hàng Của Tôi</h1>
                <p class="text-gray-400 text-lg">Theo dõi trạng thái các đơn hàng của bạn</p>
            </div>

            @if ($orders->isEmpty())
                <!-- Empty Orders -->
                <div class="text-center py-24">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-600 mb-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-2xl font-bold mb-2">Bạn chưa có đơn hàng nào</h2>
                    <p class="text-gray-400 mb-8">Hãy bắt đầu mua sắm tại TTM SHOP ngay hôm nay</p>
                    <a href="{{ route('products') }}"
                        class="inline-block px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                        Mua sắm ngay
                    </a>
                </div>
            @else
                <!-- Orders List -->
                <div class="space-y-4 mb-12">
                    @foreach ($orders as $order)
                        <a href="{{ route('orders.show', $order->id) }}"
                            class="glass rounded-2xl p-6 hover:bg-white/10 transition block">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- Order Code -->
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">Mã đơn hàng</p>
                                    <p class="text-lg font-bold text-orange-500">{{ $order->order_code }}</p>
                                </div>

                                <!-- Order Date -->
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">Ngày đặt hàng</p>
                                    <p class="font-semibold">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>

                                <!-- Order Status -->
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">Trạng thái</p>
                                    <div class="flex items-center gap-2">
                                        @if ($order->status === 'pending')
                                            <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm">
                                                Chờ xác nhận
                                            </span>
                                        @elseif($order->status === 'confirmed')
                                            <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm">
                                                Đã xác nhận
                                            </span>
                                        @elseif($order->status === 'shipping')
                                            <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm">
                                                Đang giao hàng
                                            </span>
                                        @elseif($order->status === 'delivered')
                                            <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">
                                                Đã giao hàng
                                            </span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm">
                                                Đã hủy
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Total Amount -->
                                <div class="text-right">
                                    <p class="text-gray-400 text-sm mb-1">Tổng cộng</p>
                                    <p class="text-xl font-bold text-orange-500">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $orders->links('pagination::simple-tailwind') }}
                </div>
            @endif

            <!-- Back Link -->
            <div class="mt-8">
                <a href="{{ route('home') }}"
                    class="inline-block px-6 py-2 text-orange-500 hover:text-orange-400 font-medium transition">
                    ← Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
@endsection
