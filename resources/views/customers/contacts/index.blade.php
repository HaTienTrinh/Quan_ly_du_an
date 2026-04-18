@extends('customers.layouts.layout')

@section('title', 'Liên Hệ Của Tôi')

@section('content')
<main class="min-h-screen bg-slate-50 pt-12 pb-24">
    <div class="mx-auto max-w-4xl px-4 md:px-12">

        <div class="mb-8 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Liên hệ của tôi</h1>
                <p class="mt-2 text-slate-500">Lịch sử các tin nhắn bạn đã gửi và phản hồi từ Shop TTM.</p>
            </div>
            <a href="{{ route('contact') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-5 py-3 font-semibold text-white transition hover:bg-orange-600">
                + Gửi liên hệ mới
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($contacts->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
                <p class="text-2xl font-bold text-slate-900">Chưa có liên hệ nào</p>
                <p class="mt-3 text-slate-500">Bạn chưa gửi liên hệ nào. Hãy liên hệ với chúng tôi nếu cần hỗ trợ.</p>
                <a href="{{ route('contact') }}"
                    class="mt-6 inline-flex rounded-xl bg-orange-500 px-6 py-3 font-semibold text-white transition hover:bg-orange-600">
                    Gửi liên hệ ngay
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($contacts as $contact)
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                            <div>
                                <p class="font-bold text-slate-900 text-lg">
                                    {{ $contact->subject ?: '(Không có tiêu đề)' }}
                                </p>
                                <p class="text-sm text-slate-400 mt-1">
                                    {{ $contact->created_at->format('d/m/Y H:i') }}
                                </p>
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

                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600" style="white-space: pre-wrap;">{{ $contact->message }}</div>

                        @if ($contact->admin_reply)
                            <div class="mt-4 border-t border-slate-100 pt-4">
                                <p class="text-xs font-semibold uppercase tracking-widest text-orange-500 mb-2">
                                    ↩ Phản hồi từ Shop TTM
                                    <span class="ml-2 font-normal normal-case tracking-normal text-slate-400">
                                        {{ $contact->replied_at?->format('d/m/Y H:i') }}
                                    </span>
                                </p>
                                <div class="rounded-2xl bg-orange-50 border border-orange-100 px-4 py-3 text-sm text-slate-700" style="white-space: pre-wrap;">{{ $contact->admin_reply }}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($contacts->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $contacts->links('pagination::simple-tailwind') }}
                </div>
            @endif
        @endif
    </div>
</main>
@endsection
