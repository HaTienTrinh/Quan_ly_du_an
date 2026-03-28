@extends('layouts.admin')

@section('title', 'Sản phẩm đã xóa')

@section('page_title', 'Sản phẩm đã xóa')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.products.index') }}" class="text-muted text-decoration-none">Sản phẩm</a>
    <span class="text-muted">/</span>
    <span>Đã xóa</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center mb-4">
        <form method="get" action="{{ route('admin.products.trashed') }}" class="d-flex gap-2 flex-grow-1 flex-md-grow-0" style="max-width: 420px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                       placeholder="Tìm trong thùng rác…">
            </div>
            <button type="submit" class="btn btn-dark">Tìm</button>
            @if(request()->filled('q'))
                <a href="{{ route('admin.products.trashed') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
            @endif
        </form>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary align-self-start align-self-md-center">
            <i class="bi bi-arrow-left me-1"></i> Về danh sách chính
        </a>
    </div>

    <div class="alert alert-warning border-0 rounded-3 small mb-4" role="alert">
        <i class="bi bi-info-circle me-1"></i>
        Đây là các sản phẩm đã <strong>xóa mềm</strong>. Bạn có thể <strong>khôi phục</strong> hoặc <strong>xóa vĩnh viễn</strong>.
    </div>

    <div class="stat-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Xóa lúc</th>
                        <th class="pe-4 text-end">Thao tác</th>
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
                            <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>
                            <td class="small text-muted text-nowrap">{{ $product->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.products.restore', $product->id) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success me-1">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.force-destroy', $product->id) }}" method="post" class="d-inline"
                                      onsubmit="return confirm('Xóa vĩnh viễn sản phẩm «{{ $product->name }}»? Không thể hoàn tác.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-octagon me-1"></i> Xóa vĩnh viễn
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-trash d-block mb-2" style="font-size: 2rem;"></i>
                                Thùng rác trống. <a href="{{ route('admin.products.index') }}">Quay lại danh sách</a>
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
