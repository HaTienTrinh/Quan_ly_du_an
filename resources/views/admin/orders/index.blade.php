@extends('layouts.admin')

@section('title', 'Don hang')

@section('page_title', 'Xem danh sach va loc don hang')

@section('breadcrumb')
    <span class="text-muted">Quan tri</span>
    <span class="text-muted">/</span>
    <span>Don hang</span>
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
                        placeholder="Tim theo ma don, ten nguoi nhan, ten khach hang..."
                        aria-label="Tim kiem don hang"
                    >
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <select name="status" class="form-select" aria-label="Loc trang thai">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected((string) request('status') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <button type="submit" class="btn btn-dark w-100">Loc don</button>
            </div>
            @if (request()->hasAny(['q', 'status']) && (filled(request('q')) || filled(request('status'))))
                <div class="col-6 col-md-auto">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Xoa bo loc</a>
                </div>
            @endif
        </form>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Ma don</th>
                        <th>Khach hang</th>
                        <th>Nguoi nhan</th>
                        <th>Ngay dat</th>
                        <th>So SP</th>
                        <th>Tong tien</th>
                        <th>Thanh toan</th>
                        <th class="pe-4">Trang thai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $order->order_code }}</div>
                                <div class="small text-muted">#{{ $order->id }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->user?->name ?? 'Khach vang lai' }}</div>
                                <div class="small text-muted">{{ $order->user?->email ?? 'Khong co email' }}</div>
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
                            <td class="fw-semibold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                            <td>
                                <div>{{ $order->payment_label }}</div>
                                <div class="small text-muted">
                                    {{ $order->payment_status === 'paid' ? 'Da thanh toan' : ($order->payment_status === 'refunded' ? 'Da hoan tien' : 'Chua thanh toan') }}
                                </div>
                            </td>
                            <td class="pe-4">
                                <span class="badge rounded-pill {{ $statusClasses[$order->status] ?? 'bg-light text-dark border' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Khong tim thay don hang phu hop voi bo loc hien tai.
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
