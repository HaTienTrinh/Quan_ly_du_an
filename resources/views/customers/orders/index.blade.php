@extends('customers.layouts.layout')

@section('title', 'Đơn Hàng Của Tôi')

@section('content')
    @php
        $statusClasses = [
            'pending' => 'bg-amber-100 text-amber-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'processing' => 'bg-indigo-100 text-indigo-700',
            'shipping' => 'bg-purple-100 text-purple-700',
            'delivered' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-red-100 text-red-700',
            'returned' => 'bg-slate-200 text-slate-700',
        ];

        $returnStatusClasses = [
            'pending' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-blue-100 text-blue-700',
            'shipping_back' => 'bg-indigo-100 text-indigo-700',
            'received' => 'bg-slate-200 text-slate-700',
            'inspecting' => 'bg-slate-900 text-white',
            'refunded' => 'bg-emerald-100 text-emerald-700',
            'exchanged' => 'bg-emerald-100 text-emerald-700',
            'completed' => 'bg-emerald-600 text-white',
            'rejected' => 'bg-red-100 text-red-700',
        ];

        $emptyMessages = [
            'all' => 'Bạn chưa có đơn hàng nào.',
            'pending' => 'Không có đơn hàng nào đang chờ xác nhận.',
            'confirmed' => 'Không có đơn hàng nào đã được xác nhận.',
            'processing' => 'Không có đơn hàng nào đang chuẩn bị hàng.',
            'shipping' => 'Không có đơn hàng nào đang giao hàng.',
            'delivered' => 'Không có đơn hàng nào đã giao thành công.',
            'returned' => 'Chưa có yêu cầu trả hàng nào.',
            'cancelled' => 'Không có đơn hàng nào đã hủy.',
        ];
    @endphp

    <div class="min-h-screen bg-slate-50 pt-12">
        <div class="mx-auto max-w-7xl px-4 pb-12 md:px-12">
            <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="mb-3 inline-flex rounded-full bg-orange-100 px-4 py-1 text-sm font-semibold text-orange-600">
                        Quản lý đơn hàng
                    </p>
                    <h1 class="text-4xl font-extrabold text-slate-900 md:text-5xl">Đơn hàng của tôi</h1>
                    <p class="mt-3 text-lg text-slate-500">
                        Theo dõi đơn hàng, gửi yêu cầu trả hàng và xử lý các bước sau mua tại một nơi.
                    </p>
                </div>

                <a href="{{ route('products') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-6 py-3 font-semibold text-white transition hover:bg-orange-600">
                    Tiếp tục mua sắm
                </a>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex flex-wrap gap-3">
                    @foreach ($tabs as $status => $tab)
                        <a href="{{ $status === 'all' ? route('orders.index') : route('orders.index', ['status' => $status]) }}"
                            class="inline-flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition {{ $selectedStatus === $status ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            <span>{{ $tab['label'] }}</span>
                            <span
                                class="inline-flex min-w-7 items-center justify-center rounded-full px-2 py-1 text-xs {{ $selectedStatus === $status ? 'bg-white/20 text-white' : 'bg-white text-slate-600' }}">
                                {{ $tab['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>

                @if ($selectedStatus === 'returned')
                    <p class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        Tab này hiển thị các đơn có phát sinh yêu cầu trả hàng, không làm thay đổi trạng thái giao hàng gốc của đơn.
                    </p>
                @endif
            </div>

            @if ($orders->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center shadow-sm">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $tabs[$selectedStatus]['label'] }}</h2>
                    <p class="mt-3 text-slate-500">{{ $emptyMessages[$selectedStatus] ?? 'Chưa có dữ liệu đơn hàng.' }}</p>
                    <a href="{{ route('products') }}"
                        class="mt-8 inline-flex rounded-xl bg-orange-500 px-8 py-3 font-semibold text-white transition hover:bg-orange-600">
                        Mua sắm ngay
                    </a>
                </div>
            @else
                <div class="space-y-5">
                    @foreach ($orders as $order)
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                                <div class="grid flex-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                                    <div>
                                        <p class="text-sm text-slate-500">Mã đơn hàng</p>
                                        <p class="mt-1 text-lg font-bold text-orange-500">{{ $order->order_code }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-slate-500">Ngày đặt</p>
                                        <p class="mt-1 font-semibold text-slate-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-slate-500">Sản phẩm</p>
                                        <p class="mt-1 font-semibold text-slate-900">{{ $order->items_count }} sản phẩm</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-slate-500">Tổng thanh toán</p>
                                        <p class="mt-1 text-xl font-bold text-slate-900">
                                            {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                        </p>
                                    </div>
                                </div>

                                <div class="lg:text-right">
                                    <p class="text-sm text-slate-500">Trạng thái hiện tại</p>
                                    <span
                                        class="mt-2 inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $order->status_label }}
                                    </span>

                                    @if ($order->latestReturnRequest)
                                        <div class="mt-3">
                                            <p class="text-sm text-slate-500">Yêu cầu trả hàng mới nhất</p>
                                            <span
                                                class="mt-2 inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $returnStatusClasses[$order->latestReturnRequest->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $order->latestReturnRequest->status_label }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-5">
                                <a href="{{ route('orders.show', $order) }}"
                                    class="inline-flex items-center rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700 transition hover:border-orange-500 hover:text-orange-500">
                                    Xem chi tiết
                                </a>

                                <a href="{{ route('orders.show', $order) }}#tracking"
                                    class="inline-flex items-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 font-semibold text-blue-700 transition hover:bg-blue-100">
                                    Theo dõi trạng thái
                                </a>

                                @if ($order->canBeCancelled())
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="cancel_reason" value="Khách hàng hủy đơn từ danh sách đơn hàng.">
                                        <button type="submit"
                                            class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 font-semibold text-red-600 transition hover:bg-red-100">
                                            Hủy đơn hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($order->canBeReceived())
                                    <form action="{{ route('orders.receive', $order) }}" method="POST"
                                        onsubmit="return confirm('Xác nhận bạn đã nhận được đơn hàng này?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center rounded-xl bg-emerald-500 px-4 py-2 font-semibold text-white transition hover:bg-emerald-600">
                                            Xác nhận đã nhận hàng
                                        </button>
                                    </form>
                                @endif

                                @if ($order->canBeReturned() && $order->return_requests_count < $order->items_count)
                                    <a href="{{ route('orders.returns.create', $order) }}"
                                        class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 font-semibold text-slate-700 transition hover:bg-slate-200">
                                        Yêu cầu trả hàng
                                    </a>
                                @endif

                                @if ($order->status === \App\Models\Order::STATUS_CANCELLED)
                                    <form action="{{ route('orders.reorder', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 font-semibold text-orange-600 transition hover:bg-orange-100">
                                            Mua lại
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 flex justify-center">
                    {{ $orders->links('pagination::simple-tailwind') }}
                </div>
            @endif

            <div class="mt-8">
                <a href="{{ route('home') }}" class="font-medium text-orange-500 transition hover:text-orange-600">
                    ← Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
@endsection
