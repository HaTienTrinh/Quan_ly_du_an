@extends('layouts.admin')

@section('title', 'Chi tiết liên hệ')
@section('page_title', 'Chi tiết liên hệ')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.contacts.index') }}" class="text-muted text-decoration-none">Liên hệ</a>
    <span class="text-muted">/</span>
    <span>#{{ $contact->id }}</span>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            @if ($contact->status === 'unread')
                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                    <i class="bi bi-envelope-fill me-1"></i>Chưa đọc
                </span>
            @elseif ($contact->status === 'read')
                <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">
                    <i class="bi bi-envelope-open me-1"></i>Đã đọc
                </span>
            @else
                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-reply-fill me-1"></i>Đã phản hồi lúc {{ $contact->replied_at?->format('d/m/Y H:i') }}
                </span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                  onsubmit="return confirm('Xóa liên hệ này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Xóa
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="stat-card p-4 mb-4">
                <h3 class="h5 fw-bold mb-3">Nội dung liên hệ</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted small">Tiêu đề</dt>
                    <dd class="col-sm-9">{{ $contact->subject ?: '(Không có tiêu đề)' }}</dd>

                    <dt class="col-sm-3 text-muted small">Nội dung</dt>
                    <dd class="col-sm-9">
                        <div class="rounded-3 bg-light p-3" style="white-space: pre-wrap;">{{ $contact->message }}</div>
                    </dd>

                    <dt class="col-sm-3 text-muted small">Thời gian gửi</dt>
                    <dd class="col-sm-9">{{ $contact->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>

            <div class="stat-card p-4">
                <h3 class="h5 fw-bold mb-3">
                    <i class="bi bi-reply-fill me-2 text-primary"></i>Phản hồi của admin
                </h3>

                @if ($contact->admin_reply)
                    <div class="rounded-3 bg-primary-subtle p-3 mb-4" style="white-space: pre-wrap;">{{ $contact->admin_reply }}</div>
                @endif

                <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            {{ $contact->admin_reply ? 'Cập nhật phản hồi' : 'Viết phản hồi' }}
                        </label>
                        <textarea name="admin_reply" rows="5"
                            class="form-control @error('admin_reply') is-invalid @enderror"
                            placeholder="Nhập nội dung phản hồi...">{{ old('admin_reply', $contact->admin_reply) }}</textarea>
                        @error('admin_reply')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-reply-fill me-1"></i>
                        {{ $contact->admin_reply ? 'Cập nhật phản hồi' : 'Lưu phản hồi' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stat-card p-4">
                <h3 class="h5 fw-bold mb-3">Thông tin người gửi</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted small">Họ tên</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $contact->name }}</dd>

                    <dt class="col-sm-4 text-muted small">Email</dt>
                    <dd class="col-sm-8">
                        <a href="mailto:{{ $contact->email }}" class="text-decoration-none">{{ $contact->email }}</a>
                    </dd>

                    @if ($contact->phone)
                        <dt class="col-sm-4 text-muted small">SĐT</dt>
                        <dd class="col-sm-8">{{ $contact->phone }}</dd>
                    @endif

                    @if ($contact->user)
                        <dt class="col-sm-4 text-muted small">Tài khoản</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.users.show', $contact->user) }}" class="text-decoration-none">
                                {{ $contact->user->name }}
                            </a>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
