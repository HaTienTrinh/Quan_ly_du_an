@extends('customers.layouts.layout')

@section('title', 'Đơn Hàng Của Tôi - TTM SHOP')

@section('content')
<div class="min-h-screen bg-white pt-12">
    <div class="max-w-7xl mx-auto px-4 md:px-12 pb-12">

        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-slate-900">
                Đơn Hàng Của Tôi
            </h1>
            <p class="text-slate-500 text-lg">
                Theo dõi trạng thái các đơn hàng của bạn
            </p>
        </div>

        @if ($orders->isEmpty())
            <!-- Empty -->
            <div class="text-center py-24">
                <h2 class="text-2xl font-bold mb-2 text-slate-900">
                    Bạn chưa có đơn hàng nào
                </h2>
                <p class="text-slate-500 mb-8">
                    Hãy bắt đầu mua sắm ngay
                </p>

                <a href="{{ route('products') }}"
                   class="inline-block px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg">
                    Mua sắm ngay
                </a>
            </div>
        @else
            <!-- Orders -->
            <div class="space-y-4 mb-12">

                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order->id) }}"
                       class="block bg-white border border-slate-200 shadow-sm rounded-2xl p-6 hover:shadow-md transition">

                        <div class="grid md:grid-cols-4 gap-6 items-center">

                            <!-- Code -->
                            <div>
                                <p class="text-slate-500 text-sm">Mã đơn</p>
                                <p class="text-lg font-bold text-orange-500">
                                    {{ $order->order_code }}
                                </p>
                            </div>

                            <!-- Date -->
                            <div>
                                <p class="text-slate-500 text-sm">Ngày đặt</p>
                                <p class="font-semibold text-slate-900">
                                    {{ $order->created_at->format('d/m/Y') }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <p class="text-slate-500 text-sm mb-1">Trạng thái</p>

                                @if ($order->status === 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-sm">
                                        Chờ xác nhận
                                    </span>

                                @elseif($order->status === 'confirmed')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">
                                        Đã xác nhận
                                    </span>

                                @elseif($order->status === 'shipping')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-sm">
                                        Đang giao
                                    </span>

                                @elseif($order->status === 'delivered')
                                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm">
                                        Đã giao
                                    </span>

                                @elseif($order->status === 'cancelled')
                                    <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm">
                                        Đã hủy
                                    </span>
                                @endif
                            </div>

                            <!-- Total -->
                            <div class="text-right">
                                <p class="text-slate-500 text-sm">Tổng</p>
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

        <!-- Back -->
        <div class="mt-8">
            <a href="{{ route('home') }}"
               class="text-orange-500 hover:text-orange-600 font-medium">
                ← Quay lại trang chủ
            </a>
        </div>

    </div>
</div>
@endsection