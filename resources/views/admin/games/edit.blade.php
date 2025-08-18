@extends('admin.layouts.app')

@section('title', 'Edit Games - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Edit Games</h1>
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

        <form action="{{ route('admin.games.update', $game) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Logo -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="logo" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Logo</label>
                    <div class="w-full sm:w-110">
                        @if($game->logo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($game->logo) }}" alt="Current Logo" class="w-32 h-20 object-contain">
                            </div>
                        @endif
                        <input 
                            type="file" 
                            id="logo" 
                            name="logo" 
                            accept="image/*"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 300x100 px</p>
                    </div>
                </div>

                <!-- Gambar -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="gambar" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Gambar</label>
                    <div class="w-full sm:w-110">
                        @if($game->gambar)
                            <div class="mb-2">
                                <img src="{{ Storage::url($game->gambar) }}" alt="Current Gambar" class="w-32 h-48 object-cover rounded-lg">
                            </div>
                        @endif
                        <input 
                            type="file" 
                            id="gambar" 
                            name="gambar" 
                            accept="image/*"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 6000x800 px</p>
                    </div>
                </div>

                <!-- Banner -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="banner" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Banner</label>
                    <div class="w-full sm:w-110">
                        @if($game->banner)
                            <div class="mb-2">
                                <img src="{{ Storage::url($game->banner) }}" alt="Current Banner" class="w-80 h-28 object-cover rounded-lg">
                            </div>
                        @endif
                        <input 
                            type="file" 
                            id="banner" 
                            name="banner" 
                            accept="image/*"
                            class="w-full rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                        >
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 1920x450 px</p>
                    </div>
                </div>

                <!-- Games -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="games" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Games</label>
                    <input 
                        type="text" 
                        id="games" 
                        name="games" 
                        value="{{ old('games', $game->name) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        placeholder="Masukkan nama games"
                        required
                    >
                </div>

                <!-- Sub Judul -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="sub_judul" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Sub Judul</label>
                    <input 
                        type="text" 
                        id="sub_judul" 
                        name="sub_judul" 
                        value="{{ old('sub_judul', $game->sub_judul) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        placeholder="Masukkan sub judul games"
                        required
                    >
                </div>

                <!-- Slug -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="slug" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Slug</label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        value="{{ old('slug', $game->slug) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        placeholder="Masukkan slug games"
                        required
                    >
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
                            <option value="{{ $category->id }}" {{ old('kategori', $game->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sistem Target -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="sistem_target" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Sistem Target</label>
                    <select 
                        id="sistem_target" 
                        name="sistem_target" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih sistem target</option>
                        @foreach($targets as $target)
                            <option value="{{ $target->id }}" {{ old('sistem_target', $game->target_id) == $target->id ? 'selected' : '' }}>{{ $target->judul_target }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Deskripsi -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="deskripsi" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Deskripsi</label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi" 
                        rows="4"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                        placeholder="Masukkan deskripsi games"
                        required
                    >{{ old('deskripsi', $game->description) }}</textarea>
                </div>

                <!-- Status -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="status" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Status</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih status</option>
                        <option value="1" {{ old('status', $game->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status', $game->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8 animate-on-scroll-fade">
                <!-- Right Side Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto animate-on-scroll-fade">
                    <button type="button" onclick="window.history.back()" class="text-white hover:text-gray-300 transition-colors duration-200 text-sm sm:text-base w-full sm:w-auto text-center animate-on-scroll-fade cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto animate-on-scroll-fade cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 