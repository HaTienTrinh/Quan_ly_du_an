@extends('customers.layouts.layout')

@section('title', $post->title . ' - Tin tức')

@section('content')
    <main class="min-h-screen bg-slate-50 text-slate-800">
        <section class="mx-auto max-w-5xl px-6 pb-24 pt-16 lg:px-12">
            <div class="mb-8 flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-700">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('posts') }}" class="hover:text-slate-700">Tin tức</a>
                <span>/</span>
                <span class="text-slate-800">{{ $post->title }}</span>
            </div>

            @if (session('success'))
                <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('success') }}
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

            <article class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
                <div class="relative h-72 overflow-hidden bg-slate-200 md:h-[420px]">
                    @if ($post->thumbnail)
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                            class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-7xl">📰</div>
                    @endif
                </div>

                <div class="p-8 md:p-12">
                    <div class="mb-5 flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <span class="rounded-full bg-orange-50 px-3 py-1 font-bold text-orange-500">Bài viết</span>
                        <span>{{ $post->published_at?->format('d/m/Y') ?? $post->created_at->format('d/m/Y') }}</span>
                        <span>•</span>
                        <span>{{ $post->author?->name ?? 'Admin' }}</span>
                    </div>

                    <h1 class="mb-6 text-4xl font-black text-slate-900 md:text-5xl">{{ $post->title }}</h1>

                    @if ($post->summary)
                        <p class="mb-8 border-l-4 border-orange-400 pl-5 text-lg leading-relaxed text-slate-600">
                            {{ $post->summary }}
                        </p>
                    @endif

                    <div class="prose prose-slate max-w-none leading-relaxed">
                        {!! nl2br($post->content) !!}
                    </div>
                </div>
            </article>

            <section class="mt-12 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="mb-8">
                        <h2 class="text-2xl font-black text-slate-900">Bình luận</h2>
                        <p class="mt-2 text-slate-500">{{ $post->comments->count() }} bình luận từ người dùng.</p>
                    </div>

                    <div class="space-y-6">
                        @forelse ($post->comments as $comment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $comment->user->name ?? 'Khách hàng' }}</p>
                                        <p class="mt-1 text-sm text-slate-400">{{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                    </div>

                                    @auth
                                        @if ($comment->user_id === auth()->id())
                                            <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">
                                                Bình luận của bạn
                                            </span>
                                        @endif
                                    @endauth
                                </div>

                                <p class="mt-4 whitespace-pre-line text-slate-600">{{ $comment->content }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-12 text-center text-slate-500">
                                Chưa có bình luận nào cho bài viết này.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-900">Để lại bình luận</h2>

                    @auth
                        @if (auth()->user()->isCustomer())
                            <p class="mt-2 text-slate-500">
                                Chia sẻ cảm nhận hoặc câu hỏi của bạn về bài viết này.
                            </p>

                            <form action="{{ route('posts.comments.store', $post->slug) }}" method="POST" class="mt-8 space-y-5">
                                @csrf

                                <div>
                                    <label for="content" class="mb-2 block text-sm font-semibold text-slate-700">Nội dung bình luận</label>
                                    <textarea id="content" name="content" rows="8"
                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700 focus:border-orange-500 focus:outline-none"
                                        placeholder="Viết bình luận của bạn...">{{ old('content') }}</textarea>
                                </div>

                                <button type="submit"
                                    class="w-full rounded-2xl bg-slate-900 px-6 py-4 font-bold text-white transition hover:bg-orange-500">
                                    Gửi bình luận
                                </button>
                            </form>
                        @else
                            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-slate-600">
                                Chỉ tài khoản khách hàng mới có thể bình luận bài viết.
                            </div>
                        @endif
                    @else
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-6 text-center">
                            <p class="text-slate-600">Đăng nhập để tham gia bình luận bài viết.</p>
                            <a href="{{ route('login') }}"
                                class="mt-4 inline-flex rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white transition hover:bg-orange-500">
                                Đăng nhập để bình luận
                            </a>
                        </div>
                    @endauth
                </div>
            </section>

            @if ($relatedPosts->isNotEmpty())
                <section class="mt-16">
                    <div class="mb-8">
                        <h3 class="text-2xl font-black text-slate-900">Bài viết khác</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ($relatedPosts as $relatedPost)
                            <a href="{{ route('posts.show', $relatedPost->slug) }}"
                                class="group overflow-hidden rounded-[28px] border border-slate-200 bg-white transition hover:scale-[1.02]">
                                <div class="h-48 overflow-hidden bg-slate-200">
                                    @if ($relatedPost->thumbnail)
                                        <img src="{{ asset('storage/' . $relatedPost->thumbnail) }}" alt="{{ $relatedPost->title }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-6xl">📰</div>
                                    @endif
                                </div>

                                <div class="p-5">
                                    <p class="mb-2 text-xs uppercase tracking-[0.2em] text-slate-400">
                                        {{ $relatedPost->published_at?->format('d/m/Y') ?? $relatedPost->created_at->format('d/m/Y') }}
                                    </p>
                                    <h4 class="line-clamp-2 text-lg font-bold text-slate-900 group-hover:text-orange-500">
                                        {{ $relatedPost->title }}
                                    </h4>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>
    </main>
@endsection
