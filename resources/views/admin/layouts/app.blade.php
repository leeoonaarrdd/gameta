<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Gameta')</title>
    @php
        $favicon = \App\Models\Configuration::getValue('favicon', '');
    @endphp
    @if(!empty($favicon))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/scroll-animations.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            overflow-y: auto;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .main-content {
            margin-left: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
        }
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 280px;
            }
        }
        .content-area {
            flex: 1;
        }
        /* Custom scrollbar untuk sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
        }
        /* Overlay untuk mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }
        .sidebar-overlay.open {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body class="bg-gray-900 text-white" x-data="{ sidebarOpen: false }">
    <!-- Background Pattern -->
    <div class="fixed inset-0 -z-10">
        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-950 to-purple-950"></div>
        
        <!-- Animated Grid Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(139, 92, 246, 0.3) 1px, transparent 0); background-size: 30px 30px; @media (min-width: 768px) { background-size: 50px 50px; }"></div>
        </div>
        
        <!-- Floating Orbs -->
        <div class="absolute top-1/4 left-1/4 w-32 h-32 sm:w-48 sm:h-48 md:w-64 md:h-64 bg-purple-500/10 rounded-full blur-2xl md:blur-3xl animate-pulse"></div>
        <div class="absolute top-3/4 right-1/4 w-40 h-40 sm:w-64 sm:h-64 md:w-96 md:h-96 bg-purple-600/5 rounded-full blur-2xl md:blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 bg-purple-400/8 rounded-full blur-2xl md:blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        
        <!-- Geometric Shapes -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-48 sm:w-64 sm:h-64 md:w-96 md:h-96 border border-purple-500/10 rotate-45"></div>
        <div class="absolute top-1/3 right-1/3 w-16 h-16 sm:w-24 sm:h-24 md:w-32 md:h-32 border border-purple-400/15 rotate-12"></div>
        <div class="absolute bottom-1/3 left-1/4 w-12 h-12 sm:w-16 sm:h-16 md:w-24 md:h-24 border border-purple-300/20 -rotate-45"></div>
        
        <!-- Noise Texture -->
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E');"></div>
    </div>

    <!-- Sidebar Overlay untuk mobile -->
    <div class="sidebar-overlay lg:hidden" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="sidebar bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 border-r border-gray-700/30" :class="{ 'open': sidebarOpen }">
        <!-- Logo Section -->
        <div class="p-6 border-b border-gray-700/30">
            <div class="flex items-center justify-between lg:justify-center">
                <a href="/admin" class="flex items-center justify-center group">
                    @php
                        $logo = \App\Models\Configuration::getValue('logo', '');
                    @endphp
                    @if(!empty($logo))
                        <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-48 h-12 object-contain transform group-hover:scale-110 transition-transform duration-300">
                    @else
                        <svg class="w-8 h-8 text-white transform group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.721 12.752a9.711 9.711 0 00-.945-5.003 12.754 12.754 0 01-4.339 2.708 18.991 18.991 0 01-.214 4.772 17.165 17.165 0 005.498-2.477zM14.634 15.55a17.324 17.324 0 00.332-4.647c-.952.227-1.945.347-2.966.347-1.021 0-2.014-.12-2.966-.347a17.515 17.515 0 00.332 4.647 17.385 17.385 0 005.268 0zM9.772 17.119a18.963 18.963 0 004.456 0A17.182 17.182 0 0112 21.724a17.18 17.18 0 01-2.228-4.605zM7.777 15.23a18.87 18.87 0 01-.214-4.774 12.753 12.753 0 01-4.34-2.708 9.711 9.711 0 00-.944 5.004 17.165 17.165 0 005.498 2.477zM21.356 14.752a9.765 9.765 0 01-7.478 6.817 18.64 18.64 0 001.988-4.718 18.627 18.627 0 005.49-2.098zM2.644 14.752c1.682.971 3.53 1.688 5.49 2.099a18.64 18.64 0 001.988 4.718 9.765 9.765 0 01-7.478-6.816zM13.878 2.43a9.755 9.755 0 016.116 3.986 11.267 11.267 0 01-3.746 2.504 18.63 18.63 0 00-2.37-6.49zM12 2.276a17.152 17.152 0 012.805 7.121c-.897.23-1.837.353-2.805.353-.968 0-1.908-.122-2.805-.353A17.151 17.151 0 0112 2.276zM10.122 2.43a18.629 18.629 0 00-2.37 6.49 11.266 11.266 0 01-3.746-2.504 9.755 9.755 0 016.116-3.985z"/>
                        </svg>
                    @endif
                </a>
                <!-- Close button untuk mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden p-2 text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4">
            <!-- Menu Utama -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-4">Menu Utama</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/dashboard" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-home-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/admin/configuration" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-cogs w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Konfigurasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.admins.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-user-shield w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Admin
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.members.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-users w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Member
                        </a>
                    </li>
                    <li>
                        <a href="/admin/categories" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-tags w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Kategori
                        </a>
                    </li>
                    <li>
                        <a href="/admin/targets" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-bullseye w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Sistem Target
                        </a>
                    </li>
                    <li>
                        <a href="/admin/games" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-dice w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Games
                        </a>
                    </li>
                    <li>
                        <a href="/admin/products" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-box w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Produk
                        </a>
                    </li>
                    <li>
                        <a href="/admin/purchases" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-shopping-cart w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Pembelian
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.topups.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-credit-card w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Topup
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-money-bill-wave w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Metode
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.whatsapp.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fab fa-whatsapp w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Pesan WhatsApp
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Menu Lainnya -->
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-4">Menu Lainnya</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/banners" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-images w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Kelola Banner
                        </a>
                    </li>
                    <li>
                        <a href="/admin/social-media" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-share-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Sosial Media
                        </a>
                    </li>
                    <li>
                        <a href="/admin/content" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-file-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Konten Halaman
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.faqs.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-question-circle w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Pertanyaan Umum
                        </a>
                    </li>
                    <li>
                        <a href="/admin/bantuan" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group">
                            <i class="fas fa-headset w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Bantuan
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="bg-transparent px-6 py-3">
            <div class="flex items-center justify-between">
                <!-- Hamburger Menu untuk mobile -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                
                <!-- Spacer untuk desktop -->
                <div class="hidden lg:block flex-1"></div>
                
                <!-- User Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center p-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </button>
                    <!-- Dropdown menu -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-md shadow-lg z-20 origin-top-right">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800/50 hover:text-white">Profil Saya</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800/50 hover:text-white">Pengaturan</a>
                        <div class="border-t border-gray-700/30"></div>
                        <form method="POST" action="{{ route('admin.logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-gray-800/50 hover:text-red-300">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area p-6">
            @yield('content')
        </main>
    </div>

    <!-- Alpine.js for dropdown functionality -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <!-- Admin Global JavaScript -->
    <script src="{{ asset('js/admin-global.js') }}"></script>
    
    @stack('scripts')
    <script src="{{ asset('js/scroll-animations.js') }}"></script>
</body>
</html> 