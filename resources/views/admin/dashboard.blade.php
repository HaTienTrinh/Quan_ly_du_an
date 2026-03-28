@extends('layouts.admin')

@section('title', 'Bảng điều khiển')

@section('page_title', 'Bảng điều khiển')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
@endsection

@section('content')
    @php
        $fmtMoney = fn ($n) => number_format((float) $n, 0, ',', '.') . ' ₫';
    @endphp

    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-muted mb-1 fw-medium">Khách hàng</p>
                        <p class="h3 mb-0 fw-bold">{{ number_format($stats['total_users']) }}</p>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-muted mb-1 fw-medium">Sản phẩm</p>
                        <p class="h3 mb-0 fw-bold">{{ number_format($stats['total_products']) }}</p>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-muted mb-1 fw-medium">Danh mục</p>
                        <p class="h3 mb-0 fw-bold">{{ number_format($stats['total_categories']) }}</p>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="small text-muted mb-1 fw-medium">Đơn hàng</p>
                        <p class="h3 mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</p>
                    </div>
                    <div class="stat-icon text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-xl-4">
        <div class="col-lg-4">
            <div class="stat-card p-4 h-100">
                <p class="small text-muted text-uppercase fw-semibold mb-3" style="letter-spacing: 0.06em;">Doanh thu đã giao</p>
                <p class="display-6 fw-bold mb-2" style="color: #0f172a;">{{ $fmtMoney($stats['revenue']) }}</p>
                <p class="small text-muted mb-0">Tổng từ các đơn có trạng thái <strong>Đã giao</strong>.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="stat-card p-4 h-100 border-warning border-opacity-25" style="background: linear-gradient(135deg, #fffbeb 0%, #fff 100%);">
                <p class="small text-muted text-uppercase fw-semibold mb-3" style="letter-spacing: 0.06em;">Đơn chờ xác nhận</p>
                <p class="display-6 fw-bold text-warning mb-2">{{ number_format($stats['pending_orders']) }}</p>
                <p class="small text-muted mb-0">Cần xử lý sớm để giữ trải nghiệm khách hàng.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="stat-card p-4 h-100 d-flex flex-column justify-content-center text-center">
                <i class="bi bi-lightning-charge-fill text-warning mb-2" style="font-size: 2rem;"></i>
                <p class="fw-semibold mb-1">Phím tắt</p>
                <p class="small text-muted mb-3">Kích hoạt thêm route trong <code class="small">web.php</code> để mở đầy đủ chức năng quản lý.</p>
                <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm rounded-pill">Xem website</a>
            </div>
        </div>
    </div>

    <div class="stat-card mt-4 overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #fafbfc;">
            <div>
                <h2 class="h6 mb-0 fw-semibold">Đơn hàng gần đây</h2>
                <p class="small text-muted mb-0">5 đơn mới nhất</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 border-0">Mã đơn</th>
                        <th class="border-0">Khách</th>
                        <th class="border-0">Tổng tiền</th>
                        <th class="border-0">Trạng thái</th>
                        <th class="pe-4 border-0 text-end">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_orders as $order)
                        @php
                            $badgeClass = match ($order->status) {
                                'pending' => 'warning',
                                'delivered' => 'success',
                                'cancelled' => 'secondary',
                                'shipping', 'processing', 'confirmed' => 'info',
                                default => 'light text-dark',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold text-nowrap">
                                <span class="text-primary">{{ $order->order_code }}</span>
                            </td>
                            <td>
                                <span class="text-dark">{{ $order->user?->name ?? '—' }}</span>
                            </td>
                            <td class="fw-medium">{{ $fmtMoney($order->total_amount) }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $badgeClass }}">{{ $order->status_label }}</span>
                            </td>
                            <td class="pe-4 text-end small text-muted text-nowrap">
                                {{ $order->created_at?->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Chưa có đơn hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
