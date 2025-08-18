@extends('admin.layouts.app')

@section('title', 'Edit Target - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white animate-on-scroll">Edit Target</h1>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
        <form action="{{ route('admin.targets.update', $target) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6" id="form-fields" class="animate-on-scroll-fade">
                <!-- Judul Target -->
                <div class="flex justify-between items-center gap-4 animate-on-scroll animate-on-scroll-delay-1">
                    <label for="judul_target" class="text-white font-medium w-32 flex-shrink-0">Judul Target</label>
                    <input 
                        type="text" 
                        id="judul_target" 
                        name="judul_target" 
                        value="{{ old('judul_target', $target->judul_target) }}"
                        class="w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                        required
                    >
                </div>

                <!-- Teks Header -->
                <div class="flex justify-between items-center gap-4 animate-on-scroll animate-on-scroll-delay-2">
                    <label for="teks_header" class="text-white font-medium w-32 flex-shrink-0">Teks Header</label>
                    <input 
                        type="text" 
                        id="teks_header" 
                        name="teks_header" 
                        value="{{ old('teks_header', $target->teks_header) }}"
                        class="w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                        required
                    >
                </div>

                <!-- Deskripsi/Petunjuk -->
                <div class="flex justify-between items-start gap-4 animate-on-scroll animate-on-scroll-delay-3">
                    <label for="konten" class="text-white font-medium w-32 flex-shrink-0">Deskripsi/Petunjuk</label>
                    <textarea 
                        id="konten" 
                        name="konten" 
                        rows="4"
                        class="w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none"
                        required
                    >{{ old('konten', $target->konten) }}</textarea>
                </div>

                <!-- Sparator -->
                <div class="flex justify-between items-center gap-4 animate-on-scroll animate-on-scroll-delay-4">
                    <label for="sparator" class="text-white font-medium w-32 flex-shrink-0">Sparator</label>
                    <div class="w-110">
                        <input 
                            type="text" 
                            id="sparator" 
                            name="sparator" 
                            value="{{ old('sparator', $target->sparator) }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                        >
                        <p class="text-gray-400 text-sm mt-1">Silahkan sesuaikan dengan keperluan provider</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-start gap-3 mt-6 animate-on-scroll animate-on-scroll-delay-5" id="action-buttons">
                    <button type="button" id="tambah-input-btn" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer">
                        Tambah Input
                    </button>
                    <button type="button" id="tambah-pilihan-btn" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer">
                        Tambah Pilihan
                    </button>
                </div>
                
                <!-- Existing Fields -->
                @if($target->input_fields)
                    @foreach($target->input_fields as $index => $inputField)
                    <div class="flex items-center gap-4 bg-gray-800/30 rounded-lg p-4 border border-gray-700/30 animate-on-scroll animate-on-scroll-delay-6">
                        <div class="flex flex-col gap-1">
                            <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'up')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'down')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="input_fields[{{ $index }}][judul_kolom]" 
                                value="{{ $inputField['judul_kolom'] }}"
                                placeholder="Judul Kolom"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                                required
                            >
                        </div>
                        <div class="w-32">
                            <select 
                                name="input_fields[{{ $index }}][validasi]" 
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                            >
                                <option value="teks" {{ $inputField['validasi'] == 'teks' ? 'selected' : '' }} class="bg-gray-800 text-white">Teks</option>
                                <option value="angka" {{ $inputField['validasi'] == 'angka' ? 'selected' : '' }} class="bg-gray-800 text-white">Angka</option>
                                <option value="email" {{ $inputField['validasi'] == 'email' ? 'selected' : '' }} class="bg-gray-800 text-white">Email</option>
                                <option value="password" {{ $inputField['validasi'] == 'password' ? 'selected' : '' }} class="bg-gray-800 text-white">Password</option>
                            </select>
                        </div>
                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg transition-colors duration-200" onclick="removeField(this)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                @endif
                
                @if($target->option_fields)
                    @foreach($target->option_fields as $index => $optionField)
                    <div class="flex items-center gap-4 bg-gray-800/30 rounded-lg p-4 border border-gray-700/30 animate-on-scroll animate-on-scroll-delay-7">
                        <div class="flex flex-col gap-1">
                            <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'up')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'down')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="w-110">
                            <input 
                                type="text" 
                                name="option_fields[{{ $index }}][pilihan]" 
                                value="{{ isset($optionField['judul_kolom']) && !empty($optionField['judul_kolom']) ? $optionField['judul_kolom'] : '' }}"
                                data-pilihan="{{ json_encode($optionField['pilihan'] ?? []) }}"
                                placeholder="Judul Kolom"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                                required
                            >
                            <input 
                                type="hidden" 
                                name="option_fields[{{ $index }}][pilihan_data]" 
                                value="{{ json_encode($optionField['pilihan'] ?? []) }}"
                            >
                        </div>
                        <button type="button" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-2 rounded-full font-medium transition-all duration-200" onclick="openModal(this.closest('.flex.items-center.gap-4'))">
                            Pilihan
                        </button>
                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg transition-colors duration-200" onclick="removeField(this)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-8 animate-on-scroll animate-on-scroll-delay-8">
                <!-- Right Side Buttons -->
                <div class="flex items-center gap-3 ml-auto">
                    <button type="button" onclick="window.history.back()" class="text-white hover:text-gray-300 transition-colors duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-6 py-3 rounded-full font-medium transition-all duration-200 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal List Pilihan -->
<div id="modal-pilihan" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
        
        <!-- Modal panel -->
        <div class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl">
            <!-- Modal header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">List Pilihan</h3>
                <button type="button" id="tambah-row-pilihan" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer">
                    Tambah
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="mb-6">
                <!-- Header Table -->
                <div class="flex items-center mb-4">
                    <div class="flex gap-4 text-gray-300 text-sm font-medium">
                        <span class="w-36">Nilai Provider</span>
                        <span class="w-36">Nilai Validasi</span>
                        <span class="w-36">Judul</span>
                    </div>
                </div>
                
                <!-- Table Body -->
                <div id="table-pilihan-body" class="space-y-3">
                    <!-- Row akan ditambahkan secara dinamis -->
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="flex justify-end gap-3">
                <button type="button" id="batal-modal-pilihan" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-colors duration-200 cursor-pointer">
                    Batal
                </button>
                <button type="button" id="simpan-modal-pilihan" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-6 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/targets.js"></script>
@endsection 