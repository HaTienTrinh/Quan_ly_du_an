@extends('customers.layouts.layout')

@section('title', 'Sản phẩm')

@section('content')
    <main class="max-w-7xl mx-auto px-12 pt-20 pb-32">
        <header class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
            <div>
                <span class="text-orange-500 font-bold tracking-[0.3em] text-sm uppercase mb-4 block">Bộ sưu tập</span>
                <h2 class="text-5xl font-extrabold">Sản phẩm nổi bật</h2>
            </div>
            <p class="max-w-lg text-gray-400 text-sm leading-relaxed">
                Những thiết kế được yêu thích nhất với kiểu dáng hiện đại, đế êm và phối màu ấn tượng cho cả đi chơi lẫn vận
                động nhẹ.
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($products as $product)
                <div class="glass p-8 rounded-[40px] group hover:border-orange-500/50 transition-all duration-500">
                    <div class="flex justify-between items-center mb-6">
                        <span
                            class="{{ $product['badge_bg'] }} px-4 py-1.5 rounded-full text-[10px] font-bold tracking-widest uppercase">
                            {{ $product['category'] }}
                        </span>
                        <span class="text-gray-400 font-medium text-sm">{{ $product['price'] }}</span>
                    </div>

                    <div
                        class="aspect-[4/3] bg-gradient-to-br {{ $product['color'] }} rounded-[30px] mb-8 relative flex items-center justify-center overflow-hidden shadow-inner">
                        <div class="w-32 h-6 bg-black/20 rounded-full blur-xl absolute bottom-8 rotate-[-15deg]"></div>
                        <div
                            class="text-7xl transform group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500 drop-shadow-2xl">
                            👟</div>
                    </div>

                    <h3 class="text-2xl font-bold mb-4">{{ $product['name'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-8 h-12 overflow-hidden">
                        {{ $product['desc'] }}
                    </p>

                    <button
                        class="w-fit px-6 py-3 bg-white/5 border border-white/10 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-orange-500 hover:text-black transition-all duration-300">
                        Xem chi tiết
                    </button>
                </div>
            @endforeach
        </div>
    </main>
@endsection
