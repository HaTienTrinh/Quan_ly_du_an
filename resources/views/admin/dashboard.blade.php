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

    {{-- Stat cards --}}
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

    {{-- Doanh thu + Đơn chờ + Liên hệ chưa đọc --}}
    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-lg-4">
            <div class="stat-card p-4 h-100">
                <p class="small text-muted text-uppercase fw-semibold mb-3" style="letter-spacing:.06em">Doanh thu đã giao</p>
                <p class="display-6 fw-bold mb-2" style="color:#0f172a">{{ $fmtMoney($stats['revenue']) }}</p>
                <p class="small text-muted mb-0">Tổng từ các đơn có trạng thái <strong>Đã giao</strong>.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="stat-card p-4 h-100 border-warning border-opacity-25" style="background:linear-gradient(135deg,#fffbeb 0%,#fff 100%)">
                <p class="small text-muted text-uppercase fw-semibold mb-3" style="letter-spacing:.06em">Đơn chờ xác nhận</p>
                <p class="display-6 fw-bold text-warning mb-2">{{ number_format($stats['pending_orders']) }}</p>
                <p class="small text-muted mb-0">Cần xử lý sớm để giữ trải nghiệm khách hàng.</p>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning mt-3 rounded-pill">
                    Xem đơn chờ
                </a>
            </div>
        </div>
        {{-- <div class="col-lg-4">
            <div class="stat-card p-4 h-100 {{ $stats['unread_contacts'] > 0 ? 'border-danger border-opacity-25' : '' }}"
                 style="{{ $stats['unread_contacts'] > 0 ? 'background:linear-gradient(135deg,#fff5f5 0%,#fff 100%)' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <p class="small text-muted text-uppercase fw-semibold mb-0" style="letter-spacing:.06em">Liên hệ chưa đọc</p>
                    <div class="stat-icon {{ $stats['unread_contacts'] > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-secondary bg-opacity-10 text-secondary' }}">
                        <i class="bi bi-envelope{{ $stats['unread_contacts'] > 0 ? '-fill' : '' }}"></i>
                    </div>
                </div>
                <p class="display-6 fw-bold mb-2 {{ $stats['unread_contacts'] > 0 ? 'text-danger' : 'text-muted' }}">
                    {{ number_format($stats['unread_contacts']) }}
                </p>
                <p class="small text-muted mb-0">
                    {{ $stats['unread_contacts'] > 0 ? 'Có tin nhắn mới từ khách hàng.' : 'Không có liên hệ mới.' }}
                </p>
                <a href="{{ route('admin.contacts.index', ['status' => 'unread']) }}" class="btn btn-sm btn-outline-danger mt-3 rounded-pill">
                    Xem liên hệ
                </a>
            </div>
        </div> --}}
    </div>

    {{-- Biểu đồ doanh thu --}}
    <div class="stat-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h2 class="h6 fw-semibold mb-0">Biểu đồ doanh thu</h2>
                <p class="small text-muted mb-0">Doanh thu từ các đơn đã giao thành công</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-dark active" id="btn-week">Tuần</button>
                <button type="button" class="btn btn-outline-secondary" id="btn-month">Tháng</button>
                <button type="button" class="btn btn-outline-secondary" id="btn-year">Năm</button>
            </div>
        </div>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    {{-- Đơn hàng gần đây --}}
    <div class="stat-card overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#fafbfc">
            <div>
                <h2 class="h6 mb-0 fw-semibold">Đơn hàng gần đây</h2>
                <p class="small text-muted mb-0">5 đơn mới nhất</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Xem tất cả</a>
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
                                'pending'   => 'warning',
                                'delivered' => 'success',
                                'cancelled' => 'secondary',
                                default     => 'info',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold text-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary text-decoration-none">
                                    {{ $order->order_code }}
                                </a>
                            </td>
                            <td>{{ $order->user?->name ?? '—' }}</td>
                            <td class="fw-medium">{{ $fmtMoney($order->total_amount) }}</td>
                            <td><span class="badge rounded-pill bg-{{ $badgeClass }}">{{ $order->status_label }}</span></td>
                            <td class="pe-4 text-end small text-muted text-nowrap">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size:2rem"></i>
                                Chưa có đơn hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const weeklyData  = @json($weeklyRevenue);
        const monthlyData = @json($monthlyRevenue);
        const yearlyData  = @json($yearlyRevenue);

        const ctx = document.getElementById('revenueChart').getContext('2d');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: weeklyData.labels,
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: weeklyData.data,
                    backgroundColor: 'rgba(249,115,22,0.15)',
                    borderColor: '#f97316',
                    borderWidth: 2,
                    borderRadius: 6,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => new Intl.NumberFormat('vi-VN').format(ctx.raw) + ' ₫'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(val)
                        }
                    }
                }
            }
        });

        function switchPeriod(periodData, label, btnId) {
            chart.data.labels   = periodData.labels;
            chart.data.datasets[0].data  = periodData.data;
            chart.data.datasets[0].label = label;
            chart.update();

            document.querySelectorAll('.btn-group .btn').forEach(b => {
                b.classList.remove('btn-dark', 'active');
                b.classList.add('btn-outline-secondary');
            });
            const active = document.getElementById(btnId);
            active.classList.remove('btn-outline-secondary');
            active.classList.add('btn-dark', 'active');
        }

        document.getElementById('btn-week').addEventListener('click',  () => switchPeriod(weeklyData,  'Doanh thu (₫)', 'btn-week'));
        document.getElementById('btn-month').addEventListener('click', () => switchPeriod(monthlyData, 'Doanh thu (₫)', 'btn-month'));
        document.getElementById('btn-year').addEventListener('click',  () => switchPeriod(yearlyData,  'Doanh thu (₫)', 'btn-year'));
    </script>
@endsection
