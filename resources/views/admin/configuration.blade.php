@extends('admin.layouts.app')

@section('title', 'Konfigurasi Website')

@section('content')
<div class="mx-auto max-w-4xl px-2 sm:px-4 lg:px-8">

    <!-- Tab Navigation -->
    <div class="flex justify-center mb-6 animate-on-scroll-bounce">
        <div class="flex bg-gradient-to-br from-gray-950/90 via-gray-900/80 to-gray-950/90 backdrop-blur-sm rounded-lg border border-gray-700/30 p-1">
            <button id="tab-umum" class="tab-button px-6 py-3 text-sm font-medium text-white bg-purple-500 rounded-lg transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-1 cursor-pointer" onclick="switchTab('umum')">
                Umum
            </button>
            <button id="tab-tripay" class="tab-button px-6 py-3 text-sm font-medium text-gray-300 rounded-lg hover:text-white transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-2 cursor-pointer" onclick="switchTab('tripay')">
                Tripay
            </button>
            <button id="tab-digiflazz" class="tab-button px-6 py-3 text-sm font-medium text-gray-300 rounded-lg hover:text-white transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-3 cursor-pointer" onclick="switchTab('digiflazz')">
                Digiflazz
            </button>
            <button id="tab-fonnte" class="tab-button px-6 py-3 text-sm font-medium text-gray-300 rounded-lg hover:text-white transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-4 cursor-pointer" onclick="switchTab('fonnte')">
                Fonnte
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tab-content-umum" class="tab-content">
        <!-- Form Section Umum -->
        <div class="bg-gradient-to-br from-gray-950/90 via-gray-900/80 to-gray-950/90 backdrop-blur-sm rounded-xl border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
            @if(session('success_umum'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                    {{ session('success_umum') }}
                </div>
            @endif

            @if($errors->any() && (old('active_tab') == 'umum' || !old('active_tab')))
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.configuration.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_tab" value="umum">
                
                <!-- Form Fields -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Website Title -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                        <label for="website_title" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Judul Website</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-globe text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="website_title" 
                                name="website_title" 
                                value="{{ old('website_title', $config['website_title'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Homepage Title -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                        <label for="homepage_title" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Judul Halaman Utama</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-home text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="homepage_title" 
                                name="homepage_title" 
                                value="{{ old('homepage_title', $config['homepage_title'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Logo -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                        <label for="logo" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Logo Website</label>
                        <div class="w-full sm:w-110">
                            <div id="logo-preview">
                                @if(!empty($config['logo']))
                                    <img src="{{ asset('storage/' . $config['logo']) }}" alt="Logo Preview" class="max-w-full max-h-full object-contain rounded-lg">
                                @endif
                            </div>
                            <input 
                                type="file" 
                                id="logo" 
                                name="logo" 
                                accept="image/*"
                                class="w-full rounded-full px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                                onchange="previewImage(this, 'logo-preview')"
                            >
                            <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 300x100 px</p>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="favicon" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Icon/Favicon</label>
                        <div class="w-full sm:w-110">
                            <div id="favicon-preview">
                                @if(!empty($config['favicon']))
                                    <img src="{{ asset('storage/' . $config['favicon']) }}" alt="Favicon Preview" class="max-w-full max-h-full object-contain rounded-lg">
                                @endif
                            </div>
                            <input 
                                type="file" 
                                id="favicon" 
                                name="favicon" 
                                accept="image/*,.ico"
                                class="w-full rounded-full px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                                onchange="previewImage(this, 'favicon-preview')"
                            >
                            <p class="text-gray-400 text-xs sm:text-sm mt-1">Gunakan ukuran gambar 128x128 px</p>
                        </div>
                    </div>

                    <!-- Keywords -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="keywords" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Keywords</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tags text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="keywords" 
                                name="keywords" 
                                value="{{ old('keywords', $config['keywords'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Author -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="author" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Author</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="author" 
                                name="author" 
                                value="{{ old('author', $config['author'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="description" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Deskripsi</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                <i class="fas fa-align-left text-gray-400 mt-1"></i>
                            </div>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="4"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-lg text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 resize-none backdrop-blur-sm"
                                required
                            >{{ old('description', $config['description'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll animate-on-scroll-delay-4">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="tab-content-tripay" class="tab-content hidden">
        <!-- Form Section Tripay -->
        <div class="bg-gradient-to-br from-gray-950/90 via-gray-900/80 to-gray-950/90 backdrop-blur-sm rounded-xl border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
            @if(session('success_tripay'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                    {{ session('success_tripay') }}
                </div>
            @endif

            @if($errors->any() && old('active_tab') == 'tripay')
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.configuration.update-tripay') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_tab" value="tripay">
                
                <!-- Form Fields Tripay -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- API Key -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                        <label for="tripay_api_key" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">API Key</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input 
                                type="password" 
                                id="tripay_api_key" 
                                name="tripay_api_key" 
                                value="{{ old('tripay_api_key', $config['tripay_api_key'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Private Key -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                        <label for="tripay_private_key" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Private Key</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input 
                                type="password" 
                                id="tripay_private_key" 
                                name="tripay_private_key" 
                                value="{{ old('tripay_private_key', $config['tripay_private_key'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Kode Merchant -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                        <label for="tripay_merchant_code" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kode Merchant</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-store text-gray-400"></i>
                            </div>
                            <input 
                                type="password" 
                                id="tripay_merchant_code" 
                                name="tripay_merchant_code" 
                                value="{{ old('tripay_merchant_code', $config['tripay_merchant_code'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- URL Callback -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="tripay_callback_url" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">URL Callback</label>
                        <div class="w-full sm:w-110">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-gray-400"></i>
                                </div>
                                <input 
                                    type="url" 
                                    id="tripay_callback_url" 
                                    name="tripay_callback_url" 
                                    value="{{ url('/api/tripay/callback') }}"
                                    class="w-full pl-10 pr-12 py-3 bg-gray-700/50 border border-gray-600/30 rounded-full text-gray-300 focus:outline-none cursor-not-allowed backdrop-blur-sm"
                                    readonly
                                >
                                <button type="button" onclick="copyToClipboard('tripay_callback_url')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll animate-on-scroll-delay-4">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="tab-content-digiflazz" class="tab-content hidden">
        <!-- Form Section Digiflazz -->
        <div class="bg-gradient-to-br from-gray-950/90 via-gray-900/80 to-gray-950/90 backdrop-blur-sm rounded-xl border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
            @if(session('success_digiflazz'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                    {{ session('success_digiflazz') }}
                </div>
            @endif

            @if($errors->any() && old('active_tab') == 'digiflazz')
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.configuration.update-digiflazz') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_tab" value="digiflazz">
                
                <!-- Form Fields Digiflazz -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Username -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                        <label for="digiflazz_username" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Username</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="digiflazz_username" 
                                name="digiflazz_username" 
                                value="{{ old('digiflazz_username', $config['digiflazz_username'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- Production Key -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-2">
                        <label for="digiflazz_production_key" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Production Key</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="digiflazz_production_key" 
                                name="digiflazz_production_key" 
                                value="{{ old('digiflazz_production_key', $config['digiflazz_production_key'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                required
                            >
                        </div>
                    </div>

                    <!-- URL Callback -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-3">
                        <label for="digiflazz_callback_url" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">URL Callback</label>
                        <div class="w-full sm:w-110">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-gray-400"></i>
                                </div>
                                <input 
                                    type="url" 
                                    id="digiflazz_callback_url" 
                                    name="digiflazz_callback_url" 
                                    value="{{ url('/api/digiflazz/callback') }}"
                                    class="w-full pl-10 pr-12 py-3 bg-gray-700/50 border border-gray-600/30 rounded-full text-gray-300 focus:outline-none cursor-not-allowed backdrop-blur-sm"
                                    readonly
                                >
                                <button type="button" onclick="copyToClipboard('digiflazz_callback_url')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Webhook ID -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="digiflazz_webhook_id" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Webhook ID</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-webhook text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="digiflazz_webhook_id" 
                                name="digiflazz_webhook_id" 
                                value="{{ old('digiflazz_webhook_id', $config['digiflazz_webhook_id'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                            >
                        </div>
                    </div>

                    <!-- Secret -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-4">
                        <label for="digiflazz_secret" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Secret</label>
                        <div class="w-full sm:w-110 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="digiflazz_secret" 
                                name="digiflazz_secret" 
                                value="{{ old('digiflazz_secret', $config['digiflazz_secret'] ?? '') }}"
                                class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll animate-on-scroll-delay-4">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="tab-content-fonnte" class="tab-content hidden">
        <!-- Form Section Fonnte -->
        <div class="bg-gradient-to-br from-gray-950/90 via-gray-900/80 to-gray-950/90 backdrop-blur-sm rounded-xl border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
            @if(session('success_fonnte'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                    {{ session('success_fonnte') }}
                </div>
            @endif

            @if($errors->any() && old('active_tab') == 'fonnte')
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.configuration.update-fonnte') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active_tab" value="fonnte">
                
                <!-- Form Fields Fonnte -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Fonnte Token -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4 animate-on-scroll animate-on-scroll-delay-1">
                        <label for="fonnte_token" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Token Fonnte</label>
                        <div class="w-full sm:w-110">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fab fa-whatsapp text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="fonnte_token" 
                                    name="fonnte_token" 
                                    value="{{ old('fonnte_token', $config['fonnte_token'] ?? '') }}"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                    required
                                >
                            </div>
                            <div class="mt-2 text-gray-400 text-xs animate-on-scroll animate-on-scroll-delay-2">
                                <p class="font-medium mb-1">Informasi:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Silahkan login ke web Fonnte, jika belum punya akun bisa registrasi <a href="https://fonnte.com/register" target="_blank" class="text-purple-400 hover:text-purple-300">disini</a></li>
                                    <li>Lalu masuk ke menu Device</li>
                                    <li>Lalu klik Add Device untuk menambahkan Whatsapp</li>
                                    <li>Lalu klik Connect untuk menghubungkan Whatsapp</li>
                                    <li>Silahkan Order paket untuk menghilangkan Copryright pada pesan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 sm:mt-8 animate-on-scroll animate-on-scroll-delay-3">
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('bg-purple-500', 'text-white');
        button.classList.add('text-gray-300');
    });

    // Show selected tab content
    document.getElementById(`tab-content-${tabName}`).classList.remove('hidden');

    // Add active class to selected tab button
    document.getElementById(`tab-${tabName}`).classList.remove('text-gray-300');
    document.getElementById(`tab-${tabName}`).classList.add('bg-purple-500', 'text-white');
}

// Check if there are errors or success messages and switch to appropriate tab
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are validation errors for Tripay fields
    const tripayErrors = document.querySelector('#tab-content-tripay .text-red-400');
    if (tripayErrors) {
        switchTab('tripay');
    }
    
    // Check if there are validation errors for Umum fields
    const umumErrors = document.querySelector('#tab-content-umum .text-red-400');
    if (umumErrors) {
        switchTab('umum');
    }
    
    // Check if there are validation errors for Digiflazz fields
    const digiflazzErrors = document.querySelector('#tab-content-digiflazz .text-red-400');
    if (digiflazzErrors) {
        switchTab('digiflazz');
    }
    
    // Check if there are validation errors for Fonnte fields
    const fonnteErrors = document.querySelector('#tab-content-fonnte .text-red-400');
    if (fonnteErrors) {
        switchTab('fonnte');
    }
    
    // Check if there are success messages for Tripay
    const tripaySuccess = document.querySelector('#tab-content-tripay .text-green-400');
    if (tripaySuccess) {
        switchTab('tripay');
    }
    
    // Check if there are success messages for Umum
    const umumSuccess = document.querySelector('#tab-content-umum .text-green-400');
    if (umumSuccess) {
        switchTab('umum');
    }
    
    // Check if there are success messages for Digiflazz
    const digiflazzSuccess = document.querySelector('#tab-content-digiflazz .text-green-400');
    if (digiflazzSuccess) {
        switchTab('digiflazz');
    }
    
    // Check if there are success messages for Fonnte
    const fonnteSuccess = document.querySelector('#tab-content-fonnte .text-green-400');
    if (fonnteSuccess) {
        switchTab('fonnte');
    }
    
    // Check for session flash messages
    @if(session('active_tab'))
        switchTab('{{ session('active_tab') }}');
    @endif
    
    // Check for validation errors and switch to appropriate tab
    @if($errors->any() && old('active_tab'))
        switchTab('{{ old('active_tab') }}');
    @endif
});

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="max-w-full max-h-full object-contain rounded-lg">`;
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        // Clear preview if no file selected
        preview.innerHTML = '';
    }
}

function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}

function testDigiflazzConnection() {
    const resultDiv = document.getElementById('digiflazz-test-result');
    const messageDiv = document.getElementById('digiflazz-test-message');
    
    resultDiv.classList.remove('hidden');
    messageDiv.innerHTML = '<div class="text-yellow-400">Testing koneksi ke Digiflazz...</div>';
    
    fetch('/api/digiflazz/test-connection')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.innerHTML = `<div class="text-green-400">✓ ${data.message}</div>`;
                if (data.data) {
                    messageDiv.innerHTML += `<div class="text-gray-300 mt-2">Saldo: Rp ${data.data.deposit || 'N/A'}</div>`;
                }
            } else {
                messageDiv.innerHTML = `<div class="text-red-400">✗ ${data.message}</div>`;
            }
        })
        .catch(error => {
            messageDiv.innerHTML = `<div class="text-red-400">✗ Error: ${error.message}</div>`;
        });
}

function checkDigiflazzBalance() {
    const resultDiv = document.getElementById('digiflazz-test-result');
    const messageDiv = document.getElementById('digiflazz-test-message');
    
    resultDiv.classList.remove('hidden');
    messageDiv.innerHTML = '<div class="text-yellow-400">Mengecek saldo Digiflazz...</div>';
    
    fetch('/api/digiflazz/balance')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const balanceData = data.data;
                messageDiv.innerHTML = `<div class="text-green-400">✓ Saldo berhasil diambil</div>`;
                if (balanceData.data) {
                    messageDiv.innerHTML += `<div class="text-gray-300 mt-2">Saldo: Rp ${balanceData.data.deposit || 'N/A'}</div>`;
                }
            } else {
                messageDiv.innerHTML = `<div class="text-red-400">✗ ${data.message}</div>`;
            }
        })
        .catch(error => {
            messageDiv.innerHTML = `<div class="text-red-400">✗ Error: ${error.message}</div>`;
        });
}
</script>
@endsection
