@extends('admin.layouts.app')

@section('title', 'Kelola Icon - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 animate-on-scroll-zoom">
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

    <!-- Header Section -->
    <div class="mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white animate-on-scroll">Kelola Icon</h1>
        </div>
    </div>

    <!-- Icon Management Section -->
    <div class="bg-gradient-to-r from-gray-900/50 via-gray-800/50 to-gray-900/50 border border-gray-700/30 rounded-xl p-6 backdrop-blur-sm mb-8 animate-on-scroll-zoom">
        <!-- Tambah Icon Form -->
        <form action="{{ route('admin.icons.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <!-- Nama Icon -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="name" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Nama Icon</label>
                    <div class="w-full sm:w-110">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base bg-gray-800/50 border border-gray-600/30 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama icon"
                            required
                        >
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Pilih File Icon -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="icon" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Tambah Icon</label>
                    <div class="w-full sm:w-110">
                        <input 
                            type="file" 
                            id="icon" 
                            name="icon" 
                            accept="image/*"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600 @error('icon') border-red-500 @enderror"
                            required
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 64x64 px</p>
                        @error('icon')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8 animate-on-scroll-zoom">
                <!-- Right Side Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto">
                    <a href="{{ route('admin.products.index') }}" class="text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto" style="cursor: pointer;">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto animate-on-scroll-zoom">
        <table class="min-w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
            <thead>
                <tr class="border-b border-gray-700/30">
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Gambar Icon</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/30">
                @forelse($icons as $index => $icon)
                <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll-zoom">
                    <td class="px-6 py-4 text-sm text-white">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div class="text-white font-medium">{{ $icon->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $icon->file_path) }}" alt="{{ $icon->name }}" class="w-16 h-16 object-contain rounded-lg">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3 animate-on-scroll-zoom animate-on-scroll-delay-1">
                            <button 
                                class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" 
                                title="Hapus" 
                                style="cursor: pointer;"
                                data-url="{{ route('admin.icons.destroy', $icon) }}"
                                data-item-name="icon '{{ $icon->name }}'"
                                data-message="Apakah Anda yakin ingin menghapus icon '{{ $icon->name }}'? Tindakan ini tidak dapat dibatalkan dan gambar icon akan terhapus permanen."
                            >
                                <i class="fas fa-trash text-xs"></i>
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">Tidak ada data icon</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
