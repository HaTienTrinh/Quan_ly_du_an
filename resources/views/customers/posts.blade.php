@extends('customers.layouts.layout')

@section('title', 'Tin tức')

@section('content')
    <main class="bg-slate-50 text-slate-800">

        <!-- HEADER -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 pt-20 pb-12 text-center">
            <span class="text-orange-500 font-bold tracking-[0.3em] text-xs uppercase">
                Shop TTM
            </span>
            <h2 class="text-5xl font-extrabold mt-3 mb-4">
                Tin tức & Xu hướng
            </h2>
            <p class="text-slate-500 max-w-xl mx-auto">
                Cập nhật những xu hướng giày mới nhất và mẹo chọn giày phù hợp.
            </p>
        </section>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 pb-24">

            <!-- FEATURED -->
            <section class="mb-20">
                @if ($featuredPost)
                    <div class="glass-light rounded-[32px] overflow-hidden grid md:grid-cols-2">

                        <!-- IMAGE -->
                        <div class="relative h-72 md:h-full overflow-hidden">
                            @if ($featuredPost->thumbnail)
                                <img src="{{ asset('storage/' . $featuredPost->thumbnail) }}"
                                    alt="{{ $featuredPost->title }}"
                                    class="w-full h-full object-cover hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full bg-slate-200 flex items-center justify-center text-6xl">📰</div>
                            @endif

                            <span
                                class="absolute top-5 left-5 bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                                Nổi bật
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="p-10 flex flex-col justify-center">
                            <div class="flex items-center gap-3 text-xs text-slate-400 mb-4">
                                <span class="bg-orange-50 text-orange-500 px-3 py-1 rounded-full">
                                    Bài viết
                                </span>
                                <span>📅
                                    {{ $featuredPost->published_at?->format('d/m/Y') ?? $featuredPost->created_at->format('d/m/Y') }}</span>
                            </div>

                            <h2 class="text-3xl font-black mb-4 hover:text-orange-500 transition">
                                {{ $featuredPost->title }}
                            </h2>

                            <p class="text-slate-500 mb-6 line-clamp-3">
                                {{ $featuredPost->summary ?? substr(strip_tags($featuredPost->content), 0, 200) }}
                            </p>

                            <a href="#"
                                class="text-orange-500 font-bold flex items-center gap-2 hover:gap-3 transition">
                                Đọc tiếp →
                            </a>
                        </div>

                    </div>
                @endif
            </section>
            </section>

            <!-- LIST -->
            <section>
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-2xl font-black">Bài viết mới</h3>
                </div>

                @if ($posts->isEmpty())
                    <div class="py-16 text-center">
                        <p class="text-slate-400 text-lg">Chưa có bài viết nào</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                        @foreach ($posts as $post)
                            <div
                                class="glass-light rounded-[28px] overflow-hidden group hover:scale-[1.02] transition flex flex-col">

                                <!-- IMAGE -->
                                <div class="relative h-56 overflow-hidden bg-slate-200">
                                    @if ($post->thumbnail)
                                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-6xl">📰</div>
                                    @endif

                                    <span
                                        class="absolute top-4 left-4 bg-orange-50 text-orange-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                                        Bài viết
                                    </span>
                                </div>

                                <!-- CONTENT -->
                                <div class="p-6 flex flex-col flex-grow">

                                    <div class="text-[11px] text-slate-400 mb-3 uppercase flex gap-2">
                                        <span>{{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</span>
                                        <span>•</span>
                                        <span>{{ $post->author?->name ?? 'Admin' }}</span>
                                    </div>

                                    <h4 class="text-lg font-bold mb-3 group-hover:text-orange-500 transition line-clamp-2">
                                        {{ $post->title }}
                                    </h4>

                                    <p class="text-slate-500 text-sm mb-6 line-clamp-3">
                                        {{ $post->summary ?? substr(strip_tags($post->content), 0, 150) }}
                                    </p>

                                    <div class="mt-auto">
                                        <a href="#" class="text-orange-500 text-sm font-bold">
                                            Đọc tiếp →
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif

                <!-- PAGINATION -->
                {{-- <div class="mt-16 flex justify-center">
                {{ $posts->links() }}
            </div> --}}

            </section>

        </div>
    </main>
@endsection
