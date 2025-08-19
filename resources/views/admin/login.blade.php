<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Administrator</title>
    @php
        $favicon = \App\Models\Configuration::getValue('favicon', '');
    @endphp
    @if(!empty($favicon))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/scroll-animations.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
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

    <div class="flex flex-col lg:flex-row min-h-screen">
        <!-- Left Panel - Illustration -->
        <div class="lg:hidden w-full bg-transparent relative overflow-hidden py-8 animate-on-scroll-fade">
            <div class="flex flex-col items-center justify-center">
                <!-- Monitor Illustration -->
                <div class="relative mb-4 animate-on-scroll-bounce">
                    <!-- Monitor -->
                    <div class="w-32 h-24 bg-gradient-to-br from-gray-700 via-gray-600 to-gray-800 rounded-lg shadow-2xl relative transform hover:scale-105 transition-transform duration-300">
                        <!-- Screen -->
                        <div class="w-28 h-20 bg-gradient-to-br from-blue-50 to-gray-100 rounded-md absolute top-2 left-2 flex flex-col items-center justify-center overflow-hidden">
                            <!-- Animated Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 via-blue-400/20 to-purple-400/20 animate-pulse"></div>
                            
                            <!-- User Icon -->
                            <div class="w-6 h-6 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full relative mb-1 animate-bounce" style="animation-delay: 0.5s;">
                                <div class="w-3 h-3 bg-white rounded-full absolute top-1 left-1/2 transform -translate-x-1/2"></div>
                                <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                            </div>
                            
                            <!-- UI Elements -->
                            <div class="flex gap-1 relative z-10">
                                <div class="w-0.5 h-3 bg-gradient-to-b from-purple-500 to-blue-500 rounded animate-pulse"></div>
                                <div class="w-4 h-2 bg-gradient-to-r from-gray-300 to-gray-400 rounded animate-pulse" style="animation-delay: 1s;"></div>
                                <div class="w-4 h-2 bg-gradient-to-r from-gray-300 to-gray-400 rounded animate-pulse" style="animation-delay: 1.5s;"></div>
                            </div>
                            
                            <!-- Floating Particles -->
                            <div class="absolute top-1 left-1 w-1 h-1 bg-purple-400 rounded-full animate-ping" style="animation-delay: 0.2s;"></div>
                            <div class="absolute bottom-1 right-1 w-1 h-1 bg-blue-400 rounded-full animate-ping" style="animation-delay: 0.8s;"></div>
                        </div>
                        
                        <!-- Location Pin -->
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full animate-pulse"></div>
                        
                        <!-- Gear Icon -->
                        <div class="absolute -bottom-2 left-4 w-3 h-3 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center animate-spin" style="animation-duration: 3s;">
                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                        </div>
                        
                        <!-- Users List -->
                        <div class="absolute -top-2 right-4 w-5 h-3 bg-gradient-to-r from-purple-500 to-blue-500 rounded flex items-center justify-center gap-0.5 animate-pulse">
                            <div class="w-1 h-1 bg-white rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                            <div class="w-1 h-1 bg-white rounded-full animate-bounce" style="animation-delay: 0.6s;"></div>
                        </div>
                        
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>
                
                <!-- Welcome Text -->
                <div class="text-center text-white px-4 animate-on-scroll-slide-up">
                    <h2 class="text-lg font-bold mb-1 animate-on-scroll">Selamat Datang di Admin Area</h2>
                    <p class="text-gray-300 text-sm animate-on-scroll animate-on-scroll-delay-1">Kontrol semua pengaturan website kamu disini</p>
                </div>
            </div>
        </div>
        
        <!-- Desktop Left Panel -->
        <div class="hidden lg:flex lg:flex-1 bg-transparent relative overflow-hidden animate-on-scroll-fade">
            <div class="absolute inset-0 bg-transparent"></div>
            
            <!-- Illustration Container -->
            <div class="relative z-10 flex flex-col items-center justify-center w-full">
                <!-- Monitor Illustration -->
                <div class="relative mb-8 animate-on-scroll-bounce">
                    <!-- Monitor -->
                    <div class="w-48 h-36 bg-gradient-to-br from-gray-700 via-gray-600 to-gray-800 rounded-lg shadow-2xl relative transform hover:scale-105 transition-transform duration-300">
                        <!-- Screen -->
                        <div class="w-44 h-32 bg-gradient-to-br from-blue-50 to-gray-100 rounded-md absolute top-2 left-2 flex flex-col items-center justify-center overflow-hidden">
                            <!-- Animated Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 via-blue-400/20 to-purple-400/20 animate-pulse"></div>
                            
                            <!-- User Icon -->
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full relative mb-2 animate-bounce" style="animation-delay: 0.5s;">
                                <div class="w-5 h-5 bg-white rounded-full absolute top-2 left-1/2 transform -translate-x-1/2"></div>
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                            </div>
                            
                            <!-- UI Elements -->
                            <div class="flex gap-1 relative z-10">
                                <div class="w-1 h-5 bg-gradient-to-b from-purple-500 to-blue-500 rounded animate-pulse"></div>
                                <div class="w-6 h-3 bg-gradient-to-r from-gray-300 to-gray-400 rounded animate-pulse" style="animation-delay: 1s;"></div>
                                <div class="w-6 h-3 bg-gradient-to-r from-gray-300 to-gray-400 rounded animate-pulse" style="animation-delay: 1.5s;"></div>
                            </div>
                            
                            <!-- Floating Particles -->
                            <div class="absolute top-2 left-2 w-2 h-2 bg-purple-400 rounded-full animate-ping" style="animation-delay: 0.2s;"></div>
                            <div class="absolute bottom-2 right-2 w-2 h-2 bg-blue-400 rounded-full animate-ping" style="animation-delay: 0.8s;"></div>
                            <div class="absolute top-1/2 left-1 w-1 h-1 bg-green-400 rounded-full animate-ping" style="animation-delay: 1.2s;"></div>
                        </div>
                        
                        <!-- Location Pin -->
                        <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 w-5 h-5 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full animate-pulse"></div>
                        
                        <!-- Gear Icon -->
                        <div class="absolute -bottom-4 left-6 w-5 h-5 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center animate-spin" style="animation-duration: 3s;">
                            <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                        </div>
                        
                        <!-- Users List -->
                        <div class="absolute -top-4 right-6 w-8 h-5 bg-gradient-to-r from-purple-500 to-blue-500 rounded flex items-center justify-center gap-1 animate-pulse">
                            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                            <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.6s;"></div>
                        </div>
                        
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-lg opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>
                
                <!-- Welcome Text -->
                <div class="text-center text-white px-8 animate-on-scroll-slide-up">
                    <h2 class="text-2xl font-bold mb-2 animate-on-scroll">Selamat Datang di Admin Area</h2>
                    <p class="text-gray-300 animate-on-scroll animate-on-scroll-delay-1">Kontrol semua pengaturan website kamu disini</p>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-[450px] bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 border-l border-gray-700/30 flex items-center justify-center p-8 animate-on-scroll-fade">
            <div class="w-full max-w-md">
                <!-- Logo Section -->
                <div class="text-center mb-8 animate-on-scroll-bounce">
                    @php
                        $logo = \App\Models\Configuration::getValue('logo', '');
                    @endphp
                    @if(!empty($logo))
                        <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-48 h-12 object-contain mx-auto mb-4 animate-on-scroll-scale">
                    @else
                        <h1 class="text-3xl font-bold text-white mb-2 animate-on-scroll">GAMETA</h1>
                    @endif
                    <p class="text-white mb-1 animate-on-scroll animate-on-scroll-delay-1">Selamat Datang <span class="text-purple-400">Admin!</span></p>
                    <p class="text-gray-400 animate-on-scroll animate-on-scroll-delay-2">Masukan username dan password kamu untuk masuk</p>
                </div>
                
                @if(session('error'))
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg mb-6 animate-on-scroll-zoom">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
                    @csrf
                    
                    <div class="animate-on-scroll animate-on-scroll-delay-1">
                        <label for="username" class="block text-white font-medium mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="username" name="username" 
                                   class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                   value="{{ old('username', '') }}" required autofocus>
                        </div>
                        @error('username')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="animate-on-scroll animate-on-scroll-delay-2">
                        <label for="password" class="block text-white font-medium mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" 
                                   class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-full text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 backdrop-blur-sm"
                                   required>
                        </div>
                        @error('password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white font-semibold py-3 px-4 rounded-full transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg animate-on-scroll animate-on-scroll-delay-3">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk Sekarang
                    </button>
                </form>
                
                <!-- Footer -->
                <div class="text-center mt-8 animate-on-scroll animate-on-scroll-delay-4">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} @php echo \App\Models\Configuration::getValue('website_title', 'Gameta'); @endphp. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/scroll-animations.js') }}"></script>
</body>
</html>
