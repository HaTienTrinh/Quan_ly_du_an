@extends('customers.layouts.layout')

@section('title', 'Thanh Toán - TTM SHOP')

@section('content')
    <div class="min-h-screen bg-white pt-12">
        <div class="max-w-7xl mx-auto px-4 md:px-12 pb-12">

            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-slate-900">
                    Thanh Toán
                </h1>
                <p class="text-slate-500 text-lg">
                    Hoàn thành thông tin để đặt hàng
                </p>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-red-600 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- FORM -->
                <div class="lg:col-span-2">
                    <form action="{{ route('orders.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Receiver -->
                        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-6 text-slate-900">Thông tin người nhận</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Họ và tên *</label>
                                    <input type="text" name="receiver_name"
                                        value="{{ old('receiver_name', $latestAddress->receiver_name ?? $user->name ?? '') }}"
                                        class="w-full mt-2 px-4 py-3 border border-slate-300 rounded-lg focus:border-orange-500 focus:outline-none">
                                    @error('receiver_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-slate-700">SĐT *</label>
                                    <input type="text" name="receiver_phone"
                                        value="{{ old('receiver_phone', $latestAddress->phone ?? $user->phone ?? '') }}"
                                        class="w-full mt-2 px-4 py-3 border border-slate-300 rounded-lg focus:border-orange-500 focus:outline-none">
                                    @error('receiver_phone')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <input type="text" name="receiver_province" placeholder="Tỉnh"
                                    value="{{ old('receiver_province', $latestAddress->province ?? '') }}"
                                    class="px-4 py-3 border border-slate-300 rounded-lg">

                                <input type="text" name="receiver_district" placeholder="Quận"
                                    value="{{ old('receiver_district', $latestAddress->district ?? '') }}"
                                    class="px-4 py-3 border border-slate-300 rounded-lg">

                                <input type="text" name="receiver_ward" placeholder="Phường"
                                    value="{{ old('receiver_ward', $latestAddress->ward ?? '') }}"
                                    class="px-4 py-3 border border-slate-300 rounded-lg">
                            </div>

                            <div class="mt-4">
                                <input type="text" name="receiver_address_detail" placeholder="Số nhà, tên đường..."
                                    value="{{ old('receiver_address_detail', $latestAddress->address_detail ?? '') }}"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg">
                            </div>
                        </div>

                        <!-- Payment -->
                        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-6 text-slate-900">Phương thức thanh toán</h2>

                            <div class="space-y-3">
                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500">
                                    <input type="radio" name="payment_method" value="cash" class="accent-orange-500"
                                        {{ old('payment_method', 'cash') == 'cash' ? 'checked' : '' }}>
                                    <span class="ml-3 text-slate-700">Thanh toán khi nhận hàng</span>
                                </label>

                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500">
                                    <input type="radio" name="payment_method" value="credit_card"
                                        class="accent-orange-500"
                                        {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                    <span class="ml-3 text-slate-700">Thẻ tín dụng</span>
                                </label>

                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-orange-500">
                                    <input type="radio" name="payment_method" value="bank_transfer"
                                        class="accent-orange-500"
                                        {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                    <span class="ml-3 text-slate-700">Chuyển khoản</span>
                                </label>

                            </div>
                        </div>

                        <!-- Note -->
                        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-4 text-slate-900">Ghi chú</h2>
                            <textarea name="note" rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg"
                                placeholder="Ghi chú thêm...">{{ old('note') }}</textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4">
                            <a href="{{ route('cart.index') }}"
                                class="px-6 py-3 border border-slate-300 rounded-lg text-slate-700 hover:border-orange-500">
                                ← Giỏ hàng
                            </a>

                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg">
                                Đặt hàng →
                            </button>
                        </div>

                    </form>
                </div>

                <!-- SUMMARY -->
                <div>
                    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-6 text-slate-900">Đơn hàng</h2>

                        @foreach ($cart as $item)
                            <div class="flex justify-between text-sm mb-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $item['product_name'] }}</p>
                                    <p class="text-slate-500">x{{ $item['quantity'] }}</p>
                                </div>
                                <span class="font-semibold">
                                    {{ number_format($item['subtotal'], 0, ',', '.') }} ₫
                                </span>
                            </div>
                        @endforeach

                        <div class="border-t pt-4 mt-4 space-y-2 text-slate-600">
                            <div class="flex justify-between">
                                <span>Tạm tính</span>
                                <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                            </div>

                            <div class="flex justify-between">
                                <span>Ship</span>
                                <span>{{ number_format($shippingFee, 0, ',', '.') }} ₫</span>
                            </div>
                        </div>

                        <div class="flex justify-between text-xl font-bold mt-4 text-slate-900">
                            <span>Tổng</span>
                            <span class="text-orange-500">
                                {{ number_format($totalAmount, 0, ',', '.') }} ₫
                            </span>
                        </div>

                        <div class="mt-4 text-xs text-slate-400">
                            💡 Bạn đồng ý điều khoản khi đặt hàng
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
