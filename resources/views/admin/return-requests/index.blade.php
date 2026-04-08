@extends('layouts.admin')

@section('title', 'Yêu cầu trả hàng')

@section('page_title', 'Quản lý yêu cầu trả hàng')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Yêu cầu trả hàng</span>
@endsection

@section('content')
    @php
        $statusClasses = [
            'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'approved' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'shipping_back' => 'bg-info-subtle text-info border border-info-subtle',
            'received' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'inspecting' => 'bg-dark-subtle text-dark border border-dark-subtle',
            'refunded' => 'bg-success-subtle text-success border border-success-subtle',
            'exchanged' => 'bg-success-subtle text-success border border-success-subtle',
            'completed' => 'bg-success text-white border border-success',
            'rejected' => 'bg-danger-subtle text-danger border border-danger-subtle',
        ];
    @endphp

    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-stretch align-items-lg-center mb-4">
        <form method="get" action="{{ route('admin.return-requests.index') }}" class="row g-2 flex-grow-1">
            <div class="col-12 col-lg">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                        placeholder="Tìm theo mã đơn, khách hàng hoặc sản phẩm...">
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <select name="status" class="form-select">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected((string) request('status') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <button type="submit" class="btn btn-dark w-100">Lọc</button>
            </div>
            <div class="col-6 col-md-auto">
                <a href="{{ route('admin.return-requests.index') }}" class="btn btn-outline-secondary w-100">Xóa lọc</a>
            </div>
        </form>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Sản phẩm</th>
                        <th>Hướng xử lý</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="pe-4 text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returnRequests as $returnRequest)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $returnRequest->order->order_code }}</div>
                                <div class="small text-muted">#{{ $returnRequest->id }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $returnRequest->user?->name ?? $returnRequest->order->user?->name ?? 'Khách hàng' }}</div>
                                <div class="small text-muted">{{ $returnRequest->order->receiver_name }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $returnRequest->orderItem->product_name }}</div>
                                <div class="small text-muted">SL {{ $returnRequest->orderItem->quantity }}</div>
                            </td>
                            <td>{{ $returnRequest->request_type_label }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $statusClasses[$returnRequest->status] ?? 'bg-light text-dark border' }}">
                                    {{ $returnRequest->status_label }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $returnRequest->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $returnRequest->created_at->format('H:i') }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.return-requests.show', $returnRequest) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Chưa có yêu cầu trả hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($returnRequests->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $returnRequests->links() }}
            </div>
        @endif
    </div>
@endsection
