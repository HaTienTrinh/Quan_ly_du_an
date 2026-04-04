@extends('layouts.admin')

@section('title', 'Thông tin cá nhân')

@section('page_title', 'Thông tin cá nhân')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <span>Hồ sơ của tôi</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 720px;">
        <p class="text-muted small mb-4">Cập nhật thông tin hiển thị và đăng nhập của bạn (tài khoản quản trị).</p>

        <form action="{{ route('admin.profile.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-medium">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="phone" class="form-label fw-medium">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="form-control @error('phone') is-invalid @enderror" maxlength="20">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="birth_date" class="form-label fw-medium">Ngày sinh</label>
                    <input type="date" name="birth_date" id="birth_date"
                           value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                           class="form-control @error('birth_date') is-invalid @enderror">
                    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="password" class="form-label fw-medium">Mật khẩu mới</label>
                    <input type="password" name="password" id="password" autocomplete="new-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Để trống nếu không đổi.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="password_confirmation" class="form-label fw-medium">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                           class="form-control">
                </div>

                <div class="col-12">
                    <label for="avatar" class="form-label fw-medium">Ảnh đại diện</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*"
                           class="form-control @error('avatar') is-invalid @enderror">
                    @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($user->avatar_url)
                    <div class="col-12">
                        <p class="mb-2 fw-medium small text-muted">Ảnh hiện tại</p>
                        <img src="{{ $user->avatar_url }}" alt="" class="rounded-circle border" width="88" height="88" style="object-fit: cover;">
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Lưu thay đổi
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Về bảng điều khiển</a>
            </div>
        </form>
    </div>
@endsection
