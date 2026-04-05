@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('page_title', 'Chi tiết đơn hàng')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.orders.index') }}" class="text-muted text-decoration-none">Đơn hàng</a>
    <span class="text-muted">/</span>
    <span>{{ $order->order_code }}</span>
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

        $paymentStatusLabels = [
            'paid' => 'Đã thanh toán',
            'unpaid' => 'Chưa thanh toán',
            'refunded' => 'Đã hoàn tiền',
        ];

        $paymentStatusClasses = [
            'paid' => 'bg-success-subtle text-success border border-success-subtle',
            'unpaid' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'refunded' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        ];
    @endphp

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-muted small text-uppercase fw-semibold mb-1">Mã đơn hàng</div>
            <h2 class="h3 mb-1 fw-bold">{{ $order->order_code }}</h2>
            <div class="text-muted">Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge rounded-pill {{ $statusClasses[$order->status] ?? 'bg-light text-dark border' }} px-3 py-2">
                {{ $order->status_label }}
            </span>
            <span class="badge rounded-pill {{ $paymentStatusClasses[$order->payment_status] ?? 'bg-light text-dark border' }} px-3 py-2">
                {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
            </span>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            <div class="fw-semibold mb-2">Có lỗi xảy ra:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="stat-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <h3 class="h5 mb-0 fw-bold">Sản phẩm trong đơn</h3>
                    <span class="text-muted">{{ $order->items->count() }} sản phẩm</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                @php
                                    $thumbnail = null;

                                    if ($item->product_thumbnail) {
                                        $thumbnail = \Illuminate\Support\Str::startsWith($item->product_thumbnail, ['http://', 'https://'])
                                            ? $item->product_thumbnail
                                            : asset('storage/' . $item->product_thumbnail);
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 64px; height: 64px;">
                                                @if($thumbnail)
                                                    <img src="{{ $thumbnail }}" alt="{{ $item->product_name }}" class="w-100 h-100" style="object-fit: cover;">
                                                @else
                                                    <i class="bi bi-box-seam text-muted"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                <div class="small text-muted">Mã sản phẩm: #{{ $item->product_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ number_format($item->quantity) }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }}&#8363;</td>
                                    <td class="text-end fw-semibold">{{ number_format($item->subtotal, 0, ',', '.') }}&#8363;</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="stat-card p-4">
                <h3 class="h5 fw-bold mb-3">Lịch sử trạng thái</h3>

                @if ($order->statusHistories->isEmpty())
                    <div class="text-muted">Chưa có lịch sử cập nhật cho đơn hàng này.</div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach ($order->statusHistories as $history)
                            <div class="rounded-3 border p-3 bg-light-subtle">
                                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ \App\Models\Order::STATUS_LABELS[$history->to_status] ?? $history->to_status }}
                                        </div>
                                        <div class="small text-muted mt-1">
                                            @if ($history->from_status)
                                                Từ {{ \App\Models\Order::STATUS_LABELS[$history->from_status] ?? $history->from_status }}
                                                sang {{ \App\Models\Order::STATUS_LABELS[$history->to_status] ?? $history->to_status }}
                                            @else
                                                Khởi tạo trạng thái ban đầu
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-muted small">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                                </div>

                                <div class="small mt-2 text-secondary">
                                    {{ $history->note ?: 'Không có ghi chú thêm.' }}
                                </div>

                                <div class="small text-uppercase text-muted mt-2">
                                    {{ $history->changedBy?->name ?? 'Hệ thống / khách hàng' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-4">
            <div class="stat-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Xử lý đơn hàng</h3>

                @if ($order->status === \App\Models\Order::STATUS_PENDING)
                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="confirm_admin_note" class="form-label">Ghi chú nội bộ khi xác nhận</label>
                            <textarea
                                name="admin_note"
                                id="confirm_admin_note"
                                rows="3"
                                class="form-control"
                                placeholder="Ví dụ: Đã kiểm tra thông tin người nhận, tồn kho đầy đủ."
                            >{{ old('admin_note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Xác nhận đơn hàng
                        </button>
                    </form>
                @endif

                @if ($order->canBeCancelled())
                    <form
                        action="{{ route('admin.orders.cancel', $order) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');"
                    >
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Lý do hủy đơn</label>
                            <input
                                type="text"
                                name="cancel_reason"
                                id="cancel_reason"
                                class="form-control"
                                value="{{ old('cancel_reason') }}"
                                placeholder="Nhập lý do hủy đơn hàng"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label for="cancel_admin_note" class="form-label">Ghi chú nội bộ</label>
                            <textarea
                                name="admin_note"
                                id="cancel_admin_note"
                                rows="3"
                                class="form-control"
                                placeholder="Ghi chú thêm cho bộ phận xử lý hoặc CSKH."
                            >{{ old('admin_note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng
                        </button>
                    </form>
                @endif

                @if ($order->status !== \App\Models\Order::STATUS_PENDING && ! $order->canBeCancelled())
                    <div class="alert alert-light border mb-0">
                        Đơn hàng này hiện không còn thao tác xác nhận hoặc hủy trong màn hình này.
                    </div>
                @endif
            </div>
            <div class="stat-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Thông tin khách hàng</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Khách hàng</dt>
                    <dd class="col-sm-7 fw-semibold">{{ $order->user?->name ?? 'Khách vãng lai' }}</dd>

                    <dt class="col-sm-5 text-muted small">Email</dt>
                    <dd class="col-sm-7">{{ $order->user?->email ?? 'Không có' }}</dd>

                    <dt class="col-sm-5 text-muted small">Điện thoại</dt>
                    <dd class="col-sm-7">{{ $order->user?->phone ?? 'Không có' }}</dd>

                    <dt class="col-sm-5 text-muted small">Người nhận</dt>
                    <dd class="col-sm-7">{{ $order->receiver_name }}</dd>

                    <dt class="col-sm-5 text-muted small">SĐT nhận hàng</dt>
                    <dd class="col-sm-7">{{ $order->receiver_phone }}</dd>

                    <dt class="col-sm-5 text-muted small">Địa chỉ</dt>
                    <dd class="col-sm-7">{{ $order->full_address }}</dd>
                </dl>
            </div>

            <div class="stat-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Thanh toán</h3>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính</span>
                    <span>{{ number_format($order->subtotal, 0, ',', '.') }}&#8363;</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí vận chuyển</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}&#8363;</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Giảm giá</span>
                    <span>{{ number_format($order->discount_amount, 0, ',', '.') }}&#8363;</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Tổng thanh toán</span>
                    <span class="text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}&#8363;</span>
                </div>

                <hr>

                <div class="small text-muted mb-1">Phương thức thanh toán</div>
                <div class="fw-semibold mb-3">{{ $order->payment_label }}</div>

                <div class="small text-muted mb-1">Trạng thái thanh toán</div>
                <div class="fw-semibold mb-3">{{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}</div>

                @if ($order->paid_at)
                    <div class="small text-muted mb-1">Thời gian thanh toán</div>
                    <div class="fw-semibold">{{ $order->paid_at->format('d/m/Y H:i') }}</div>
                @endif
            </div>

            <div class="stat-card p-4">
                <h3 class="h5 fw-bold mb-3">Ghi chú và xử lý</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">Ghi chú khách</dt>
                    <dd class="col-sm-7">{{ $order->note ?: 'Không có' }}</dd>

                    <dt class="col-sm-5 text-muted small">Ghi chú nội bộ</dt>
                    <dd class="col-sm-7">{{ $order->admin_note ?: 'Không có' }}</dd>

                    <dt class="col-sm-5 text-muted small">Xác nhận bởi</dt>
                    <dd class="col-sm-7">{{ $order->confirmedBy?->name ?? 'Chưa xác nhận' }}</dd>

                    <dt class="col-sm-5 text-muted small">Thời gian xác nhận</dt>
                    <dd class="col-sm-7">{{ $order->confirmed_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</dd>

                    @if ($order->status === \App\Models\Order::STATUS_CANCELLED)
                        <dt class="col-sm-5 text-muted small">Lý do hủy</dt>
                        <dd class="col-sm-7">{{ $order->cancel_reason ?: 'Không có' }}</dd>

                        <dt class="col-sm-5 text-muted small">Hủy lúc</dt>
                        <dd class="col-sm-7">{{ $order->cancelled_at?->format('d/m/Y H:i') ?? 'Chưa có' }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
