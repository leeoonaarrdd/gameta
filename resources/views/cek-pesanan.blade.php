@extends('layouts.app')

@section('title', 'Cek Pesanan')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Cek Pesanan Section -->
    <div class="mb-12 animate-on-scroll-bounce">
        <h1 class="text-3xl font-bold text-white mb-8 text-center animate-on-scroll">Cek Pesanan</h1>
        
        <!-- Search Form -->
        <div class="max-w-2xl mx-auto mb-8 animate-on-scroll-fade">
            <form id="search-form" class="flex gap-4">
                <div class="flex-1 animate-on-scroll animate-on-scroll-delay-1">
                    <label for="order_id" class="block text-sm font-medium text-gray-300 mb-2">Masukan Order ID kamu</label>
                    <input 
                        type="text" 
                        id="order_id" 
                        name="order_id" 
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                    >
                </div>
                <div class="flex items-end animate-on-scroll animate-on-scroll-delay-2">
                    <button type="submit" id="search-button" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105">
                        Cari Pesanan →
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Info Message -->
        <div class="max-w-2xl mx-auto text-center animate-on-scroll-slide-up">
            <p class="text-gray-300 text-sm leading-relaxed">
                Pesanan kamu tidak terdaftar meskipun kamu yakin sudah memesan? harap tunggu 1-2 jam namun jika pesanan masih tidak muncul maka kamu dapat menghubungi kami 
                <a href="#" class="text-purple-400 hover:text-purple-300 underline">disini</a>
            </p>
        </div>
    </div>
    
    <!-- 10 Pesanan Terbaru Section -->
    <div class="mb-12 animate-on-scroll-fade">
        <h2 class="text-2xl font-bold text-white mb-6 animate-on-scroll">10 Pesanan Terbaru</h2>
        
        <!-- Table -->
        <div class="overflow-x-auto animate-on-scroll-slide-up">
            <table class="w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                <thead>
                    <tr class="border-b border-gray-700/30">
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">Order ID</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Games</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Produk</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($latestOrders as $index => $order)
                        <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                            <td class="px-6 py-4 text-sm text-white">{{ $order->masked_order_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $order->product->game->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $order->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                        'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                        'success' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                        'failed' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        'canceled' => 'bg-red-500/20 text-red-400 border-red-500/30'
                                    ];
                                    $statusColor = $statusColors[strtolower($order->status)] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }} border">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr class="animate-on-scroll-zoom">
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-lg font-medium animate-on-scroll">Belum ada pesanan yang tersedia</span>
                                    <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Pesanan akan muncul di sini setelah ada transaksi</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('js/cek-pesanan.js') }}"></script>
@endsection 