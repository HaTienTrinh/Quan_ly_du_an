@extends('customers.layouts.layout')

@section('title', 'Sản phẩm')

@section('content')
    <style>
        .filter-group input[type="checkbox"]:checked + span {
            color: #f97316;
            font-weight: 700;
        }

        .glass-light {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(249, 115, 22, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .glass-light:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 45px rgba(249, 115, 22, 0.15);
            border-color: rgba(249, 115, 22, 0.25);
        }
    </style>

    <main class="min-h-screen bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-12">
            <div class="mb-12 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-4xl font-black text-slate-900">Tất cả sản phẩm</h2>
                    <p class="mt-1 text-sm text-slate-400">Tìm thấy {{ $products->total() }} sản phẩm</p>
                </div>

                <form action="{{ route('products') }}" method="GET" class="relative w-full md:w-96">
                    @foreach ($selectedCategories as $selectedCategory)
                        <input type="hidden" name="categories[]" value="{{ $selectedCategory }}">
                    @endforeach

                    @if ($priceRange !== '')
                        <input type="hidden" name="price_range" value="{{ $priceRange }}">
                    @endif

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm kiếm mẫu giày..."
                        class="w-full rounded-2xl border border-slate-100 bg-slate-50 py-3 pl-4 pr-12 text-black shadow-sm transition-all focus:border-orange-500 focus:bg-white focus:outline-none">
                    <button type="submit" class="absolute right-4 top-3.5">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            @if (session('success'))
                <div class="mb-8 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col gap-12 lg:flex-row">
                <aside class="w-full lg:w-72">
                    <form action="{{ route('products') }}" method="GET" class="space-y-8">
                        <input type="hidden" name="search" value="{{ $search }}">

                        <div class="rounded-[28px] border border-slate-100 bg-white p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between border-b pb-3">
                                <h4 class="text-sm font-black uppercase tracking-widest text-slate-900">Danh mục</h4>
                                <a href="{{ route('products') }}"
                                    class="text-xs font-semibold text-slate-400 transition hover:text-orange-500">
                                    Xóa lọc
                                </a>
                            </div>

                            <div class="space-y-4 filter-group">
                                @forelse ($categories as $category)
                                    <label class="flex cursor-pointer items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="categories[]"
                                            value="{{ $category->id }}"
                                            class="h-4 w-4 accent-orange-500"
                                            @checked(in_array($category->id, $selectedCategories, true))>
                                        <span class="text-sm text-slate-600 transition hover:text-orange-500">
                                            {{ $category->name }}
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-sm text-slate-400">Chưa có danh mục nào để lọc.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[28px] border border-slate-100 bg-white p-6 shadow-sm">
                            <h4 class="mb-4 flex items-center gap-2 text-sm font-black uppercase tracking-widest text-slate-700">
                                <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                                Khoảng giá
                            </h4>

                            <div class="relative">
                                <select
                                    name="price_range"
                                    class="w-full appearance-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    <option value="">Tất cả giá</option>
                                    <option value="under_1000000" @selected($priceRange === 'under_1000000')>Dưới 1.000.000d</option>
                                    <option value="1000000_2000000" @selected($priceRange === '1000000_2000000')>1.000.000d - 2.000.000d</option>
                                    <option value="over_2000000" @selected($priceRange === 'over_2000000')>Trên 2.000.000d</option>
                                </select>
                                <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    ▼
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="mt-4 w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-900">
                                Áp dụng bộ lọc
                            </button>
                        </div>

                        <div class="relative overflow-hidden rounded-[32px] bg-slate-900 p-6 text-white">
                            <p class="mb-2 text-[10px] font-bold uppercase text-orange-400">Member Only</p>
                            <h5 class="mb-4 text-lg font-bold leading-tight">Giảm ngay 10% <br>cho đơn đầu tiên</h5>
                            <button type="button"
                                class="rounded-lg bg-orange-500 px-4 py-2 text-[10px] font-black uppercase text-black">
                                Đăng ký
                            </button>
                            <div class="absolute -bottom-2 -right-4 rotate-[-20deg] text-5xl opacity-20">👟</div>
                        </div>
                    </form>
                </aside>

                <div class="flex-1">
                    @if ($products->isEmpty())
                        <div class="py-20 text-center">
                            <p class="text-slate-400">Không tìm thấy sản phẩm nào phù hợp với bộ lọc hiện tại.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($products as $product)
                                <a href="{{ route('products.show', $product->id) }}"
                                    class="glass-light block rounded-[32px] p-6 transition group hover:scale-[1.02]">
                                    <div class="mb-6 flex aspect-square items-center justify-center rounded-[24px] border bg-slate-50">
                                        @if ($product->thumbnail)
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                alt="{{ $product->name }}"
                                                class="h-full w-full rounded-[24px] object-cover transition duration-700 group-hover:scale-110">
                                        @else
                                            <div class="text-7xl transition group-hover:scale-110">👟</div>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <h4 class="line-clamp-2 text-xl font-bold text-slate-900 group-hover:text-orange-500">
                                            {{ $product->name }}
                                        </h4>
                                        <p class="text-sm text-slate-400">{{ $product->category->name ?? '' }}</p>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        @if ($product->sale_price)
                                            <div>
                                                <span class="text-sm text-slate-500 line-through">
                                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                                </span>
                                                <span class="text-xl font-black text-orange-500">
                                                    {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xl font-black text-slate-900">
                                                {{ number_format($product->price, 0, ',', '.') }}đ
                                            </span>
                                        @endif

                                        <span
                                            class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition group-hover:bg-slate-900">
                                            Xem chi tiết
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if ($products->hasPages())
                            <div class="mt-10 flex flex-col gap-4 rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between">
                                <p class="text-sm text-slate-500">
                                    Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }}
                                    trên {{ $products->total() }} sản phẩm
                                </p>

                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($products->onFirstPage())
                                        <span class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-300">
                                            Trước
                                        </span>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}"
                                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-orange-300 hover:text-orange-500">
                                            Trước
                                        </a>
                                    @endif

                                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                        @if ($page === $products->currentPage())
                                            <span class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-bold text-white shadow-sm">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-orange-300 hover:text-orange-500">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if ($products->hasMorePages())
                                        <a href="{{ $products->nextPageUrl() }}"
                                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-orange-300 hover:text-orange-500">
                                            Sau
                                        </a>
                                    @else
                                        <span class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-300">
                                            Sau
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection
