@extends('layouts.app')

@section('title', 'TopUp Saldo')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<style>
/* Hide number input arrows */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>

@section('content')
<div class="flex justify-center px-4 sm:px-6 lg:px-8 mt-8 mb-8">
    <div class="max-w-6xl w-full">
        <!-- Mobile Sidebar - Horizontal -->
        <div class="lg:hidden mb-6 animate-on-scroll-slide-up">
            <nav class="flex flex-row overflow-x-auto space-x-2 bg-gray-900/50 rounded-lg p-2">
                <a href="{{ route('member.dashboard') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-1">
                    <i class="fas fa-home-alt w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                
                <a href="{{ route('member.purchases.index') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-2">
                    <i class="fas fa-clipboard-list w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">Pesanan Saya</span>
                </a>
                
                                 <a href="{{ route('member.topup-saldo') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-3">
                     <i class="fas fa-credit-card w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                     <span class="text-sm">TopUp Saldo</span>
                 </a>
                
                <a href="{{ route('member.pengaturan-akun') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-4">
                    <i class="fas fa-user-cog w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">Pengaturan Akun</span>
                </a>
                
                <form method="POST" action="{{ route('member.logout') }}" class="block">
                    @csrf
                    <button type="submit" class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-4">
                        <i class="fas fa-sign-out-alt w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                        <span class="text-sm">Log Out</span>
                    </button>
                </form>
            </nav>
        </div>

        <!-- Desktop Layout - Sidebar + Main Content -->
        <div class="hidden lg:flex">
            <!-- Desktop Sidebar -->
            <div class="w-64 flex-shrink-0 p-6 animate-on-scroll-fade">
                <nav class="space-y-2">
                    <a href="{{ route('member.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-1">
                        <i class="fas fa-home-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('member.purchases.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-2">
                        <i class="fas fa-clipboard-list w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        Pesanan Saya
                    </a>
                    
                                         <a href="{{ route('member.topup-saldo') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-3">
                         <i class="fas fa-credit-card w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                         TopUp Saldo
                     </a>
                    
                    <a href="{{ route('member.pengaturan-akun') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-4">
                        <i class="fas fa-user-cog w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        Pengaturan Akun
                    </a>
                    
                    <form method="POST" action="{{ route('member.logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-4">
                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Log Out
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Desktop Main Content -->
            <div class="flex-1 p-8 animate-on-scroll-fade">
                <!-- Form Section -->
                <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-bounce">
                    <!-- Header Section -->
                    <div class="flex items-center justify-between mb-6 animate-on-scroll-slide-up">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">TopUp Saldo</h1>
                        <a href="{{ route('member.topup.history') }}" class="flex items-center text-purple-400 hover:text-purple-300 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-1">
                            <i class="fas fa-history w-4 h-4 mr-2"></i>
                            <span class="text-sm">Riwayat Topup</span>
                        </a>
                    </div>

                    <!-- Jumlah TopUp Section -->
                    <div class="space-y-4 sm:space-y-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                            <label for="topup-amount" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Jumlah TopUp</label>
                            <input type="number" 
                                   id="topup-amount" 
                                   class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                                   placeholder="Masukkan jumlah topup"
                                   min="10000">
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                            <div class="w-full sm:w-32"></div>
                            <p class="text-gray-400 text-xs sm:text-sm w-full sm:w-110">Minimal topup saldo Rp 10.000</p>
                        </div>

                        <!-- Metode Pembayaran Section -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                            <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Metode Pembayaran</label>
                            <div class="w-full sm:w-110 space-y-4">
                                @php
                                    $hasPaymentMethods = false;
                                @endphp
                                
                                @foreach($paymentCategories as $category)
                                    @if(isset($paymentMethods[$category->name]) && $paymentMethods[$category->name]->count() > 0)
                                        @php
                                            $hasPaymentMethods = true;
                                        @endphp
                                        <div class="payment-category animate-on-scroll-scale">
                                            <h3 class="text-gray-300 text-sm font-medium mb-3 animate-on-scroll">{{ $category->name }}</h3>
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach($paymentMethods[$category->name] as $index => $method)
                                                <button type="button" class="payment-method-btn bg-gray-800/50 border border-gray-600/30 rounded-lg p-3 hover:bg-gray-700/50 hover:border-purple-400/60 transition-all duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}" 
                                                        data-method="{{ $method->id }}" 
                                                        data-method-name="{{ $method->name }}">
                                                    @if($method->image)
                                                        <img src="{{ asset('storage/' . $method->image) }}" 
                                                             alt="{{ $method->name }}" 
                                                             class="w-full h-8 object-contain">
                                                    @else
                                                        <div class="flex items-center">
                                                            <div class="w-8 h-8 bg-gray-600 rounded flex items-center justify-center mr-3">
                                                                <span class="text-white text-xs font-bold">{{ substr($method->name, 0, 2) }}</span>
                                                            </div>
                                                            <span class="text-white text-sm">{{ $method->name }}</span>
                                                        </div>
                                                    @endif
                                                </button>
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

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 pt-4 animate-on-scroll animate-on-scroll-delay-4">
                            <div class="w-full sm:w-32"></div>
                            <div class="w-full sm:w-110 flex justify-end space-x-3">
                                <button type="button" id="cancel-btn" class="px-6 py-2 text-white hover:text-gray-300 transition-colors duration-200">
                                    Batal
                                </button>
                                <button type="button" id="topup-btn" class="px-6 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-full transition-colors duration-200">
                                    Topup Saldo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Main Content -->
        <div class="lg:hidden p-4 animate-on-scroll-fade">
            <!-- Form Section -->
            <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-bounce">
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-6 animate-on-scroll-slide-up">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">TopUp Saldo</h1>
                    <a href="{{ route('member.topup.history') }}" class="flex items-center text-purple-400 hover:text-purple-300 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-1">
                        <i class="fas fa-history w-4 h-4 mr-2"></i>
                        <span class="text-sm">Riwayat Topup</span>
                    </a>
                </div>

                <!-- Jumlah TopUp Section -->
                <div class="space-y-4 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                        <label for="topup-amount-mobile" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Jumlah TopUp</label>
                        <input type="number" 
                               id="topup-amount-mobile" 
                               class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                               placeholder="Masukkan jumlah topup"
                               min="10000">
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-gray-400 text-xs sm:text-sm w-full sm:w-110">Minimal topup saldo Rp 10.000</p>
                    </div>

                    <!-- Metode Pembayaran Section -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                        <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Metode Pembayaran</label>
                        <div class="w-full sm:w-110 space-y-4">
                            @php
                                $hasPaymentMethods = false;
                            @endphp
                            
                            @foreach($paymentCategories as $category)
                                @if(isset($paymentMethods[$category->name]) && $paymentMethods[$category->name]->count() > 0)
                                    @php
                                        $hasPaymentMethods = true;
                                    @endphp
                                    <div class="payment-category animate-on-scroll-scale">
                                        <h3 class="text-gray-300 text-sm font-medium mb-3 animate-on-scroll">{{ $category->name }}</h3>
                                        <div class="grid grid-cols-2 gap-3">
                                            @foreach($paymentMethods[$category->name] as $index => $method)
                                            <button type="button" class="payment-method-btn-mobile bg-gray-800/50 border border-gray-600/30 rounded-lg p-3 hover:bg-gray-700/50 hover:border-purple-400/60 transition-all duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}" 
                                                    data-method="{{ $method->id }}" 
                                                    data-method-name="{{ $method->name }}">
                                                @if($method->image)
                                                    <img src="{{ asset('storage/' . $method->image) }}" 
                                                         alt="{{ $method->name }}" 
                                                         class="w-full h-8 object-contain">
                                                @else
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 bg-gray-600 rounded flex items-center justify-center mr-3">
                                                            <span class="text-white text-xs font-bold">{{ substr($method->name, 0, 2) }}</span>
                                                        </div>
                                                        <span class="text-white text-sm">{{ $method->name }}</span>
                                                    </div>
                                                @endif
                                            </button>
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

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 pt-4 animate-on-scroll animate-on-scroll-delay-4">
                        <div class="w-full sm:w-32"></div>
                        <div class="w-full sm:w-110 flex justify-end space-x-3">
                            <button type="button" id="cancel-btn-mobile" class="px-6 py-2 text-white hover:text-gray-300 transition-colors duration-200">
                                Batal
                            </button>
                            <button type="button" id="topup-btn-mobile" class="px-6 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-full transition-colors duration-200">
                                Topup Saldo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/topup-saldo.js') }}"></script>
@endsection
