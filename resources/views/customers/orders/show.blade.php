@extends('customers.layouts.layout')

@section('title', 'Chi Tiết Đơn Hàng')

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

        $steps = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang chuẩn bị hàng',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao thành công',
        ];

        $stepKeys = array_keys($steps);
        $currentIndex = array_search($order->status, $stepKeys, true);
        $currentIndex = $currentIndex === false ? -1 : $currentIndex;
        $sortedHistories = $order->statusHistories->sortBy('created_at');
        $reachedStatuses = $sortedHistories->pluck('to_status')->all();
        $canCreateReturnRequest = $order->canBeReturned() && $order->items->contains(fn ($item) => $item->returnRequest === null);
    @endphp

    <div class="min-h-screen bg-slate-50 pt-12">
        <div class="mx-auto max-w-6xl px-4 pb-12 md:px-12">
            <div class="mb-8">
                <a href="{{ route('orders.index') }}" class="inline-block text-orange-500 transition hover:text-orange-600">
                    ← Quay lại danh sách đơn hàng
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

            <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Đơn hàng</p>
                        <h1 class="mt-2 text-3xl font-extrabold text-slate-900">{{ $order->order_code }}</h1>
                        <p class="mt-3 text-slate-500">
                            Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <span
                            class="inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $order->status_label }}
                        </span>

                        @if ($canCreateReturnRequest)
                            <a href="{{ route('orders.returns.create', $order) }}"
                                class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 font-semibold text-slate-700 transition hover:bg-slate-200">
                                Yêu cầu trả hàng
                            </a>
                        @endif

                        @if ($order->canBeCancelled())
                            <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="cancel_reason" value="Khách hàng hủy đơn từ trang chi tiết đơn hàng.">
                                <button type="submit"
                                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 font-semibold text-red-600 transition hover:bg-red-100">
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
                                    class="rounded-xl bg-emerald-500 px-4 py-2 font-semibold text-white transition hover:bg-emerald-600">
                                    Xác nhận đã nhận hàng
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-8 lg:col-span-2">
                    <div id="tracking" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between gap-4">
                            <h2 class="text-xl font-bold text-slate-900">Theo dõi trạng thái</h2>
                            <span
                                class="inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $order->status_label }}
                            </span>
                        </div>

                        <div class="space-y-5">
                            @foreach ($steps as $key => $label)
                                @php
                                    $index = array_search($key, $stepKeys, true);
                                    $isDone = in_array($key, $reachedStatuses, true) || $index <= $currentIndex;
                                    $isCurrent = $order->status === $key;
                                @endphp

                                <div class="flex gap-4">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $isDone ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                                        {{ $loop->iteration }}
                                    </div>

                                    <div class="flex-1 border-b border-slate-100 pb-5 last:border-b-0 last:pb-0">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <p class="font-semibold text-slate-900">{{ $label }}</p>
                                            @if ($isCurrent)
                                                <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">
                                                    Hiện tại
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $isDone ? 'Đã cập nhật trạng thái này.' : 'Đang chờ bước tiếp theo.' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($order->status === 'cancelled')
                            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                                <p class="font-semibold">Đơn hàng đã bị hủy</p>
                                <p class="mt-1 text-sm">
                                    Lý do: {{ $order->cancel_reason ?: 'Không có ghi chú.' }}
                                </p>
                                @if ($order->cancelled_at)
                                    <p class="mt-1 text-sm">Thời gian: {{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-xl font-bold text-slate-900">Chi tiết sản phẩm</h2>

                        <div class="space-y-4">
                            @foreach ($order->items as $item)
                                <div class="flex gap-4 rounded-2xl bg-slate-50 p-4">
                                    <div class="h-20 w-20 overflow-hidden rounded-2xl bg-slate-200">
                                        @if ($item->product_thumbnail)
                                            <img src="{{ asset('storage/' . $item->product_thumbnail) }}"
                                                alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-400">
                                                No image
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="font-semibold text-slate-900">{{ $item->product_name }}</h3>
                                        @if ($item->product_size)
                                            <p class="mt-1 text-sm text-slate-500">
                                                Size: <span class="font-semibold text-slate-700">{{ $item->product_size }}</span>
                                            </p>
                                        @endif
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ number_format($item->unit_price, 0, ',', '.') }} ₫ x {{ $item->quantity }}
                                        </p>

                                        @if ($item->returnRequest)
                                            <div class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $returnStatusClasses[$item->returnRequest->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                Trả hàng: {{ $item->returnRequest->status_label }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-right font-bold text-slate-900">
                                        {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between gap-4">
                            <h2 class="text-xl font-bold text-slate-900">Yêu cầu trả hàng</h2>
                            @if ($canCreateReturnRequest)
                                <a href="{{ route('orders.returns.create', $order) }}"
                                    class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                                    Tạo yêu cầu mới
                                </a>
                            @endif
                        </div>

                        @if ($order->returnRequests->isEmpty())
                            <p class="text-slate-500">Đơn hàng này chưa có yêu cầu trả hàng nào.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($order->returnRequests->sortByDesc('created_at') as $returnRequest)
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <p class="font-semibold text-slate-900">{{ $returnRequest->orderItem->product_name }}</p>
                                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $returnStatusClasses[$returnRequest->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                        {{ $returnRequest->status_label }}
                                                    </span>
                                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                                        {{ $returnRequest->request_type_label }}
                                                    </span>
                                                </div>
                                                <p class="mt-2 text-sm text-slate-500">{{ $returnRequest->reason }}</p>
                                                @if ($returnRequest->logistics_method_label)
                                                    <p class="mt-2 text-sm text-slate-500">
                                                        Vận chuyển trả về: {{ $returnRequest->logistics_method_label }}
                                                    </p>
                                                @endif
                                                @if ($returnRequest->replacementOrder)
                                                    <p class="mt-2 text-sm text-slate-500">
                                                        Đơn gửi lại:
                                                        <a href="{{ route('orders.show', $returnRequest->replacementOrder) }}"
                                                            class="font-medium text-orange-500 transition hover:text-orange-600">
                                                            {{ $returnRequest->replacementOrder->order_code }}
                                                        </a>
                                                    </p>
                                                @endif
                                                @if ($returnRequest->rejection_reason)
                                                    <p class="mt-2 text-sm font-medium text-red-600">
                                                        Từ chối: {{ $returnRequest->rejection_reason }}
                                                    </p>
                                                @endif
                                            </div>

                                            <p class="text-sm text-slate-500">{{ $returnRequest->created_at->format('d/m/Y H:i') }}</p>
                                        </div>

                                        @if (! empty($returnRequest->evidence_assets))
                                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                                @foreach ($returnRequest->evidence_assets as $evidence)
                                                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-3">
                                                        @if ($evidence['type'] === 'image')
                                                            <a href="{{ $evidence['url'] }}" target="_blank" class="block">
                                                                <img src="{{ $evidence['url'] }}" alt="{{ $evidence['name'] }}"
                                                                    class="h-48 w-full rounded-xl object-cover">
                                                            </a>
                                                        @elseif ($evidence['type'] === 'video')
                                                            <video controls class="h-48 w-full rounded-xl object-cover">
                                                                <source src="{{ $evidence['url'] }}">
                                                            </video>
                                                        @else
                                                            <a href="{{ $evidence['url'] }}" target="_blank"
                                                                class="flex h-48 items-center justify-center rounded-xl bg-slate-100 text-sm font-medium text-slate-600">
                                                                Mở tệp minh chứng
                                                            </a>
                                                        @endif

                                                        <div class="mt-3 flex items-center justify-between gap-3">
                                                            <span class="text-sm font-medium text-slate-700">{{ $evidence['name'] }}</span>
                                                            <a href="{{ $evidence['url'] }}" target="_blank"
                                                                class="text-sm font-medium text-orange-500 transition hover:text-orange-600">
                                                                Mở file
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($returnRequest->statusHistories->isNotEmpty())
                                            <div class="mt-4 space-y-3 border-t border-slate-200 pt-4">
                                                @foreach ($returnRequest->statusHistories as $history)
                                                    <div class="rounded-xl bg-white px-4 py-3">
                                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                            <div class="text-sm font-semibold text-slate-900">
                                                                {{ \App\Models\ReturnRequest::STATUS_LABELS[$history->to_status] ?? $history->to_status }}
                                                            </div>
                                                            <div class="text-sm text-slate-500">
                                                                {{ $history->created_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        </div>
                                                        <p class="mt-2 text-sm text-slate-500">
                                                            {{ $history->note ?: 'Không có ghi chú.' }}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-xl font-bold text-slate-900">Lịch sử cập nhật</h2>

                        @if ($sortedHistories->isEmpty())
                            <p class="text-slate-500">Chưa có lịch sử cập nhật cho đơn hàng này.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($sortedHistories as $history)
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <p class="font-semibold text-slate-900">
                                                    {{ \App\Models\Order::STATUS_LABELS[$history->to_status] ?? $history->to_status }}
                                                </p>
                                                <p class="mt-1 text-sm text-slate-500">
                                                    @if ($history->from_status)
                                                        Từ
                                                        {{ \App\Models\Order::STATUS_LABELS[$history->from_status] ?? $history->from_status }}
                                                        sang
                                                    @endif
                                                    {{ \App\Models\Order::STATUS_LABELS[$history->to_status] ?? $history->to_status }}
                                                </p>
                                            </div>

                                            <p class="text-sm text-slate-500">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                                        </div>

                                        <p class="mt-3 text-sm text-slate-600">
                                            {{ $history->note ?: 'Không có ghi chú thêm.' }}
                                        </p>

                                        <p class="mt-2 text-xs font-medium uppercase tracking-[0.2em] text-slate-400">
                                            {{ $history->changedBy?->name ?? 'Hệ thống / Khách hàng' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-24">
                        <h2 class="mb-6 text-xl font-bold text-slate-900">Thông tin đơn hàng</h2>

                        <div class="space-y-3 text-slate-600">
                            <div class="flex justify-between gap-4">
                                <span>Tạm tính</span>
                                <span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Phí vận chuyển</span>
                                <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Giảm giá</span>
                                <span>{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between border-t border-slate-200 pt-4 text-lg font-bold text-slate-900">
                            <span>Tổng thanh toán</span>
                            <span class="text-orange-500">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                        </div>

                        <div class="mt-6 space-y-3 border-t border-slate-200 pt-6 text-slate-700">
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_label }}</p>
                            <p>
                                <strong>Thanh toán:</strong>
                                @if ($order->payment_status === 'paid')
                                    <span class="font-semibold text-emerald-600">Đã thanh toán</span>
                                @elseif($order->payment_status === 'refunded')
                                    <span class="font-semibold text-slate-600">Đã hoàn tiền</span>
                                @else
                                    <span class="font-semibold text-amber-600">Chưa thanh toán</span>
                                @endif
                            </p>
                            @if ($order->paid_at)
                                <p><strong>Thời gian thanh toán:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-xl font-bold text-slate-900">Thông tin nhận hàng</h2>

                        <div class="space-y-3 text-slate-700">
                            <p><strong>Người nhận:</strong> {{ $order->receiver_name }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->full_address }}</p>

                            @if ($order->note)
                                <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
