@extends('customers.layouts.layout')

@section('title', $product->name . ' - TTM SHOP')

@section('content')
    <main class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-12">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 mb-8 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-700">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('products') }}" class="hover:text-slate-700">Sản phẩm</a>
                <span>/</span>
                <span class="text-slate-800">{{ $product->name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                <!-- Product Images -->
                <div class="flex flex-col gap-4">
                    <div
                        class="aspect-square bg-slate-50 rounded-[32px] overflow-hidden flex items-center justify-center border">
                        @if ($product->thumbnail)
                            <img id="mainImage" src="{{ asset('storage/' . $product->thumbnail) }}"
                                alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-9xl">👟</div>
                        @endif
                    </div>

                    @if ($product->images->count() > 0)
                        <div class="grid grid-cols-4 gap-4">
                            @if ($product->thumbnail)
                                <div class="aspect-square bg-slate-50 rounded-lg overflow-hidden cursor-pointer border-2 border-orange-500"
                                    onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $product->thumbnail) }}'">
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif

                            @foreach ($product->images->take(3) as $image)
                                <div class="aspect-square bg-slate-50 rounded-lg overflow-hidden cursor-pointer border-2 border-slate-200 hover:border-orange-400"
                                    onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $image->image_path) }}'">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div>
                    <!-- Category -->
                    <div class="mb-4">
                        <span class="bg-orange-50 text-orange-500 text-xs font-bold px-3 py-1 rounded-full">
                            {{ $product->category->name ?? 'Khác' }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl font-black mb-4 text-slate-900">{{ $product->name }}</h1>

                    <!-- Description -->
                    <p class="text-slate-600 text-lg mb-8">
                        {{ $product->description }}
                    </p>

                    <!-- Rating (optional) -->
                    <div class="flex items-center gap-4 mb-8 pb-8 border-b border-slate-200">
                        <div class="flex text-yellow-400 text-2xl">★★★★★</div>
                        <span class="text-slate-500 text-sm">(128 đánh giá)</span>
                    </div>

                    <!-- Price -->
                    <div class="mb-8">
                        <div class="flex items-center gap-4 mb-4">
                            @if ($product->sale_price)
                                <div>
                                    <p class="text-sm text-slate-500 line-through">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </p>
                                    <p class="text-4xl font-black text-orange-500">
                                        {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                    </p>
                                </div>
                                <div class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm font-bold">
                                    -{{ round((1 - $product->sale_price / $product->price) * 100) }}%
                                </div>
                            @else
                                <p class="text-4xl font-black text-slate-900">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-8 p-4 bg-slate-50 rounded-lg">
                        @if ($product->stock > 10)
                            <p class="text-green-600 font-semibold">✓ Còn hàng ({{ $product->stock }} sản phẩm)</p>
                        @elseif ($product->stock > 0)
                            <p class="text-orange-500 font-semibold">⚠ Chỉ còn {{ $product->stock }} sản phẩm</p>
                        @else
                            <p class="text-red-500 font-semibold">✗ Hết hàng</p>
                        @endif
                    </div>

                    <!-- Quantity & Add to Cart -->
                    @auth
                        <form action="{{ route('cart.add') }}" method="POST" class="mb-8">
                            @csrf
                            <div class="flex items-center gap-4 mb-4">
                                <label class="text-slate-700 font-semibold">Số lượng:</label>
                                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden">
                                    <input type="number" name="quantity" value="1" min="1"
                                        max="{{ $product->stock }}" class="w-16 text-center bg-white focus:outline-none">
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                </div>
                                <span class="text-slate-500 text-sm">/ {{ $product->stock }} sản phẩm có sẵn</span>
                            </div>

                            <button type="submit"
                                class="w-full py-4 bg-slate-900 text-white font-black text-lg rounded-xl hover:bg-orange-500 transition-all duration-300 flex items-center justify-center gap-2"
                                @if ($product->stock == 0) disabled @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Thêm vào giỏ hàng
                            </button>
                        </form>

                        <button
                            class="w-full py-4 border-2 border-slate-300 text-slate-900 font-semibold rounded-xl hover:border-orange-500 hover:text-orange-500 transition">
                            ♥ Thêm vào yêu thích
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full block text-center py-4 bg-slate-900 text-white font-black text-lg rounded-xl hover:bg-orange-500 transition-all duration-300">
                            Đăng nhập để mua hàng
                        </a>
                    @endauth

                    <!-- Additional Info -->
                    <div class="mt-12 space-y-4 pt-8 border-t border-slate-200">
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">🚚</span>
                            <div>
                                <p class="font-semibold text-slate-900">Giao hàng miễn phí</p>
                                <p class="text-slate-500 text-sm">Cho đơn hàng từ 500.000đ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">↩️</span>
                            <div>
                                <p class="font-semibold text-slate-900">Hoàn trả 30 ngày</p>
                                <p class="text-slate-500 text-sm">Không hài lòng? Hoàn trả tiền đầy đủ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">✓</span>
                            <div>
                                <p class="font-semibold text-slate-900">Bảo hành chính hãng</p>
                                <p class="text-slate-500 text-sm">Bảo hành 12 tháng từ ngày mua</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Content -->
            <div class="mb-16 p-12 bg-slate-50 rounded-[32px]">
                <h2 class="text-2xl font-black mb-6 text-slate-900">Chi tiết sản phẩm</h2>
                <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">
                    {!! nl2br($product->description) !!}
                </div>
            </div>

            <!-- Related Products -->
            @if ($relatedProducts->count() > 0)
                <section>
                    <div class="flex justify-between items-end mb-12">
                        <div>
                            <h3 class="text-3xl font-black mb-2">Sản phẩm liên quan</h3>
                            <div class="h-1.5 w-16 bg-orange-400 rounded-full"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach ($relatedProducts as $item)
                            <a href="{{ route('products.show', $item->id) }}"
                                class="glass-light p-6 rounded-[32px] group hover:scale-[1.02] transition block">
                                <div
                                    class="aspect-square bg-slate-50 rounded-[24px] mb-6 flex items-center justify-center border">
                                    @if ($item->thumbnail)
                                        <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->name }}"
                                            class="w-full h-full object-cover rounded-[24px] group-hover:scale-110 transition duration-700">
                                    @else
                                        <div class="text-7xl group-hover:scale-110 transition">👟</div>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-lg font-bold group-hover:text-orange-500 line-clamp-2">
                                        {{ $item->name }}
                                    </h4>
                                    <p class="text-slate-400 text-sm">{{ $item->category->name ?? '' }}</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    @if ($item->sale_price)
                                        <div>
                                            <span class="text-sm text-slate-500 line-through">
                                                {{ number_format($item->price, 0, ',', '.') }}đ
                                            </span>
                                            <span class="text-lg font-black text-orange-500">
                                                {{ number_format($item->sale_price, 0, ',', '.') }}đ
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-lg font-black">
                                            {{ number_format($item->price, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>
@endsection
