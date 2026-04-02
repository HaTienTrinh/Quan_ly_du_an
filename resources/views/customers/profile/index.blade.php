@extends('customers.layouts.layout')

@section('title', 'Thông Tin Cá Nhân - TTM SHOP')

@section('content')
<div class="min-h-screen bg-white pt-12">
    <div class="max-w-5xl mx-auto px-4 md:px-12 pb-12">

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">
                Thông Tin Cá Nhân
            </h1>
            <p class="text-slate-500">
                Xem và quản lý thông tin tài khoản của bạn
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- LEFT -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Account -->
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">
                        Tài Khoản
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6 text-slate-700">

                        <div>
                            <p class="text-sm text-slate-500">Họ và tên</p>
                            <p class="font-semibold text-lg">{{ $user->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Email</p>
                            <p class="font-semibold text-lg">{{ $user->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Số điện thoại</p>
                            <p class="font-semibold text-lg">
                                {{ $user->phone ?: 'Chưa cập nhật' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500">Vai trò</p>
                            <p class="font-semibold text-lg text-orange-500">
                                Khách hàng
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Address -->
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

            <!-- RIGHT -->
            <div>
                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sticky top-24">

                    <h2 class="text-xl font-bold text-slate-900 mb-6">
                        Lối Tắt
                    </h2>

                    <div class="space-y-4">

                        <!-- Orders -->
                        <a href="{{ route('orders.index') }}"
                           class="block rounded-xl border border-slate-200 px-4 py-4 hover:border-orange-500 hover:bg-orange-50 transition">

                            <p class="font-semibold text-slate-900">
                                Đơn hàng của tôi
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                Theo dõi trạng thái đơn hàng
                            </p>
                        </a>

                        <!-- Cart -->
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