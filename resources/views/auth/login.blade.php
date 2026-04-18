<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* BACKGROUND */
        .auth-bg {
            min-height: 100vh;
            background:
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)),
                url('{{ asset("images/background.jpg") }}') center/cover no-repeat;
            display: flex;
        }

        .auth-content {
            display: flex;
            width: 100%;
        }

        /* LEFT SIDE */
        .auth-left {
            flex: 1;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            position: relative; /* Quan trọng để nút .brand canh theo lớp này */
        }

        /* Sửa nút Quay lại trang chủ */
        .brand {
            position: absolute;
            top: 30px;
            left: 40px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8); /* Màu trắng mờ */
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-decoration: none;
            transition: 0.3s;
        }

        .brand:hover {
            color: #fff;
            border-color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .auth-left h1 {
            font-size: 52px;
            font-weight: 800;
            line-height: 1.2;
        }

        .auth-left p {
            margin-top: 15px;
            font-size: 16px;
            opacity: 0.9;
        }

        /* RIGHT SIDE */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            transition: 0.3s;
        }

        .auth-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.18);
        }

        .auth-title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #1f2937;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            display: inline-block;
        }

        /* INPUT */
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e5e7eb;
        }

        .form-control:focus {
            border-color: #ff7a00;
            box-shadow: 0 0 0 0.2rem rgba(255, 122, 0, 0.15);
        }

        /* BUTTON */
        .btn-primary {
            background: linear-gradient(135deg, #ff7a00, #ff9a3c);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(255, 122, 0, 0.3);
        }

        .auth-card a {
            color: #ff7a00;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-card a:hover {
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: #ff7a00;
            border-color: #ff7a00;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .auth-left {
                display: none;
            }
            .auth-bg {
                background: #f3f4f6; /* Nền xám nhẹ cho mobile */
            }
        }
    </style>
</head>

<body>

    <div class="auth-bg">
        <div class="auth-content">

            <div class="auth-left">
                <a href="{{ route('home') }}" class="brand px-4 py-2 small rounded-3">
                    ← Quay lại trang chủ
                </a>

                <h1>
                    Bứt phá phong cách <br>
                    với từng bước.
                </h1>

                <p>
                    Khám phá những mẫu giày thời trang, năng động
                    và êm ái cho mọi nhịp sống.
                </p>
            </div>

            <div class="auth-right">
                <div class="auth-card">
                    <div class="auth-title">Đăng nhập</div>

                    @if (session('error'))
                        <p class="mb-3 text-sm" style="color:#dc2626;">{{ session('error') }}</p>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                   class="form-control"
                                   style="@error('email') border-color:#dc2626; @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="example@gmail.com">
                            @error('email')
                                <p class="mt-1 text-sm" style="color:#dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password">Mật khẩu</label>
                            <input type="password" id="password" name="password"
                                   class="form-control"
                                   style="@error('password') border-color:#dc2626; @enderror"
                                   placeholder="Nhập mật khẩu">
                            @error('password')
                                <p class="mt-1 text-sm" style="color:#dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label small" for="remember">Ghi nhớ</label>
                            </div>
                            <a href="#" class="small">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Đăng nhập
                        </button>
                    </form>

                    <div class="text-center my-4 text-muted small">hoặc</div>

                    <p class="text-center mb-0 small">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}">Đăng ký ngay</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>