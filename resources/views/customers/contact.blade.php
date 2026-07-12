@extends('customers.layouts.layout')

@section('title', 'Liên hệ')

@section('content')
<main class="bg-slate-50 text-slate-800">

    <!-- HEADER -->
    <section class="max-w-7xl mx-auto px-6 lg:px-12 pt-20 pb-16 text-center">
        <span class="text-orange-500 font-bold tracking-[0.3em] text-xs uppercase">
            Shop TT
        </span>
        <h2 class="text-5xl font-extrabold mt-3 mb-4">
            Liên hệ với chúng tôi
        </h2>
        <p class="text-slate-500 max-w-xl mx-auto">
            Bạn cần tư vấn chọn giày, hỗ trợ đơn hàng hoặc hợp tác? 
            Đội ngũ TT luôn sẵn sàng hỗ trợ bạn.
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

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6">
                    <ul class="list-disc ps-4 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- NAME -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                        class="w-full mt-2 px-4 py-3 rounded-xl border @error('name') border-red-400 @else border-slate-200 @enderror bg-white focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                        class="w-full mt-2 px-4 py-3 rounded-xl border @error('email') border-red-400 @else border-slate-200 @enderror bg-white focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- PHONE -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()?->phone) }}"
                        class="w-full mt-2 px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">
                </div>

                <!-- SUBJECT -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Tiêu đề</label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                        placeholder="Ví dụ: Hỏi về đơn hàng, tư vấn sản phẩm..."
                        class="w-full mt-2 px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">
                </div>

                <!-- MESSAGE -->
                <div>
                    <label class="text-sm font-semibold text-slate-600">Nội dung <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="5" required
                        class="w-full mt-2 px-4 py-3 rounded-xl border @error('message') border-red-400 @else border-slate-200 @enderror bg-white focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full py-3 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-400 transition-all shadow-md hover:shadow-orange-200">
                    Gửi liên hệ
                </button>

            </form>
        </div>

    </section>

    {{-- @auth
        @if ($myContacts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 lg:px-12 pb-24">
            <h3 class="text-2xl font-black mb-6">Lịch sử liên hệ của bạn</h3>
            <div class="space-y-4">
                @foreach ($myContacts as $contact)
                    <div class="glass-light rounded-[24px] p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                            <div>
                                <p class="font-bold text-slate-900">{{ $contact->subject ?: '(Không có tiêu đề)' }}</p>
                                <p class="text-sm text-slate-400 mt-1">{{ $contact->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if ($contact->status === 'replied')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 border border-emerald-200 flex-shrink-0">
                                    ✓ Đã phản hồi
                                </span>
                            @elseif ($contact->status === 'read')
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 border border-blue-200 flex-shrink-0">
                                    ✓ Đã đọc
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 border border-slate-200 flex-shrink-0">
                                    Chờ phản hồi
                                </span>
                            @endif
                        </div>

                        <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-600 mb-4" style="white-space: pre-wrap;">
                            {{ $contact->message }}
                        </div>

                        @if ($contact->admin_reply)
                            <div class="border-t border-slate-200 pt-4">
                                <p class="text-xs font-semibold uppercase tracking-widest text-orange-500 mb-2">↩ Phản hồi từ Shop TT</p>
                                <div class="rounded-xl bg-orange-50 border border-orange-100 px-4 py-3 text-sm text-slate-700" style="white-space: pre-wrap;">
                                    {{ $contact->admin_reply }}
                                </div>
                                <p class="text-xs text-slate-400 mt-2">{{ $contact->replied_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    @endauth --}}

</main>
@endsection