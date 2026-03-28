@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('page_title', 'Sửa sản phẩm')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.products.index') }}" class="text-muted text-decoration-none">Sản phẩm</a>
    <span class="text-muted">/</span>
    <span>Sửa</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 720px;">
        <form action="{{ route('admin.products.update', $product) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Mô tả</label>
                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="price" class="form-label fw-medium">Giá gốc <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01"
                           class="form-control @error('price') is-invalid @enderror" required min="0">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="sale_price" class="form-label fw-medium">Giá khuyến mãi</label>
                    <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01"
                           class="form-control @error('sale_price') is-invalid @enderror" min="0">
                    @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="stock" class="form-label fw-medium">Tồn kho <span class="text-danger">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}"
                           class="form-control @error('stock') is-invalid @enderror" required min="0">
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="thumbnail" class="form-label fw-medium">Ảnh sản phẩm</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="form-control @error('thumbnail') is-invalid @enderror">
                    @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">PNG, JPG, WebP — tối đa 4MB.</div>
                </div>

                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                               @checked(old('is_active', $product->is_active))>
                        <label class="form-check-label" for="is_active">Hiển thị sản phẩm</label>
                    </div>
                </div>

                @if($product->thumbnail_url)
                    <div class="col-12">
                        <p class="mb-2 fw-medium">Ảnh hiện tại</p>
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="rounded border" style="max-width: 240px; object-fit: cover;">
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Cập nhật sản phẩm
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
