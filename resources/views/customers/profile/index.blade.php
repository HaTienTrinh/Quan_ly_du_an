@extends('customers.layouts.layout')

@section('title', 'Thông Tin Cá Nhân')

@section('content')
    @php
        $shouldOpenAddresses = $errors->addressStore->any() || $errors->addressUpdate->any();
    @endphp

    <div class="min-h-screen bg-slate-50 pt-12">
        <div class="mx-auto max-w-7xl px-4 pb-12 md:px-12">
            <div class="mb-10">
                <h1 class="mb-2 text-3xl font-extrabold text-slate-900 md:text-4xl">Thông tin cá nhân</h1>
                <p class="text-slate-500">
                    Cập nhật tài khoản, quản lý địa chỉ nhận hàng và dùng địa chỉ mặc định khi đặt hàng.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Cập nhật tài khoản</h2>
                            <p class="mt-1 text-sm text-slate-500">Thông tin cơ bản của bạn và ảnh đại diện.</p>
                        </div>

                        @if ($errors->profile->any())
                            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                <ul class="space-y-1">
                                    @foreach ($errors->profile->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Họ và tên <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Số điện thoại</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label for="birth_date" class="mb-1 block text-sm font-medium text-slate-700">Ngày sinh</label>
                                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Mật khẩu mới</label>
                                    <input type="password" name="password" id="password" autocomplete="new-password"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <p class="mt-1 text-xs text-slate-500">Để trống nếu không đổi. Tối thiểu 6 ký tự.</p>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Xác nhận mật khẩu</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>
                            </div>

                            <div>
                                <label for="avatar" class="mb-1 block text-sm font-medium text-slate-700">Ảnh đại diện</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                    class="w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-orange-50 file:px-4 file:py-2 file:font-semibold file:text-orange-700 hover:file:bg-orange-100">
                            </div>

                            @if ($user->avatar_url)
                                <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4">
                                    <img src="{{ $user->avatar_url }}" alt="" class="h-20 w-20 rounded-full border border-slate-200 object-cover">
                                    <div>
                                        <p class="font-semibold text-slate-900">Ảnh đại diện hiện tại</p>
                                        <p class="text-sm text-slate-500">Bạn có thể thay ảnh mới bất kỳ lúc nào.</p>
                                    </div>
                                </div>
                            @endif

                            <button type="submit"
                                class="rounded-xl bg-orange-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-orange-600">
                                Lưu thay đổi
                            </button>
                        </form>
                    </div>

                    <div id="addresses" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <details class="group" {{ $shouldOpenAddresses ? 'open' : '' }}>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 rounded-2xl bg-slate-50 px-5 py-4 transition hover:bg-slate-100">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">Địa chỉ nhận hàng</h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Xem danh sách địa chỉ đã lưu, chỉnh sửa từng địa chỉ hoặc thêm địa chỉ mới.
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-slate-700">
                                        {{ $addresses->count() }} địa chỉ
                                    </span>
                                    <span class="rounded-full bg-orange-500 px-4 py-2 text-sm font-semibold text-white">
                                        Xem
                                    </span>
                                </div>
                            </summary>

                            <div class="mt-6 space-y-6">
                                @if ($errors->addressStore->any() || $errors->addressUpdate->any())
                                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                        <ul class="space-y-1">
                                            @foreach ($errors->addressStore->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            @foreach ($errors->addressUpdate->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="space-y-4">
                                    @forelse ($addresses as $address)
                                        <div class="rounded-3xl border border-slate-200 p-5">
                                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                                <div class="space-y-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="text-lg font-bold text-slate-900">{{ $address->receiver_name }}</p>
                                                        @if ($address->is_default)
                                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                                Mặc định
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <p class="text-sm text-slate-600">{{ $address->phone }}</p>
                                                    <p class="text-sm text-slate-500">{{ $address->full_address }}</p>
                                                </div>

                                                <div class="flex flex-wrap gap-3">
                                                    @if (! $address->is_default)
                                                        <form action="{{ route('profile.addresses.default', $address) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                                                Đặt mặc định
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <details class="group">
                                                        <summary class="cursor-pointer list-none rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-100">
                                                            Chỉnh sửa
                                                        </summary>

                                                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                                                            <form action="{{ route('profile.addresses.update', $address) }}" method="POST" class="space-y-4">
                                                                @csrf
                                                                @method('PUT')

                                                                <div class="grid gap-4 md:grid-cols-2">
                                                                    <div>
                                                                        <label class="mb-1 block text-sm font-medium text-slate-700">Người nhận *</label>
                                                                        <input type="text" name="receiver_name" value="{{ $address->receiver_name }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                                    </div>

                                                                    <div>
                                                                        <label class="mb-1 block text-sm font-medium text-slate-700">Số điện thoại *</label>
                                                                        <input type="text" name="phone" value="{{ $address->phone }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                                    </div>
                                                                </div>

                                                                <div class="grid gap-4 md:grid-cols-3">
                                                                    <div>
                                                                        <label class="mb-1 block text-sm font-medium text-slate-700">Tỉnh / Thành phố *</label>
                                                                        <input type="text" name="province" value="{{ $address->province }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                                    </div>

                                                                    <div>
                                                                        <label class="mb-1 block text-sm font-medium text-slate-700">Quận / Huyện *</label>
                                                                        <input type="text" name="district" value="{{ $address->district }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                                    </div>

                                                                    <div>
                                                                        <label class="mb-1 block text-sm font-medium text-slate-700">Phường / Xã *</label>
                                                                        <input type="text" name="ward" value="{{ $address->ward }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <label class="mb-1 block text-sm font-medium text-slate-700">Địa chỉ chi tiết *</label>
                                                                    <input type="text" name="address_detail" value="{{ $address->address_detail }}"
                                                                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                                </div>

                                                                <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                                                                    <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500"
                                                                        {{ $address->is_default ? 'checked' : '' }}>
                                                                    Dùng làm địa chỉ mặc định
                                                                </label>

                                                                <button type="submit"
                                                                    class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600">
                                                                    Lưu địa chỉ
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </details>

                                                    <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-3xl border border-dashed border-slate-300 px-6 py-12 text-center text-slate-500">
                                            Bạn chưa có địa chỉ nhận hàng nào. Hãy thêm địa chỉ đầu tiên để checkout nhanh hơn.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="rounded-3xl border border-dashed border-orange-200 bg-orange-50/60 p-5">
                                    <h3 class="mb-4 text-lg font-bold text-slate-900">Thêm địa chỉ mới</h3>

                                    <form action="{{ route('profile.addresses.store') }}" method="POST" class="space-y-4">
                                        @csrf

                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Người nhận *</label>
                                                <input type="text" name="receiver_name" value="{{ old('receiver_name', $user->name) }}"
                                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Số điện thoại *</label>
                                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-3">
                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Tỉnh / Thành phố *</label>
                                                <input type="text" name="province" value="{{ old('province') }}"
                                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Quận / Huyện *</label>
                                                <input type="text" name="district" value="{{ old('district') }}"
                                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Phường / Xã *</label>
                                                <input type="text" name="ward" value="{{ old('ward') }}"
                                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-slate-700">Địa chỉ chi tiết *</label>
                                            <input type="text" name="address_detail" value="{{ old('address_detail') }}" placeholder="Số nhà, tên đường, tòa nhà..."
                                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        </div>

                                        <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                                            <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500"
                                                {{ old('is_default') ? 'checked' : '' }}>
                                            Đặt làm địa chỉ mặc định
                                        </label>

                                        <button type="submit"
                                            class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            Thêm địa chỉ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </details>
                    </div>
                </div>

                <div>
                    <div class="sticky top-24 space-y-6">
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-xl font-bold text-slate-900">Thông tin nhanh</h2>

                            <div class="space-y-4 text-sm text-slate-600">
                                <div>
                                    <p class="text-slate-400">Tài khoản</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p>{{ $user->email }}</p>
                                </div>

                                <div>
                                    <p class="text-slate-400">Số điện thoại</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ $user->phone ?: 'Chưa cập nhật' }}</p>
                                </div>

                                <div>
                                    <p class="text-slate-400">Địa chỉ mặc định</p>
                                    @if ($primaryAddress)
                                        <p class="mt-1 font-semibold text-slate-900">{{ $primaryAddress->receiver_name }}</p>
                                        <p>{{ $primaryAddress->phone }}</p>
                                        <p>{{ $primaryAddress->full_address }}</p>
                                    @else
                                        <p class="mt-1">Chưa có địa chỉ mặc định.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="mb-6 text-xl font-bold text-slate-900">Lối tắt</h2>

                            <div class="space-y-4">
                                <a href="{{ route('orders.index') }}"
                                    class="block rounded-xl border border-slate-200 px-4 py-4 transition hover:border-orange-500 hover:bg-orange-50">
                                    <p class="font-semibold text-slate-900">Đơn hàng của tôi</p>
                                    <p class="mt-1 text-sm text-slate-500">Theo dõi trạng thái đơn hàng</p>
                                </a>

                                <a href="{{ route('cart.index') }}"
                                    class="block rounded-xl border border-slate-200 px-4 py-4 transition hover:border-orange-500 hover:bg-orange-50">
                                    <p class="font-semibold text-slate-900">Giỏ hàng</p>
                                    <p class="mt-1 text-sm text-slate-500">Tiếp tục mua sắm</p>
                                </a>

                                <a href="{{ route('checkout') }}"
                                    class="block rounded-xl border border-slate-200 px-4 py-4 transition hover:border-orange-500 hover:bg-orange-50">
                                    <p class="font-semibold text-slate-900">Thanh toán</p>
                                    <p class="mt-1 text-sm text-slate-500">Dùng địa chỉ mặc định khi đặt hàng</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
