<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;

            color: white;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .orange-glow {
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.4);
        }
    </style>
</head>

<body class="overflow-x-hidden">

    <nav class="flex items-center justify-between px-12 py-6 sticky top-0 z-50 bg-[#05070a]/80 backdrop-blur-md">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center orange-glow">
                <span class="text-black font-bold text-xl">TTM</span>
            </div>
            <div>
                <h1 class="font-extrabold tracking-tighter text-xl leading-none">SHOP TTM</h1>
                <p class="text-[10px] tracking-[0.2em] text-gray-400">SNEAKER STUDIO</p>
            </div>
        </div>

        <ul class="hidden md:flex items-center gap-8 text-sm font-medium uppercase tracking-widest text-gray-300">
            <li><a href="{{ route('home') }}"
                    class="{{ Route::currentRouteName() === 'home' ? 'text-white border-b-2 border-orange-500 pb-1' : 'hover:text-orange-500 transition' }}">Trang
                    chủ</a></li>
            <li><a href="{{ route('products') }}"
                    class="{{ Route::currentRouteName() === 'products' ? 'text-white border-b-2 border-orange-500 pb-1' : 'hover:text-orange-500 transition' }}">Sản
                    phẩm</a></li>
            <li><a href="{{ route('posts') }}"
                    class="{{ Route::currentRouteName() === 'posts' ? 'text-white border-b-2 border-orange-500 pb-1' : 'hover:text-orange-500 transition' }}">Tin
                    tức</a></li>
            <li><a href="{{ route('contact') }}"
                    class="{{ Route::currentRouteName() === 'contact' ? 'text-white border-b-2 border-orange-500 pb-1' : 'hover:text-orange-500 transition' }}">Liên
                    hệ</a></li>
        </ul>

        <div class="flex items-center gap-6">

            @guest
                <a href="{{ route('login') }}" class="text-sm font-medium uppercase hover:text-orange-500 transition">Đăng
                    nhập</a>
            @else
                <span class="nav-link text-white">Xin chào, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-white">Đăng xuất</button>
                </form>
            @endguest



            <a href="{{ route('cart.index') }}" class="relative cursor-pointer hover:text-orange-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @php
                    $cart = Session::get('cart', []);
                    $itemCount = 0;
                    foreach ($cart as $item) {
                        $itemCount += $item['quantity'];
                    }
                @endphp
                @if ($itemCount > 0)
                    <span
                        class="absolute -top-2 -right-2 bg-orange-600 text-[10px] w-4 h-4 rounded-full flex items-center justify-center">{{ $itemCount }}</span>
                @endif
            </a>
        </div>
    </nav>
    @yield('content')
    <footer class="mt-32 border-t border-white/10 bg-[#05070a]/80 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

                <div class="col-span-1 lg:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center orange-glow">
                            <span class="text-black font-bold text-sm">TTM</span>
                        </div>
                        <h1 class="font-extrabold tracking-tighter text-lg">SHOP TTM</h1>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        Mang đến trải nghiệm bước chân êm ái và phong cách thời thượng nhất. Đồng hành cùng bạn trên mọi
                        hành trình.
                    </p>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-10 h-10 glass rounded-full flex items-center justify-center hover:bg-orange-500 transition-all duration-300 group">
                            <svg class="w-5 h-5 group-hover:text-black" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 glass rounded-full flex items-center justify-center hover:bg-orange-500 transition-all duration-300 group">
                            <svg class="w-5 h-5 group-hover:text-black" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">Khám phá</h4>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-orange-500 transition">Giày Chạy Bộ</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition">Giày Sneaker Nam</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition">Giày Sneaker Nữ</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition">Bộ Sưu Tập Giới Hạn</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">Hỗ trợ khách hàng</h4>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-orange-500 transition">Hướng dẫn chọn size</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition">Chính sách đổi trả</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition">Theo dõi đơn hàng</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition">Hệ thống cửa hàng</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">Đăng ký nhận tin</h4>
                    <p class="text-gray-400 text-sm mb-4">Nhận ngay ưu đãi 10% cho đơn hàng đầu tiên của bạn.</p>
                    <form class="flex flex-col gap-3">
                        <input type="email" placeholder="Email của bạn..."
                            class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-orange-500 transition">
                        <button type="submit"
                            class="bg-orange-500 text-black font-bold py-3 rounded-xl hover:bg-orange-400 transition text-sm orange-glow">
                            ĐĂNG KÝ NGAY
                        </button>
                    </form>
                </div>

            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-xs">
                    © 2026 Luna Steps Sneaker Studio. All rights reserved. Designed by Trịnh.
                </p>
                <div class="flex gap-6 text-xs text-gray-500">
                    <a href="#" class="hover:text-white">Điều khoản sử dụng</a>
                    <a href="#" class="hover:text-white">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>
