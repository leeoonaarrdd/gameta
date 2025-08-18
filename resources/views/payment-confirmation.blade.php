@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran - ' . \App\Models\Configuration::getValue('site_name', 'Gameta'))

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column - Detail Pesanan, Produk, Player -->
            <div class="space-y-6">
                <!-- Detail Pesanan -->
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-gray-900/90 border border-gray-600/40 rounded-xl p-6 backdrop-blur-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Detail Pesanan
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Order ID:</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-white font-mono text-sm" id="order-id">{{ $order['order_id'] ?? 'ORD20241201001' }}</span>
                                <button onclick="copyToClipboard('{{ $order['order_id'] ?? 'ORD20241201001' }}')" class="text-purple-400 hover:text-purple-300 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v6a2 2 0 01-2 2h-8a2 2 0 01-2-2v-6a2 2 0 012-2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                @if(isset($order['status']) && $order['status'] == 'completed') bg-green-500/20 text-green-400 border border-green-500/30
                                @elseif(isset($order['status']) && $order['status'] == 'processing') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                @elseif(isset($order['status']) && $order['status'] == 'cancelled') bg-red-500/20 text-red-400 border border-red-500/30
                                @elseif(isset($order['status']) && $order['status'] == 'failed') bg-red-500/20 text-red-400 border border-red-500/30
                                @else bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 @endif">
                                @if(isset($order['status']))
                                    @if($order['status'] == 'completed') Selesai
                                    @elseif($order['status'] == 'processing') Diproses
                                    @elseif($order['status'] == 'cancelled') Dibatalkan
                                    @elseif($order['status'] == 'failed') Gagal
                                    @else Menunggu Pembayaran
                                    @endif
                                @else
                                    Menunggu Pembayaran
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Tanggal Transaksi:</span>
                            <span class="text-white text-sm">{{ $order['created_at'] ?? 'Unknown' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Harga:</span>
                            <span class="text-white text-sm">Rp {{ number_format($order['price'] ?? 4000, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Biaya Admin:</span>
                            <span class="text-white text-sm">Rp {{ number_format($order['admin_fee'] ?? 28, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2 border-t border-gray-600/40">
                            <span class="text-white font-semibold">Total:</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-purple-400 font-bold text-lg">Rp {{ number_format($order['total'] ?? 4028, 0, ',', '.') }}</span>
                                <button onclick="copyToClipboard('{{ $order['total'] ?? 4028 }}')" class="text-purple-400 hover:text-purple-300 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v6a2 2 0 01-2 2h-8a2 2 0 01-2-2v-6a2 2 0 012-2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Produk -->
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-gray-900/90 border border-gray-600/40 rounded-xl p-6 backdrop-blur-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Detail Produk
                    </h2>
                    

                    
                    <div class="flex items-center space-x-4">
                        @if(isset($order['game']['gambar']) && !empty($order['game']['gambar']))
                            <img src="{{ asset('storage/' . $order['game']['gambar']) }}" 
                                 alt="{{ $order['game']['name'] ?? $order['game_name'] }}" 
                                 class="w-24 h-32 object-cover rounded-lg flex-shrink-0">
                        @else
                            <div class="w-20 h-20 bg-purple-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div>
                            <h3 class="text-white font-medium mb-2">{{ $order['game_name'] ?? 'Mobile Legend : Bang Bang' }}</h3>
                            <p class="text-gray-300 text-sm">{{ $order['product_name'] ?? '12 Diamonds (11 + 1 Bonus)' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Detail Player -->
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-gray-900/90 border border-gray-600/40 rounded-xl p-6 backdrop-blur-sm">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Detail Player
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Nickname:</span>
                            <span class="text-white font-medium">{{ $order['player_nickname'] ?? 'Unknown' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">ID Player:</span>
                            <span class="text-white text-sm">
                                @if(isset($order['player_fields']) && is_array($order['player_fields']) && !empty($order['player_fields']))
                                    @if(count($order['player_fields']) === 1)
                                        {{ $order['player_fields'][0] }}
                                    @else
                                        {{ $order['player_fields'][0] }} ({{ implode(' - ', array_slice($order['player_fields'], 1)) }})
                                    @endif
                                @elseif(isset($order['player_id']) && !empty($order['player_id']))
                                    {{ $order['player_id'] }}
                                @else
                                    Unknown
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Detail Pembayaran -->
            <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-gray-900/90 border border-gray-600/40 rounded-xl p-6 backdrop-blur-sm">
                <h2 class="text-lg font-semibold text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Detail Pembayaran
                </h2>
                
                <div class="space-y-6">
                    <!-- Payment Info -->
                    <div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-4">
                            Selesaikan pembayaranmu untuk menghindari pembatalan otomatis
                        </p>
                        
                        @if(!(isset($order['payment_method']) && strtolower($order['payment_method']) === 'innerpay' && isset($order['status']) && $order['status'] === 'completed'))
                        <div class="mb-6">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-red-400 font-medium">Batas Waktu Pembayaran ({{ \App\Models\Configuration::getValue('invoice_duration', '30') }} menit):</span>
                            </div>
                            <div class="text-2xl font-bold text-white" id="countdown" 
                                 @if(isset($order['expired_at']) && $order['expired_at']) data-expired-at="{{ $order['expired_at'] }}" @endif>
                                @if(isset($order['expired_at']) && $order['expired_at'])
                                    <span id="minutes">--</span> menit <span id="seconds">--</span> detik
                                @else
                                    <span id="minutes">29</span> menit <span id="seconds">57</span> detik
                                @endif
                            </div>
                        </div>
                        @endif
                        @if(!(isset($order['payment_method']) && strtolower($order['payment_method']) === 'innerpay' && isset($order['status']) && $order['status'] === 'completed'))
                        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-6">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <p class="text-yellow-400 font-medium text-sm mb-1">Penting!</p>
                                    <p class="text-yellow-300 text-xs leading-relaxed">
                                        Pastikan jumlah transfer sesuai dengan total yang tertera. Pesanan akan diproses otomatis setelah transfer diterima.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="flex justify-between items-center mb-16">
                            <span class="text-white font-semibold">Metode:</span>
                            <div class="flex items-center space-x-3">
                                @if(isset($order['payment_method_image']) && !empty($order['payment_method_image']) && $order['payment_method_image'] !== null)
                                    <img src="{{ asset('storage/' . $order['payment_method_image']) }}" 
                                         alt="{{ $order['payment_method'] ?? 'Payment Method' }}" 
                                         class="w-45 h-15 object-contain"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-8 h-8 bg-gray-600/50 rounded flex items-center justify-center" style="display: none;">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-600/50 rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-white font-semibold">Pembayaran:</span>
                            <span class="text-white text-sm">{{ $order['payment_method_code'] ?? 'Unknown' }}</span>
                        </div>

                        <!-- InnerPay Payment Section -->
                        @if(isset($order['payment_method']) && strtolower($order['payment_method']) === 'innerpay' && isset($order['status']) && $order['status'] === 'completed')
                            <div class="mt-6 p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                                <h3 class="text-green-400 font-semibold mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pembayaran via InnerPay Berhasil
                                </h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-300 text-sm">Status:</span>
                                        <span class="text-green-400 font-medium">Pembayaran Berhasil</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-300 text-sm">Saldo Dipotong:</span>
                                        <span class="text-white font-medium">Rp {{ number_format($order['total'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-300 text-sm">Waktu Pembayaran:</span>
                                        <span class="text-white text-sm">{{ $order['processed_at'] ?? 'Unknown' }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 p-3 bg-green-500/20 rounded-lg">
                                    <p class="text-green-300 text-sm">
                                        Pembayaran telah berhasil diproses menggunakan saldo akun Anda. Pesanan akan segera diproses oleh tim kami.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Tripay Payment Section -->
                        @if(isset($order['tripay_reference']) && !empty($order['tripay_reference']))
                            <div class="mt-6 p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg">
                                <h3 class="text-purple-400 font-semibold mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pembayaran via Tripay
                                </h3>
                                
                                <!-- QR Code -->
                                @if(isset($order['qr_code']) && !empty($order['qr_code']))
                                    <div class="mb-4">
                                        <p class="text-gray-300 text-sm mb-2">Scan QR Code untuk pembayaran:</p>
                                        <div class="flex justify-center">
                                            <img src="{{ $order['qr_code'] }}" alt="QR Code" class="w-48 h-48 object-contain bg-white rounded-lg p-2">
                                        </div>
                                    </div>
                                @endif

                                <!-- Payment URL -->
                                @if(isset($order['payment_url']) && !empty($order['payment_url']))
                                    <div class="mb-4">
                                        <p class="text-gray-300 text-sm mb-2">Atau klik link pembayaran:</p>
                                        <a href="{{ $order['payment_url'] }}" target="_blank" 
                                           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            Bayar Sekarang
                                        </a>
                                    </div>
                                @endif

                                <!-- Reference -->
                                <div class="text-xs text-gray-400" data-tripay-reference="{{ $order['tripay_reference'] }}">
                                    <p>Reference: {{ $order['tripay_reference'] }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-gray-900/90 border border-gray-600/40 rounded-lg p-6 flex items-center space-x-4 backdrop-blur-sm">
        <svg class="w-8 h-8 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <div>
            <p class="text-white font-medium">Memeriksa status pembayaran...</p>
            <p class="text-gray-400 text-sm">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/payment-confirmation.js') }}"></script>
@endpush
