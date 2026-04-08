@extends('layouts.admin')

@section('title', 'Đơn hàng')

@section('page_title', 'Xem danh sách và lọc đơn hàng')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Đơn hàng</span>
@endsection

@section('content')
    @php
        $statusClasses = [
            'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'confirmed' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'processing' => 'bg-info-subtle text-info border border-info-subtle',
            'shipping' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'delivered' => 'bg-success-subtle text-success border border-success-subtle',
            'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'returned' => 'bg-dark-subtle text-dark border border-dark-subtle',
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($summaryCards as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card h-100 p-4 d-flex align-items-center gap-3">
                    <div class="stat-icon {{ $card['class'] }}">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-semibold">{{ $card['label'] }}</div>
                        <div class="fs-4 fw-bold">{{ number_format($card['value']) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-stretch align-items-lg-center mb-4">
        <form method="get" action="{{ route('admin.orders.index') }}" class="row g-2 flex-grow-1">
            <div class="col-12 col-lg">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control border-start-0"
                        placeholder="Tìm theo mã đơn, tên người nhận, tên khách hàng..."
                        aria-label="Tìm kiếm đơn hàng"
                    >
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <select name="status" class="form-select" aria-label="Lọc trạng thái">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected((string) request('status') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <button type="submit" class="btn btn-dark w-100">Lọc đơn</button>
            </div>
            @if (request()->hasAny(['q', 'status']) && (filled(request('q')) || filled(request('status'))))
                <div class="col-6 col-md-auto">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Xóa bộ lọc</a>
                </div>
            @endif
        </form>

        <a href="{{ route('admin.return-requests.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-repeat me-1"></i> Yêu cầu trả hàng
        </a>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Người nhận</th>
                        <th>Ngày đặt</th>
                        <th>Số SP</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th class="pe-4 text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $order->order_code }}
                                </a>
                                <div class="small text-muted">#{{ $order->id }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->user?->name ?? 'Khách vãng lai' }}</div>
                                <div class="small text-muted">{{ $order->user?->email ?? 'Không có email' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->receiver_name }}</div>
                                <div class="small text-muted">{{ $order->receiver_phone }}</div>
                            </td>
                            <td>
                                <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td>{{ number_format($order->items_count) }}</td>
                            <td class="fw-semibold">{{ number_format($order->total_amount, 0, ',', '.') }}&#8363;</td>
                            <td>
                                <div>{{ $order->payment_label }}</div>
                                <div class="small text-muted">
                                    {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : ($order->payment_status === 'refunded' ? 'Đã hoàn tiền' : 'Chưa thanh toán') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $statusClasses[$order->status] ?? 'bg-light text-dark border' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
