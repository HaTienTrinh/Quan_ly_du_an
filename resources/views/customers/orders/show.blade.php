@extends('customers.layouts.layout')

@section('title', 'Chi Tiết Đơn Hàng - TTM SHOP')

@section('content')
<div class="min-h-screen bg-white pt-12">
    <div class="max-w-4xl mx-auto px-4 md:px-12 pb-12">

        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('orders.index') }}"
               class="text-orange-500 hover:text-orange-600 mb-4 inline-block">
                ← Quay lại
            </a>

            <h1 class="text-3xl font-extrabold text-slate-900">
                Đơn Hàng {{ $order->order_code }}
            </h1>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- LEFT -->
            <div class="lg:col-span-2 space-y-6">

                <!-- STATUS -->
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-slate-900">
                        Trạng thái đơn hàng
                    </h2>

                    @php
                        $statuses = [
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'shipping' => 'Đang giao',
                            'delivered' => 'Hoàn thành'
                        ];

                        $currentIndex = array_search($order->status, array_keys($statuses));
                    @endphp

                    @foreach ($statuses as $key => $label)
                        @php
                            $index = array_search($key, array_keys($statuses));
                            $active = $index <= $currentIndex;
                        @endphp

                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full
                                {{ $active ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                                {{ $loop->iteration }}
                            </div>

                            <div>
                                <p class="font-semibold text-slate-900">{{ $label }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $active ? 'Đã cập nhật' : 'Chờ xử lý' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- SHIPPING -->
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-slate-900">
                        Thông tin giao hàng
                    </h2>

                    <div class="space-y-3 text-slate-700">
                        <p><strong>Người nhận:</strong> {{ $order->receiver_name }}</p>
                        <p><strong>SĐT:</strong> {{ $order->receiver_phone }}</p>
                        <p>
                            <strong>Địa chỉ:</strong><br>
                            {{ $order->receiver_address_detail }},
                            {{ $order->receiver_ward }},
                            {{ $order->receiver_district }},
                            {{ $order->receiver_province }}
                        </p>

                        @if ($order->note)
                            <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                        @endif
                    </div>
                </div>

                <!-- ITEMS -->
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-slate-900">
                        Sản phẩm ({{ $order->items->count() }})
                    </h2>

                    <div class="space-y-4">
                        @foreach ($order->items as $item)
                            <div class="flex gap-4 p-4 bg-slate-50 rounded-lg">

                                <div class="w-20 h-20 rounded-lg overflow-hidden">
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
                                    <h3 class="font-semibold text-slate-900">
                                        {{ $item->product_name }}
                                    </h3>

                                    <p class="text-sm text-slate-500">
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

            </div>

            <!-- RIGHT -->
            <div>
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sticky top-24">

                    <h2 class="text-xl font-bold mb-6 text-slate-900">
                        Thanh toán
                    </h2>

                    <div class="space-y-2 text-slate-600 mb-4">
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

                    <div class="flex justify-between text-lg font-bold border-t pt-4 text-slate-900">
                        <span>Tổng</span>
                        <span class="text-orange-500">
                            {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                        </span>
                    </div>

                    <!-- Payment -->
                    <div class="mt-6 text-slate-700 space-y-2">
                        <p><strong>Phương thức:</strong> {{ $order->payment_method }}</p>

                        <p>
                            <strong>Trạng thái:</strong>
                            @if ($order->payment_status === 'paid')
                                <span class="text-green-600">Đã thanh toán</span>
                            @else
                                <span class="text-yellow-600">Chưa thanh toán</span>
                            @endif
                        </p>

                        <p class="text-sm text-slate-500">
                            Ngày: {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection