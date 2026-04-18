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
        $availableSizes = $returnRequest->orderItem?->product?->sizes ?? collect();
        $selectedReplacementSizeId = old('replacement_product_color_id', $returnRequest->orderItem?->product_color_id);
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

                    <dt class="col-sm-4 text-muted small">Size khách đã mua</dt>
                    <dd class="col-sm-8">{{ $returnRequest->orderItem->product_size_name ?: 'Không có size' }}</dd>

                    <dt class="col-sm-4 text-muted small">Lý do</dt>
                    <dd class="col-sm-8">{{ $returnRequest->reason }}</dd>

                    <dt class="col-sm-4 text-muted small">Cách xử lý khách chọn</dt>
                    <dd class="col-sm-8">{{ $returnRequest->request_type_label }}</dd>

                    @if ($returnRequest->request_type === \App\Models\ReturnRequest::TYPE_EXCHANGE)
                        <dt class="col-sm-4 text-muted small">Size muốn đổi</dt>
                        <dd class="col-sm-8">
                            @if ($returnRequest->exchangeSize)
                                <strong>{{ $returnRequest->exchangeSize->name }}</strong>
                            @else
                                <span class="text-muted">Khách chưa chọn</span>
                            @endif
                        </dd>
                    @endif

                    <dt class="col-sm-4 text-muted small">Phương án vận chuyển về</dt>
                    <dd class="col-sm-8">{{ $returnRequest->logistics_method_label ?? 'Khách chưa chọn' }}</dd>

                    <dt class="col-sm-4 text-muted small">Ghi chú admin</dt>
                    <dd class="col-sm-8">{{ $returnRequest->admin_note ?: 'Chưa có' }}</dd>

                    @if ($returnRequest->replacementOrder)
                        <dt class="col-sm-4 text-muted small">Đơn gửi lại</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.orders.show', $returnRequest->replacementOrder) }}" class="text-decoration-none">
                                {{ $returnRequest->replacementOrder->order_code }}
                            </a>
                        </dd>
                    @endif

                    @if ($returnRequest->rejection_reason)
                        <dt class="col-sm-4 text-muted small">Lý do từ chối</dt>
                        <dd class="col-sm-8 text-danger">{{ $returnRequest->rejection_reason }}</dd>
                    @endif
                </dl>
            </div>

            {{-- Kết quả Inspection --}}
            @if ($returnRequest->inspection)
                <div class="stat-card p-4 mb-4 border-{{ $returnRequest->inspection->is_valid ? 'success' : 'danger' }} border-2">
                    <h3 class="h5 fw-bold mb-3">
                        <i class="bi bi-{{ $returnRequest->inspection->is_valid ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} me-2"></i>
                        Kết quả kiểm tra hàng
                    </h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small">Kết luận</dt>
                        <dd class="col-sm-8">
                            @if ($returnRequest->inspection->is_valid)
                                <span class="badge bg-success">Hàng hợp lệ</span>
                            @else
                                <span class="badge bg-danger">Hàng gian lận</span>
                            @endif
                        </dd>
                        @if ($returnRequest->inspection->note)
                            <dt class="col-sm-4 text-muted small">Ghi chú</dt>
                            <dd class="col-sm-8">{{ $returnRequest->inspection->note }}</dd>
                        @endif
                        <dt class="col-sm-4 text-muted small">Thời gian</dt>
                        <dd class="col-sm-8">{{ $returnRequest->inspection->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            @endif

            {{-- Thông tin Refund (nếu có) --}}
            @if ($returnRequest->refund)
                <div class="stat-card p-4 mb-4 border-success border-2">
                    <h3 class="h5 fw-bold mb-3"><i class="bi bi-cash-coin text-success me-2"></i>Hoàn tiền</h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small">Số tiền</dt>
                        <dd class="col-sm-8 fw-bold text-success">{{ number_format($returnRequest->refund->amount) }}đ</dd>
                        <dt class="col-sm-4 text-muted small">Trạng thái</dt>
                        <dd class="col-sm-8">
                            @php $refundStatusMap = ['pending' => ['label' => 'Chờ xử lý', 'class' => 'warning'], 'processed' => ['label' => 'Đã hoàn tiền', 'class' => 'success'], 'failed' => ['label' => 'Thất bại', 'class' => 'danger']]; @endphp
                            <span class="badge bg-{{ $refundStatusMap[$returnRequest->refund->status]['class'] ?? 'secondary' }}">
                                {{ $refundStatusMap[$returnRequest->refund->status]['label'] ?? $returnRequest->refund->status }}
                            </span>
                        </dd>
                    </dl>
                </div>
            @endif

            {{-- Thông tin Reship (nếu có) --}}
            @if ($returnRequest->reship)
                <div class="stat-card p-4 mb-4 border-info border-2">
                    <h3 class="h5 fw-bold mb-3">
                        <i class="bi bi-truck text-info me-2"></i>
                        @if ($returnRequest->status === \App\Models\ReturnRequest::STATUS_REJECTED)
                            Gửi trả hàng lại khách (do gian lận)
                        @else
                            Gửi hàng đổi cho khách
                        @endif
                    </h3>
                    <dl class="row mb-2">
                        <dt class="col-sm-4 text-muted small">Mã vận đơn</dt>
                        <dd class="col-sm-8">{{ $returnRequest->reship->tracking_code ?: 'Chưa có' }}</dd>
                        <dt class="col-sm-4 text-muted small">Đơn vị vận chuyển</dt>
                        <dd class="col-sm-8">{{ $returnRequest->reship->carrier ?: 'Chưa có' }}</dd>
                        <dt class="col-sm-4 text-muted small">Trạng thái</dt>
                        <dd class="col-sm-8">
                            @php $reshipStatusMap = ['pending' => ['label' => 'Chờ gửi', 'class' => 'warning'], 'shipped' => ['label' => 'Đang giao', 'class' => 'info'], 'delivered' => ['label' => 'Đã giao', 'class' => 'success']]; @endphp
                            <span class="badge bg-{{ $reshipStatusMap[$returnRequest->reship->status]['class'] ?? 'secondary' }}">
                                {{ $reshipStatusMap[$returnRequest->reship->status]['label'] ?? $returnRequest->reship->status }}
                            </span>
                        </dd>
                    </dl>
                    {{-- Form nhập mã vận đơn --}}
                    @if ($returnRequest->reship->status !== 'delivered')
                        <form action="{{ route('admin.reships.update', $returnRequest->reship) }}" method="POST" class="border-top pt-3 mt-2">
                            @csrf
                            @method('PATCH')
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="tracking_code" class="form-control form-control-sm"
                                        placeholder="Mã vận đơn"
                                        value="{{ old('tracking_code', $returnRequest->reship->tracking_code) }}">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="carrier" class="form-control form-control-sm"
                                        placeholder="Đơn vị vận chuyển"
                                        value="{{ old('carrier', $returnRequest->reship->carrier) }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="pending" @selected($returnRequest->reship->status === 'pending')>Chờ gửi</option>
                                        <option value="shipped" @selected($returnRequest->reship->status === 'shipped')>Đang giao</option>
                                        <option value="delivered" @selected($returnRequest->reship->status === 'delivered')>Đã giao</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">Cập nhật</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            @endif

            <div class="stat-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Minh chứng khách gửi</h3>

                @if (empty($returnRequest->evidence_assets))
                    <div class="text-muted">Khách hàng chưa gửi file minh chứng.</div>
                @else
                    <div class="row g-3">
                        @foreach ($returnRequest->evidence_assets as $evidence)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                    @if ($evidence['type'] === 'image')
                                        <a href="{{ $evidence['url'] }}" target="_blank" class="d-block mb-3">
                                            <img src="{{ $evidence['url'] }}" alt="{{ $evidence['name'] }}"
                                                class="img-fluid rounded-3 w-100"
                                                style="max-height: 320px; object-fit: cover;">
                                        </a>
                                    @elseif ($evidence['type'] === 'video')
                                        <video controls class="w-100 rounded-3 mb-3" style="max-height: 320px; object-fit: cover;">
                                            <source src="{{ $evidence['url'] }}">
                                        </video>
                                    @else
                                        <div class="rounded-3 border bg-white p-4 text-center text-muted mb-3">
                                            <i class="bi bi-paperclip fs-3 d-block mb-2"></i>
                                            Không xem trước được tệp này
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <span class="fw-semibold">{{ $evidence['name'] }}</span>
                                        <a href="{{ $evidence['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Mở file
                                        </a>
                                    </div>
                                </div>
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
                    <div class="alert alert-info small">
                        Phương án vận chuyển về do khách hàng chọn: <strong>{{ $returnRequest->logistics_method_label ?? 'Khách chưa chọn' }}</strong>.
                    </div>

                    <form action="{{ route('admin.return-requests.approve', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú admin</label>
                            <textarea name="admin_note" rows="3" class="form-control">{{ old('admin_note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Duyệt yêu cầu</button>
                    </form>

                    <form action="{{ route('admin.return-requests.reject', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Lý do từ chối</label>
                            <textarea name="rejection_reason" rows="3" class="form-control" required>{{ old('rejection_reason') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú admin</label>
                            <textarea name="admin_note" rows="2" class="form-control">{{ old('admin_note') }}</textarea>
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
                        <button type="submit" class="btn btn-info text-white w-100">Đánh dấu đang vận chuyển về</button>
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
                        <button type="submit" class="btn btn-secondary w-100">Đã nhận hàng trả về</button>
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
                        <button type="submit" class="btn btn-dark w-100">Chuyển sang kiểm tra</button>
                    </form>
                @endif

                @if ($returnRequest->status === \App\Models\ReturnRequest::STATUS_INSPECTING && ! $returnRequest->inspection)
                    <div class="alert alert-warning border-warning">
                        <div class="fw-bold mb-1"><i class="bi bi-search me-1"></i> Kiểm tra hàng (INSPECTION)</div>
                        <div class="small">Chọn kết quả sau khi kiểm tra hàng khách gửi về.</div>
                    </div>

                    <form action="{{ route('admin.return-requests.process-inspection', $returnRequest) }}" method="POST" id="inspectionForm">
                        @csrf
                        <input type="hidden" name="verdict" id="verdictInput" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú kiểm tra</label>
                            <textarea name="note" rows="2" class="form-control @error('note') is-invalid @enderror"
                                placeholder="Tình trạng hàng...">{{ old('note') }}</textarea>
                            @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Số tiền hoàn: chỉ hiện khi type = refund --}}
                        @if ($returnRequest->request_type === \App\Models\ReturnRequest::TYPE_REFUND)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Số tiền hoàn (VNĐ) <span class="text-muted fw-normal small">— chỉ áp dụng khi hợp lệ</span></label>
                                <input type="number" name="amount"
                                    value="{{ old('amount', $returnRequest->orderItem->subtotal) }}"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    min="0" step="1000">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif

                        {{-- Size gửi lại: chỉ hiện khi type = exchange --}}
                        @if ($returnRequest->request_type === \App\Models\ReturnRequest::TYPE_EXCHANGE)
                            @php
                                $sizeOptions = $returnRequest->orderItem->product->colors
                                    ->filter(fn($c) => $c->size)
                                    ->groupBy('size');
                            @endphp
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Size gửi lại cho khách
                                    <span class="text-muted fw-normal small">— chỉ áp dụng khi hợp lệ</span>
                                </label>
                                <select name="replacement_color_id" id="replacementColorSelect"
                                    class="form-select @error('replacement_color_id') is-invalid @enderror">
                                    <option value="">-- Chọn size --</option>
                                    @foreach ($returnRequest->orderItem->product->sizes as $size)
                                        <option value="{{ $size->id }}"
                                            @selected((int) old('replacement_color_id', $returnRequest->exchangeSize?->id) === $size->id)>
                                            {{ $size->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('replacement_color_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Khách muốn đổi sang: <strong>{{ $returnRequest->exchangeSize?->name ?? 'Chưa chọn' }}</strong></div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú admin</label>
                            <textarea name="admin_note" rows="2" class="form-control">{{ old('admin_note') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg" onclick="submitInspection('valid')">
                                <i class="bi bi-check-circle-fill me-2"></i>Hàng HỢP LỆ
                                <div class="small fw-normal opacity-75">
                                    → Tạo đơn {{ $returnRequest->request_type === 'refund' ? 'hoàn tiền' : 'gửi hàng đổi' }} cho khách
                                </div>
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" onclick="submitInspection('fraud')">
                                <i class="bi bi-x-circle-fill me-2"></i>Hàng GIAN LẬN
                                <div class="small fw-normal opacity-75">
                                    → Từ chối + tăng vi phạm + tạo đơn gửi TRẢ hàng lại khách
                                </div>
                            </button>
                        </div>
                    </form>

                    <script>
                        function submitInspection(verdict) {
                            // Khi gian lận: bỏ required trên select màu/size
                            const sel = document.getElementById('replacementColorSelect');
                            if (verdict === 'fraud' && sel) sel.removeAttribute('required');
                            if (!confirm('Xác nhận kết quả: ' + (verdict === 'valid' ? 'HÀNG HỢP LỆ' : 'HÀNG GIAN LẬN') + '?')) return;
                            document.getElementById('verdictInput').value = verdict;
                            document.getElementById('inspectionForm').submit();
                        }
                    </script>
                @endif

                @if ($returnRequest->canBeRefunded())
                    <form action="{{ route('admin.return-requests.refund', $returnRequest) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Ghi chú hoàn tiền</label>
                            <textarea name="admin_note" rows="3" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Xác nhận đã hoàn tiền</button>
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
                        <button type="submit" class="btn btn-success w-100">Xác nhận đã đổi hàng</button>
                    </form>
                @endif

                @if ($returnRequest->canBeCompleted())
                    <form action="{{ route('admin.return-requests.complete', $returnRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        @if (
                            $returnRequest->request_type === \App\Models\ReturnRequest::TYPE_EXCHANGE
                            && ! $returnRequest->replacementOrder
                            && $availableSizes->isNotEmpty()
                        )
                            @php
                                $availableSizes = $availableColors->filter(fn($c) => $c->size)->groupBy('size');
                            @endphp
                            <div class="mb-3">
                                <label for="replacement_product_color_id" class="form-label">Size gửi lại cho khách</label>
                                <select name="replacement_product_color_id" id="replacement_product_color_id"
                                    class="form-select @error('replacement_product_color_id') is-invalid @enderror" required>
                                    <option value="">Chọn size</option>
                                    @foreach ($availableSizes as $size)
                                        <option value="{{ $size->id }}" @selected((string) $selectedReplacementSizeId === (string) $size->id)>
                                            {{ $size->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('replacement_product_color_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Ghi chú hoàn tất</label>
                            <textarea name="admin_note" rows="3" class="form-control">{{ old('admin_note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            {{ $returnRequest->request_type === \App\Models\ReturnRequest::TYPE_EXCHANGE ? 'Yêu cầu trả hàng đã hoàn tất và tạo đơn gửi lại' : 'Yêu cầu trả hàng đã hoàn tất' }}
                        </button>
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
