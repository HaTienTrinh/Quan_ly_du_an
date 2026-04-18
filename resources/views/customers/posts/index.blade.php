@extends('customers.layouts.layout')

@section('title', 'Tin tức')

@section('content')
    <main class="bg-slate-50 text-slate-800">
        <section class="mx-auto max-w-7xl px-6 pb-12 pt-20 text-center lg:px-12">
            <span class="text-xs font-bold uppercase tracking-[0.3em] text-orange-500">
                Shop TTM
            </span>
            <h2 class="mt-3 mb-4 text-5xl font-extrabold">
                Tin tức & Xu hướng
            </h2>
            <p class="mx-auto max-w-xl text-slate-500">
                Cập nhật những xu hướng giày mới nhất và mẹo chọn giày phù hợp.
            </p>
        </section>

        <div class="mx-auto max-w-7xl px-6 pb-24 lg:px-12">
            <section class="mb-20">
                @if ($featuredPost)
                    <div class="glass-light grid overflow-hidden rounded-[32px] md:grid-cols-2">
                        <div class="relative h-72 overflow-hidden md:h-full">
                            @if ($featuredPost->thumbnail)
                                <img src="{{ asset('storage/' . $featuredPost->thumbnail) }}"
                                    alt="{{ $featuredPost->title }}"
                                    class="h-full w-full object-cover transition duration-500 hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-slate-200 text-6xl">📰</div>
                            @endif

                            <span
                                class="absolute top-5 left-5 rounded-full bg-orange-500 px-3 py-1 text-[10px] font-bold uppercase text-white">
                                Nổi bật
                            </span>
                        </div>

                        <div class="flex flex-col justify-center p-10">
                            <div class="mb-4 flex items-center gap-3 text-xs text-slate-400">
                                <span class="rounded-full bg-orange-50 px-3 py-1 text-orange-500">
                                    Bài viết
                                </span>
                                <span>🗓 {{ $featuredPost->published_at?->format('d/m/Y') ?? $featuredPost->created_at->format('d/m/Y') }}</span>
                            </div>

                            <h2 class="mb-4 text-3xl font-black transition hover:text-orange-500">
                                {{ $featuredPost->title }}
                            </h2>

                            <p class="mb-6 line-clamp-3 text-slate-500">
                                {{ $featuredPost->summary ?? substr(strip_tags($featuredPost->content), 0, 200) }}
                            </p>

                            <a href="{{ route('posts.show', $featuredPost->slug) }}"
                                class="flex items-center gap-2 font-bold text-orange-500 transition hover:gap-3">
                                Đọc tiếp →
                            </a>
                        </div>
                    </div>
                @endif
            </section>

            <section>
                <div class="mb-10 flex items-center justify-between">
                    <h3 class="text-2xl font-black">Bài viết mới</h3>
                </div>

                @if ($posts->isEmpty())
                    <div class="py-16 text-center">
                        <p class="text-lg text-slate-400">Chưa có bài viết nào</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($posts as $post)
                            <div class="glass-light group flex flex-col overflow-hidden rounded-[28px] transition hover:scale-[1.02]">
                                <div class="relative h-56 overflow-hidden bg-slate-200">
                                    @if ($post->thumbnail)
                                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-6xl">📰</div>
                                    @endif

                                    <span
                                        class="absolute top-4 left-4 rounded-full bg-orange-50 px-3 py-1 text-[10px] font-bold uppercase text-orange-500">
                                        Bài viết
                                    </span>
                                </div>

                                <div class="flex flex-grow flex-col p-6">
                                    <div class="mb-3 flex gap-2 text-[11px] uppercase text-slate-400">
                                        <span>{{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</span>
                                        <span>•</span>
                                        <span>{{ $post->author?->name ?? 'Admin' }}</span>
                                    </div>

                                    <h4 class="mb-3 line-clamp-2 text-lg font-bold transition group-hover:text-orange-500">
                                        {{ $post->title }}
                                    </h4>

                                    <p class="mb-6 line-clamp-3 text-sm text-slate-500">
                                        {{ $post->summary ?? substr(strip_tags($post->content), 0, 150) }}
                                    </p>

                                    <div class="mt-auto">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="text-sm font-bold text-orange-500">
                                            Đọc tiếp →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            @if ($posts->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $posts->links('pagination::simple-tailwind') }}
                </div>
            @endif
        </div>
    </main>
@endsection
