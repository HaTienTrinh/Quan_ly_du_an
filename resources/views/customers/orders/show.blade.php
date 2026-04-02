@extends('customers.layouts.layout')

@section('title', 'Chi Tiết Đơn Hàng - TTM SHOP')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#0f1419] to-[#05070a] pt-12">
        <div class="max-w-4xl mx-auto px-4 md:px-12 pb-12">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('orders.index') }}" class="text-orange-500 hover:text-orange-400 mb-4 inline-block">
                    ← Quay lại danh sách đơn hàng
                </a>
                <h1 class="text-4xl font-extrabold tracking-tighter">Đơn Hàng {{ $order->order_code }}</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Status -->
                    <div class="glass rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-6">Trạng thái đơn hàng</h2>

                        <div class="space-y-4">
                            <!-- Timeline -->
                            <div class="relative">
                                @php
                                    $statuses = [
                                        'pending' => ['label' => 'Chờ xác nhận', 'color' => 'yellow'],
                                        'confirmed' => ['label' => 'Đã xác nhận', 'color' => 'blue'],
                                        'shipping' => ['label' => 'Đang giao hàng', 'color' => 'purple'],
                                        'delivered' => ['label' => 'Đã giao hàng', 'color' => 'green'],
                                        'cancelled' => ['label' => 'Đã hủy', 'color' => 'red'],
                                    ];

                                    $currentStatus = $order->status;
                                    $statusOrder = ['pending', 'confirmed', 'shipping', 'delivered'];
                                @endphp

                                @foreach ($statusOrder as $status)
                                    @php
                                        $isCompleted = in_array(
                                            $currentStatus,
                                            array_slice($statusOrder, array_search($status, $statusOrder)),
                                        );
                                    @endphp

                                    <div class="flex items-center gap-4 mb-4">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $isCompleted ? 'bg-' . $statuses[$status]['color'] . '-500' : 'bg-gray-700' }}">
                                            @if ($isCompleted)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <span class="text-gray-400 text-sm">{{ $loop->index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold">{{ $statuses[$status]['label'] }}</p>
                                            <p class="text-gray-400 text-sm">
                                                @if ($status === 'pending' && $order->created_at)
                                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                                @elseif($status === 'confirmed' && $order->confirmed_at)
                                                    {{ $order->confirmed_at->format('d/m/Y H:i') }}
                                                @else
                                                    Sẽ cập nhật
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="glass rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-6">Thông tin giao hàng</h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Người nhận</p>
                                <p class="font-semibold">{{ $order->receiver_name }}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 text-sm mb-1">Số điện thoại</p>
                                <p class="font-semibold">{{ $order->receiver_phone }}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 text-sm mb-1">Địa chỉ giao hàng</p>
                                <p class="font-semibold">
                                    {{ $order->receiver_address_detail }},<br>
                                    {{ $order->receiver_ward }},
                                    {{ $order->receiver_district }},<br>
                                    {{ $order->receiver_province }}
                                </p>
                            </div>

                            @if ($order->note)
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">Ghi chú</p>
                                    <p class="font-semibold">{{ $order->note }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="glass rounded-2xl p-6">
                        <h2 class="text-xl font-bold mb-6">Sản phẩm ({{ $order->items->count() }})</h2>

                        <div class="space-y-4">
                            @foreach ($order->items as $item)
                                <div class="flex gap-4 p-4 bg-white/5 rounded-lg">
                                    <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden">
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
                                        <h3 class="font-semibold mb-2">{{ $item->product_name }}</h3>
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
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Order Summary -->
                    <div class="glass rounded-2xl p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-6">Chi tiết thanh toán</h2>

                        <div class="space-y-3 mb-6 pb-6 border-b border-white/10">
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

                        <div class="flex justify-between text-xl font-bold mb-6 pb-6 border-b border-white/10">
                            <span>Tổng cộng:</span>
                            <span class="text-orange-500">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                        </div>

                        <!-- Payment Info -->
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Phương thức thanh toán</p>
                                <p class="font-semibold">
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
                                <p class="text-gray-400 text-sm mb-2">Trạng thái thanh toán</p>
                                @if ($order->payment_status === 'paid')
                                    <span class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-semibold">
                                        Đã thanh toán
                                    </span>
                                @else
                                    <span
                                        class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-lg text-sm font-semibold">
                                        Chưa thanh toán
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Order Date -->
                        <div class="mt-6 pt-6 border-t border-white/10">
                            <p class="text-gray-400 text-sm mb-1">Ngày tạo đơn hàng</p>
                            <p class="font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
