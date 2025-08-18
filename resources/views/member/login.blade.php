@extends('layouts.app')

@section('title', 'Login Member')

@section('content')
<div class="flex justify-center px-4 sm:px-6 lg:px-8 mt-8 mb-8">
    <div class="max-w-3xl w-full rounded-lg shadow-lg overflow-hidden relative animate-on-scroll-bounce">
        <div class="flex flex-col lg:flex-row relative">

            <!-- Form Section -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 relative z-10 bg-gradient-to-r from-gray-950/95 via-gray-950/85 via-40% to-transparent backdrop-blur-md lg:bg-gradient-to-r lg:from-gray-950/95 lg:via-gray-950/85 lg:via-40% lg:to-transparent animate-on-scroll-fade">
                <!-- Blur transisi ke gambar - hanya tampil di desktop -->
                <div class="hidden lg:block absolute top-0 right-0 h-full w-40 bg-gray-950/60 blur-[40px] pointer-events-none"></div>
                
                <!-- Blur transisi ke gambar di mobile - di bagian bawah form -->
                <div class="lg:hidden absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-gray-950/80 via-gray-950/40 to-transparent blur-[20px] pointer-events-none"></div>

                <div class="max-w-md mx-auto relative z-10">
                    <div class="text-center mb-8 animate-on-scroll-slide-up">
                        <h2 class="text-3xl font-bold text-white mb-2 animate-on-scroll">Login Member</h2>
                        <p class="text-gray-300 animate-on-scroll animate-on-scroll-delay-1">Masuk ke akun member Anda</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-500/20 border border-green-500/30 rounded-lg animate-on-scroll-zoom">
                            <p class="text-green-400 text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form class="space-y-6" method="POST" action="{{ route('member.login') }}">
                        @csrf
                        
                        <div class="animate-on-scroll animate-on-scroll-delay-1">
                            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                                Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    value="{{ old('username') }}"
                                    required
                                >
                            </div>
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="animate-on-scroll animate-on-scroll-delay-2">
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm"
                                    required
                                >
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="animate-on-scroll animate-on-scroll-delay-3">
                            <button 
                                type="submit" 
                                class="w-full bg-gradient-to-r from-purple-500 to-purple-400 text-white py-3 px-4 rounded-full hover:from-purple-600 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 font-medium"
                            >
                                Masuk
                            </button>
                        </div>

                        <div class="text-center animate-on-scroll animate-on-scroll-delay-4">
                            <p class="text-sm text-gray-300">
                                Belum punya akun? 
                                <a href="{{ route('member.register') }}" class="font-medium text-purple-400 hover:text-purple-300">
                                    Daftar di sini
                                </a>
                            </p>
                            <p class="text-sm text-gray-300 mt-2">
                                <a href="{{ route('member.reset-password') }}" class="font-medium text-purple-400 hover:text-purple-300">
                                    Lupa Password
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Image Section -->
            <div class="w-full lg:w-1/2 relative order-first lg:order-last animate-on-scroll-scale">
                <!-- Gambar -->
                <img 
                    src="{{ asset('images/Lionel Messi Lionel Messi.jpg') }}" 
                    alt="Lionel Messi" 
                    class="w-full h-64 lg:h-full object-cover transition-all duration-500 ease-in-out"
                >

                <!-- Gradient overlay untuk mobile dan desktop -->
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 via-gray-950/60 via-30% to-transparent lg:bg-gradient-to-l lg:from-transparent lg:via-gray-950/30 lg:to-gray-950/95 transition-all duration-500 ease-in-out"></div>

                <!-- Layer blur untuk desktop -->
                <div class="hidden lg:block absolute left-0 top-0 h-full w-32 bg-gray-950/80 blur-2xl transition-all duration-500 ease-in-out"></div>
                
                <!-- Layer blur untuk mobile - di bagian atas gambar -->
                <div class="lg:hidden absolute top-0 left-0 right-0 h-16 bg-gradient-to-b from-gray-950/80 via-gray-950/40 to-transparent blur-[20px] transition-all duration-500 ease-in-out"></div>
            </div>

        </div>
    </div>
</div>
@endsection
