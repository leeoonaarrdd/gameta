@extends('layouts.app')

@section('title', 'Bantuan')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-12 animate-on-scroll-bounce">
        <h1 class="text-4xl font-bold text-white uppercase tracking-wide animate-on-scroll">BANTUAN</h1>
        <p class="text-white text-lg mt-2 animate-on-scroll animate-on-scroll-delay-1">Hubungi Tim Customer Support Kami</p>
    </div>
    
    <!-- Contact Form Container -->
    <div class="bg-gray-800/50 rounded-lg p-8 animate-on-scroll-fade">
        <form id="contact-form" class="space-y-6">
            @csrf
            <!-- Nama Lengkap Field -->
            <div class="animate-on-scroll animate-on-scroll-delay-1">
                <label for="nama" class="block text-white text-sm font-medium mb-2">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        class="w-full pl-10 pr-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
                        placeholder="Masukkan nama lengkap Anda"
                        required
                    >
                </div>
            </div>
            
            <!-- No. WhatsApp Field -->
            <div class="animate-on-scroll animate-on-scroll-delay-2">
                <label for="whatsapp" class="block text-white text-sm font-medium mb-2">No. Whatsapp</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                    </div>
                    <input 
                        type="tel" 
                        id="whatsapp" 
                        name="whatsapp" 
                        class="w-full pl-10 pr-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
                        placeholder="Contoh: 0857 8574 0081"
                        required
                    >
                </div>
            </div>
            
            <!-- Isi Pesan Field -->
            <div class="animate-on-scroll animate-on-scroll-delay-3">
                <label for="pesan" class="block text-white text-sm font-medium mb-2">Isi Pesan</label>
                <div class="relative">
                    <textarea 
                        id="pesan" 
                        name="pesan" 
                        rows="6"
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/30 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none cursor-text"
                        placeholder="Tulis pesan Anda di sini..."
                        required
                    ></textarea>
                    <div class="absolute bottom-2 right-2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end animate-on-scroll animate-on-scroll-delay-4">
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors duration-200 cursor-pointer"
                >
                    <span>Kirim Pesan</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Prevent multiple script execution
if (window.bantuanScriptLoaded) {
    console.log('Bantuan script already loaded, skipping...');
} else {
    window.bantuanScriptLoaded = true;
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contact-form');
        const namaInput = document.getElementById('nama');
        const whatsappInput = document.getElementById('whatsapp');
        const pesanInput = document.getElementById('pesan');
    
    // Debounce function to prevent rapid submissions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Auto-format phone number
    whatsappInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Format as Indonesian phone number
        if (value.length > 0) {
            // Remove country code if present
            if (value.startsWith('62')) {
                value = value.substring(2);
            }
            
            // Keep leading zero for display
            if (value.startsWith('0')) {
                // Format with spaces for better readability
                value = value.match(/.{1,4}/g).join(' ');
            } else {
                // Add leading zero if not present
                value = '0' + value;
                // Format with spaces for better readability
                value = value.match(/.{1,4}/g).join(' ');
            }
        }
        
        e.target.value = value;
    });
    
    // Form submission
    let isSubmitting = false; // Flag to prevent double submission
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Prevent double submission
        if (isSubmitting) {
            return;
        }
        
        const nama = namaInput.value.trim();
        const whatsapp = whatsappInput.value.trim();
        const pesan = pesanInput.value.trim();
        
        // Validation
        if (!nama || !whatsapp || !pesan) {
            alert('Semua field harus diisi');
            return;
        }
        
        // Validate WhatsApp number format
        const cleanPhone = whatsapp.replace(/\s/g, '');
        
        // Check if it's a valid Indonesian phone number
        let isValid = false;
        
        // Format: 08xxxxxxxxxx
        if (/^0[0-9]{9,11}$/.test(cleanPhone)) {
            isValid = true;
        }
        // Format: 628xxxxxxxxxx
        else if (/^62[0-9]{9,11}$/.test(cleanPhone)) {
            isValid = true;
        }
        // Format: +628xxxxxxxxxx
        else if (/^\+62[0-9]{9,11}$/.test(cleanPhone)) {
            isValid = true;
        }
        
        if (!isValid) {
            alert('Format nomor WhatsApp tidak valid. Gunakan format: 08xxxxxxxxxx atau 628xxxxxxxxxx');
            return;
        }
        
        // Set submitting flag
        isSubmitting = true;
        
        // Disable submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>Mengirim...</span>';
        
        // Clean phone number for sending
        const cleanPhoneForSending = whatsapp.replace(/\s/g, '');
        
        // Send AJAX request to controller
        fetch('/bantuan/kirim', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                name: nama,
                wa: cleanPhoneForSending,
                message: pesan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                form.reset();
            } else {
                alert(data.message || 'Terjadi kesalahan saat mengirim pesan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
        })
        .finally(() => {
            // Reset submitting flag
            isSubmitting = false;
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    });
}
</script>
@endpush
@endsection 