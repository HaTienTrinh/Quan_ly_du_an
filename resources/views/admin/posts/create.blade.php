@extends('layouts.admin')

@section('title', 'Thêm bài viết')

@section('page_title', 'Thêm bài viết')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.posts.index') }}" class="text-muted text-decoration-none">Bài viết</a>
    <span class="text-muted">/</span>
    <span>Thêm mới</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 800px;">
        <form action="{{ route('admin.posts.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label fw-medium">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror" required maxlength="255">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="summary" class="form-label fw-medium">Tóm tắt</label>
                    <textarea name="summary" id="summary" rows="3" class="form-control @error('summary') is-invalid @enderror">{{ old('summary') }}</textarea>
                    @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="content" class="form-label fw-medium">Nội dung <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="12" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="thumbnail" class="form-label fw-medium">Ảnh đại diện</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="form-control @error('thumbnail') is-invalid @enderror">
                    @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">PNG, JPG, WebP — tối đa 4MB.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="status" class="form-label fw-medium">Trạng thái <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" @selected(old('status', 'draft') === 'draft')>Bản nháp</option>
                        <option value="published" @selected(old('status') === 'published')>Xuất bản</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="published_at" class="form-label fw-medium">Thời điểm xuất bản</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                           value="{{ old('published_at') }}"
                           class="form-control @error('published_at') is-invalid @enderror">
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Áp dụng khi trạng thái là «Xuất bản». Để trống để dùng thời điểm hiện tại.</div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Lưu bài viết
                </button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
