@extends('layouts.admin')

@section('title', 'Sửa bài viết')

@section('page_title', 'Sửa bài viết')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.posts.index') }}" class="text-muted text-decoration-none">Bài viết</a>
    <span class="text-muted">/</span>
    <span>Sửa</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 800px;">
        <form action="{{ route('admin.posts.update', $post) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label fw-medium">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}"
                           class="form-control @error('title') is-invalid @enderror" required maxlength="255">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="summary" class="form-label fw-medium">Tóm tắt</label>
                    <textarea name="summary" id="summary" rows="3" class="form-control @error('summary') is-invalid @enderror">{{ old('summary', $post->summary) }}</textarea>
                    @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="content" class="form-label fw-medium">Nội dung <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="12" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $post->content) }}</textarea>
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
                        <option value="draft" @selected(old('status', $post->status) === 'draft')>Bản nháp</option>
                        <option value="published" @selected(old('status', $post->status) === 'published')>Xuất bản</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="published_at" class="form-label fw-medium">Thời điểm xuất bản</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                           class="form-control @error('published_at') is-invalid @enderror">
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Áp dụng khi trạng thái là «Xuất bản». Để trống để giữ ngày hiện có hoặc dùng thời điểm hiện tại.</div>
                </div>

                @if($post->thumbnail_url)
                    <div class="col-12">
                        <p class="mb-2 fw-medium">Ảnh hiện tại</p>
                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="rounded border" style="max-width: 240px; object-fit: cover;">
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Cập nhật bài viết
                </button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
