@extends('admin.layouts.app')

@section('title', 'Edit Metode Pembayaran - Admin Panel')

@section('content')
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
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Edit Metode Pembayaran</h1>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Nama Metode -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="name" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Nama Metode</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $paymentMethod->name) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        placeholder="Contoh: Transfer Bank BCA"
                        required
                    >
                </div>

                <!-- Gambar -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="image" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Gambar</label>
                    <div class="w-full sm:w-110">
                        @if($paymentMethod->image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($paymentMethod->image) }}" alt="Current Image" class="w-32 h-20 object-contain">
                            </div>
                        @endif
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 300x100 px</p>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="kategori" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kategori</label>
                    <select 
                        id="kategori" 
                        name="kategori" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('kategori', $paymentMethod->kategori) == $category->name ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Provider -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="provider" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Provider</label>
                    <select 
                        id="provider" 
                        name="provider" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih provider</option>
                        <option value="Manual" {{ old('provider', $paymentMethod->provider) == 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Tripay" {{ old('provider', $paymentMethod->provider) == 'Tripay' ? 'selected' : '' }}>Tripay</option>
                    </select>
                </div>

                <!-- Kode Metode -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="method_code" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kode Metode</label>
                    <div class="w-full sm:w-110">
                        <input 
                            type="text" 
                            id="method_code" 
                            name="method_code" 
                            value="{{ old('method_code', $paymentMethod->method_code) }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                            required
                        >
                        <div class="mt-2 text-gray-400 text-xs">
                            <p class="font-medium mb-1">Informasi:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Silahkan isi dengan No. Rekening jika menggunakan provider Manual</li>
                                <li>Silahkan isi dengan <a href="https://tripay.co.id/developer?tab=channels" target="_blank" class="text-purple-400 hover:text-purple-300">Kode Metode</a> jika menggunakan provider Tripay</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Biaya Admin -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="admin_fee" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Biaya Admin</label>
                    <div class="w-full sm:w-110 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm font-medium">Rp</span>
                                </div>
                                <input 
                                    type="number" 
                                    id="admin_fee" 
                                    name="admin_fee" 
                                    value="{{ old('admin_fee', $paymentMethod->admin_fee) }}"
                                    min="0"
                                    class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                                    style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                                >
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-400 text-sm font-medium">%</span>
                                </div>
                                <input 
                                    type="number" 
                                    id="admin_fee_percentage" 
                                    name="admin_fee_percentage" 
                                    value="{{ old('admin_fee_percentage', $paymentMethod->admin_fee_percentage) }}"
                                    min="0"
                                    max="100"
                                    class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                                    style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                                >
                            </div>
                        </div>
                        <div class="text-gray-400 text-xs">
                            <p class="font-medium mb-1">Informasi:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Field kiri untuk biaya admin flat (nominal tetap)</li>
                                <li>Field kanan untuk biaya admin persen (%)</li>
                                <li>Isi salah satu field saja sesuai kebutuhan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Kode Unik -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="has_unique_code" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kode Unik</label>
                    <select 
                        id="has_unique_code" 
                        name="has_unique_code" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih opsi</option>
                        <option value="1" {{ old('has_unique_code', $paymentMethod->has_unique_code) == '1' ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ old('has_unique_code', $paymentMethod->has_unique_code) == '0' ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="is_active" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Status</label>
                    <select 
                        id="is_active" 
                        name="is_active" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih status</option>
                        <option value="1" {{ old('is_active', $paymentMethod->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $paymentMethod->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8 animate-on-scroll-fade">
                <!-- Right Side Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto animate-on-scroll-fade">
                    <button type="button" onclick="window.history.back()" class="text-white hover:text-gray-300 transition-colors duration-200 text-sm sm:text-base w-full sm:w-auto text-center cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
