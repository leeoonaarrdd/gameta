@extends('admin.layouts.app')

@section('title', 'Edit Pertanyaan Umum - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Edit Pertanyaan Umum</h1>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
                {{ session('error') }}
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

        <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Kategori -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="kategori" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kategori</label>
                    <select 
                        id="kategori" 
                        name="kategori" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base"
                        required
                    >
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('kategori', $faq->kategori) == $category->name ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pertanyaan -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="pertanyaan" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Pertanyaan</label>
                    <textarea 
                        id="pertanyaan" 
                        name="pertanyaan" 
                        rows="3"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                        required
                    >{{ old('pertanyaan', $faq->pertanyaan) }}</textarea>
                </div>

                <!-- Konten -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll-zoom">
                    <label for="konten" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Konten</label>
                    <textarea 
                        id="konten" 
                        name="konten" 
                        rows="6"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 resize-none text-sm sm:text-base"
                        required
                    >{{ old('konten', $faq->konten) }}</textarea>
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
