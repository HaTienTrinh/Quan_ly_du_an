@extends('customers.layouts.layout')

@section('title', 'Thông Tin Cá Nhân - TTM SHOP')

@section('content')
<div class="min-h-screen bg-white pt-12">
    <div class="max-w-5xl mx-auto px-4 md:px-12 pb-12">

        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">
                Thông Tin Cá Nhân
            </h1>
            <p class="text-slate-500">
                Xem và cập nhật thông tin tài khoản của bạn
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">
                        Cập nhật tài khoản
                    </h2>

                    <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 @error('name') border-red-400 @enderror">
                                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 @error('email') border-red-400 @enderror">
                                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Số điện thoại</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 @error('phone') border-red-400 @enderror">
                                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-1">Ngày sinh</label>
                                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 @error('birth_date') border-red-400 @enderror">
                                @error('birth_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Mật khẩu mới</label>
                                <input type="password" name="password" id="password" autocomplete="new-password"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 @error('password') border-red-400 @enderror">
                                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                <p class="mt-1 text-xs text-slate-500">Để trống nếu không đổi. Tối thiểu 6 ký tự.</p>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        <div>
                            <label for="avatar" class="block text-sm font-medium text-slate-700 mb-1">Ảnh đại diện</label>
                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                   class="w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-orange-50 file:px-4 file:py-2 file:font-semibold file:text-orange-700 hover:file:bg-orange-100 @error('avatar') border border-red-400 rounded-xl @enderror">
                            @error('avatar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        @if($user->avatar_url)
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->avatar_url }}" alt="" class="h-20 w-20 rounded-full border border-slate-200 object-cover">
                                <p class="text-sm text-slate-500">Ảnh hiện tại</p>
                            </div>
                        @endif

                        <button type="submit"
                                class="rounded-xl bg-orange-500 px-6 py-3 text-sm font-bold text-black transition hover:bg-orange-400">
                            Lưu thay đổi
                        </button>
                    </form>
                </div>

                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">
                        Địa Chỉ Giao Hàng
                    </h2>

                    @if ($primaryAddress)
                        <div class="space-y-3 text-slate-700">

                            <div>
                                <p class="text-sm text-slate-500">Người nhận</p>
                                <p class="font-semibold">{{ $primaryAddress->receiver_name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-slate-500">Số điện thoại</p>
                                <p class="font-semibold">{{ $primaryAddress->phone }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-slate-500">Địa chỉ</p>
                                <p class="font-semibold">{{ $primaryAddress->full_address }}</p>
                            </div>

                        </div>
                    @else
                        <p class="text-slate-500">
                            Bạn chưa có địa chỉ giao hàng nào.
                        </p>
                    @endif
                </div>

            </div>

            <div>
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sticky top-24">

                    <h2 class="text-xl font-bold text-slate-900 mb-6">
                        Lối Tắt
                    </h2>

                    <div class="space-y-4">

                        <a href="{{ route('orders.index') }}"
                           class="block rounded-xl border border-slate-200 px-4 py-4 hover:border-orange-500 hover:bg-orange-50 transition">

                            <p class="font-semibold text-slate-900">
                                Đơn hàng của tôi
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                Theo dõi trạng thái đơn hàng
                            </p>
                        </a>

                        <a href="{{ route('cart.index') }}"
                           class="block rounded-xl border border-slate-200 px-4 py-4 hover:border-orange-500 hover:bg-orange-50 transition">

                            <p class="font-semibold text-slate-900">
                                Giỏ hàng
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                Tiếp tục mua sắm
                            </p>
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
