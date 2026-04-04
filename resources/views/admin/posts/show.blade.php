@extends('layouts.admin')

@section('title', $post->title)

@section('page_title', 'Chi tiết bài viết')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.posts.index') }}" class="text-muted text-decoration-none">Bài viết</a>
    <span class="text-muted">/</span>
    <span class="text-truncate d-inline-block" style="max-width: 280px;">{{ $post->title }}</span>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="stat-card p-4 text-center">
                @if($post->thumbnail_url)
                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="img-fluid rounded-3 border mb-3 w-100" style="max-height: 320px; object-fit: cover;">
                @else
                    <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted mx-auto mb-3" style="height: 280px; max-width: 100%;">
                        <i class="bi bi-newspaper" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <h2 class="h5 fw-bold mb-1">{{ $post->title }}</h2>
                <p class="small font-monospace text-muted mb-3">{{ $post->slug }}</p>
                @if($post->status === 'published')
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đã xuất bản</span>
                @else
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Bản nháp</span>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="stat-card p-4 h-100">
                <h3 class="h6 text-uppercase text-muted fw-semibold mb-3" style="letter-spacing: 0.06em;">Thông tin bài viết</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted small">Tác giả</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $post->author?->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Tóm tắt</dt>
                    <dd class="col-sm-8">{{ $post->summary ?: '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Xuất bản lúc</dt>
                    <dd class="col-sm-8">{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Tạo lúc</dt>
                    <dd class="col-sm-8">{{ $post->created_at?->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4 text-muted small">Cập nhật</dt>
                    <dd class="col-sm-8">{{ $post->updated_at?->format('d/m/Y H:i') }}</dd>
                </dl>
                <hr class="my-4">
                <h4 class="h6 fw-semibold mb-2">Nội dung</h4>
                <div class="post-content small" style="white-space: pre-wrap;">{{ $post->content }}</div>
                <hr class="my-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Danh sách</a>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="post" class="d-inline"
                          onsubmit="return confirm('Chuyển bài viết này vào thùng rác?');">
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
