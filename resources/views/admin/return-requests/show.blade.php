@extends('layouts.admin')

@section('title', 'Chi tiết yêu cầu trả hàng')

@section('page_title', 'Chi tiết yêu cầu trả hàng')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.return-requests.index') }}" class="text-muted text-decoration-none">Yêu cầu trả hàng</a>
    <span class="text-muted">/</span>
    <span>#{{ $returnRequest->id }}</span>
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

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-muted small text-uppercase fw-semibold mb-1">Yêu cầu trả hàng</div>
            <h2 class="h3 mb-1 fw-bold">#{{ $returnRequest->id }} · {{ $returnRequest->order->order_code }}</h2>
            <div class="text-muted">Khởi tạo lúc {{ $returnRequest->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge rounded-pill {{ $statusClasses[$returnRequest->status] ?? 'bg-light text-dark border' }} px-3 py-2">
                {{ $returnRequest->status_label }}
            </span>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                {{ $returnRequest->request_type_label }}
            </span>
            <a href="{{ route('admin.return-requests.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
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
                <h3 class="h5 fw-bold mb-3">Thông tin yêu cầu</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted small">Khách hàng</dt>
                    <dd class="col-sm-8">{{ $returnRequest->user?->name ?? $returnRequest->order->user?->name ?? 'Khách hàng' }}</dd>

                    <dt class="col-sm-4 text-muted small">Sản phẩm</dt>
                    <dd class="col-sm-8">{{ $returnRequest->orderItem->product_name }}</dd>

                    <dt class="col-sm-4 text-muted small">Lý do</dt>
                    <dd class="col-sm-8">{{ $returnRequest->reason }}</dd>

                    <dt class="col-sm-4 text-muted small">Cách xử lý khách chọn</dt>
                    <dd class="col-sm-8">{{ $returnRequest->request_type_label }}</dd>

                    <dt class="col-sm-4 text-muted small">Phương thức vận chuyển về</dt>
                    <dd class="col-sm-8">{{ $returnRequest->logistics_method_label ?? 'Chưa chọn' }}</dd>

                    <dt class="col-sm-4 text-muted small">Ghi chú admin</dt>
                    <dd class="col-sm-8">{{ $returnRequest->admin_note ?: 'Chưa có' }}</dd>

                    @if ($returnRequest->rejection_reason)
                        <dt class="col-sm-4 text-muted small">Lý do từ chối</dt>
                        <dd class="col-sm-8 text-danger">{{ $returnRequest->rejection_reason }}</dd>
                    @endif
                </dl>
            </div>

            <div class="stat-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Minh chứng khách gửi</h3>

                @if (empty($returnRequest->evidence_urls))
                    <div class="text-muted">Khách hàng chưa gửi file minh chứng.</div>
                @else
                    <div class="row g-3">
                        @foreach ($returnRequest->evidence_urls as $index => $url)
                            <div class="col-md-6">
                                <a href="{{ $url }}" target="_blank" class="d-flex align-items-center gap-3 border rounded-3 p-3 text-decoration-none">
                                    <i class="bi bi-paperclip fs-4"></i>
                                    <span>Minh chứng {{ $index + 1 }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="stat-card p-4">
                <h3 class="h5 fw-bold mb-3">Lịch sử xử lý</h3>
                <div class="d-flex flex-column gap-3">
                    @foreach ($returnRequest->statusHistories as $history)
                        <div class="border rounded-3 p-3 bg-light-subtle">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <div class="fw-semibold">
                                        {{ \App\Models\ReturnRequest::STATUS_LABELS[$history->to_status] ?? $history->to_status }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $history->changedBy?->name ?? 'Hệ thống / Khách hàng' }}
                                    </div>
                                </div>
                                <div class="small text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="small mt-2 text-secondary">{{ $history->note ?: 'Không có ghi chú.' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="stat-card p-4">
                <h3 class="h5 fw-bold mb-3">Thao tác xử lý</h3>

                @if ($returnRequest->canBeApproved())
                    <form action="{{ route('admin.return-requests.approve', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Phương án vận chuyển về</label>
                            <select name="logistics_method" class="form-select" required>
                                <option value="customer_ship">Khách tự gửi hàng về</option>
                                <option value="system_pickup">Hệ thống đến lấy hàng</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú admin</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Duyệt yêu cầu</button>
                    </form>

                    <form action="{{ route('admin.return-requests.reject', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Lý do từ chối</label>
                            <textarea name="rejection_reason" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú admin</label>
                            <textarea name="admin_note" rows="2" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">Từ chối yêu cầu</button>
                    </form>
                @endif

                @if ($returnRequest->canBeMarkedShippingBack())
                    <form action="{{ route('admin.return-requests.shipping-back', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú vận chuyển</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-info text-white w-100">Chuyển sang Shipping Back</button>
                    </form>
                @endif

                @if ($returnRequest->canBeMarkedReceived())
                    <form action="{{ route('admin.return-requests.receive', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú khi nhận hàng</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary w-100">Đánh dấu Received</button>
                    </form>
                @endif

                @if ($returnRequest->canBeMarkedInspecting())
                    <form action="{{ route('admin.return-requests.inspect', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú kiểm tra</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Chuyển sang Inspecting</button>
                    </form>
                @endif

                @if ($returnRequest->canBeRefunded())
                    <form action="{{ route('admin.return-requests.refund', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú hoàn tiền</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Xác nhận Refunded</button>
                    </form>
                @endif

                @if ($returnRequest->canBeExchanged())
                    <form action="{{ route('admin.return-requests.exchange', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú đổi hàng</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Xác nhận Exchanged</button>
                    </form>
                @endif

                @if ($returnRequest->canBeCompleted())
                    <form action="{{ route('admin.return-requests.complete', $returnRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú hoàn tất</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Hoàn tất yêu cầu</button>
                    </form>
                @endif

                @if (
                    ! $returnRequest->canBeApproved()
                    && ! $returnRequest->canBeRejected()
                    && ! $returnRequest->canBeMarkedShippingBack()
                    && ! $returnRequest->canBeMarkedReceived()
                    && ! $returnRequest->canBeMarkedInspecting()
                    && ! $returnRequest->canBeRefunded()
                    && ! $returnRequest->canBeExchanged()
                    && ! $returnRequest->canBeCompleted()
                )
                    <div class="alert alert-light border mb-0">
                        Yêu cầu này hiện không còn thao tác chuyển trạng thái khả dụng.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
