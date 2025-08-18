@extends('admin.layouts.app')

@section('title', 'Pengaturan Halaman Bantuan - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Pengaturan Halaman Bantuan</h1>
        </div>
    </div>
    
    <!-- Form Section -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="bantuanConfigForm" action="{{ route('admin.bantuan.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6">
                <!-- WhatsApp Number -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="whatsappNumber" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">No. WhatsApp</label>
                    <input 
                        type="tel" 
                        id="whatsappNumber"
                        name="whatsapp_number"
                        value="{{ old('whatsapp_number', \App\Models\Configuration::getValue('bantuan_whatsapp', '085654008642')) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                </div>
            
                <!-- Message Template -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="messageTemplate" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Template Pesan</label>
                    <div class="w-full sm:w-110 space-y-3">
                        <!-- Variables Panel -->
                        <div class="bg-gradient-to-r from-purple-500/20 to-purple-600/20 border border-purple-500/30 rounded-lg p-3 hover:from-purple-500/30 hover:to-purple-600/30 transition-all duration-300">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-code text-purple-400 text-xs"></i>
                                <h3 class="text-white font-semibold text-xs">Variabel yang dapat digunakan</h3>
                            </div>
                            <ul class="space-y-1">
                                <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group">
                                    <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#name#</code>
                                    <span class="text-gray-300 text-xs group-hover:text-gray-200">- Nama</span>
                                </li>
                                <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group">
                                    <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#wa#</code>
                                    <span class="text-gray-300 text-xs group-hover:text-gray-200">- No. Whatsapp</span>
                                </li>
                                <li class="flex items-center space-x-2 hover:bg-purple-500/30 rounded p-1 transition-all duration-200 cursor-pointer group">
                                    <code class="text-purple-200 font-mono text-xs group-hover:text-purple-100">#message#</code>
                                    <span class="text-gray-300 text-xs group-hover:text-gray-200">- Isi Pesan</span>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Textarea -->
                        <textarea 
                            id="messageTemplate"
                            name="message_template"
                            rows="4"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                            required
                        >{{ old('message_template', \App\Models\Configuration::getValue('bantuan_template', "Pesan baru dari #name#\nNo. Whatsapp: #wa#\nPesan: #message#")) }}</textarea>
                    </div>
                </div>
            
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll-zoom">
                <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const whatsappInput = document.getElementById('whatsappNumber');
    
    // Auto-format phone number
    whatsappInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Format as Indonesian phone number
        if (value.length > 0) {
            if (value.startsWith('62')) {
                value = value.substring(2);
            }
            if (value.startsWith('0')) {
                value = value.substring(1);
            }
            
            // Format with spaces
            if (value.length > 0) {
                value = value.match(/.{1,4}/g).join(' ');
            }
        }
        
        e.target.value = value;
    });
});
</script>
@endpush
@endsection
