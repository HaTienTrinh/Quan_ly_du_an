@extends('layouts.admin')

@section('title', 'Dev Tools')
@section('page_title', '🧪 Dev Tools — Fake thời gian giao hàng')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Dev Tools</span>
@endsection

@section('content')
    <div class="alert alert-warning rounded-3 mb-4">
        <strong>⚠ Chỉ dùng để test!</strong>
        Thay đổi thời gian giao hàng trong DB để kiểm tra tính năng
        <strong>trả hàng</strong> và <strong>đánh giá</strong> (giới hạn 7 ngày).
        Không ảnh hưởng đến đăng nhập hay CSRF.
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="stat-card p-4">
        <h3 class="h5 fw-bold mb-4">Đơn hàng đã giao thành công</h3>

        @if ($orders->isEmpty())
            <p class="text-muted">Chưa có đơn hàng nào ở trạng thái "Đã giao thành công".</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Thời gian giao hiện tại</th>
                            <th>Trạng thái hạn</th>
                            <th style="min-width: 260px;">Fake thành N ngày trước</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $deliveredAt = $order->getDeliveredAt();
                                $daysAgo     = $deliveredAt ? (int) now()->diffInDays($deliveredAt) : null;
                                $daysLeft    = $daysAgo !== null ? max(0, 7 - $daysAgo) : null;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td>{{ $order->user?->name ?? '—' }}</td>
                                <td>
                                    @if ($deliveredAt)
                                        {{ $deliveredAt->format('d/m/Y H:i') }}
                                        <div class="small text-muted">{{ $daysAgo }} ngày trước</div>
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($daysLeft !== null)
                                        @if ($daysLeft > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                Còn {{ $daysLeft }} ngày
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                Hết hạn
                                            </span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.dev.fake-delivered', $order) }}" method="POST"
                                          class="d-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="days_ago" value="{{ $daysAgo ?? 0 }}"
                                               min="0" max="365"
                                               class="form-control form-control-sm" style="width: 75px;">
                                        <span class="text-muted small flex-shrink-0">ngày trước</span>
                                        <button type="submit" class="btn btn-sm btn-warning flex-shrink-0">Fake</button>
                                    </form>
                                    <div class="small text-muted mt-1">
                                        0 = hôm nay &nbsp;·&nbsp; 6 = còn 1 ngày &nbsp;·&nbsp; 8 = hết hạn
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
