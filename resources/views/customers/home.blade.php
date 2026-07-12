@extends('customers.layouts.layout')

@section('title', 'Trang chủ')

@section('content')
    <style>
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

        .orange-shadow {
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.25);
        }

        .text-gradient-dark {
            background: linear-gradient(to right, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reviews-marquee {
            position: relative;
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
        }

        .reviews-track {
            display: flex;
            gap: 1.75rem;
            width: max-content;
            padding-block: 0.5rem;
            will-change: transform;
            animation: reviews-scroll 32s linear infinite;
        }

        .reviews-marquee:hover .reviews-track {
            animation-play-state: paused;
        }

        .review-slide {
            flex: 0 0 340px;
        }

        @keyframes reviews-scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(calc(-50% - 0.875rem));
            }
        }

        @media (max-width: 768px) {
            .review-slide {
                flex-basis: 285px;
            }

            .reviews-track {
                animation-duration: 24s;
            }
        }
    </style>

    <main class="bg-slate-50 text-slate-800 overflow-hidden">

        <!-- HERO -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 pt-20 pb-32 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
                <div
                    class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full border border-orange-200 bg-orange-50 text-xs font-bold text-orange-500">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                    </span>
                    BỘ SƯU TẬP MỚI 2026
                </div>

                <h2 class="text-6xl lg:text-7xl font-black leading-[1.1] tracking-tight">
                    Bứt phá phong <br> cách với <span class="text-orange-400">từng bước.</span>
                </h2>

                <p class="text-slate-500 text-lg max-w-md leading-relaxed">
                    Khám phá những mẫu giày thời trang, năng động và êm ái cho mọi nhịp sống.
                </p>

                <div class="flex flex-wrap gap-5">
                    <a
                        href="{{ route('products') }}" class="px-8 py-4 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-2xl transition-all orange-shadow uppercase text-sm tracking-widest">Mua
                        ngay</a>
                    <a
                       href="{{ route('posts') }}" class="px-8 py-4 bg-white text-slate-600 border border-slate-200 rounded-2xl hover:bg-slate-100 uppercase text-sm tracking-widest">
                        Tìm hiểu thêm
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-8">
                    <div class="text-center">
                        <h4 class="text-3xl font-black">120+</h4>
                        <p class="text-xs text-slate-400 uppercase">Mẫu</p>
                    </div>
                    <div class="text-center border-x border-slate-200">
                        <h4 class="text-3xl font-black">24h</h4>
                        <p class="text-xs text-slate-400 uppercase">Giao</p>
                    </div>
                    <div class="text-center">
                        <h4 class="text-3xl font-black">4.9★</h4>
                        <p class="text-xs text-slate-400 uppercase">Đánh giá</p>
                    </div>
                </div>
            </div>

            <!-- HERO CARD -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-orange-200/30 rounded-full blur-[100px]"></div>
                <!-- HERO CARD -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-orange-200/30 rounded-full blur-[100px]"></div>

                    @if ($products->isNotEmpty())
                        @php $featured = $products->first(); @endphp
                        <div class="glass-light w-full max-w-lg mx-auto p-10 rounded-[48px] relative z-10">
                            <div class="aspect-square bg-white rounded-[36px] mb-8 flex items-center justify-center border">
                                @if ($featured->thumbnail)
                                    <img src="{{ asset('storage/' . $featured->thumbnail) }}" alt="{{ $featured->name }}"
                                        class="w-full h-full object-cover rounded-[36px]">
                                @else
                                    <div class="text-7xl">👟</div>
                                @endif
                            </div>

                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-xs text-orange-400 font-bold uppercase">Đề cử</p>
                                    <h3 class="text-4xl font-black">{{ $featured->name }}</h3>
                                </div>
                                <div class="bg-slate-800 text-white px-4 py-2 rounded-xl text-xs">
                                    {{ $featured->category->name ?? 'Hot' }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- PRODUCTS -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 pb-32">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h3 class="text-3xl font-black mb-2">Sản phẩm nổi bật</h3>
                    <div class="h-1.5 w-16 bg-orange-400 rounded-full"></div>
                </div>
                <a href="{{ route('products') }}" class="text-slate-400 hover:text-orange-500 text-xs uppercase">Tất cả
                    →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach ($products as $product)
                    <a href="{{ route('products.show', $product->id) }}"
                        class="glass-light p-6 rounded-[32px] group hover:scale-[1.02] transition block">
                        <div class="aspect-square bg-slate-50 rounded-[24px] mb-6 flex items-center justify-center border">
                            @if ($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover rounded-[24px] group-hover:scale-110 transition duration-700">
                            @else
                                <div class="text-7xl group-hover:scale-110 transition">👟</div>
                            @endif
                        </div>
                        <div class="mb-4">
                            <h4 class="text-xl font-bold group-hover:text-orange-500 line-clamp-2">{{ $product->name }}</h4>
                            <p class="text-slate-400 text-sm">{{ $product->category->name ?? '' }}</p>
                        </div>
                        <div class="flex justify-between items-center">
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
                                <span class="text-xl font-black">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @endif
                            <span
                                class="px-4 py-2 bg-orange-500 text-white rounded-xl text-sm font-semibold group-hover:bg-slate-900 transition">
                                Xem chi tiết
                            </span>
                        </div>
                    </a>
                @endforeach

            </div>
        </section>

        <section class="max-w-7xl mx-auto px-6 lg:px-12 pb-32">

            <!-- Title -->
            <div class="text-center mb-16">
                <h3 class="text-3xl font-black text-slate-800 mb-2">
                    Khách hàng nói gì
                </h3>
                <p class="text-slate-400">
                    Trải nghiệm thực tế từ người dùng
                </p>
                <div class="h-1.5 w-16 bg-orange-400 rounded-full mx-auto mt-4"></div>
            </div>

            <div class="hidden">
                <h3 class="text-3xl font-black text-slate-800 mb-2">
                    Khách hàng nói gì
                </h3>
                <p class="text-slate-400">
                    Trải nghiệm thực tế từ người dùng
                </p>
                <div class="h-1.5 w-16 bg-orange-400 rounded-full mx-auto mt-4"></div>
            </div>

            <!-- Reviews -->
            @if ($reviews->isNotEmpty())
                @php
                    $reviewGroups = [$reviews, $reviews];
                @endphp

                <div class="reviews-marquee">
                    <div class="reviews-track">
                        @foreach ($reviewGroups as $groupIndex => $reviewGroup)
                            @foreach ($reviewGroup as $review)
                                @php
                                    $reviewerName = $review->user?->name ?? 'Khách hàng';
                                    $nameParts =
                                        preg_split('/\s+/u', trim($reviewerName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                    $initials = collect($nameParts)
                                        ->take(2)
                                        ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                        ->implode('');
                                    $reviewContent = filled($review->comment)
                                        ? $review->comment
                                        : 'Sản phẩm đúng mô tả, trải nghiệm mua hàng rất ổn.';
                                @endphp

                                <article
                                    class="review-slide glass-light p-6 rounded-[28px] space-y-4 hover:scale-[1.02] transition"
                                    @if ($groupIndex === 1) aria-hidden="true" @endif>
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex text-orange-400 text-lg">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <span>{!! $star <= $review->rating ? '&#9733;' : '&#9734;' !!}</span>
                                            @endfor
                                        </div>

                                        <span
                                            class="max-w-[11rem] truncate rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">
                                            {{ $review->product?->name ?? 'Sản phẩm' }}
                                        </span>
                                    </div>

                                    <p class="min-h-24 text-slate-600 leading-relaxed">
                                        &ldquo;{{ $reviewContent }}&rdquo;
                                    </p>

                                    <div class="flex items-center gap-3 pt-4">
                                        @if ($review->user?->avatar_url)
                                            <img src="{{ $review->user->avatar_url }}" alt="{{ $reviewerName }}"
                                                class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center font-bold">
                                                {{ $initials !== '' ? $initials : 'KH' }}
                                            </div>
                                        @endif

                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $reviewerName }}</p>
                                            <p class="text-xs text-slate-400">
                                                Đánh giá ngày {{ $review->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @else
                <div class="glass-light rounded-[28px] p-10 text-center">
                    <p class="text-slate-500">
                        Chưa có đánh giá nào từ khách hàng. Hãy là người đầu tiên chia sẻ trải nghiệm mua sắm.
                    </p>
                </div>
            @endif

            <div class="hidden">

                <!-- Review Item -->
                <div class="glass-light p-6 rounded-[28px] space-y-4 hover:scale-[1.02] transition">

                    <!-- Stars -->
                    <div class="flex text-orange-400 text-lg">
                        ★★★★★
                    </div>

                    <!-- Content -->
                    <p class="text-slate-600 leading-relaxed">
                        “Giày rất êm, đi cả ngày không bị đau chân. Thiết kế đẹp và rất đáng tiền!”
                    </p>

                    <!-- User -->
                    <div class="flex items-center gap-3 pt-4">
                        <div
                            class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center font-bold">
                            T
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Trịnh Tiến</p>
                            <p class="text-xs text-slate-400">Khách hàng</p>
                        </div>
                    </div>

                </div>

                <!-- Review 2 -->
                <div class="glass-light p-6 rounded-[28px] space-y-4 hover:scale-[1.02] transition">
                    <div class="flex text-orange-400 text-lg">★★★★★</div>
                    <p class="text-slate-600">
                        “Giao hàng nhanh, đóng gói đẹp. Mình sẽ ủng hộ shop dài dài.”
                    </p>
                    <div class="flex items-center gap-3 pt-4">
                        <div
                            class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center font-bold">
                            H
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Hải Nam</p>
                            <p class="text-xs text-slate-400">Khách hàng</p>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="glass-light p-6 rounded-[28px] space-y-4 hover:scale-[1.02] transition">
                    <div class="flex text-orange-400 text-lg">★★★★☆</div>
                    <p class="text-slate-600">
                        “Form giày đẹp, đúng size. Giá hợp lý so với chất lượng.”
                    </p>
                    <div class="flex items-center gap-3 pt-4">
                        <div
                            class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center font-bold">
                            L
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Linh Chi</p>
                            <p class="text-xs text-slate-400">Khách hàng</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
@endsection
