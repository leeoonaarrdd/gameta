@extends('layouts.app')

@section('title', $game->name . ' - TopUp ' . \App\Models\Configuration::getValue('site_name', 'Gameta'))

@section('content')
<div class="min-h-screen" data-game-id="{{ $game->id }}" data-member-logged-in="{{ Auth::guard('member')->check() ? 'true' : 'false' }}">
    <!-- Header Section with Game Banner -->
    <section class="relative overflow-visible w-full">
        @if($game->banner)
            <div class="overflow-hidden shadow-2xl relative w-full animate-on-scroll-scale">
                <img 
                    src="{{ asset('storage/' . $game->banner) }}" 
                    alt="{{ $game->name }} Banner" 
                    class="w-full h-full object-cover"
                    style="mask-image: linear-gradient(to bottom, transparent 0%, black 20%, black 60%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 20%, black 60%, transparent 100%);"
                />
            </div>  
        @endif
        
        <!-- Game Information Section - Overlapping Banner -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-16 mb-8 animate-on-scroll-bounce">
                <div class="text-center mt-8">
                    <!-- Game Details -->
                    <div class="max-w-3xl mx-auto">
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2 drop-shadow-lg animate-on-scroll">{{ $game->name }}</h1>
                        @if($game->description)
                            <div class="prose prose-invert max-w-none animate-on-scroll animate-on-scroll-delay-1">
                                <p class="text-gray-300 leading-relaxed drop-shadow-lg">{{ $game->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">

    <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column - Product Selection -->
            <div class="lg:col-span-2 animate-on-scroll-fade">                
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @forelse($products as $index => $product)
                            <div class="product-card bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-purple-900/90 hover:from-gray-700/90 hover:via-gray-600/80 hover:to-purple-800/90 border border-gray-600/40 hover:border-purple-400/60 rounded-lg p-4 cursor-pointer transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-purple-500/30 backdrop-blur-sm animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}" 
                                 data-product-id="{{ $product->id }}" 
                                 data-product-name="{{ $product->name }}" 
                                 data-product-price="{{ Auth::guard('member')->check() ? $product->price_member : $product->price_tamu }}"
                                 data-product-price-tamu="{{ $product->price_tamu }}"
                                 data-product-price-member="{{ $product->price_member }}">
                                <div class="text-center mb-3">
                                    @if($product->icon && $product->icon->file_path)
                                        <img src="{{ asset('storage/' . $product->icon->file_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-20 h-20 object-contain mx-auto mb-2">
                                    @else
                                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                            <span class="text-white text-xs">💎</span>
                                        </div>
                                    @endif
                                    <span class="text-white font-medium text-sm">{{ $product->name }}</span>
                                </div>
                                <div class="text-center">
                                    @auth('member')
                                        <div class="text-gray-300 font-bold text-lg">Rp {{ number_format($product->price_member, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-gray-300 font-bold text-lg">Rp {{ number_format($product->price_tamu, 0, ',', '.') }}</div>
                                    @endauth
                                </div>
                            </div>
                        @empty
                        <div class="col-span-full text-center py-12 animate-on-scroll-zoom">
                            <div class="w-16 h-16 bg-gray-950/50 rounded-full flex items-center justify-center mx-auto mb-4 animate-on-scroll-rotate">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-white mb-2 animate-on-scroll">Belum ada produk tersedia</h3>
                            <p class="text-gray-400 animate-on-scroll animate-on-scroll-delay-1">Produk untuk game ini sedang dalam persiapan. Silakan cek kembali nanti.</p>
                        </div>
                        @endforelse
                    </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6 animate-on-scroll-fade">
                
                <!-- Player Input Section -->
                <div id="player-input-section" class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-purple-900/90 hover:from-gray-700/90 hover:via-gray-600/80 hover:to-purple-800/90 border border-gray-600/40 hover:border-purple-400/60 rounded-xl p-6 shadow-2xl hover:shadow-purple-500/30 transition-all duration-300 backdrop-blur-sm animate-on-scroll-bounce">
                    <h3 class="text-lg font-semibold text-white mb-4 animate-on-scroll">{{ $game->target->teks_header ?? 'Masukan Data Player' }}</h3>
                    
                    @if($game->target && $game->target->konten)
                        <div class="mb-4 animate-on-scroll animate-on-scroll-delay-1">
                            <p class="text-gray-300 text-sm">{{ $game->target->konten }}</p>
                        </div>
                    @endif
                    
                    <div class="space-y-4">
                        @if($game->target && $game->target->input_fields)
                            @foreach($game->target->input_fields as $index => $field)
                                <div class="space-y-2 animate-on-scroll animate-on-scroll-delay-{{ min($index + 2, 4) }}">
                                    <input type="text" 
                                           id="player-field-{{ $index }}" 
                                           name="player_fields[]"
                                           placeholder="{{ $field['judul_kolom'] ?? $field['label'] ?? 'Field ' . ($index + 1) }}"
                                           data-validation="{{ $field['validasi'] ?? 'teks' }}"
                                           class="w-full bg-gray-950/95 border border-gray-700/50 rounded-full px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 hover:border-gray-600/70 backdrop-blur-sm">
                                    @if(isset($field['description']))
                                        <p class="text-gray-400 text-xs">{{ $field['description'] }}</p>
                                    @endif
                                </div>
                                
                                @if($game->target->sparator && $index < count($game->target->input_fields) - 1)
                                    <div class="flex items-center justify-center my-4 animate-on-scroll-slide-up">
                                        <div class="flex-1 border-t border-gray-500/30 bg-gradient-to-r from-transparent via-gray-500/30 to-transparent"></div>
                                        <span class="px-3 text-gray-400 text-sm font-medium">{{ $game->target->sparator }}</span>
                                        <div class="flex-1 border-t border-gray-500/30 bg-gradient-to-r from-transparent via-gray-500/30 to-transparent"></div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <!-- Fallback jika tidak ada target atau input fields -->
                            <div class="space-y-3 animate-on-scroll animate-on-scroll-delay-2">
                                <input type="text" 
                                       id="player-id" 
                                       class="w-full bg-gray-950/95 border border-gray-700/50 rounded-full px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300 hover:border-gray-600/70 backdrop-blur-sm">
                                <p class="text-gray-400 text-sm">Masukan ID Player akun kamu</p>
                            </div>
                        @endif
                        
                        @if($game->target && $game->target->option_fields)
                            @foreach($game->target->option_fields as $index => $field)
                                <div class="space-y-2 animate-on-scroll animate-on-scroll-delay-{{ min($index + 3, 4) }}">
                                    <select id="option-field-{{ $index }}" 
                                            name="option_fields[]"
                                            class="w-full bg-gray-950/95 border border-gray-700/50 rounded-full px-4 py-3 text-white focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 hover:border-gray-600/70 backdrop-blur-sm">
                                        <option value="" disabled selected>{{ $field['judul_kolom'] ?? 'Option ' . ($index + 1) }}</option>
                                        @if(isset($field['pilihan']) && is_array($field['pilihan']))
                                            @foreach($field['pilihan'] as $option)
                                                <option value="{{ $option['nilai_validasi'] ?? $option['nilai_provider'] ?? '' }}">
                                                    {{ $option['judul'] ?? $option['nilai_provider'] ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if(isset($field['description']))
                                        <p class="text-gray-400 text-xs">{{ $field['description'] }}</p>
                                    @endif
                                </div>
                                
                                @if($game->target->sparator && $index < count($game->target->option_fields) - 1)
                                    <div class="flex items-center justify-center my-4 animate-on-scroll-slide-up">
                                        <div class="flex-1 border-t border-gray-500/30 bg-gradient-to-r from-transparent via-gray-500/30 to-transparent"></div>
                                        <span class="px-3 text-gray-400 text-sm font-medium">{{ $game->target->sparator }}</span>
                                        <div class="flex-1 border-t border-gray-500/30 bg-gradient-to-r from-transparent via-gray-500/30 to-transparent"></div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-purple-900/90 hover:from-gray-700/90 hover:via-gray-600/80 hover:to-purple-800/90 border border-gray-600/40 hover:border-purple-400/60 rounded-xl p-6 shadow-2xl hover:shadow-purple-500/30 transition-all duration-300 backdrop-blur-sm animate-on-scroll-scale">
                    <h3 class="text-lg font-semibold text-white mb-4 animate-on-scroll">Pilih Pembayaran</h3>
                    
                    <div class="space-y-4">
                        @php
                            $hasPaymentMethods = false;
                        @endphp
                        
                        @foreach($paymentCategories as $category)
                            @if(isset($paymentMethods[$category->name]) && $paymentMethods[$category->name]->count() > 0)
                                @php
                                    $hasPaymentMethods = true;
                                @endphp
                                <div class="payment-category animate-on-scroll animate-on-scroll-delay-1">
                                    <h4 class="text-white font-medium mb-3">{{ $category->name }}</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach($paymentMethods[$category->name] as $index => $method)
                                            @php
                                                $isInnerPay = strtolower($method->name) === 'innerpay';
                                                $isMemberLoggedIn = auth()->guard('member')->check();
                                                $showMethod = !$isInnerPay || ($isInnerPay && $isMemberLoggedIn);
                                            @endphp
                                            @if($showMethod)
                                            <div class="payment-method bg-gradient-to-br from-gray-900/90 via-gray-800/80 to-gray-950/90 border border-gray-600/40 rounded-lg p-3 cursor-pointer transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-purple-500/20 hover:from-gray-800/90 hover:via-gray-700/80 hover:to-gray-900/90 animate-on-scroll animate-on-scroll-delay-{{ min($index + 2, 4) }}"
                                                 data-method-id="{{ $method->id }}" 
                                                 data-method-name="{{ $method->name }}">
                                                @if($method->image)
                                                    <img src="{{ asset('storage/' . $method->image) }}" 
                                                         alt="{{ $method->name }}" 
                                                         class="w-full h-8 object-contain">
                                                @else
                                                    <div class="w-full h-8 bg-gray-950/90 border border-gray-600/40 rounded flex items-center justify-center backdrop-blur-sm">
                                                        <span class="text-white text-sm">{{ $method->name }}</span>
                                                    </div>
                                                @endif
                                                @if($isInnerPay && $isMemberLoggedIn)
                                                    @php
                                                        $member = auth()->guard('member')->user();
                                                    @endphp
                                                    <div class="text-xs text-gray-400 mt-1 text-center">
                                                        Saldo: Rp {{ number_format($member->balance, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        
                        @if(!$hasPaymentMethods)
                        <div class="text-center py-8 animate-on-scroll-zoom">
                            <div class="w-12 h-12 bg-gray-950/50 rounded-full flex items-center justify-center mx-auto mb-3 animate-on-scroll-rotate">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-white mb-1 animate-on-scroll">Belum ada metode pembayaran</h3>
                            <p class="text-gray-400 text-xs animate-on-scroll animate-on-scroll-delay-1">Metode pembayaran sedang dalam persiapan</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Purchase Confirmation -->
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-purple-900/90 hover:from-gray-700/90 hover:via-gray-600/80 hover:to-purple-800/90 border border-gray-600/40 hover:border-purple-400/60 rounded-xl p-6 shadow-2xl hover:shadow-purple-500/30 transition-all duration-300 backdrop-blur-sm animate-on-scroll-zoom">
                    <h3 class="text-lg font-semibold text-white mb-4 animate-on-scroll">No. Whatsapp</h3>
                    
                    <div class="space-y-4">
                        <input type="text" 
                               id="whatsapp" 
                               placeholder="Masukkan No. Whatsapp" 
                               class="w-full bg-gray-950/95 border border-gray-700/50 rounded-full px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 hover:border-gray-600/70 backdrop-blur-sm animate-on-scroll animate-on-scroll-delay-1">
                    </div>
                </div>

                <!-- Checkout Button -->
                <button id="btn-checkout" 
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-full transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center space-x-2 animate-on-scroll animate-on-scroll-delay-2" 
                        disabled>
                    <svg id="loading-icon" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text">Konfirmasi Pembelian</span>
                </button>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Hidden form for submission -->
<form id="checkout-form" method="POST" action="#" class="hidden">
    @csrf
    <input type="hidden" name="product_id" id="form-product-id">
    <input type="hidden" name="payment_method_id" id="form-payment-method-id">
    <input type="hidden" name="whatsapp" id="form-whatsapp">
    <input type="hidden" name="player_fields" id="form-player-fields">
    <input type="hidden" name="option_fields" id="form-option-fields">
</form>

@push('scripts')
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-gradient-to-br from-gray-800/90 via-gray-700/80 to-purple-900/90 border border-gray-600/40 rounded-lg p-6 flex items-center space-x-4 backdrop-blur-sm">
        <svg class="w-8 h-8 text-purple-500 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <div>
            <p class="text-white font-medium">Memproses pesanan...</p>
            <p class="text-gray-400 text-sm">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembelian -->
<div id="confirmation-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" style="opacity: 0; transform: scale(0.95);">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
        
        <!-- Modal panel -->
        <div class="relative inline-block w-full max-w-md p-4 my-4 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl">
            <!-- Modal header -->
            <div class="text-center mb-4">
                <div class="w-14 h-14 bg-gray-800/30 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-white mb-1">Informasi Pembelian</h2>
                <p class="text-gray-400 text-xs">Pastikan data pesanan Anda sudah benar.</p>
            </div>

            <!-- Modal body -->
            <div class="mb-4 space-y-4">
                <!-- Detail Player -->
                <div>
                    <h3 class="text-white font-semibold mb-2 text-sm">Detail Player</h3>
                    <div class="space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Nickname</span>
                            <span class="text-gray-400 text-sm" id="modal-nickname">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">ID Player</span>
                            <span class="text-white text-sm" id="modal-player-id">-</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Pembelian -->
                <div>
                    <h3 class="text-white font-semibold mb-2 text-sm">Detail Pembelian</h3>
                    <div class="space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Games</span>
                            <span class="text-white text-sm" id="modal-game-name">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Item</span>
                            <span class="text-white text-sm" id="modal-product-name">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">No. Whatsapp</span>
                            <span class="text-white text-sm" id="modal-whatsapp">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Pembayaran</span>
                            <span class="text-white text-sm" id="modal-payment-method">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Harga</span>
                            <span class="text-white text-sm" id="modal-price">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Biaya Admin</span>
                            <span class="text-white text-sm" id="modal-admin-fee">-</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-700/50 pt-1">
                            <span class="text-white font-semibold text-sm">Total</span>
                            <span class="text-white font-bold text-sm" id="modal-total">-</span>
                        </div>
                    </div>
                </div>

                <!-- Disclaimer -->
                <div class="text-center">
                    <p class="text-gray-400 text-xs leading-relaxed">
                        Dengan melakukan pembelian ini, anda telah menyetujui Syarat & Ketentuan dan Kebijakan Privasi yang berlaku.
                    </p>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="flex justify-end gap-3">
                <button 
                    id="btn-cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-colors duration-200 cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    id="btn-confirm-payment"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer"
                >
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush
