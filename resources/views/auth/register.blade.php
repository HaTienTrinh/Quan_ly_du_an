@extends('layouts.app')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header text-center fw-bold fs-5">Đăng ký tài khoản</div>
            <div class="card-body p-4">

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    {{-- Họ tên --}}
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Nguyễn Văn A">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="example@gmail.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="0901234567">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mật khẩu --}}
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Ít nhất 8 ký tự">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div class="mb-4">
                        <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Nhập lại mật khẩu">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                </form>

                <hr>
                <p class="text-center mb-0">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}">Đăng nhập ngay</a>
                </p>

            </div>
        </div>
    </div>
</div>
@endsection
