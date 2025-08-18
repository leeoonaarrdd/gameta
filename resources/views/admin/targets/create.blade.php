@extends('admin.layouts.app')

@section('title', 'Tambah Target - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Tambah Target</h1>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
        <form action="{{ route('admin.targets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6" id="form-fields" class="animate-on-scroll-fade">
                <!-- Judul Target -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                    <label for="judul_target" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Judul Target</label>
                    <input 
                        type="text" 
                        id="judul_target" 
                        name="judul_target" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                </div>

                <!-- Teks Header -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                    <label for="teks_header" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Teks Header</label>
                    <input 
                        type="text" 
                        id="teks_header" 
                        name="teks_header" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                </div>

                <!-- Deskripsi/Petunjuk -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                    <label for="konten" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Deskripsi/Petunjuk</label>
                    <textarea 
                        id="konten" 
                        name="konten" 
                        rows="4"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                        required
                    ></textarea>
                </div>

                <!-- Sparator -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                    <label for="sparator" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Sparator</label>
                    <div class="w-full sm:w-110">
                        <input 
                            type="text" 
                            id="sparator" 
                            name="sparator" 
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Silahkan sesuaikan dengan keperluan provider</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-start gap-2 sm:gap-3 mt-4 sm:mt-6" id="action-buttons" class="animate-on-scroll animate-on-scroll-delay-4">
                    <button type="button" id="tambah-input-btn" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-3 sm:px-4 py-2 rounded-full font-medium transition-all duration-200 text-sm sm:text-base cursor-pointer">
                        Tambah Input
                    </button>
                    <button type="button" id="tambah-pilihan-btn" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-3 sm:px-4 py-2 rounded-full font-medium transition-all duration-200 text-sm sm:text-base cursor-pointer">
                        Tambah Pilihan
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8 animate-on-scroll animate-on-scroll-fade">
                <!-- Right Side Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto animate-on-scroll-fade">
                    <button type="button" onclick="window.history.back()" class="text-white hover:text-gray-300 transition-colors duration-200 text-sm sm:text-base w-full sm:w-auto text-center cursor-pointer animate-on-scroll-fade">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer animate-on-scroll-fade">
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
        <div class="relative inline-block w-full max-w-sm sm:max-w-md lg:max-w-xl p-4 sm:p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl">
            <!-- Modal header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2 sm:gap-0">
                <h3 class="text-base sm:text-lg font-semibold text-white">List Pilihan</h3>
                <button type="button" id="tambah-row-pilihan" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-3 sm:px-4 py-2 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto curosr-pointer">
                    Tambah
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="mb-4 sm:mb-6">
                <!-- Header Table -->
                <div class="flex items-center mb-3 sm:mb-4 overflow-x-auto">
                    <div class="flex gap-2 sm:gap-4 text-gray-300 text-xs sm:text-sm font-medium min-w-max">
                        <span class="w-24 sm:w-36">Nilai Provider</span>
                        <span class="w-24 sm:w-36">Nilai Validasi</span>
                        <span class="w-24 sm:w-36">Judul</span>
                    </div>
                </div>
                
                <!-- Table Body -->
                <div id="table-pilihan-body" class="space-y-2 sm:space-y-3">
                    <!-- Row akan ditambahkan secara dinamis -->
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                <button type="button" id="batal-modal-pilihan" class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-300 hover:text-white transition-colors duration-200 w-full sm:w-auto text-center cursor-pointer">
                    Batal
                </button>
                <button type="button" id="simpan-modal-pilihan" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/targets.js"></script>
@endsection 