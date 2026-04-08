@extends('customers.layouts.layout')

@section('title', 'Yêu Cầu Trả Hàng')

@section('content')
    <div class="min-h-screen bg-slate-50 pt-12">
        <div class="mx-auto max-w-5xl px-4 pb-12 md:px-12">
            <div class="mb-8">
                <a href="{{ route('orders.show', $order) }}" class="inline-block text-orange-500 transition hover:text-orange-600">
                    ← Quay lại chi tiết đơn hàng
                </a>
            </div>

            <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Yêu cầu trả hàng</p>
                <h1 class="mt-3 text-3xl font-extrabold text-slate-900">Đơn {{ $order->order_code }}</h1>
                <p class="mt-3 text-slate-500">
                    Chọn đúng sản phẩm cần xử lý, nhập lý do và gửi minh chứng để hệ thống kiểm duyệt.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                    <ul class="list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('orders.returns.store', $order) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">
                @csrf

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">1. Chọn sản phẩm</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($eligibleItems as $item)
                            <label
                                class="flex cursor-pointer gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-orange-300">
                                <input type="radio" name="order_item_id" value="{{ $item->id }}"
                                    class="mt-2 h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                    @checked((int) old('order_item_id') === (int) $item->id)>

                                <div class="h-20 w-20 overflow-hidden rounded-2xl bg-slate-100">
                                    @if ($item->product_thumbnail)
                                        <img src="{{ asset('storage/' . $item->product_thumbnail) }}"
                                            alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-slate-400">
                                            No image
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900">{{ $item->product_name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Số lượng: {{ $item->quantity }} · Giá mua:
                                        {{ number_format($item->unit_price, 0, ',', '.') }} ₫
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">2. Chọn hướng xử lý</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-orange-300">
                            <input type="radio" name="request_type" value="refund"
                                class="h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                @checked(old('request_type', 'refund') === 'refund')>
                            <p class="mt-3 font-semibold text-slate-900">Hoàn tiền</p>
                            <p class="mt-1 text-sm text-slate-500">Hoàn lại theo phương thức phù hợp sau khi kiểm duyệt.</p>
                        </label>

                        <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-orange-300">
                            <input type="radio" name="request_type" value="exchange"
                                class="h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                @checked(old('request_type') === 'exchange')>
                            <p class="mt-3 font-semibold text-slate-900">Đổi hàng</p>
                            <p class="mt-1 text-sm text-slate-500">Tạo xử lý đổi sang sản phẩm thay thế sau khi kiểm tra hàng.</p>
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">3. Lý do và minh chứng</h2>

                    <div class="mt-5">
                        <label for="reason" class="mb-2 block text-sm font-semibold text-slate-700">Lý do trả hàng</label>
                        <textarea id="reason" name="reason" rows="5"
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200"
                            placeholder="Ví dụ: sản phẩm lỗi, sai mẫu, không đúng mô tả...">{{ old('reason') }}</textarea>
                    </div>

                    <div class="mt-5">
                        <label for="evidences" class="mb-2 block text-sm font-semibold text-slate-700">
                            Hình ảnh / video minh chứng
                        </label>
                        <input id="evidences" name="evidences[]" type="file" multiple accept="image/*,video/*"
                            class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-orange-50 file:px-4 file:py-2 file:font-semibold file:text-orange-600 hover:file:bg-orange-100">
                        <p class="mt-2 text-sm text-slate-500">
                            Tối đa 5 file, mỗi file không quá 20MB. Hỗ trợ ảnh JPG/PNG/WEBP và video MP4/MOV/WEBM.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-orange-500 px-6 py-3 font-semibold text-white transition hover:bg-orange-600">
                        Gửi yêu cầu trả hàng
                    </button>

                    <a href="{{ route('orders.show', $order) }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 px-6 py-3 font-semibold text-slate-700 transition hover:border-slate-400">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
