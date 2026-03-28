@extends('layouts.admin')

@section('title', 'Sản phẩm')

@section('page_title', 'Quản lý sản phẩm')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Sản phẩm</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center mb-4">
        <form method="get" action="{{ route('admin.products.index') }}" class="row g-2 flex-grow-1 flex-md-grow-0">
            <div class="col-auto flex-grow-1" style="min-width: 240px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                           placeholder="Tìm theo tên hoặc slug…" aria-label="Tìm kiếm sản phẩm">
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
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                </div>
            @endif
        </form>
        <div class="d-flex flex-shrink-0">
            <a href="{{ route('admin.products.create') }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                <i class="bi bi-plus-lg me-1"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 72px;">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Tồn kho</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                @if($product->thumbnail_url)
                                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="rounded border" width="56" height="56" style="object-fit: cover;">
                                @else
                                    <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width:56px;height:56px;">
                                        <i class="bi bi-box-seam" style="font-size: 1.25rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="small text-muted font-monospace">{{ $product->slug }}</div>
                            </td>
                            <td>{{ $product->category?->name ?? 'Chưa có' }}</td>
                            <td>
                                @if($product->sale_price)
                                    <div class="fw-semibold text-dark">{{ number_format($product->sale_price, 0, ',', '.') }}₫</div>
                                    <div class="small text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                                @else
                                    <div class="fw-semibold">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đang hiển thị</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Đang ẩn</span>
                                @endif
                            </td>
                            <td>{{ number_format($product->stock) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                Chưa có sản phẩm nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-4 py-3 border-top">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
