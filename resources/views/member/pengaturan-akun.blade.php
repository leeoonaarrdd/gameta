@extends('layouts.app')

@section('title', 'Pengaturan Akun')

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

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg animate-on-scroll-zoom">
                        <p class="text-red-400">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Header -->
                <div class="mb-8 animate-on-scroll-bounce">
                    <h1 class="text-3xl font-bold text-white mb-2 animate-on-scroll">Pengaturan Akun</h1>
                    <p class="text-gray-400 animate-on-scroll animate-on-scroll-delay-1">Kelola informasi akun dan keamanan Anda</p>
                </div>

                <!-- Form Card -->
                <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-scale">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                    <div class="relative p-8">
                        <form method="POST" action="{{ route('member.update-profile') }}" class="space-y-4 sm:space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Username -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                                <label for="username" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Username</label>
                                <div class="w-full sm:w-110 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="username" 
                                        name="username" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                        value="{{ old('username', Auth::guard('member')->user()->username) }}"
                                        required
                                    >
                                </div>
                                @error('username')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                                <label for="email" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Email</label>
                                <div class="w-full sm:w-110 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                        value="{{ old('email', Auth::guard('member')->user()->email) }}"
                                        required
                                    >
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- No WhatsApp -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                                <label for="phone" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">No WhatsApp</label>
                                <div class="w-full sm:w-110 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fab fa-whatsapp text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="phone" 
                                        name="phone" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                        value="{{ old('phone', Auth::guard('member')->user()->phone) }}"
                                        placeholder="628xxxxxxxxxx"
                                        required
                                    >
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-700/30 my-8 animate-on-scroll-slide-up"></div>

                            <!-- Password Section -->
                            <div class="mb-4 animate-on-scroll animate-on-scroll-delay-4">
                                <h3 class="text-lg font-medium text-white mb-4">Ubah Password</h3>
                                <p class="text-sm text-gray-400 mb-6">Kosongkan field password jika tidak ingin mengubah password</p>
                            </div>

                            <!-- Password Lama -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                                <label for="current_password" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Password Lama</label>
                                <div class="w-full sm:w-110 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="current_password" 
                                        name="current_password" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                        placeholder="Masukkan password lama"
                                    >
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Baru -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                                <label for="new_password" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Password Baru</label>
                                <div class="w-full sm:w-110 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="new_password" 
                                        name="new_password" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                        placeholder="Minimal 8 karakter"
                                    >
                                </div>
                                @error('new_password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                                <label for="new_password_confirmation" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Konfirmasi Password Baru</label>
                                <div class="w-full sm:w-110 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="new_password_confirmation" 
                                        name="new_password_confirmation" 
                                        class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                        placeholder="Ulangi password baru"
                                    >
                                </div>
                                @error('new_password_confirmation')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll animate-on-scroll-delay-4">
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto"
                                >
                                    Simpan
                                </button>
                            </div>
                        </form>
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

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg animate-on-scroll-zoom">
                    <p class="text-red-400">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Header -->
            <div class="mb-6 animate-on-scroll-bounce">
                <h1 class="text-2xl font-bold text-white mb-2 animate-on-scroll">Pengaturan Akun</h1>
                <p class="text-gray-400 text-sm animate-on-scroll animate-on-scroll-delay-1">Kelola informasi akun dan keamanan Anda</p>
            </div>

            <!-- Form Card -->
            <div class="relative overflow-hidden rounded-xl border border-gray-700/30 animate-on-scroll-scale">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 backdrop-blur-md"></div>
                <div class="relative p-6">
                    <form method="POST" action="{{ route('member.update-profile') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Username -->
                        <div class="animate-on-scroll animate-on-scroll-delay-1">
                            <label for="username_mobile" class="block text-sm font-medium text-gray-300 mb-2">
                                Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="username_mobile" 
                                    name="username" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    value="{{ old('username', Auth::guard('member')->user()->username) }}"
                                    required
                                >
                            </div>
                            @error('username')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="animate-on-scroll animate-on-scroll-delay-2">
                            <label for="email_mobile" class="block text-sm font-medium text-gray-300 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    id="email_mobile" 
                                    name="email" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    value="{{ old('email', Auth::guard('member')->user()->email) }}"
                                    required
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No WhatsApp -->
                        <div class="animate-on-scroll animate-on-scroll-delay-3">
                            <label for="phone_mobile" class="block text-sm font-medium text-gray-300 mb-2">
                                No WhatsApp
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fab fa-whatsapp text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="phone_mobile" 
                                    name="phone" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    value="{{ old('phone', Auth::guard('member')->user()->phone) }}"
                                    placeholder="628xxxxxxxxxx"
                                    required
                                >
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-700/30 my-6 animate-on-scroll-slide-up"></div>

                        <!-- Password Section -->
                        <div class="mb-4 animate-on-scroll animate-on-scroll-delay-4">
                            <h3 class="text-lg font-medium text-white mb-3">Ubah Password</h3>
                            <p class="text-sm text-gray-400 mb-4">Kosongkan field password jika tidak ingin mengubah password</p>
                        </div>

                        <!-- Password Lama -->
                        <div class="animate-on-scroll animate-on-scroll-delay-4">
                            <label for="current_password_mobile" class="block text-sm font-medium text-gray-300 mb-2">
                                Password Lama
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="current_password_mobile" 
                                    name="current_password" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    placeholder="Masukkan password lama"
                                >
                            </div>
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Baru -->
                        <div class="animate-on-scroll animate-on-scroll-delay-4">
                            <label for="new_password_mobile" class="block text-sm font-medium text-gray-300 mb-2">
                                Password Baru
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="new_password_mobile" 
                                    name="new_password" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    placeholder="Minimal 8 karakter"
                                >
                            </div>
                            @error('new_password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="animate-on-scroll animate-on-scroll-delay-4">
                            <label for="new_password_confirmation_mobile" class="block text-sm font-medium text-gray-300 mb-2">
                                Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="new_password_confirmation_mobile" 
                                    name="new_password_confirmation" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                            @error('new_password_confirmation')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 animate-on-scroll animate-on-scroll-delay-4">
                            <button 
                                type="submit" 
                                class="w-full bg-gradient-to-r from-purple-500 to-purple-400 text-white py-3 px-6 rounded-full hover:from-purple-600 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 font-medium"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
