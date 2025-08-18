@extends('admin.layouts.app')

@section('title', 'Kelola Banner - Admin Panel')

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
        <h1 class="text-3xl font-bold text-white animate-on-scroll">Kelola Banner</h1>
    </div>

    <!-- Banner Management Section -->
    <div class="bg-gradient-to-r from-gray-900/50 via-gray-800/50 to-gray-900/50 border border-gray-700/30 rounded-xl p-6 backdrop-blur-sm animate-on-scroll-zoom">
        <!-- Tambah Banner Form -->
        <div class="mb-8 animate-on-scroll-zoom">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="banner" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Tambah Banner</label>
                    <div class="w-full sm:w-110">
                        <input 
                            type="file" 
                            id="banner" 
                            name="banner" 
                            accept="image/*"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                            required
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 1200 x 450 px</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8">
                    <!-- Right Side Buttons -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto animate-on-scroll-zoom">
                        <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
                @error('banner')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700/30 my-6"></div>


        
        @if(count($banners) > 0)
            <!-- Data Table -->
            <div class="overflow-x-auto animate-on-scroll-zoom">
                <table class="w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                    <tbody class="divide-y divide-gray-700/30">
                        @foreach($banners as $banner)
                        <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll-zoom">
                            <td class="px-6 py-4 w-full">
                                <div class="flex items-center justify-between animate-on-scroll-zoom animate-on-scroll-delay-1 animate-on-scroll-slide-up">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $banner['path'] }}" alt="Banner" class="w-80 h-24 object-cover rounded-lg">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button 
                                            class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" 
                                            title="Hapus"
                                            data-url="{{ route('admin.banners.destroy', $banner['name']) }}"
                                            data-item-name="banner '{{ $banner['name'] }}'"
                                            data-message="Apakah Anda yakin ingin menghapus banner '{{ $banner['name'] }}'? Tindakan ini tidak dapat dibatalkan dan banner akan terhapus permanen."
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                            Hapus
                                        </button>
                                    </div>
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
                    <i class="fas fa-images text-6xl"></i>
                </div>
                <p class="text-gray-400">Belum ada banner yang tersedia</p>
            </div>
        @endif
    </div>
</div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/banners.js') }}"></script>
@endpush 