@extends('layouts.admin')

@section('title', $user->name)

@section('page_title', 'Chi tiết tài khoản')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">Tài khoản</a>
    <span class="text-muted">/</span>
    <span class="text-truncate d-inline-block" style="max-width: 240px;">{{ $user->name }}</span>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="stat-card p-4 text-center">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="" class="rounded-circle border mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 120px; height: 120px;">
                        <i class="bi bi-person" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <h2 class="h5 fw-bold mb-1">{{ $user->name }}</h2>
                <p class="small font-monospace text-muted mb-2">{{ $user->email }}</p>
                @if($user->role === 'admin')
                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle">Quản trị</span>
                @else
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Khách hàng</span>
                @endif
                @if($user->is_active)
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle ms-1">Hoạt động</span>
                @else
                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle ms-1">Đã khóa</span>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="stat-card p-4 h-100">
                <h3 class="h6 text-uppercase text-muted fw-semibold mb-3" style="letter-spacing: 0.06em;">Thông tin</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted small">Số điện thoại</dt>
                    <dd class="col-sm-8">{{ $user->phone ?: '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Ngày sinh</dt>
                    <dd class="col-sm-8">{{ $user->birth_date?->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Bài viết (tác giả)</dt>
                    <dd class="col-sm-8">{{ number_format($user->posts_count) }}</dd>

                    <dt class="col-sm-4 text-muted small">Đơn hàng</dt>
                    <dd class="col-sm-8">{{ number_format($user->orders_count) }}</dd>

                    <dt class="col-sm-4 text-muted small">Địa chỉ đã lưu</dt>
                    <dd class="col-sm-8">{{ number_format($user->addresses_count) }}</dd>

                    <dt class="col-sm-4 text-muted small">Tạo lúc</dt>
                    <dd class="col-sm-8">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4 text-muted small">Cập nhật</dt>
                    <dd class="col-sm-8">{{ $user->updated_at?->format('d/m/Y H:i') }}</dd>
                </dl>
                <hr class="my-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        <i class="bi bi-pencil me-1"></i> Sửa tài khoản
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Danh sách</a>
                    {{-- @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="post" class="d-inline"
                              onsubmit="return confirm('Chuyển tài khoản này vào thùng rác?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Xóa mềm
                            </button>
                        </form>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
@endsection
