@extends('layouts.admin')

@section('title', 'Bài viết')

@section('page_title', 'Quản lý bài viết')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Bài viết</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center mb-4">
        <form method="get" action="{{ route('admin.posts.index') }}" class="row g-2 flex-grow-1 flex-md-grow-0">
            <div class="col-auto flex-grow-1" style="min-width: 240px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                           placeholder="Tìm theo tiêu đề, slug hoặc tóm tắt…" aria-label="Tìm kiếm bài viết">
                </div>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select" aria-label="Lọc trạng thái" onchange="this.form.submit()">
                    <option value="">Mọi trạng thái</option>
                    <option value="draft" @selected(request('status') === 'draft')>Bản nháp</option>
                    <option value="published" @selected(request('status') === 'published')>Đã xuất bản</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark">Lọc</button>
            </div>
            @if(request()->hasAny(['q', 'status']))
                <div class="col-auto">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                </div>
            @endif
        </form>
        <div class="d-flex flex-shrink-0 gap-2">
            <a href="{{ route('admin.posts.trashed') }}" class="btn btn-outline-secondary">
                <i class="bi bi-trash3 me-1"></i> Đã xóa
            </a>
            <a href="{{ route('admin.posts.create') }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                <i class="bi bi-plus-lg me-1"></i> Thêm bài viết
            </a>
        </div>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 72px;">Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Xuất bản</th>
                        <th class="pe-4 text-end" style="min-width: 160px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td class="ps-4">
                                @if($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="rounded border" width="56" height="56" style="object-fit: cover;">
                                @else
                                    <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width:56px;height:56px;">
                                        <i class="bi bi-newspaper" style="font-size: 1.25rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $post->title }}</div>
                                <div class="small text-muted font-monospace">{{ $post->slug }}</div>
                            </td>
                            <td>{{ $post->author?->name ?? '—' }}</td>
                            <td>
                                @if($post->status === 'published')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đã xuất bản</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Bản nháp</span>
                                @endif
                            </td>
                            <td class="small text-muted text-nowrap">{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="post" class="d-inline"
                                          onsubmit="return confirm('Chuyển bài viết «{{ $post->title }}» vào thùng rác?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa mềm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Chưa có bài viết nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
            <div class="px-4 py-3 border-top">{{ $posts->links() }}</div>
        @endif
    </div>
@endsection
