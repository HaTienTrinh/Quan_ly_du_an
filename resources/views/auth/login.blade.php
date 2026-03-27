@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header text-center fw-bold fs-5">Đăng nhập</div>
            <div class="card-body p-4">

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="example@gmail.com"
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mật khẩu --}}
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Mật khẩu">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ghi nhớ đăng nhập --}}
                    <div class="mb-4 form-check">
                        <input type="checkbox" name="remember" id="remember"
                               class="form-check-input"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </form>

                <hr>
                <p class="text-center mb-0">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}">Đăng ký ngay</a>
                </p>

            </div>
        </div>
    </div>
</div>
@endsection
