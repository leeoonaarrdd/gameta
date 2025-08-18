@extends('admin.layouts.app')

@section('title', 'Kelola Pesan WhatsApp - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Kelola Pesan WhatsApp</h1>
        </div>
    </div>

    <!-- Form Fields -->
    <div class="space-y-6">
        <!-- Card 1: Pesan Topup Saldo -->
        <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl">
            @if(session('success_topup'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                    {{ session('success_topup') }}
                </div>
            @endif

            @if($errors->any() && old('active_section') == 'topup')
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.whatsapp.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_section" value="topup">
                
                <div class="space-y-4 sm:space-y-6">
                    <h2 class="text-xl font-semibold text-white border-b border-gray-600/30 pb-2">Pesan Topup Saldo</h2>
                    
                    <!-- Variables Panel -->
                    <div class="bg-gradient-to-r from-purple-500/20 to-purple-600/20 border border-purple-500/30 rounded-lg p-3 hover:from-purple-500/30 hover:to-purple-600/30 transition-all duration-300 animate-on-scroll-zoom">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-code text-purple-400 text-xs"></i>
                            <h3 class="text-white font-semibold text-xs">Variabel yang dapat digunakan</h3>
                        </div>
                        <ul class="space-y-1">
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#username#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Username pengguna</span>
                            </li>
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#topup_id#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Topup Saldo ID</span>
                            </li>
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#method#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Metode pembayaran</span>
                            </li>
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#quantity#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Jumlah topup saldo</span>
                            </li>
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#balance#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Total saldo yang didapat</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Topup Saldo Baru -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                        <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Topup Saldo Baru</label>
                        <div class="w-full sm:w-110">
                            <textarea 
                                name="topup_new_message" 
                                rows="4"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                                placeholder="Masukkan pesan untuk topup saldo baru..."
                            >{{ old('topup_new_message', $topupNewMessage) }}</textarea>
                        </div>
                    </div>

                    <!-- Topup Saldo Berhasil -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                        <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Topup Saldo Berhasil</label>
                        <div class="w-full sm:w-110">
                            <textarea 
                                name="topup_success_message" 
                                rows="4"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                                placeholder="Masukkan pesan untuk topup saldo berhasil..."
                            >{{ old('topup_success_message', $topupSuccessMessage) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll-zoom">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto animate-on-scroll-zoom">
                        Simpan
                    </button>
                </div>


            </form>
        </div>

        <!-- Card 2: Pesan Lainnya -->
        <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl">
            @if(session('success_lainnya'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                    {{ session('success_lainnya') }}
                </div>
            @endif

            @if($errors->any() && old('active_section') == 'lainnya')
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.whatsapp.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_section" value="lainnya">
                
                <div class="space-y-4 sm:space-y-6">
                    <h2 class="text-xl font-semibold text-white border-b border-gray-600/30 pb-2">Pesan Lainnya</h2>
                    
                    <!-- Variables Panel -->
                    <div class="bg-gradient-to-r from-purple-500/20 to-purple-600/20 border border-purple-500/30 rounded-lg p-3 hover:from-purple-500/30 hover:to-purple-600/30 transition-all duration-300 animate-on-scroll-zoom">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-code text-purple-400 text-xs"></i>
                            <h3 class="text-white font-semibold text-xs">Variabel yang dapat digunakan</h3>
                        </div>
                        <ul class="space-y-1">
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#username#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Username pengguna</span>
                            </li>
                            <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group animate-on-scroll-zoom">
                                <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#otp#</code>
                                <span class="text-gray-300 text-xs group-hover:text-gray-200">- Kode OTP (untuk Reset Password & Verifikasi Akun)</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Ucapan untuk pengguna baru -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                        <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Ucapan untuk pengguna baru</label>
                        <div class="w-full sm:w-110">
                            <textarea 
                                name="new_user_message" 
                                rows="6"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                                placeholder="Masukkan pesan ucapan untuk pengguna baru..."
                            >{{ old('new_user_message', $newUserMessage) }}</textarea>
                        </div>
                    </div>

                    <!-- Kode OTP - Verifikasi Akun -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                        <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kode OTP - Verifikasi Akun</label>
                        <div class="w-full sm:w-110">
                            <textarea 
                                name="otp_verification_message" 
                                rows="6"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                                placeholder="Masukkan pesan OTP verifikasi akun..."
                            >{{ old('otp_verification_message', $otpVerificationMessage) }}</textarea>
                        </div>
                    </div>

                    <!-- Kode OTP - Reset Password -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                        <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kode OTP - Reset Password</label>
                        <div class="w-full sm:w-110">
                            <textarea 
                                name="otp_reset_password_message" 
                                rows="6"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                                placeholder="Masukkan pesan OTP reset password..."
                            >{{ old('otp_reset_password_message', $otpResetPasswordMessage) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll-zoom">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto animate-on-scroll-zoom">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
