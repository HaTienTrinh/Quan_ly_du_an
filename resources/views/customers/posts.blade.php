@extends('customers.layouts.layout')

@section('title', 'Tin tức')

@section('content')
    <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-12">
        
        <section class="mb-20">
            <div class="bg-white rounded-[32px] overflow-hidden shadow-sm flex flex-col md:flex-row hover:shadow-md transition-shadow duration-300">
                <div class="md:w-1/2 relative h-64 md:h-auto overflow-hidden">
                    <img src="{{ $featuredPost['image'] }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="featured">
                    <span class="absolute top-6 left-6 bg-blue-600 text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full">Nổi bật</span>
                </div>
                
                <div class="md:w-1/2 p-10 lg:p-16 flex flex-col justify-center">
                    <div class="flex items-center gap-4 text-xs text-slate-400 mb-6 font-medium">
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full">{{ $featuredPost['category'] }}</span>
                        <span class="flex items-center gap-1">📅 {{ $featuredPost['date'] }}</span>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6 leading-tight hover:text-blue-600 cursor-pointer transition">
                        {{ $featuredPost['title'] }}
                    </h2>
                    <p class="text-slate-500 mb-8 leading-relaxed line-clamp-3">
                        {{ $featuredPost['desc'] }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-600">👤 {{ $featuredPost['author'] }}</span>
                        <a href="#" class="text-blue-600 font-bold text-sm flex items-center gap-2 hover:gap-3 transition-all">
                            Đọc tiếp <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-2xl font-bold mb-10">Bài viết mới nhất</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col h-full">
                    <div class="relative overflow-hidden h-56">
                        <img src="{{ $post['image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="post">
                        <span class="absolute top-4 left-4 bg-white/90 backdrop-blur text-slate-800 text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-sm">
                            {{ $post['category'] }}
                        </span>
                    </div>

                    <div class="p-8 flex flex-col flex-grow">
                        <div class="flex items-center gap-4 text-[10px] text-slate-400 mb-4 font-bold uppercase tracking-wider">
                            <span>{{ $post['date'] }}</span>
                            <span>{{ $post['author'] }}</span>
                        </div>
                        <h4 class="text-xl font-bold mb-4 group-hover:text-blue-600 transition leading-snug">
                            {{ $post['title'] }}
                        </h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-8 line-clamp-3">
                            {{ $post['desc'] }}
                        </p>
                        
                        <div class="mt-auto">
                            <a href="#" class="text-blue-600 font-bold text-xs flex items-center gap-1 hover:gap-2 transition-all">
                                Xem thêm <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-16 flex justify-center">
                <button class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 uppercase text-xs tracking-widest">
                    Xem thêm bài viết
                </button>
            </div>
        </section>

    </div>
@endsection
