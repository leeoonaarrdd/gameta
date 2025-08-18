@extends('layouts.app')

@section('title', \App\Models\Configuration::getValue('homepage_title', 'Gameta - TopUp Game Terpercaya'))

@push('styles')
<style>
    .banner-slide[data-center-slide="true"] {
        transition: all 0.3s ease !important;
        position: relative;
    }
    
    .banner-slide[data-center-slide="true"]:hover {
        filter: brightness(1.1) !important;
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.3) !important;
    }
    
    .banner-slide[data-center-slide="true"]:hover::after {
        content: "Klik untuk slide berikutnya";
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
    }
</style>
@endpush

@section('content')
    <!-- Banner Section -->
    <section class="relative pt-2 pb-1 sm:py-3 md:py-4 lg:ml-0 overflow-x-hidden animate-on-scroll-bounce">
        <div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-8">
            @if(!empty($banners) && count($banners) > 0)
                <div id="banner-frame" class="relative rounded-2xl overflow-hidden sm:overflow-visible h-[200px] sm:h-[260px] md:h-[320px] lg:h-[400px]">
                    <div id="banner-carousel" class="relative w-full h-full" style="perspective: 1200px; transform-style: preserve-3d;">
                        @foreach($banners as $index => $banner)
                            <div class="banner-slide absolute inset-0 mx-auto w-[80%] sm:w-[85%] md:w-[70%] rounded-2xl overflow-hidden shadow-2xl">
                                <img 
                                    src="{{ $banner['path'] }}" 
                                    alt="Banner {{ $index + 1 }}" 
                                    class="banner-foreground relative w-full h-full object-contain select-none"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Default Hero Section -->
                <div class="relative h-48 sm:h-64 md:h-80 lg:h-96 bg-gradient-to-r from-green-600 to-blue-600 rounded-xl overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-blue-500/20"></div>
                </div>
            @endif
        </div>
    </section>

    <!-- Categories Section -->
    @if(count($categories) > 0)
        @foreach($categories as $category)
            <section class="pt-2 pb-4 sm:py-6 md:py-8 animate-on-scroll">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    @php
                        $words = explode(' ', $category->name, 2);
                        $firstWord = $words[0];
                        $rest = count($words) > 1 ? ' ' . $words[1] : '';
                    @endphp
                    <h2 class="text-2xl font-bold mb-8 animate-on-scroll">
                        <span class="text-white animate-on-scroll">{{ $firstWord }}</span>
                        @if($rest)
                            <span class="text-purple-400 drop-shadow-[0_0_8px_rgba(192,132,252,0.7)] animate-on-scroll animate-on-scroll-delay-1">{{ $rest }}</span>
                        @endif
                    </h2>
                    <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-3 sm:gap-4 px-2 sm:px-0">
                        @if($category->games->count() > 0)
                            @foreach($category->games as $index => $game)
                                <a href="{{ route('checkout.show', $game->slug) }}" class="group rounded-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-1 hover:shadow-lg block animate-on-scroll-zoom animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                                    <div class="aspect-[2/3] w-full rounded-lg mb-2 overflow-hidden bg-gray-800/50">
                                        @if($game->gambar)
                                            <img src="{{ asset('storage/' . $game->gambar) }}" alt="{{ $game->name }}" 
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-700/50">
                                                <span class="text-gray-400 text-sm">No Image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-center px-1">
                                        <h3 class="text-white font-medium text-xs sm:text-sm line-clamp-1 group-hover:text-purple-400 transition-colors">{{ $game->name }}</h3>
                                        <p class="text-purple-400 text-[10px] sm:text-xs opacity-80">{{ $game->sub_judul ?? 'Developer' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <!-- Placeholder jika tidak ada game dalam kategori -->
                            <div class="col-span-full text-center py-8 animate-on-scroll">
                                <p class="text-gray-400">Belum ada game dalam kategori ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach
         @endif

    <!-- Why Choose Us Section -->
    <section class="py-8 animate-on-scroll-fade">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="text-center mb-8 animate-on-scroll">
                                            <h2 class="text-3xl font-bold text-white mb-4 animate-on-scroll animate-once">Mengapa Harus TopUp Games Kamu di Gameta?</h2>
                    <p class="text-purple-400 text-lg animate-on-scroll animate-on-scroll-delay-1 animate-once">Cari tahu apa yang buat kamu harus topup game kamu di Gameta</p>
                    </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <!-- Cheapest Price -->
                <div class="text-center animate-on-scroll animate-on-scroll-delay-1">
                    <div class="w-16 h-16 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 animate-on-scroll-bounce">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-2 animate-on-scroll">Harga Termurah</h3>
                    <p class="text-gray-400 text-sm animate-on-scroll">Dapatkan harga terbaik untuk semua produk digital dengan diskon eksklusif</p>
                </div>

                <!-- Best Payment Methods -->
                <div class="text-center animate-on-scroll animate-on-scroll-delay-2">
                    <div class="w-16 h-16 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 animate-on-scroll-scale">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-2 animate-on-scroll">Metode Lengkap</h3>
                    <p class="text-gray-400 text-sm animate-on-scroll">Berbagai metode pembayaran yang aman dan mudah digunakan</p>
                </div>

                <!-- Attractive Promotions -->
                <div class="text-center animate-on-scroll animate-on-scroll-delay-3">
                    <div class="w-16 h-16 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 animate-on-scroll-flip">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-2 animate-on-scroll">Banyak Promosi Menarik</h3>
                    <p class="text-gray-400 text-sm animate-on-scroll">Promosi dan bonus menarik setiap hari untuk member setia</p>
                </div>

                <!-- Instant Delivery -->
                <div class="text-center animate-on-scroll animate-on-scroll-delay-4">
                    <div class="w-16 h-16 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 animate-on-scroll-rotate">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-semibold text-lg mb-2 animate-on-scroll">Proses Pengiriman Instant</h3>
                    <p class="text-gray-400 text-sm animate-on-scroll">Pengiriman produk digital secara instan setelah pembayaran berhasil</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gameta Services Section -->
    <section class="py-8 animate-on-scroll-bottom">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-on-scroll">
                                    <h2 class="text-3xl font-bold text-white mb-4 animate-on-scroll-slide-up">Layanan Gameta</h2>
                    <p class="text-purple-400 text-lg animate-on-scroll-slide-up animate-on-scroll-delay-1">Gameta memiliki provider game berkualitas yang terintegrasi dengan lebih dari 50 game dengan total produk +1000 produk</p>
            </div>
            
            @if(count($games) > 0)
                <div class="services-scroll-container mt-8 overflow-hidden no-js animate-on-scroll-zoom">
                    <div class="services-scroll-track flex gap-4 sm:gap-6" style="width: max-content;">
                        <!-- First set of games -->
                        @foreach($games as $game)
                            <div class="service-item flex-shrink-0 w-20 sm:w-24 md:w-28 lg:w-32 h-20 sm:h-24 md:h-28 lg:h-32 group">
                                @if($game->logo)
                                    <img src="{{ asset('storage/' . $game->logo) }}" 
                                         alt="{{ $game->name }}" 
                                         class="w-full h-full object-contain p-2 group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-400 text-xs text-center">{{ $game->name }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <!-- Duplicate set for seamless loop -->
                        @foreach($games as $game)
                            <div class="service-item flex-shrink-0 w-20 sm:w-24 md:w-28 lg:w-32 h-20 sm:h-24 md:h-28 lg:h-32 group">
                                @if($game->logo)
                                    <img src="{{ asset('storage/' . $game->logo) }}" 
                                         alt="{{ $game->name }}" 
                                         class="w-full h-full object-contain p-2 group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-400 text-xs text-center">{{ $game->name }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-8 animate-on-scroll-flip">
                    <div class="w-full h-32 flex items-center justify-center bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/50">
                        <span class="text-gray-400">Belum ada game yang ditambahkan</span>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/banner-carousel.js') }}"></script>
<script src="{{ asset('js/services-scroll.js') }}"></script>
@endpush 