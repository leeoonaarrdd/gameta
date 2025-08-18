@extends('admin.layouts.app')

@section('title', 'Kelola Kategori Metode Pembayaran - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-600/20 border border-green-500/30 text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-600/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Header -->
    <div class="flex items-center justify-between animate-on-scroll-bounce">
        <h1 class="text-3xl font-bold text-white animate-on-scroll">Kelola Kategori Metode Pembayaran</h1>
    </div>

    <!-- Category Management Section -->
    <div class="bg-gradient-to-r from-gray-900/50 via-gray-800/50 to-gray-900/50 border border-gray-700/30 rounded-xl p-6 backdrop-blur-sm animate-on-scroll-zoom">
        <!-- Tambah Kategori Form -->
        <div class="mb-8">
            <form action="{{ route('admin.payment-method-categories.store') }}" method="POST">
                @csrf
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="name" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Tambah Kategori</label>
                    <div class="w-full sm:w-110">
                        <input 
                            type="text" 
                            id="name" 
                            name="name"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center gap-4 mt-6 sm:mt-8">
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto animate-on-scroll-zoom">
                        <a href="{{ route('admin.payment-methods.index') }}" class="text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700/30 my-6"></div>

        @if(count($categories) > 0)
            <!-- Data Table -->
            <div class="overflow-x-auto animate-on-scroll-zoom">
                <table class="min-w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                    <thead>
                        <tr class="border-b border-gray-700/30">
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($categories as $index => $category)
                        <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll-zoom">
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-white font-medium">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 animate-on-scroll-zoom animate-on-scroll-delay-1">
                                    <button 
                                        class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1"
                                        title="Hapus"
                                        data-url="{{ route('admin.payment-method-categories.destroy', $category) }}"
                                        data-item-name="kategori metode pembayaran '{{ $category->name }}'"
                                        data-message="Apakah Anda yakin ingin menghapus kategori metode pembayaran '{{ $category->name }}'? Semua metode pembayaran yang terkait dengan kategori ini akan terpengaruh."
                                    >
                                        <i class="fas fa-trash text-xs"></i>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-tags text-6xl"></i>
                </div>
                <p class="text-gray-400">Belum ada kategori metode pembayaran yang tersedia</p>
            </div>
        @endif
    </div>
</div>
</div>

@endsection


