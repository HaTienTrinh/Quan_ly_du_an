@extends('layouts.admin')

@section('title', 'Sửa tài khoản')

@section('page_title', 'Sửa tài khoản')

@section('breadcrumb')
    <span class="text-muted">Quản trị</span>
    <span class="text-muted">/</span>
    <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">Tài khoản</a>
    <span class="text-muted">/</span>
    <span>Sửa</span>
@endsection

@section('content')
    <div class="stat-card p-4 p-md-5" style="max-width: 720px;">
        <form action="{{ route('admin.users.update', $user) }}" method="post" enctype="multipart/form-data">
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
                    <label for="role" class="form-label fw-medium">Vai trò <span class="text-danger">*</span></label>
                    @if ($user->id === 1)
                        <input type="text" class="form-control bg-light" value="Quản trị" readonly>
                        <div class="form-text text-warning"><i class="bi bi-shield-lock me-1"></i>Tài khoản admin mặc định không thể thay đổi vai trò.</div>
                    @else
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="customer" @selected(old('role', $user->role) === 'customer')>Khách hàng</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Quản trị</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>

                <div class="col-12 col-md-6 d-flex align-items-center">
                    @if ($user->id === 1)
                        <div class="mt-3">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>Luôn hoạt động
                            </span>
                            <div class="form-text text-warning mt-1"><i class="bi bi-shield-lock me-1"></i>Không thể đình chỉ tài khoản này.</div>
                        </div>
                    @else
                        <div class="form-check mt-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   @checked(old('is_active', $user->is_active ? '1' : '0') === '1')>
                            <label class="form-check-label" for="is_active">Tài khoản đang hoạt động</label>
                        </div>
                    @endif
                </div>

                <div class="col-12 col-md-6">
                    <label for="password" class="form-label fw-medium">Mật khẩu mới</label>
                    <input type="password" name="password" id="password" autocomplete="new-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Để trống nếu không đổi. Tối thiểu 6 ký tự.</div>
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
                    <div class="form-text">PNG, JPG, WebP — tối đa 4MB.</div>
                </div>

                @if($user->avatar_url)
                    <div class="col-12">
                        <p class="mb-2 fw-medium">Ảnh hiện tại</p>
                        <img src="{{ $user->avatar_url }}" alt="" class="rounded-circle border" width="96" height="96" style="object-fit: cover;">
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn text-white" style="background: linear-gradient(135deg, #f97316, #ea580c);">
                    Cập nhật
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection
