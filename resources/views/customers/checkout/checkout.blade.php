@extends('customers.layouts.layout')

@section('title', 'Thanh Toán - TTM SHOP')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#0f1419] to-[#05070a] pt-12">
        <div class="max-w-7xl mx-auto px-4 md:px-12 pb-12">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tighter mb-4">Thanh Toán</h1>
                <p class="text-gray-400 text-lg">Hoàn thành thông tin để đặt hàng</p>
            </div>

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="mb-8 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                    <ul class="text-red-200 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <form action="{{ route('orders.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Thông tin người nhận -->
                        <div class="glass rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-6">Thông tin người nhận</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Họ và tên *</label>
                                    <input type="text" name="receiver_name"
                                        value="{{ old('receiver_name', $user->name) }}"
                                        class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition"
                                        required>
                                    @error('receiver_name')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Số điện thoại *</label>
                                    <input type="text" name="receiver_phone"
                                        value="{{ old('receiver_phone', $latestAddress->phone ?? ($user->phone ?? '')) }}"
                                        class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition"
                                        required>
                                    @error('receiver_phone')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Tỉnh/Thành phố *</label>
                                    <input type="text" name="receiver_province"
                                        value="{{ old('receiver_province', $latestAddress->province ?? '') }}"
                                        class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition"
                                        required>
                                    @error('receiver_province')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Quận/Huyện *</label>
                                    <input type="text" name="receiver_district"
                                        value="{{ old('receiver_district', $latestAddress->district ?? '') }}"
                                        class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition"
                                        required>
                                    @error('receiver_district')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Phường/Xã *</label>
                                    <input type="text" name="receiver_ward"
                                        value="{{ old('receiver_ward', $latestAddress->ward ?? '') }}"
                                        class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition"
                                        required>
                                    @error('receiver_ward')
                                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Địa chỉ chi tiết *</label>
                                <input type="text" name="receiver_address_detail"
                                    value="{{ old('receiver_address_detail', $latestAddress->address_detail ?? '') }}"
                                    placeholder="Số nhà, tên đường..."
                                    class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition"
                                    required>
                                @error('receiver_address_detail')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="glass rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-6">Phương thức thanh toán</h2>

                            <div class="space-y-3">
                                <label
                                    class="flex items-center p-4 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-orange-500 transition">
                                    <input type="radio" name="payment_method" value="cash" checked
                                        class="w-4 h-4 accent-orange-500">
                                    <span class="ml-4">
                                        <span class="block font-semibold">Thanh toán khi nhận hàng (COD)</span>
                                        <span class="text-sm text-gray-400">Thanh toán bằng tiền mặt khi nhận hàng</span>
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-4 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-orange-500 transition">
                                    <input type="radio" name="payment_method" value="credit_card"
                                        class="w-4 h-4 accent-orange-500">
                                    <span class="ml-4">
                                        <span class="block font-semibold">Thẻ tín dụng</span>
                                        <span class="text-sm text-gray-400">Visa, Mastercard, JCB...</span>
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-4 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-orange-500 transition">
                                    <input type="radio" name="payment_method" value="bank_transfer"
                                        class="w-4 h-4 accent-orange-500">
                                    <span class="ml-4">
                                        <span class="block font-semibold">Chuyển khoản ngân hàng</span>
                                        <span class="text-sm text-gray-400">Chuyển tiền vào tài khoản ngân hàng</span>
                                    </span>
                                </label>
                            </div>

                            @error('payment_method')
                                <p class="text-red-400 text-sm mt-3">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ghi chú -->
                        <div class="glass rounded-2xl p-6">
                            <h2 class="text-xl font-bold mb-6">Ghi chú thêm</h2>

                            <textarea name="note" rows="4" placeholder="Nhập ghi chú cho đơn hàng (tùy chọn)..."
                                class="w-full px-4 py-3 bg-white/5 border border-gray-600 rounded-lg focus:border-orange-500 focus:outline-none transition resize-none">{{ old('note') }}</textarea>

                            @error('note')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4">
                            <a href="{{ route('cart.index') }}"
                                class="px-6 py-3 border border-gray-600 rounded-lg font-semibold hover:border-orange-500 transition">
                                ← Quay lại giỏ hàng
                            </a>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                                Đặt hàng →
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="glass rounded-2xl p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-6">Đơn hàng của bạn</h2>

                        <div class="space-y-4 mb-6 pb-6 border-b border-white/10">
                            @foreach ($cart as $item)
                                <div class="flex justify-between text-sm">
                                    <div class="text-gray-300">
                                        <p class="font-medium">{{ $item['product_name'] }}</p>
                                        <p class="text-xs text-gray-500">x{{ $item['quantity'] }}</p>
                                    </div>
                                    <span class="font-semibold">{{ number_format($item['subtotal'], 0, ',', '.') }}
                                        ₫</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 mb-6 pb-6 border-b border-white/10">
                            <div class="flex justify-between text-gray-300">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Phí vận chuyển:</span>
                                <span>Miễn phí</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Giảm giá:</span>
                                <span>0 ₫</span>
                            </div>
                        </div>

                        <div class="flex justify-between text-xl font-bold">
                            <span>Tổng cộng:</span>
                            <span class="text-orange-500">{{ number_format($total, 0, ',', '.') }} ₫</span>
                        </div>

                        <div class="mt-6 p-4 bg-white/5 rounded-lg border border-white/10">
                            <p class="text-xs text-gray-400">
                                💡 Bằng việc đặt hàng, bạn đã đồng ý với các điều khoản và điều kiện của chúng tôi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
