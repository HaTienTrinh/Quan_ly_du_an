@extends('customers.layouts.layout')

@section('title', 'Đặt Hàng Thành Công - TTM SHOP')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#0f1419] to-[#05070a] pt-12">
        <div class="max-w-3xl mx-auto px-4 md:px-12 pb-12">
            <!-- Success Message -->
            <div class="glass rounded-2xl p-8 text-center mb-8">
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tighter mb-4">Đặt Hàng Thành Công!</h1>
                <p class="text-gray-400 mb-2">Cảm ơn bạn đã mua sắm tại TTM SHOP</p>
                <p class="text-gray-500 text-sm">Chúng tôi sẽ xác nhận đơn hàng của bạn trong thời gian sớm nhất</p>
            </div>

            <!-- Order Details -->
            <div class="glass rounded-2xl overflow-hidden">
                <!-- Order Header -->
                <div class="p-6 border-b border-white/10 bg-gradient-to-r from-orange-500/10 to-transparent">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Mã đơn hàng</p>
                            <p class="text-2xl font-bold text-orange-500">{{ $order->order_code }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Ngày đặt hàng</p>
                            <p class="text-xl font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="p-6 border-b border-white/10">
                    <h2 class="text-lg font-bold mb-4">Thông tin người nhận</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Tên</p>
                            <p class="text-white font-semibold">{{ $order->receiver_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Số điện thoại</p>
                            <p class="text-white font-semibold">{{ $order->receiver_phone }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-400 text-sm mb-1">Địa chỉ giao hàng</p>
                            <p class="text-white">
                                {{ $order->receiver_address_detail }},
                                {{ $order->receiver_ward }},
                                {{ $order->receiver_district }},
                                {{ $order->receiver_province }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="p-6 border-b border-white/10">
                    <h2 class="text-lg font-bold mb-4">Sản phẩm trong đơn hàng</h2>
                    <div class="space-y-4">
                        @foreach ($order->items as $item)
                            <div class="flex gap-4 p-4 bg-white/5 rounded-lg">
                                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden">
                                    @if ($item->product_thumbnail)
                                        <img src="{{ asset('storage/' . $item->product_thumbnail) }}"
                                            alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-500">
                                            Không có hình
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <h3 class="font-semibold mb-1">{{ $item->product_name }}</h3>
                                    <p class="text-gray-400 text-sm">
                                        {{ number_format($item->unit_price, 0, ',', '.') }} ₫ x {{ $item->quantity }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="p-6 border-b border-white/10">
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-300">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Phí vận chuyển:</span>
                            <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Giảm giá:</span>
                            <span>{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                </div>

                <!-- Order Total -->
                <div class="p-6 bg-orange-500/10 border-t border-orange-500/20">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold">Tổng cộng:</span>
                        <span class="text-3xl font-bold text-orange-500">
                            {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                        </span>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="p-6 border-t border-white/10">
                    <h2 class="text-lg font-bold mb-4">Trạng thái đơn hàng</h2>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full bg-green-500"></div>
                            <div>
                                <p class="font-semibold">Đơn hàng đã được tiếp nhận</p>
                                <p class="text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 opacity-50">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-600"></div>
                            <div>
                                <p class="font-semibold">Chờ xác nhận</p>
                                <p class="text-gray-400 text-sm">Sẽ cập nhật trong thời gian sớm nhất</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 opacity-50">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-600"></div>
                            <div>
                                <p class="font-semibold">Đang giao hàng</p>
                                <p class="text-gray-400 text-sm">Sẽ cập nhật sau khi xác nhận</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 opacity-50">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-600"></div>
                            <div>
                                <p class="font-semibold">Đã giao hàng</p>
                                <p class="text-gray-400 text-sm">Sẽ cập nhật khi hoàn thành giao hàng</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="p-6 border-t border-white/10">
                    <h2 class="text-lg font-bold mb-4">Thông tin thanh toán</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Phương thức thanh toán</p>
                            <p class="text-white font-semibold">
                                @if ($order->payment_method === 'cash')
                                    Thanh toán khi nhận hàng
                                @elseif($order->payment_method === 'credit_card')
                                    Thẻ tín dụng
                                @elseif($order->payment_method === 'bank_transfer')
                                    Chuyển khoản ngân hàng
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm mb-1">Trạng thái thanh toán</p>
                            <p class="text-white font-semibold">
                                @if ($order->payment_status === 'paid')
                                    <span class="text-green-400">Đã thanh toán</span>
                                @else
                                    <span class="text-yellow-400">Chưa thanh toán</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions  -->
            <div class="mt-8 flex gap-4 justify-center">
                <a href="{{ route('home') }}"
                    class="px-8 py-3 border border-gray-600 rounded-lg font-semibold hover:border-orange-500 transition">
                    ← Quay lại trang chủ
                </a>
                <a href="{{ route('orders.show', $order->id) }}"
                    class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                    Xem chi tiết đơn hàng →
                </a>
            </div>
        </div>
    </div>
@endsection
