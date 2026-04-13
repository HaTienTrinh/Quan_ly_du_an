@extends('customers.layouts.layout')

@section('title', 'Thanh Toán - TTM SHOP')

@section('content')
    @php
        $addressOptions = $savedAddresses->map(fn ($address) => [
            'id' => $address->id,
            'receiver_name' => $address->receiver_name,
            'phone' => $address->phone,
            'province' => $address->province,
            'district' => $address->district,
            'ward' => $address->ward,
            'address_detail' => $address->address_detail,
            'full_address' => $address->full_address,
            'is_default' => $address->is_default,
        ])->values();

        $selectedAddressId = old('address_id', $selectedAddress?->id);
    @endphp

    <div class="min-h-screen bg-white pt-12">
        <div class="mx-auto max-w-7xl px-4 pb-12 md:px-12">
            <div class="mb-12">
                <h1 class="mb-4 text-4xl font-extrabold text-slate-900 md:text-5xl">Thanh Toán</h1>
                <p class="text-lg text-slate-500">Hoàn thành thông tin để đặt hàng.</p>
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

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <form action="{{ route('orders.store') }}" method="POST" class="space-y-8">
                        @csrf

                        @foreach ($selectedItemIds as $productId)
                            <input type="hidden" name="selected_items[]" value="{{ $productId }}">
                        @endforeach

                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">Thông tin người nhận</h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Chỉ hiển thị địa chỉ mặc định. Bấm vào để chọn địa chỉ khác nếu cần.
                                    </p>
                                </div>

                                <a href="{{ route('profile') }}#addresses"
                                    class="inline-flex rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-100">
                                    Quản lý địa chỉ
                                </a>
                            </div>

                            @if ($selectedAddress)
                                <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-4" {{ $errors->any() ? 'open' : '' }}>
                                    <summary class="flex cursor-pointer list-none items-start justify-between gap-4">
                                        <div class="space-y-2">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p id="selected_address_name" class="font-semibold text-slate-900">{{ old('receiver_name', $selectedAddress->receiver_name) }}</p>
                                                @if ($selectedAddress->is_default)
                                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                        Mặc định
                                                    </span>
                                                @endif
                                            </div>
                                            <p id="selected_address_phone" class="text-sm text-slate-600">{{ old('receiver_phone', $selectedAddress->phone) }}</p>
                                            <p id="selected_address_full" class="text-sm text-slate-500">
                                                {{ old('receiver_address_detail', $selectedAddress->address_detail) }},
                                                {{ old('receiver_ward', $selectedAddress->ward) }},
                                                {{ old('receiver_district', $selectedAddress->district) }},
                                                {{ old('receiver_province', $selectedAddress->province) }}
                                            </p>
                                        </div>

                                        <span class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm">
                                            Xem địa chỉ khác
                                        </span>
                                    </summary>

                                    <div class="mt-5 space-y-3 border-t border-slate-200 pt-5">
                                        @foreach ($savedAddresses as $address)
                                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-orange-300 hover:bg-orange-50/50">
                                                <input type="radio" name="address_id" value="{{ $address->id }}"
                                                    class="mt-1 h-4 w-4 border-slate-300 text-orange-500 focus:ring-orange-500"
                                                    {{ (string) $selectedAddressId === (string) $address->id ? 'checked' : '' }}>

                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="font-semibold text-slate-900">{{ $address->receiver_name }}</p>
                                                        @if ($address->is_default)
                                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                                Mặc định
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-sm text-slate-600">{{ $address->phone }}</p>
                                                    <p class="mt-1 text-sm text-slate-500">{{ $address->full_address }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-500">
                                    Bạn chưa có địa chỉ mặc định. Hãy vào trang profile để thêm địa chỉ nhận hàng trước khi thanh toán.
                                </div>
                            @endif

                            <input type="hidden" name="receiver_name" id="receiver_name"
                                value="{{ old('receiver_name', $selectedAddress->receiver_name ?? $user->name ?? '') }}">
                            <input type="hidden" name="receiver_phone" id="receiver_phone"
                                value="{{ old('receiver_phone', $selectedAddress->phone ?? $user->phone ?? '') }}">
                            <input type="hidden" name="receiver_province" id="receiver_province"
                                value="{{ old('receiver_province', $selectedAddress->province ?? '') }}">
                            <input type="hidden" name="receiver_district" id="receiver_district"
                                value="{{ old('receiver_district', $selectedAddress->district ?? '') }}">
                            <input type="hidden" name="receiver_ward" id="receiver_ward"
                                value="{{ old('receiver_ward', $selectedAddress->ward ?? '') }}">
                            <input type="hidden" name="receiver_address_detail" id="receiver_address_detail"
                                value="{{ old('receiver_address_detail', $selectedAddress->address_detail ?? '') }}">
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold text-slate-900">Phương thức thanh toán</h2>

                            <div class="space-y-3">
                                <label class="flex cursor-pointer items-center rounded-lg border p-4 hover:border-orange-500">
                                    <input type="radio" name="payment_method" value="cash" class="accent-orange-500"
                                        {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }}>
                                    <span class="ml-3 text-slate-700">Thanh toán khi nhận hàng</span>
                                </label>

                                <label class="flex cursor-pointer items-center rounded-lg border p-4 hover:border-orange-500">
                                    <input type="radio" name="payment_method" value="credit_card" class="accent-orange-500"
                                        {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                    <span class="ml-3 text-slate-700">Thẻ tín dụng</span>
                                </label>

                                <label class="flex cursor-pointer items-center rounded-lg border p-4 hover:border-orange-500">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="accent-orange-500"
                                        {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                    <span class="ml-3 text-slate-700">Chuyển khoản</span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-xl font-bold text-slate-900">Ghi chú</h2>
                            <textarea name="note" rows="4" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-black"
                                placeholder="Ghi chú thêm...">{{ old('note') }}</textarea>
                        </div>

                        <div class="flex gap-4">
                            <a href="{{ route('cart.index') }}"
                                class="rounded-lg border border-slate-300 px-6 py-3 text-slate-700 hover:border-orange-500">
                                ← Giỏ hàng
                            </a>

                            <button type="submit"
                                class="flex-1 rounded-lg bg-orange-500 px-6 py-3 font-semibold text-white hover:bg-orange-600">
                                Đặt hàng →
                            </button>
                        </div>
                    </form>
                </div>

                <div>
                    <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-6 text-xl font-bold text-slate-900">Đơn hàng</h2>

                        @foreach ($cart as $item)
                            <div class="mb-3 flex justify-between text-sm">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $item['product_name'] }}</p>
                                    @if (! empty($item['product_size']))
                                        <p class="text-slate-500">Size: <span class="font-semibold text-slate-700">{{ $item['product_size'] }}</span></p>
                                    @endif
                                    <p class="text-slate-500">x{{ $item['quantity'] }}</p>
                                </div>
                                <span class="font-semibold">
                                    {{ number_format($item['subtotal'], 0, ',', '.') }} ₫
                                </span>
                            </div>
                        @endforeach

                        <div class="mt-4 space-y-2 border-t pt-4 text-slate-600">
                            <div class="flex justify-between">
                                <span>Tạm tính</span>
                                <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                            </div>

                            <div class="flex justify-between">
                                <span>Ship</span>
                                <span>{{ number_format($shippingFee, 0, ',', '.') }} ₫</span>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between text-xl font-bold text-slate-900">
                            <span>Tổng</span>
                            <span class="text-orange-500">
                                {{ number_format($totalAmount, 0, ',', '.') }} ₫
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addressMap = @json($addressOptions);
            const addressLookup = Object.fromEntries(addressMap.map(address => [String(address.id), address]));
            const radioButtons = document.querySelectorAll('input[name="address_id"]');

            const fields = {
                receiver_name: document.getElementById('receiver_name'),
                receiver_phone: document.getElementById('receiver_phone'),
                receiver_province: document.getElementById('receiver_province'),
                receiver_district: document.getElementById('receiver_district'),
                receiver_ward: document.getElementById('receiver_ward'),
                receiver_address_detail: document.getElementById('receiver_address_detail'),
            };

            const summary = {
                name: document.getElementById('selected_address_name'),
                phone: document.getElementById('selected_address_phone'),
                full: document.getElementById('selected_address_full'),
            };

            function applyAddress(addressId) {
                const address = addressLookup[String(addressId)];

                if (! address) {
                    return;
                }

                fields.receiver_name.value = address.receiver_name ?? '';
                fields.receiver_phone.value = address.phone ?? '';
                fields.receiver_province.value = address.province ?? '';
                fields.receiver_district.value = address.district ?? '';
                fields.receiver_ward.value = address.ward ?? '';
                fields.receiver_address_detail.value = address.address_detail ?? '';

                if (summary.name) {
                    summary.name.textContent = address.receiver_name ?? '';
                }

                if (summary.phone) {
                    summary.phone.textContent = address.phone ?? '';
                }

                if (summary.full) {
                    summary.full.textContent = address.full_address ?? '';
                }
            }

            radioButtons.forEach((radioButton) => {
                radioButton.addEventListener('change', function () {
                    applyAddress(this.value);
                });
            });
        });
    </script>
@endsection
