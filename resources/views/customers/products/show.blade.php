@extends('customers.layouts.layout')

@section('title', $product->name )

@section('content')
    <main class="min-h-screen bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-12">
            <div class="mb-8 flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-700">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('products') }}" class="hover:text-slate-700">Sản phẩm</a>
                <span>/</span>
                <span class="text-slate-800">{{ $product->name }}</span>
            </div>

            @if (session('success'))
                <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-16 grid grid-cols-1 gap-12 lg:grid-cols-2">
                <div class="flex flex-col gap-4">
                    <div
                        class="aspect-square overflow-hidden rounded-[32px] border bg-slate-50 flex items-center justify-center">
                        @if ($product->thumbnail)
                            <img id="mainImage" src="{{ asset('storage/' . $product->thumbnail) }}"
                                alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="text-9xl">👟</div>
                        @endif
                    </div>

                    @if ($product->images->count() > 0)
                        <div class="grid grid-cols-4 gap-4">
                            @if ($product->thumbnail)
                                <div class="aspect-square cursor-pointer overflow-hidden rounded-lg border-2 border-orange-500 bg-slate-50"
                                    onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $product->thumbnail) }}'">
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                                        class="h-full w-full object-cover">
                                </div>
                            @endif

                            @foreach ($product->images->take(3) as $image)
                                <div class="aspect-square cursor-pointer overflow-hidden rounded-lg border-2 border-slate-200 bg-slate-50 hover:border-orange-400"
                                    onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $image->image_path) }}'">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}"
                                        class="h-full w-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <div class="mb-4">
                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-orange-500">
                            {{ $product->category->name ?? 'Khác' }}
                        </span>
                    </div>

                    <h1 class="mb-4 text-4xl font-black text-slate-900">{{ $product->name }}</h1>

                    <p class="mb-8 text-lg text-slate-600">
                        {{ $product->description }}
                    </p>

                    <div class="mb-8 flex items-center gap-4 border-b border-slate-200 pb-8">
                        <div class="flex text-2xl text-yellow-400">
                            @for ($star = 1; $star <= 5; $star++)
                                <span>{{ $star <= round($averageRating) ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                        <span class="text-sm text-slate-500">
                            {{ $reviewCount > 0 ? number_format($averageRating, 1) . '/5' : 'Chưa có đánh giá' }}
                            @if ($reviewCount > 0)
                                ({{ $reviewCount }} đánh giá)
                            @endif
                        </span>
                    </div>

                    <div class="mb-8">
                        <div class="mb-4 flex items-center gap-4">
                            @if ($product->sale_price)
                                <div>
                                    <p class="text-sm text-slate-500 line-through">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </p>
                                    <p class="text-4xl font-black text-orange-500">
                                        {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                    </p>
                                </div>
                                <div class="rounded-lg bg-red-500 px-3 py-1 text-sm font-bold text-white">
                                    -{{ round((1 - $product->sale_price / $product->price) * 100) }}%
                                </div>
                            @else
                                <p class="text-4xl font-black text-slate-900">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-8 rounded-lg bg-slate-50 p-4">
                        @if ($product->stock > 10)
                            <p class="font-semibold text-green-600">✓ Còn hàng ({{ $product->stock }} sản phẩm)</p>
                        @elseif ($product->stock > 0)
                            <p class="font-semibold text-orange-500">⚠ Chỉ còn {{ $product->stock }} sản phẩm</p>
                        @else
                            <p class="font-semibold text-red-500">✕ Hết hàng</p>
                        @endif
                    </div>

                    @auth
                        <form action="{{ route('cart.add') }}" method="POST" class="mb-8">
                            @csrf
                            @php
                                $sizeGroups = $product->colors->filter(fn($c) => $c->size)->groupBy('size');
                                $noSizeColors = $product->colors->filter(fn($c) => !$c->size);
                            @endphp
                            @if ($sizeGroups->isNotEmpty())
                                <div class="mb-5">
                                    <label class="mb-3 block font-semibold text-slate-700">Chọn size</label>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($sizeGroups as $size => $colorsInSize)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="product_color_id"
                                                    value="{{ $colorsInSize->first()->id }}"
                                                    class="peer sr-only"
                                                    @checked((string) old('product_color_id') === (string) $colorsInSize->first()->id)>
                                                <span class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition peer-checked:border-orange-500 peer-checked:bg-orange-500 peer-checked:text-white hover:border-orange-400">
                                                    {{ $size }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif ($noSizeColors->isNotEmpty())
                                @foreach ($noSizeColors as $color)
                                    <input type="hidden" name="product_color_id" value="{{ $color->id }}">
                                @endforeach
                            @endif

                            <div class="mb-4 flex items-center gap-4">
                                <label class="font-semibold text-slate-700">Số lượng:</label>
                                <div class="flex items-center overflow-hidden rounded-lg border border-slate-300 text-gray-500">
                                    <input type="number" name="quantity" value="1" min="1"
                                        max="{{ $product->stock }}" class="w-16 bg-white text-center focus:outline-none">
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                </div>
                                <span class="text-sm text-slate-500">/ {{ $product->stock }} sản phẩm có sẵn</span>
                            </div>

                            <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-4 text-lg font-black text-white transition-all duration-300 hover:bg-orange-500"
                                @if ($product->stock == 0) disabled @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Thêm vào giỏ hàng
                            </button>
                        </form>

                        <button
                            class="w-full rounded-xl border-2 border-slate-300 py-4 font-semibold text-slate-900 transition hover:border-orange-500 hover:text-orange-500">
                            ♥ Thêm vào yêu thích
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="block w-full rounded-xl bg-slate-900 py-4 text-center text-lg font-black text-white transition-all duration-300 hover:bg-orange-500">
                            Đăng nhập để mua hàng
                        </a>
                    @endauth

                    <div class="mt-12 space-y-4 border-t border-slate-200 pt-8">
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">🚚</span>
                            <div>
                                <p class="font-semibold text-slate-900">Giao hàng miễn phí</p>
                                <p class="text-sm text-slate-500">Cho đơn hàng từ 500.000đ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">↩️</span>
                            <div>
                                <p class="font-semibold text-slate-900">Hoàn trả 30 ngày</p>
                                <p class="text-sm text-slate-500">Không hài lòng? Hoàn trả tiền đầy đủ</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-2xl">✓</span>
                            <div>
                                <p class="font-semibold text-slate-900">Bảo hành chính hãng</p>
                                <p class="text-sm text-slate-500">Bảo hành 12 tháng từ ngày mua</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-16 rounded-[32px] bg-slate-50 p-12">
                <h2 class="mb-6 text-2xl font-black text-slate-900">Chi tiết sản phẩm</h2>
                <div class="prose prose-sm max-w-none leading-relaxed text-slate-600">
                    {!! nl2br($product->description) !!}
                </div>
            </div>

            <section class="mb-16 grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <h2 class="text-3xl font-black text-slate-900">Đánh giá sản phẩm</h2>
                            <p class="mt-2 text-slate-500">
                                Chỉ khách hàng đã nhận hàng mới có thể gửi đánh giá cho sản phẩm này.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 px-5 py-4 text-center">
                            <p class="text-3xl font-black text-slate-900">
                                {{ $reviewCount > 0 ? number_format($averageRating, 1) : '0.0' }}
                            </p>
                            <div class="mt-1 flex justify-center text-lg text-yellow-400">
                                @for ($star = 1; $star <= 5; $star++)
                                    <span>{{ $star <= round($averageRating) ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $reviewCount }} đánh giá</p>
                        </div>
                    </div>

                    @forelse ($product->reviews as $review)
                        <div class="border-t border-slate-200 py-6 first:border-t-0 first:pt-0 last:pb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="font-bold text-slate-900">{{ $review->user->name ?? 'Khách hàng' }}</p>
                                        @auth
                                            @if ($review->user_id === auth()->id())
                                                <span
                                                    class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">
                                                    Đánh giá của bạn
                                                </span>
                                            @endif
                                        @endauth
                                    </div>

                                    <div class="mt-2 flex text-lg text-yellow-400">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <span>{{ $star <= $review->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>

                                <p class="text-sm text-slate-400">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <p class="mt-4 text-slate-600">
                                {{ $review->comment ?: 'Khách hàng chưa để lại nhận xét bằng văn bản.' }}
                            </p>
                        </div>
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-slate-300 px-6 py-12 text-center text-slate-500">
                            Sản phẩm này chưa có đánh giá nào.
                        </div>
                    @endforelse
                </div>

                <div class="rounded-[32px] border border-slate-200 bg-slate-50 p-8">
                    <h3 class="text-2xl font-black text-slate-900">
                        {{ $userReview ? 'Cập nhật đánh giá của bạn' : 'Viết đánh giá của bạn' }}
                    </h3>

                    @auth
                        @if (auth()->user()->isCustomer() && $canReview)
                            <p class="mt-2 text-slate-500">
                                Bạn đã nhận sản phẩm này, vì vậy bạn có thể chia sẻ cảm nhận của mình.
                            </p>

                            <form action="{{ route('products.reviews.store', $product) }}" method="POST"
                                class="mt-8 space-y-6">
                                @csrf

                                <div>
                                    <label class="mb-3 block text-sm font-semibold text-slate-700">Số sao đánh giá</label>
                                    <div class="flex flex-wrap gap-3">
                                        @for ($star = 5; $star >= 1; $star--)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $star }}"
                                                    class="peer sr-only"
                                                    {{ (int) old('rating', $userReview?->rating) === $star ? 'checked' : '' }}>
                                                <span
                                                    class="inline-flex rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 transition peer-checked:border-orange-500 peer-checked:bg-orange-500 peer-checked:text-white hover:border-orange-300">
                                                    {{ $star }} sao
                                                </span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div>
                                    <label for="comment" class="mb-3 block text-sm font-semibold text-slate-700">Nhận xét của
                                        bạn</label>
                                    <textarea id="comment" name="comment" rows="6"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-700 focus:border-orange-500 focus:outline-none"
                                        placeholder="Sản phẩm có đúng như mong đợi không? Chất lượng, form dáng, trải nghiệm sử dụng thế nào?">{{ old('comment', $userReview?->comment) }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full rounded-2xl bg-slate-900 px-6 py-4 font-black text-white transition hover:bg-orange-500">
                                    {{ $userReview ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}
                                </button>
                            </form>
                        @elseif (auth()->user()->isCustomer())
                            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-700">
                                Bạn cần hoàn tất và xác nhận đã nhận đơn hàng chứa sản phẩm này trước khi đánh giá.
                            </div>
                        @else
                            <div class="mt-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-slate-600">
                                Chỉ tài khoản khách hàng mới có thể đánh giá sản phẩm.
                            </div>
                        @endif
                    @else
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-white px-5 py-6 text-center">
                            <p class="text-slate-600">Đăng nhập bằng tài khoản khách hàng đã mua và nhận hàng để đánh giá sản
                                phẩm.</p>
                            <a href="{{ route('login') }}"
                                class="mt-4 inline-flex rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white transition hover:bg-orange-500">
                                Đăng nhập để đánh giá
                            </a>
                        </div>
                    @endauth
                </div>
            </section>

            @if ($relatedProducts->count() > 0)
                <section>
                    <!-- Title -->
                    <div class="mb-10 flex items-end justify-between">
                        <div>
                            <h3 class="mb-2 text-3xl font-black text-slate-900">
                                Sản phẩm liên quan
                            </h3>
                            <div class="h-1 w-14 rounded-full bg-orange-500"></div>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ($relatedProducts as $item)
                            <a href="{{ route('products.show', $item->id) }}"
                                class="group block rounded-3xl border border-slate-200 bg-white p-5 transition duration-300 hover:shadow-lg hover:-translate-y-1">

                                <!-- Image -->
                                <div
                                    class="mb-5 aspect-square flex items-center justify-center overflow-hidden rounded-2xl bg-white">
                                    @if ($item->thumbnail)
                                        <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->name }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="text-6xl">👟</div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="mb-3">
                                    <h4
                                        class="line-clamp-2 text-base font-bold text-slate-900 transition group-hover:text-orange-600">
                                        {{ $item->name }}
                                    </h4>
                                    <p class="text-sm text-slate-400">
                                        {{ $item->category->name ?? '' }}
                                    </p>
                                </div>

                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    @if ($item->sale_price)
                                        <div>
                                            <span class="text-sm text-slate-400 line-through">
                                                {{ number_format($item->price, 0, ',', '.') }}đ
                                            </span>
                                            <span class="block text-lg font-black text-orange-600">
                                                {{ number_format($item->sale_price, 0, ',', '.') }}đ
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-lg font-black text-slate-900">
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
