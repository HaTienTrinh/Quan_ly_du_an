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
                    Chọn đúng sản phẩm cần xử lý, cách xử lý mong muốn và phương án gửi hàng về để cửa hàng kiểm duyệt nhanh hơn.
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
                                            Chưa có ảnh
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900">{{ $item->product_name }}</p>
                                    @if ($item->product_size)
                                        <p class="mt-1 text-sm text-slate-500">Size: <span class="font-semibold text-slate-700">{{ $item->product_size }}</span></p>
                                    @endif
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
                            <p class="mt-1 text-sm text-slate-500">Hoàn lại tiền sau khi cửa hàng kiểm tra và xác nhận yêu cầu hợp lệ.</p>
                        </label>

                        <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-orange-300">
                            <input type="radio" name="request_type" value="exchange"
                                class="h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                @checked(old('request_type') === 'exchange')>
                            <p class="mt-3 font-semibold text-slate-900">Đổi hàng</p>
                            <p class="mt-1 text-sm text-slate-500">Cửa hàng sẽ gửi lại hàng thay thế sau khi yêu cầu đổi hàng hoàn tất.</p>
                        </label>
                    </div>

                    {{-- Phần chọn màu/size — chỉ hiện khi chọn exchange VÀ sản phẩm có biến thể --}}
                    <div id="exchangeColorSection" class="mt-5 hidden">
                        <p class="mb-3 font-semibold text-slate-700">Chọn size muốn đổi sang</p>
                        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                            @foreach ($eligibleItems as $item)
                                @php
                                    $sizeGroups = $item->product?->colors->filter(fn($c) => $c->size)->groupBy('size') ?? collect();
                                @endphp
                                @if ($sizeGroups->isNotEmpty())
                                    <div class="color-options" data-item-id="{{ $item->id }}" style="display:none">
                                        @foreach ($sizeGroups as $size => $colorsInSize)
                                            <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 p-3 transition hover:border-orange-300">
                                                <input type="radio" name="exchange_color_id"
                                                    value="{{ $colorsInSize->first()->id }}"
                                                    class="color-radio sr-only"
                                                    disabled
                                                    @checked((int) old('exchange_color_id') === $colorsInSize->first()->id)>
                                                <span class="text-sm font-bold text-slate-800 peer-checked:text-orange-500">
                                                    {{ $size }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="color-options no-variants" data-item-id="{{ $item->id }}"></div>
                                @endif
                            @endforeach
                        </div>
                        <p id="noVariantMsg" class="hidden mt-2 text-sm text-slate-500 italic">
                            Sản phẩm này không có biến thể — sẽ đổi đúng loại đã mua.
                        </p>
                        @error('exchange_color_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <script>
                    function updateColorSection() {
                        const isExchange = document.querySelector('input[name="request_type"]:checked')?.value === 'exchange';
                        const selectedItemId = document.querySelector('input[name="order_item_id"]:checked')?.value;
                        const section = document.getElementById('exchangeColorSection');
                        const noVariantMsg = document.getElementById('noVariantMsg');

                        // Disable + ẩn tất cả trước
                        document.querySelectorAll('.color-radio').forEach(el => el.disabled = true);
                        document.querySelectorAll('.color-options').forEach(el => el.style.display = 'none');
                        noVariantMsg.classList.add('hidden');

                        if (!isExchange || !selectedItemId) {
                            section.classList.add('hidden');
                            return;
                        }

                        const colorDiv = document.querySelector(`.color-options[data-item-id="${selectedItemId}"]`);

                        if (!colorDiv) {
                            section.classList.add('hidden');
                            return;
                        }

                        section.classList.remove('hidden');

                        if (colorDiv.classList.contains('no-variants')) {
                            // Sản phẩm không có biến thể
                            noVariantMsg.classList.remove('hidden');
                        } else {
                            colorDiv.style.display = 'grid';
                            colorDiv.style.gridTemplateColumns = 'repeat(auto-fill, minmax(160px, 1fr))';
                            colorDiv.style.gap = '0.75rem';
                            colorDiv.querySelectorAll('.color-radio').forEach(el => el.disabled = false);
                        }
                    }

                    document.querySelectorAll('input[name="request_type"], input[name="order_item_id"]')
                        .forEach(el => el.addEventListener('change', updateColorSection));

                    updateColorSection();
                </script>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">3. Phương án vận chuyển về</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-orange-300">
                            <input type="radio" name="logistics_method" value="customer_ship"
                                class="h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                @checked(old('logistics_method', 'customer_ship') === 'customer_ship')>
                            <p class="mt-3 font-semibold text-slate-900">Khách tự gửi hàng về</p>
                            <p class="mt-1 text-sm text-slate-500">Bạn chủ động gửi hàng về cửa hàng theo hướng dẫn sau khi yêu cầu được duyệt.</p>
                        </label>

                        <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-orange-300">
                            <input type="radio" name="logistics_method" value="system_pickup"
                                class="h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                @checked(old('logistics_method') === 'system_pickup')>
                            <p class="mt-3 font-semibold text-slate-900">Cửa hàng đến lấy hàng</p>
                            <p class="mt-1 text-sm text-slate-500">Cửa hàng sẽ liên hệ để hẹn thời gian nhận lại sản phẩm từ bạn.</p>
                        </label>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900">4. Lý do và minh chứng</h2>

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
