@extends('customers.layouts.layout')

@section('title', 'Liên hệ')

@section('content')
    <main class="max-w-7xl mx-auto px-12 py-20">
        <header class="mb-16">
            <span class="text-orange-500 font-bold tracking-[0.3em] text-sm uppercase mb-2 block">Kết nối cùng chúng tôi</span>
            <h2 class="text-5xl font-extrabold">Liên hệ tư vấn</h2>
        </header>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 p-4 rounded-xl mb-8">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="glass p-12 rounded-[40px]">
                <h3 class="text-2xl font-bold mb-10">Thông tin cửa hàng</h3>
                
                <div class="space-y-8">
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 glass rounded-2xl flex items-center justify-center text-orange-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold mb-1">Địa chỉ</p>
                            <p class="text-gray-400 text-sm">123 Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 glass rounded-2xl flex items-center justify-center text-orange-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold mb-1">Điện thoại</p>
                            <p class="text-gray-400 text-sm">0123 456 789</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 glass rounded-2xl flex items-center justify-center text-orange-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold mb-1">Email</p>
                            <p class="text-gray-400 text-sm">hello@lunasteps.vn</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass p-12 rounded-[40px]">
                <form action="/lien-he" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Họ và tên</label>
                        <input type="text" name="name" placeholder="Nhập họ tên" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm focus:outline-none focus:border-orange-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" placeholder="Nhập email" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm focus:outline-none focus:border-orange-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nội dung</label>
                        <textarea name="message" rows="5" placeholder="Bạn cần tư vấn mẫu giày nào?" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm focus:outline-none focus:border-orange-500 transition"></textarea>
                    </div>

                    <button type="submit" class="w-fit px-10 py-4 bg-orange-500 text-black font-bold rounded-2xl hover:bg-orange-400 transition-all orange-glow uppercase text-sm tracking-widest">
                        Gửi liên hệ
                    </button>
                </form>
            </div>

        </div>
    </main>
@endsection
