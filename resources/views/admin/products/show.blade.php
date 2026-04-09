@extends('layouts.admin')

@section('title', $product->name)

@section('page_title', 'Chi tiết sản phẩm')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.products.index') }}" class="text-muted text-decoration-none">Sản phẩm</a>
    <span class="text-muted">/</span>
    <span>{{ $product->name }}</span>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="stat-card p-4 text-center">
                @if($product->thumbnail_url)
                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="img-fluid rounded-3 border mb-3 w-100" style="max-height: 320px; object-fit: cover;">
                @else
                    <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted mx-auto mb-3" style="height: 280px; max-width: 100%;">
                        <i class="bi bi-box-seam" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <h2 class="h5 fw-bold mb-1">{{ $product->name }}</h2>
                <p class="small font-monospace text-muted mb-3">{{ $product->slug }}</p>
                @if($product->is_active)
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Đang hiển thị</span>
                @else
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Đang ẩn</span>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="stat-card p-4 h-100">
                <h3 class="h6 text-uppercase text-muted fw-semibold mb-3" style="letter-spacing: 0.06em;">Thông tin sản phẩm</h3>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted small">Danh mục</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $product->category?->name ?? 'Chưa có' }}</dd>

                    <dt class="col-sm-4 text-muted small">Giá gốc</dt>
                    <dd class="col-sm-8 fw-semibold">{{ number_format($product->price, 0, ',', '.') }}₫</dd>

                    <dt class="col-sm-4 text-muted small">Giá khuyến mãi</dt>
                    <dd class="col-sm-8">{{ $product->sale_price ? number_format($product->sale_price, 0, ',', '.').'₫' : '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Tồn kho</dt>
                    <dd class="col-sm-8">{{ number_format($product->stock) }}</dd>

                    <dt class="col-sm-4 text-muted small">Màu máy</dt>
                    <dd class="col-sm-8">
                        @if ($product->colors->isEmpty())
                            —
                        @else
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($product->colors as $color)
                                    <span class="badge rounded-pill bg-light text-dark border d-inline-flex align-items-center gap-2 px-3 py-2">
                                        <span class="rounded-circle border" style="width: 12px; height: 12px; background-color: {{ $color->hex_code ?: '#cccccc' }};"></span>
                                        {{ $color->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-muted small">Mô tả</dt>
                    <dd class="col-sm-8">{{ $product->description ?: '—' }}</dd>

                    <dt class="col-sm-4 text-muted small">Tạo lúc</dt>
                    <dd class="col-sm-8">{{ $product->created_at?->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4 text-muted small">Cập nhật</dt>
                    <dd class="col-sm-8">{{ $product->updated_at?->format('d/m/Y H:i') }}</dd>
                </dl>
                <hr class="my-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Danh sách</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="post" class="d-inline"
                          onsubmit="return confirm('Chuyển sản phẩm này vào thùng rác?');">
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
