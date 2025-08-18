@extends('layouts.app')

@section('title', 'Dashboard Member')

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
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg animate-on-scroll-zoom">
                        <p class="text-green-400">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Top Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Account Information Card -->
                    <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-bounce">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                        <div class="relative p-6">
                            <div class="text-center mb-4 animate-on-scroll-slide-up">
                                <div class="w-20 h-20 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center animate-on-scroll-rotate">
                                    <i class="fas fa-user text-3xl text-white"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between animate-on-scroll animate-on-scroll-delay-1">
                                    <span class="text-gray-400">Username</span>
                                    <span class="text-white font-medium">{{ Auth::guard('member')->user()->username }}</span>
                                </div>
                                <div class="flex justify-between animate-on-scroll animate-on-scroll-delay-2">
                                    <span class="text-gray-400">No. Whatsapp</span>
                                    <span class="text-white font-medium">{{ Auth::guard('member')->user()->phone }}</span>
                                </div>
                                <div class="flex justify-between animate-on-scroll animate-on-scroll-delay-3">
                                    <span class="text-gray-400">Bergabung</span>
                                    <span class="text-white font-medium">{{ Auth::guard('member')->user()->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                            </div>
                            
                            <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-full transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-4">
                                Edit Akun
                            </button>
                        </div>
                    </div>

                    <!-- Welcome & Balance Section -->
                    <div class="space-y-6">
                        <!-- Welcome Card -->
                        <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-scale">
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                            <div class="relative p-6">
                                <h3 class="text-lg font-semibold text-white mb-2 animate-on-scroll">
                                    Selamat Datang {{ Auth::guard('member')->user()->username }} 😊
                                </h3>
                                <p class="text-gray-400 text-sm animate-on-scroll animate-on-scroll-delay-1">
                                    Ayo topup kebutuhan games kamu dan saldo akun kamu di menu yang tersedia.
                                </p>
                            </div>
                        </div>

                        <!-- Balance Card -->
                        <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-zoom">
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                            <div class="relative p-6">
                                <div class="mb-4 animate-on-scroll-slide-up">
                                    <p class="text-gray-400 text-sm mb-1">Sisa Saldo</p>
                                    <p class="text-2xl font-bold text-white animate-on-scroll animate-on-scroll-delay-1">Rp {{ number_format(Auth::guard('member')->user()->balance, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('member.topup-saldo') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-full transition-colors duration-200 inline-block text-center animate-on-scroll animate-on-scroll-delay-2">
                                    Topup Saldo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- My Orders Summary -->
                    <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-flip">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                        <div class="relative p-6">
                            <h3 class="text-lg font-semibold text-white mb-4 animate-on-scroll">Pesanan Saya</h3>
                            <div class="flex items-center justify-between">
                                <div class="animate-on-scroll-slide-up">
                                    <p class="text-gray-400 text-sm mb-1">Total Pesanan</p>
                                    <p class="text-2xl font-bold text-white animate-on-scroll animate-on-scroll-delay-1">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</p>
                                </div>
                                <div class="p-3 bg-purple-500/20 rounded-lg animate-on-scroll-rotate">
                                    <i class="fas fa-shopping-cart text-2xl text-purple-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Breakdown -->
                    <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-bounce">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                        <div class="relative p-6">
                            <div class="flex justify-between items-center mb-4 animate-on-scroll-slide-up">
                                <h3 class="text-lg font-semibold text-white">Status Pesanan</h3>
                                <a href="{{ route('member.purchases.index') }}" class="text-purple-400 hover:text-purple-300 text-sm flex items-center">
                                    Lihat semua pesanan →
                                </a>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="text-center animate-on-scroll animate-on-scroll-delay-1">
                                    <p class="text-2xl font-bold text-yellow-400">{{ $pendingCount ?? 0 }}</p>
                                    <p class="text-gray-400 text-sm">Pending</p>
                                </div>
                                <div class="text-center animate-on-scroll animate-on-scroll-delay-2">
                                    <p class="text-2xl font-bold text-blue-400">{{ $processingCount ?? 0 }}</p>
                                    <p class="text-gray-400 text-sm">Processing</p>
                                </div>
                                <div class="text-center animate-on-scroll animate-on-scroll-delay-3">
                                    <p class="text-2xl font-bold text-green-400">{{ $successCount ?? 0 }}</p>
                                    <p class="text-gray-400 text-sm">Success</p>
                                </div>
                                <div class="text-center animate-on-scroll animate-on-scroll-delay-4">
                                    <p class="text-2xl font-bold text-red-400">{{ $cancelledCount ?? 0 }}</p>
                                    <p class="text-gray-400 text-sm">Canceled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Main Content -->
        <div class="lg:hidden p-4 animate-on-scroll-fade">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg animate-on-scroll-zoom">
                    <p class="text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Top Row -->
            <div class="grid grid-cols-1 gap-6 mb-6">
                <!-- Account Information Card -->
                <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-bounce">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                    <div class="relative p-6">
                        <div class="text-center mb-4 animate-on-scroll-slide-up">
                            <div class="w-20 h-20 bg-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center animate-on-scroll-rotate">
                                <i class="fas fa-user text-3xl text-white"></i>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between animate-on-scroll animate-on-scroll-delay-1">
                                <span class="text-gray-400">Username</span>
                                <span class="text-white font-medium">{{ Auth::guard('member')->user()->username }}</span>
                            </div>
                            <div class="flex justify-between animate-on-scroll animate-on-scroll-delay-2">
                                <span class="text-gray-400">No. Whatsapp</span>
                                <span class="text-white font-medium">{{ Auth::guard('member')->user()->phone }}</span>
                            </div>
                            <div class="flex justify-between animate-on-scroll animate-on-scroll-delay-3">
                                <span class="text-gray-400">Bergabung</span>
                                <span class="text-white font-medium">{{ Auth::guard('member')->user()->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>
                        
                        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-full transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-4">
                            Edit Akun
                        </button>
                    </div>
                </div>

                <!-- Welcome & Balance Section -->
                <div class="space-y-6">
                    <!-- Welcome Card -->
                    <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-scale">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                        <div class="relative p-6">
                            <h3 class="text-lg font-semibold text-white mb-2 animate-on-scroll">
                                Selamat Datang {{ Auth::guard('member')->user()->username }} 😊
                            </h3>
                            <p class="text-gray-400 text-sm animate-on-scroll animate-on-scroll-delay-1">
                                Ayo topup kebutuhan games kamu dan saldo akun kamu di menu yang tersedia.
                            </p>
                        </div>
                    </div>

                    <!-- Balance Card -->
                    <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-zoom">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                        <div class="relative p-6">
                            <div class="mb-4 animate-on-scroll-slide-up">
                                <p class="text-gray-400 text-sm mb-1">Sisa Saldo</p>
                                <p class="text-2xl font-bold text-white animate-on-scroll animate-on-scroll-delay-1">Rp {{ number_format(Auth::guard('member')->user()->balance, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('member.topup-saldo') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-full transition-colors duration-200 inline-block text-center animate-on-scroll animate-on-scroll-delay-2">
                                Topup Saldo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="grid grid-cols-1 gap-6">
                <!-- My Orders Summary -->
                <div class="relative overflow-hidden rounded-xl border border-gray-700/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                    <div class="relative p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Pesanan Saya</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm mb-1">Total Pesanan</p>
                                <p class="text-2xl font-bold text-white">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-lg">
                                <i class="fas fa-shopping-cart text-2xl text-blue-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Breakdown -->
                <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-bounce">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                    <div class="relative p-6">
                        <div class="flex justify-between items-center mb-4 animate-on-scroll-slide-up">
                            <h3 class="text-lg font-semibold text-white">Status Pesanan</h3>
                            <a href="{{ route('member.purchases.index') }}" class="text-blue-400 hover:text-blue-300 text-sm flex items-center">
                                Lihat semua pesanan →
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-4">
                            <div class="text-center animate-on-scroll animate-on-scroll-delay-1">
                                <p class="text-2xl font-bold text-yellow-400">{{ $pendingCount ?? 0 }}</p>
                                <p class="text-gray-400 text-sm">Pending</p>
                            </div>
                            <div class="text-center animate-on-scroll animate-on-scroll-delay-2">
                                <p class="text-2xl font-bold text-blue-400">{{ $processingCount ?? 0 }}</p>
                                <p class="text-gray-400 text-sm">Processing</p>
                            </div>
                            <div class="text-center animate-on-scroll animate-on-scroll-delay-3">
                                <p class="text-2xl font-bold text-green-400">{{ $successCount ?? 0 }}</p>
                                <p class="text-gray-400 text-sm">Success</p>
                            </div>
                            <div class="text-center animate-on-scroll animate-on-scroll-delay-4">
                                <p class="text-2xl font-bold text-red-400">{{ $cancelledCount ?? 0 }}</p>
                                <p class="text-gray-400 text-sm">Canceled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
