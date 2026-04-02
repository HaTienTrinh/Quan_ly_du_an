@extends('customers.layouts.layout')

@section('title', 'Cửa hàng - Luna Steps')

@section('content')
    <style>
        .filter-group input[type="checkbox"]:checked+label {
            color: #f97316;
            font-weight: bold;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid #f1f5f9;
        }
    </style>

    <main class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-12">

            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
                <div>
                    <h2 class="text-4xl font-black text-slate-900">Tất cả sản phẩm</h2>
                    <p class="text-slate-400 text-sm mt-1">Tìm thấy {{ count($products) }} sản phẩm</p>
                </div>

                <form action="{{ route('products') }}" method="GET" class="relative w-full md:w-96">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm mẫu giày..."
                        class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:border-orange-500 focus:bg-white transition-all shadow-sm">
                    <button type="submit" class="absolute right-4 top-3.5">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            @if (session('success'))
                <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-12">

                <aside class="w-full lg:w-64 space-y-10">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-widest text-slate-900 mb-6 border-b pb-2">Danh mục
                        </h4>
                        <div class="space-y-4 filter-group">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="cat1" class="w-4 h-4 accent-orange-500">
                                <label for="cat1"
                                    class="text-sm text-slate-600 cursor-pointer hover:text-orange-500 transition">Running</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="cat2" class="w-4 h-4 accent-orange-500">
                                <label for="cat2"
                                    class="text-sm text-slate-600 cursor-pointer hover:text-orange-500 transition">Streetwear</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="cat3" class="w-4 h-4 accent-orange-500">
                                <label for="cat3"
                                    class="text-sm text-slate-600 cursor-pointer hover:text-orange-500 transition">Classic</label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <!-- Title -->
                        <h4 class="text-sm font-black uppercase tracking-widest text-slate-700 flex items-center gap-2">
                            <span class="w-2 h-2 bg-orange-400 rounded-full"></span>
                            Khoảng giá
                        </h4>

                        <!-- Select -->
                        <div class="relative">
                            <select
                                class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 
        focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition">

                                <option>Tất cả giá</option>
                                <option>Dưới 1.000.000đ</option>
                                <option>1.000.000đ - 2.000.000đ</option>
                                <option>Trên 2.000.000đ</option>
                            </select>

                            <!-- Icon dropdown -->
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                ▼
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 rounded-[32px] p-6 text-white overflow-hidden relative">
                        <p class="text-[10px] font-bold text-orange-400 mb-2 uppercase">Member Only</p>
                        <h5 class="text-lg font-bold mb-4 leading-tight">Giảm ngay 10% <br>cho đơn đầu tiên</h5>
                        <button class="text-[10px] font-black uppercase bg-orange-500 text-black px-4 py-2 rounded-lg">Đăng
                            ký</button>
                        <div class="absolute -bottom-2 -right-4 text-5xl opacity-20 rotate-[-20deg]">👟</div>
                    </div>
                </aside>

                <div class="flex-1">
                    @if ($products->isEmpty())
                        <div class="py-20 text-center">
                            <p class="text-slate-400">Không tìm thấy sản phẩm nào.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                            @foreach ($products as $product)
                                <div class="glass-light p-6 rounded-[32px] group relative">
                                    @if ($product->category)
                                        <span
                                            class="absolute top-8 left-8 z-10 bg-white shadow-sm text-slate-900 text-[10px] font-black px-3 py-1 rounded-full">
                                            {{ $product->category->name ?? 'Khác' }}
                                        </span>
                                    @endif

                                    <div
                                        class="aspect-square bg-slate-50 rounded-[24px] mb-6 relative flex items-center justify-center overflow-hidden border border-slate-50">
                                        @if ($product->thumbnail)
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                        @else
                                            <div class="text-6xl">👟</div>
                                        @endif
                                    </div>

                                    <div class="space-y-1 mb-6">
                                        <h4
                                            class="text-lg font-bold text-slate-900 group-hover:text-orange-500 transition truncate">
                                            {{ $product->name }}
                                        </h4>
                                        <p class="text-slate-400 text-xs font-medium">
                                            {{ $product->category->name ?? 'Khác' }}</p>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-black text-slate-900">
                                            {{ number_format($product->price, 0, ',', '.') }}đ
                                        </span>
                                        @auth
                                            <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit"
                                                    class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center hover:bg-orange-500 transition-all shadow-lg hover:shadow-orange-100"
                                                    title="Thêm vào giỏ hàng">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}"
                                                class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center hover:bg-orange-500 transition-all shadow-lg hover:shadow-orange-100"
                                                title="Đăng nhập để mua hàng">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                </svg>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
@endsection
