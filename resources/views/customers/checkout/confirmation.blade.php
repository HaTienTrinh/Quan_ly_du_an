@extends('customers.layouts.layout')

@section('title', 'Đặt Hàng Thành Công - TT SHOP')

@section('content')
<div class="min-h-screen bg-white pt-12">
    <div class="max-w-3xl mx-auto px-4 md:px-12 pb-12">

        <!-- Success -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-8 text-center mb-8">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl md:text-4xl font-extrabold mb-4 text-slate-900">
                Đặt Hàng Thành Công!
            </h1>
            <p class="text-slate-600 mb-2">Cảm ơn bạn đã mua sắm tại TT SHOP</p>
            <p class="text-slate-400 text-sm">Chúng tôi sẽ xác nhận đơn hàng sớm nhất</p>
        </div>

        <!-- Order -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">

            <!-- Header -->
            <div class="p-6 border-b border-slate-200 bg-orange-50">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-slate-500 text-sm">Mã đơn hàng</p>
                        <p class="text-2xl font-bold text-orange-500">
                            {{ $order->order_code }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm">Ngày đặt</p>
                        <p class="text-lg font-semibold text-slate-900">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer -->
            <div class="p-6 border-b border-slate-200">
                <h2 class="font-bold mb-4 text-slate-900">Thông tin người nhận</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-slate-500 text-sm">Tên</p>
                        <p class="font-semibold text-slate-900">{{ $order->receiver_name }}</p>
                    </div>

                    <div>
                        <p class="text-slate-500 text-sm">SĐT</p>
                        <p class="font-semibold text-slate-900">{{ $order->receiver_phone }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-slate-500 text-sm">Địa chỉ</p>
                        <p class="text-slate-800">
                            {{ $order->receiver_address_detail }},
                            {{ $order->receiver_ward }},
                            {{ $order->receiver_district }},
                            {{ $order->receiver_province }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="p-6 border-b border-slate-200">
                <h2 class="font-bold mb-4 text-slate-900">Sản phẩm</h2>

                <div class="space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 p-4 bg-slate-50 rounded-lg">
                            <div class="w-16 h-16 rounded-lg overflow-hidden">
                                @if ($item->product_thumbnail)
                                    <img src="{{ asset('storage/' . $item->product_thumbnail) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                        No img
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900">{{ $item->product_name }}</h3>
                                @if ($item->product_size_name)
                                    <p class="text-slate-500 text-sm">Size: {{ $item->product_size_name }}</p>
                                @endif
                                <p class="text-slate-500 text-sm">
                                    {{ number_format($item->unit_price, 0, ',', '.') }} ₫ x {{ $item->quantity }}
                                </p>
                            </div>

                            <div class="font-bold text-slate-900">
                                {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary -->
            <div class="p-6 border-b border-slate-200 text-slate-600 space-y-2">
                <div class="flex justify-between">
                    <span>Tạm tính</span>
                    <span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                </div>

                <div class="flex justify-between">
                    <span>Ship</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                </div>

                <div class="flex justify-between">
                    <span>Giảm giá</span>
                    <span>{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                </div>
            </div>

            <!-- Total -->
            <div class="p-6 bg-orange-50">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-slate-900">Tổng</span>
                    <span class="text-2xl font-bold text-orange-500">
                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                    </span>
                </div>
            </div>

            <!-- Status -->
            <div class="p-6 border-t border-slate-200">
                <h2 class="font-bold mb-4 text-slate-900">Trạng thái</h2>

                <div class="space-y-3 text-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        Đã tiếp nhận
                    </div>

                    <div class="flex items-center gap-3 opacity-50">
                        <div class="w-3 h-3 border rounded-full"></div>
                        Chờ xác nhận
                    </div>

                    <div class="flex items-center gap-3 opacity-50">
                        <div class="w-3 h-3 border rounded-full"></div>
                        Đang giao
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="p-6 border-t border-slate-200">
                <h2 class="font-bold mb-4 text-slate-900">Thanh toán</h2>

                <div class="grid md:grid-cols-2 gap-6 text-slate-700">
                    <div>
                        <p class="text-sm text-slate-500">Phương thức</p>
                        <p class="font-semibold">
                            {{ $order->payment_label }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">Trạng thái</p>
                        <p class="font-semibold">
                            @php
                                $paymentStatusLabels = [
                                    'paid'     => 'Đã thanh toán',
                                    'unpaid'   => 'Chưa thanh toán',
                                    'refunded' => 'Đã hoàn tiền',
                                ];
                            @endphp
                            {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Actions -->
        <div class="mt-8 flex gap-4 justify-center">
            <a href="{{ route('home') }}"
                class="px-6 py-3 border border-slate-300 rounded-lg text-black hover:border-orange-500">
                ← Trang chủ
            </a>

            <a href="{{ route('orders.show', $order->id) }}"
                class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg">
                Xem đơn →
            </a>
        </div>

    </div>
</div>
@endsection
