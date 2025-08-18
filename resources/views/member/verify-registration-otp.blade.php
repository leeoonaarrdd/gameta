@extends('layouts.app')

@section('title', 'Verifikasi Akun')

@section('content')
<div class="flex justify-center px-4 sm:px-6 lg:px-8 mt-8 mb-8">
    <div class="max-w-3xl w-full rounded-lg shadow-lg overflow-hidden relative">
        <div class="flex flex-col lg:flex-row relative">

            <!-- Form Section -->
            <div class="w-full lg:w-1/2 p-8 lg:p-12 relative z-10 bg-gradient-to-r from-gray-950/95 via-gray-950/85 via-40% to-transparent backdrop-blur-md lg:bg-gradient-to-r lg:from-gray-950/95 lg:via-gray-950/85 lg:via-40% lg:to-transparent">
                <!-- Blur transisi ke gambar - hanya tampil di desktop -->
                <div class="hidden lg:block absolute top-0 right-0 h-full w-40 bg-gray-950/60 blur-[40px] pointer-events-none"></div>
                
                <!-- Blur transisi ke gambar di mobile - di bagian bawah form -->
                <div class="lg:hidden absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-gray-950/80 via-gray-950/40 to-transparent blur-[20px] pointer-events-none"></div>

                <div class="max-w-md mx-auto relative z-10">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-white mb-2">Verifikasi Akun</h2>
                        <p class="text-gray-300">Masukkan kode OTP yang telah dikirim ke WhatsApp Anda</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-500/20 border border-green-500/30 rounded-lg">
                            <p class="text-green-400 text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-500/20 border border-red-500/30 rounded-lg">
                            <p class="text-red-400 text-sm">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form class="space-y-6" method="POST" action="{{ route('member.verify-registration-otp.post') }}">
                        @csrf
                        
                        <div>
                            <label for="otp" class="block text-sm font-medium text-gray-300 mb-2">
                                Kode OTP
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="otp" 
                                    name="otp" 
                                    class="w-full pl-12 pr-3 py-3 bg-gray-800/50 border border-gray-600/30 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 backdrop-blur-sm text-center text-lg tracking-widest"
                                    value="{{ old('otp') }}"
                                    maxlength="6"
                                    placeholder="000000"
                                    required
                                >
                            </div>
                            @error('otp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button 
                                type="submit" 
                                class="w-full bg-gradient-to-r from-purple-500 to-purple-400 text-white py-3 px-4 rounded-full hover:from-purple-600 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 font-medium"
                            >
                                Verifikasi Akun
                            </button>
                        </div>

                        <div class="text-center space-y-2">
                            <p class="text-sm text-gray-300">
                                <a href="{{ route('member.register') }}" class="font-medium text-purple-400 hover:text-purple-300">
                                    Kirim Ulang OTP
                                </a>
                            </p>
                            <p class="text-sm text-gray-300">
                                <a href="{{ route('member.login') }}" class="font-medium text-purple-400 hover:text-purple-300">
                                    Kembali ke Login
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Image Section -->
            <div class="w-full lg:w-1/2 relative order-first lg:order-last">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    
    // Hanya izinkan angka
    otpInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Auto focus dan select
    otpInput.focus();
    otpInput.select();
});
</script>
@endsection
