@extends('customers.layouts.layout')

@section('title', 'Liên hệ')

@section('content')
<main class="bg-slate-50 text-slate-800">

    <!-- HEADER -->
    <section class="max-w-7xl mx-auto px-6 lg:px-12 pt-20 pb-16 text-center">
        <span class="text-orange-500 font-bold tracking-[0.3em] text-xs uppercase">
            Shop TTM
        </span>
        <h2 class="text-5xl font-extrabold mt-3 mb-4">
            Liên hệ với chúng tôi
        </h2>
        <p class="text-slate-500 max-w-xl mx-auto">
            Bạn cần tư vấn chọn giày, hỗ trợ đơn hàng hoặc hợp tác? 
            Đội ngũ Luna luôn sẵn sàng hỗ trợ bạn.
        </p>
    </section>

    <!-- CONTENT -->
    <section class="max-w-7xl mx-auto px-6 lg:px-12 pb-24 grid grid-cols-1 lg:grid-cols-2 gap-12">

        <!-- LEFT -->
        <div class="space-y-8">

            <!-- INFO -->
            <div class="glass-light p-8 rounded-[32px] space-y-6">
                <h3 class="text-xl font-bold">Thông tin cửa hàng</h3>

                <div class="space-y-5 text-sm">

                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center">📍</div>
                        <p>Tòa nhà FPT Polytechnic., Cổng số 2, 13 Trịnh Văn Bô, Xuân Phương, Hà Nội</p>
                    </div>

                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center">📞</div>
                        <p>0981725836</p>
                    </div>

                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center">✉️</div>
                        <p>caodang.fpt.edu.vn</p>
                    </div>

                </div>
            </div>

            <!-- HOURS -->
            <div class="glass-light p-8 rounded-[32px]">
                <h3 class="text-xl font-bold mb-4">Giờ làm việc</h3>
                <div class="text-sm text-slate-500 space-y-2">
                    <p>Thứ 2 - Thứ 6: 8:00 - 21:00</p>
                    <p>Thứ 7 - CN: 9:00 - 22:00</p>
                </div>
            </div>

            <!-- MAP -->
            <div class="glass-light p-4 rounded-[32px]">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.8639311820666!2d105.74468687593208!3d21.03812978061353!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2s!4v1775008414989!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

        </div>

        <!-- RIGHT FORM -->
        <div class="glass-light p-10 rounded-[32px]">

            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-600 p-4 rounded-xl mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="/lien-he" method="POST" class="space-y-6">
                @csrf

                <!-- NAME -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Họ và tên</label>
                    <input type="text" name="name" required
                        class="w-full mt-2 px-4 py-3 rounded-xl border border-slate-200 bg-white 
                        focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" required
                        class="w-full mt-2 px-4 py-3 rounded-xl border border-slate-200 bg-white 
                        focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">
                </div>

                <!-- MESSAGE -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Nội dung</label>
                    <textarea name="message" rows="5" required
                        class="w-full mt-2 px-4 py-3 rounded-xl border border-slate-200 bg-white 
                        focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition"></textarea>
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full py-3 bg-orange-500 text-white font-bold rounded-xl 
                    hover:bg-orange-400 transition-all shadow-md hover:shadow-orange-200">
                    Gửi liên hệ
                </button>

            </form>
        </div>

    </section>
</main>
@endsection