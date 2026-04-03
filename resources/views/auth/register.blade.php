<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        /* BACKGROUND */
        .auth-bg {
            min-height: 100vh;
            background:
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)),
                url('{{ asset('images/background.jpg') }}') center/cover no-repeat;
            display: flex;
        }

        .auth-content {
            display: flex;
            width: 100%;
        }

        /* LEFT */
        .auth-left {
            flex: 1;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
        }

        .brand {
            position: absolute;
            top: 30px;
            left: 40px;
            font-size: 26px;
            font-weight: 800;
        }

        .auth-left h1 {
            font-size: 48px;
            font-weight: 800;
        }

        /* RIGHT */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* CARD */
        .auth-card {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.6s ease;
            transition: 0.3s;
        }

        .auth-card:hover {
            transform: translateY(-5px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        label {
            font-weight: 500;
        }

        /* INPUT */
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #ff7a00;
            box-shadow: 0 0 0 0.2rem rgba(255, 122, 0, 0.15);
            transform: scale(1.01);
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
            transform: scale(1.04);
            box-shadow: 0 10px 25px rgba(255, 122, 0, 0.3);
        }

        a {
            color: #ff7a00;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            display: block;
        }

        /* INPUT ANIMATION (nhẹ) */
        .form-control {
            opacity: 0;
            transform: translateY(10px);
            animation: inputFade 0.5s forwards;
        }

        .form-control:nth-child(1) {
            animation-delay: 0.2s;
        }

        .form-control:nth-child(2) {
            animation-delay: 0.3s;
        }

        .form-control:nth-child(3) {
            animation-delay: 0.4s;
        }

        .form-control:nth-child(4) {
            animation-delay: 0.5s;
        }

        @keyframes inputFade {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .auth-left {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="auth-bg">
        <div class="auth-content">

            <!-- LEFT -->
            <div class="auth-left">
                <h1>
                    Gia nhập cộng đồng <br>
                    yêu giày thời thượng.
                </h1>
                <p class="mt-3 opacity-75">
                    Đăng ký ngay để nhận những ưu đãi đặc quyền và <br>
                    cập nhật xu hướng mới nhất từ chúng tôi.
                </p>
            </div>

            <!-- RIGHT -->
            <div class="auth-right">
                <div class="auth-card">

                    <div class="auth-title">Đăng ký</div>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label>Họ tên</label>
                            <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@gmail.com">
                        </div>

                        <div class="mb-3">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="0901234567">
                        </div>

                        <div class="mb-3">
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="Ít nhất 8 ký tự">
                        </div>

                        <div class="mb-4">
                            <label>Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Nhập lại mật khẩu">
                        </div>

                        <button class="btn btn-primary w-100">
                            Đăng ký
                        </button>
                    </form>

                    <hr>

                    <p class="text-center mb-0">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}">Đăng nhập</a>
                    </p>

                </div>
            </div>

        </div>
    </div>

</body>

</html>
