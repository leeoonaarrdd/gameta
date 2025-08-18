@extends('layouts.app')

@section('title', 'Invoice TopUp')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="flex justify-center px-4 sm:px-6 lg:px-8 mt-8 mb-8">
    <div class="max-w-2xl w-full">
        <!-- Header Bar -->
        <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 shadow-xl">
            <!-- Header Navigation -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('member.topup-saldo') }}" class="flex items-center text-gray-300 hover:text-white transition-colors duration-200 mr-4">
                        <i class="fas fa-arrow-left w-4 h-4 mr-2"></i>
                        <span class="text-sm">Detail</span>
                    </a>
                    <span class="text-gray-400 text-sm">#{{ $topup->topup_id }}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-400 text-sm mr-2">#{{ $topup->topup_id }}</span>
                    <button onclick="copyToClipboard('{{ $topup->topup_id }}')" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <i class="fas fa-copy w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Transaction Information -->
            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-white text-sm">Topup ID</span>
                    <span class="text-white text-sm font-mono">#{{ $topup->topup_id }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-white text-sm">Jumlah</span>
                    <span class="text-white text-sm">Rp {{ number_format($topup->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-white text-sm">Biaya Admin</span>
                    <span class="text-white text-sm">Rp 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-white text-sm">Pembayaran</span>
                    <span class="text-white text-sm">{{ $topup->payment_method }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-white text-sm">Status</span>
                    <span class="text-orange-400 text-sm font-medium">{{ ucfirst($topup->status) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-white text-sm">Tanggal Transaksi</span>
                    <span class="text-white text-sm">{{ $topup->tanggal->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            <!-- Payment Details Section -->
            <div class="border-t border-gray-700/30 pt-6">
                <h3 class="text-white text-lg font-medium mb-3">Detail Pembayaran</h3>
                <p class="text-gray-400 text-sm mb-4">Selesaikan pembayaranmu untuk menghindari pembatalan otomatis</p>
                
                <!-- Countdown Timer -->
                <div class="bg-gray-900/50 rounded-lg p-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white mb-2" id="countdown">
                            <span id="minutes">{{ $invoiceDuration }}</span> menit <span id="seconds">00</span> detik
                        </div>
                        <p class="text-gray-400 text-xs">Batas waktu pembayaran</p>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-gray-900/50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white text-sm">Pembayaran</span>
                        <div class="text-right">
                            <div class="text-white text-sm font-medium">{{ $topup->payment_account ?? $paymentAccount }}</div>
                            @if($topup->payment_provider)
                                <div class="text-gray-400 text-xs">{{ $topup->payment_provider }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tripay Payment Section (if Tripay) -->
                @if($topup->payment_provider === 'Tripay')
                    <div class="mt-4 p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg">
                        <h3 class="text-purple-400 font-semibold mb-3 flex items-center">
                            <i class="fas fa-credit-card w-4 h-4 mr-2"></i>
                            Pembayaran via Tripay
                        </h3>
                        
                        <!-- QRIS - Only QR Code -->
                        @if(strtolower($topup->payment_method) === 'qris' && $topup->tripay_qr_code)
                            <div class="mb-4">
                                <p class="text-gray-300 text-sm mb-2">Scan QR Code untuk pembayaran:</p>
                                <div class="flex justify-center">
                                    <img src="{{ $topup->tripay_qr_code }}" alt="QR Code" class="w-48 h-48 object-contain bg-white rounded-lg p-2">
                                </div>
                            </div>
                        @endif

                        <!-- Virtual Account - Only Payment URL -->
                        @if(strtolower($topup->payment_method) !== 'qris' && $topup->tripay_payment_url)
                            <div class="mb-4">
                                <p class="text-gray-300 text-sm mb-2">Klik link pembayaran:</p>
                                <a href="{{ $topup->tripay_payment_url }}" target="_blank" 
                                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-external-link-alt w-4 h-4 mr-2"></i>
                                    Bayar Sekarang
                                </a>
                            </div>
                        @endif

                        <!-- Reference -->
                        @if($topup->tripay_reference)
                            <div class="text-xs text-gray-400">
                                <p>Reference: {{ $topup->tripay_reference }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Countdown Timer
function startCountdown() {
    const now = new Date().getTime();
    const paymentTime = new Date('{{ $topup->tanggal }}').getTime();
    const timeLimit = {{ $invoiceDuration }} * 60 * 1000; // {{ $invoiceDuration }} minutes in milliseconds
    const endTime = paymentTime + timeLimit;
    
    const timer = setInterval(function() {
        const currentTime = new Date().getTime();
        const timeLeft = endTime - currentTime;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            document.getElementById('countdown').innerHTML = '<span class="text-red-400">Waktu habis</span>';
            return;
        }
        
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }, 1000);
}

// Copy to clipboard functions
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('ID Topup berhasil disalin!');
    }).catch(function() {
        showToast('Gagal menyalin ID Topup');
    });
}

function copyPaymentInfo() {
    const paymentInfo = '{{ $topup->payment_account ?? $paymentAccount }}';
    navigator.clipboard.writeText(paymentInfo).then(function() {
        showToast('Kode metode pembayaran berhasil disalin!');
    }).catch(function() {
        showToast('Gagal menyalin kode metode pembayaran');
    });
}

// Check payment status
function checkPaymentStatus() {
    fetch('/api/topup/status/{{ $topup->topup_id }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.status === 'success') {
                    showToast('Pembayaran berhasil!');
                    setTimeout(() => {
                        window.location.href = '{{ route("member.dashboard") }}';
                    }, 2000);
                } else if (data.data.status === 'pending') {
                    showToast('Pembayaran masih dalam proses');
                } else {
                    showToast('Status: ' + data.data.status);
                }
            } else {
                showToast('Gagal mendapatkan status pembayaran');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat mengecek status');
        });
}

// Toast notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        document.body.removeChild(toast);
    }, 3000);
}

// Start countdown when page loads
document.addEventListener('DOMContentLoaded', function() {
    startCountdown();
});
</script>
@endsection
