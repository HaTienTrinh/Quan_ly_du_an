@extends('customers.layouts.layout')

@section('title', 'Giỏ Hàng - TT SHOP')

@section('content')
    @php
        $cartCollection = collect($cart);
    @endphp

    <div class="min-h-screen bg-white pt-12">
        <div class="mx-auto max-w-7xl px-4 md:px-12">
            <div class="mb-12">
                <h1 class="mb-4 text-4xl font-extrabold tracking-tighter text-slate-900 md:text-5xl">
                    Giỏ Hàng Của Bạn
                </h1>
                <p class="text-lg text-slate-500">
                    Kiểm tra, quản lý và chọn một hoặc nhiều sản phẩm trước khi thanh toán.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-8 rounded-lg border border-red-200 bg-red-50 p-4">
                    <ul class="text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-8 rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-sm text-green-600">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-8 rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            @if (empty($cart))
                <div class="py-24 text-center">
                    <h2 class="mb-2 text-2xl font-bold text-slate-900">Giỏ hàng trống</h2>
                    <p class="mb-8 text-slate-500">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                    <a href="{{ route('products') }}"
                        class="inline-block rounded-lg bg-orange-500 px-8 py-3 font-semibold text-white transition hover:bg-orange-600">
                        Tiếp tục mua sắm
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="flex flex-col gap-4 border-b border-slate-200 p-6 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">
                                        Sản phẩm ({{ $itemCount }})
                                    </h2>
                                    <p class="mt-1 text-sm text-slate-500">Chọn sản phẩm bạn muốn thanh toán ở đợt này.</p>
                                </div>

                                <label class="inline-flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" id="select-all-items" class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500" checked>
                                    Chọn tất cả
                                </label>
                            </div>

                            <div class="divide-y divide-slate-200">
                                @foreach ($cart as $item)
                                    <div class="cart-item p-6 transition hover:bg-slate-50"
                                        data-product-id="{{ $item['item_key'] ?? $item['product_id'] }}"
                                        data-subtotal="{{ (float) $item['subtotal'] }}"
                                        data-quantity="{{ (int) $item['quantity'] }}">
                                        <div class="flex gap-4">
                                            <div class="pt-8">
                                                <input type="checkbox"
                                                    class="cart-item-checkbox h-5 w-5 rounded border-slate-300 text-orange-500 focus:ring-orange-500"
                                                    value="{{ $item['item_key'] ?? $item['product_id'] }}" checked>
                                            </div>

                                            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg">
                                                @if ($item['product_thumbnail'])
                                                    <img src="{{ asset('storage/' . $item['product_thumbnail']) }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center bg-slate-100 text-slate-400">
                                                        Không có hình
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-1">
                                                <h3 class="mb-1 text-lg font-semibold text-slate-900">
                                                    {{ $item['product_name'] }}
                                                </h3>

                                                @if (! empty($item['product_size_name']))
                                                    <p class="mb-2 text-sm text-slate-500">
                                                        Size: {{ $item['product_size_name'] }}
                                                    </p>
                                                @endif

                                                <p class="mb-3 font-bold text-orange-500">
                                                    {{ number_format($item['unit_price'], 0, ',', '.') }} ₫
                                                </p>

                                                <form action="{{ route('cart.update', $item['item_key'] ?? $item['product_id']) }}"
                                                    method="POST"
                                                    class="mb-3 flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="flex items-center overflow-hidden rounded-lg border border-slate-300">
                                                        <button type="button"
                                                            onclick="var q=this.closest('form').querySelector('input[name=quantity]'); if(q.value>1)q.stepDown();"
                                                            class="px-3 py-2 text-gray-600 hover:bg-slate-100">-</button>

                                                        <input type="number" name="quantity"
                                                            value="{{ $item['quantity'] }}" min="1"
                                                            class="w-12 bg-transparent text-center text-gray-600">

                                                        <button type="button"
                                                            onclick="var q=this.closest('form').querySelector('input[name=quantity]'); q.stepUp();"
                                                            class="px-3 py-2 text-gray-600 hover:bg-slate-100">+</button>
                                                    </div>

                                                    <button type="submit"
                                                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm text-white transition hover:bg-orange-600">
                                                        Cập nhật
                                                    </button>
                                                </form>
                                            </div>

                                            <div class="text-right">
                                                <div class="mb-3 text-lg font-bold text-slate-900">
                                                    {{ number_format($item['subtotal'], 0, ',', '.') }} ₫
                                                </div>

                                                <form action="{{ route('cart.remove', $item['item_key'] ?? $item['product_id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg bg-red-50 px-4 py-2 text-sm text-red-600 transition hover:bg-red-100">
                                                        Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('products') }}"
                                class="inline-block px-6 py-2 font-medium text-orange-500 transition hover:text-orange-400">
                                ← Tiếp tục mua sắm
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold text-slate-900">Chi tiết đơn hàng</h2>

                            <div class="mb-6 rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-sm text-slate-600">
                                    <span>Đã chọn:</span>
                                    <span id="selected-item-count" class="font-semibold text-slate-900">{{ $itemCount }}</span>
                                </div>
                            </div>

                            <div class="mb-6 space-y-3 border-b border-slate-200 pb-6">
                                <div class="flex justify-between text-slate-600">
                                    <span>Tạm tính:</span>
                                    <span id="selected-subtotal">{{ number_format($total, 0, ',', '.') }} ₫</span>
                                </div>

                                <div class="flex justify-between text-slate-600">
                                    <span>Phí vận chuyển:</span>
                                    <span class="text-orange-500">Tính ở bước thanh toán</span>
                                </div>

                                <div class="flex justify-between text-slate-600">
                                    <span>Giảm giá:</span>
                                    <span>0 ₫</span>
                                </div>
                            </div>

                            <div class="mb-6 flex justify-between text-xl font-bold text-slate-900">
                                <span>Tổng cộng:</span>
                                <span id="selected-total" class="text-orange-500">
                                    {{ number_format($total, 0, ',', '.') }} ₫
                                </span>
                            </div>

                            @auth
                                <form action="{{ route('checkout') }}" method="GET" id="checkout-selection-form">
                                    <div id="selected-items-hidden-inputs"></div>

                                    <button type="submit" id="checkout-button"
                                        class="mb-3 block w-full rounded-lg bg-orange-500 px-6 py-3 text-center font-semibold text-white transition hover:bg-orange-600">
                                        Tiến hành thanh toán
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}"
                                    class="mb-3 block w-full rounded-lg bg-orange-500 px-6 py-3 text-center font-semibold text-white hover:bg-orange-600">
                                    Đăng nhập để thanh toán
                                </a>
                            @endauth

                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-lg bg-red-50 px-6 py-3 font-semibold text-red-600 transition hover:bg-red-100">
                                    Xóa giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemRows = Array.from(document.querySelectorAll('.cart-item'));
            const itemCheckboxes = Array.from(document.querySelectorAll('.cart-item-checkbox'));
            const selectAllCheckbox = document.getElementById('select-all-items');
            const selectedItemCount = document.getElementById('selected-item-count');
            const selectedSubtotal = document.getElementById('selected-subtotal');
            const selectedTotal = document.getElementById('selected-total');
            const hiddenInputsContainer = document.getElementById('selected-items-hidden-inputs');
            const checkoutButton = document.getElementById('checkout-button');

            function formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
            }

            function syncSummary() {
                let total = 0;
                let quantity = 0;
                const selectedIds = [];

                itemRows.forEach((row) => {
                    const checkbox = row.querySelector('.cart-item-checkbox');

                    if (!checkbox.checked) {
                        return;
                    }

                    total += Number(row.dataset.subtotal || 0);
                    quantity += Number(row.dataset.quantity || 0);
                    selectedIds.push(checkbox.value);
                });

                if (selectedItemCount) {
                    selectedItemCount.textContent = quantity;
                }

                if (selectedSubtotal) {
                    selectedSubtotal.textContent = formatCurrency(total);
                }

                if (selectedTotal) {
                    selectedTotal.textContent = formatCurrency(total);
                }

                if (hiddenInputsContainer) {
                    hiddenInputsContainer.innerHTML = selectedIds
                        .map((id) => `<input type="hidden" name="selected_items[]" value="${id}">`)
                        .join('');
                }

                if (checkoutButton) {
                    checkoutButton.disabled = selectedIds.length === 0;
                    checkoutButton.classList.toggle('opacity-50', selectedIds.length === 0);
                    checkoutButton.classList.toggle('cursor-not-allowed', selectedIds.length === 0);
                }

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = itemCheckboxes.length > 0 && itemCheckboxes.every((checkbox) => checkbox.checked);
                }
            }

            itemCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', syncSummary);
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    itemCheckboxes.forEach((checkbox) => {
                        checkbox.checked = this.checked;
                    });

                    syncSummary();
                });
            }

            syncSummary();
        });
    </script>
@endsection
