@extends('layouts.admin')

@section('title', 'Sửa danh mục')

@section('page_title', 'Chỉnh sửa danh mục')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.categories.index') }}" class="text-muted text-decoration-none">Danh mục</a>
    <span class="text-muted">/</span>
    <span>{{ $category->name }}</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 640px;">
        <form action="{{ route('admin.categories.update', $category) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-medium">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Slug cập nhật theo tên khi bạn đổi tên.</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-medium">Mô tả</label>
                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if($category->image)
                <div class="mb-2">
                    <span class="small text-muted d-block mb-1">Ảnh hiện tại</span>
                    <img src="{{ asset('storage/'.$category->image) }}" alt="" class="rounded border" style="max-height: 120px; object-fit: cover;">
                </div>
            @endif

            <div class="mb-3">
                <label for="image" class="form-label fw-medium">Ảnh mới (nếu đổi)</label>
                <input type="file" name="image" id="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4 form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                       @checked(old('is_active', $category->is_active))>
                <label class="form-check-label" for="is_active">Hiển thị trên cửa hàng</label>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Cập nhật
                </button>
                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-primary">Xem chi tiết</a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Quay lại danh sách</a>
            </div>
        </form>
    </div>
@endsection
