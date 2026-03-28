@extends('layouts.admin')

@section('title', 'Danh mục')

@section('page_title', 'Quản lý danh mục')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Danh mục</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center mb-4">
        <form method="get" action="{{ route('admin.categories.index') }}" class="row g-2 flex-grow-1 flex-md-grow-0">
            <div class="col-auto flex-grow-1" style="min-width: 200px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                           placeholder="Tìm theo tên, slug, mô tả…" aria-label="Tìm kiếm">
                </div>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select" aria-label="Lọc trạng thái" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected(request('status') === 'active')>Đang hiển thị</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Đang ẩn</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark">Lọc</button>
            </div>
            @if(request()->hasAny(['q', 'status']))
                <div class="col-auto">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                </div>
            @endif
        </form>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.categories.trashed') }}" class="btn btn-outline-secondary">
                <i class="bi bi-trash3 me-1"></i> Đã xóa
            </a>
            <a href="{{ route('admin.categories.create') }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                <i class="bi bi-plus-lg me-1"></i> Thêm danh mục
            </a>
        </div>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 72px;">Ảnh</th>
                        <th>Tên & slug</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Sản phẩm</th>
                        <th class="pe-4 text-end" style="min-width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4">
                                @if($category->image)
                                    <img src="{{ asset('storage/'.$category->image) }}" alt="" class="rounded border" width="48" height="48" style="object-fit: cover;">
                                @else
                                    <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width:48px;height:48px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $category->name }}</div>
                                <div class="small text-muted font-monospace">{{ $category->slug }}</div>
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đang hiển thị</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Đang ẩn</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $category->products_count }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="post" class="d-inline"
                                          onsubmit="return confirm('Chuyển danh mục «{{ $category->name }}» vào thùng rác?');">
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Chưa có danh mục nào. <a href="{{ route('admin.categories.create') }}">Thêm mới</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-4 py-3 border-top">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection
