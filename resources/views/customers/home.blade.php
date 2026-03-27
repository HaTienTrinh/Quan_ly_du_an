@extends('customers.layouts.layout')

@section('title', 'Trang chủ')

@section('content')
    <main class="max-w-7xl mx-auto px-12 pt-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <div>
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-gray-700 text-xs text-gray-400 mb-6">
                <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                Bộ sưu tập mới đã lên kệ
            </div>
            <h2 class="text-6xl font-extrabold leading-tight mb-6">
                Bứt phá phong <br> cách với từng <br> <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-500">bước chân.</span>
            </h2>
            <p class="text-gray-400 text-lg max-w-md mb-10 leading-relaxed">
                Khám phá những mẫu giày thời trang, năng động và êm ái cho mọi nhịp sống. Thiết kế nổi bật, phối đồ dễ, tạo
                dấu ấn riêng ngay từ cái nhìn đầu tiên.
            </p>

            <div class="flex gap-4 mb-16">
                <button
                    class="px-8 py-4 bg-orange-500 text-black font-bold rounded-full hover:bg-orange-400 transition orange-glow uppercase text-sm">
                    Xem sản phẩm
                </button>
                <button
                    class="px-8 py-4 bg-white/10 text-white font-bold rounded-full hover:bg-white/20 transition backdrop-blur-md border border-white/10 uppercase text-sm">
                    Liên hệ ngay
                </button>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="glass p-6 rounded-3xl">
                    <h4 class="text-3xl font-bold mb-1">120+</h4>
                    <p class="text-xs text-gray-500 uppercase">Mẫu giày thời thượng</p>
                </div>
                <div class="glass p-6 rounded-3xl">
                    <h4 class="text-3xl font-bold mb-1">24h</h4>
                    <p class="text-xs text-gray-500 uppercase">Hỗ trợ tư vấn nhanh</p>
                </div>
                <div class="glass p-6 rounded-3xl">
                    <h4 class="text-3xl font-bold mb-1">4.9<span class="text-orange-500">★</span></h4>
                    <p class="text-xs text-gray-500 uppercase">Đánh giá từ khách hàng</p>
                </div>
            </div>
        </div>

        <div class="relative flex justify-center">
            <div class="absolute inset-0 bg-orange-500/20 blur-[120px] rounded-full"></div>

            <div class="glass w-full max-w-md p-8 rounded-[40px] relative z-10 overflow-hidden">
                <div
                    class="aspect-square bg-gradient-to-br from-orange-400 to-orange-600 rounded-[32px] mb-8 relative flex items-center justify-center">
                    <div class="w-48 h-12 bg-white/30 rounded-full blur-xl absolute bottom-12 rotate-[-15deg]"></div>
                    <div class="text-8xl transform -rotate-[25deg] drop-shadow-2xl">👟</div>
                </div>

                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Mẫu nổi bật</p>
                        <h3 class="text-4xl font-bold">Velocity One</h3>
                    </div>
                    <div
                        class="bg-white/10 px-4 py-2 rounded-full border border-white/10 text-xs font-bold uppercase tracking-tighter">
                        New Drop
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
