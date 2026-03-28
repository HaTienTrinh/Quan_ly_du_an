@extends('layouts.admin')

@section('title', $category->name)

@section('page_title', 'Chi tiết danh mục')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.categories.index') }}" class="text-muted text-decoration-none">Danh mục</a>
    <span class="text-muted">/</span>
    <span>{{ $category->name }}</span>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="stat-card p-4 text-center">
                @if($category->image)
                    <img src="{{ asset('storage/'.$category->image) }}" alt="" class="img-fluid rounded-3 border mb-3 w-100" style="max-height: 280px; object-fit: cover;">
                @else
                    <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted mx-auto mb-3" style="height: 200px; max-width: 100%;">
                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <h2 class="h5 fw-bold mb-1">{{ $category->name }}</h2>
                <p class="small font-monospace text-muted mb-3">{{ $category->slug }}</p>
                @if($category->is_active)
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đang hiển thị</span>
                @else
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Đang ẩn</span>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="stat-card p-4 h-100">
                <h3 class="h6 text-uppercase text-muted fw-semibold mb-3" style="letter-spacing: 0.06em;">Thông tin</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted small">Mô tả</dt>
                    <dd class="col-sm-8">{{ $category->description ?: '—' }}</dd>
                    <dt class="col-sm-4 text-muted small">Số sản phẩm</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $category->products_count }}</dd>
                    <dt class="col-sm-4 text-muted small">Tạo lúc</dt>
                    <dd class="col-sm-8">{{ $category->created_at?->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-4 text-muted small">Cập nhật</dt>
                    <dd class="col-sm-8">{{ $category->updated_at?->format('d/m/Y H:i') }}</dd>
                </dl>
                <hr class="my-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Danh sách</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="post" class="d-inline"
                          onsubmit="return confirm('Chuyển danh mục này vào thùng rác?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Xóa mềm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
