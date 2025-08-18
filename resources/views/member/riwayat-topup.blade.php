@extends('layouts.app')

@section('title', 'Riwayat TopUp')

@section('content')
<div class="flex justify-center px-4 sm:px-6 lg:px-8 mt-8 mb-8">
    <div class="max-w-6xl w-full">
        <!-- Mobile Sidebar - Horizontal -->
        <div class="lg:hidden mb-6 animate-on-scroll-slide-up">
            <nav class="flex flex-row overflow-x-auto space-x-2 bg-gray-900/50 rounded-lg p-2">
                <a href="{{ route('member.dashboard') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-1">
                    <i class="fas fa-home-alt w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                
                <a href="{{ route('member.purchases.index') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-2">
                    <i class="fas fa-clipboard-list w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">Pesanan Saya</span>
                </a>
                
                <a href="{{ route('member.topup-saldo') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-3">
                    <i class="fas fa-credit-card w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">TopUp Saldo</span>
                </a>
                
                <a href="{{ route('member.pengaturan-akun') }}" class="flex items-center px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-4">
                    <i class="fas fa-user-cog w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                    <span class="text-sm">Pengaturan Akun</span>
                </a>
                
                <form method="POST" action="{{ route('member.logout') }}" class="block">
                    @csrf
                    <button type="submit" class="flex items-center px-3 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-all duration-200 group whitespace-nowrap animate-on-scroll animate-on-scroll-delay-4">
                        <i class="fas fa-sign-out-alt w-4 h-4 mr-2 group-hover:text-purple-400 transition-colors duration-200"></i>
                        <span class="text-sm">Log Out</span>
                    </button>
                </form>
            </nav>
        </div>

        <!-- Mobile Main Content -->
        <div class="lg:hidden">
            <!-- Header Section -->
            <div class="mb-8 animate-on-scroll-bounce">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('member.topup-saldo') }}" class="text-gray-400 hover:text-white transition-colors duration-200 animate-on-scroll">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <h1 class="text-3xl font-bold text-white animate-on-scroll animate-on-scroll-delay-1">Riwayat TopUp</h1>
                    </div>
                </div>
                
                <!-- Controls -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-on-scroll-fade">
                    <!-- Entries Control -->
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-1">
                        <span class="text-gray-300 text-sm">Show</span>
                        <form method="GET" action="{{ route('member.topup.history') }}" class="flex items-center gap-2">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="entries" onchange="this.form.submit()" class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60">
                                <option value="10" {{ request('entries', 25) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('entries', 25) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('entries', 25) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('entries', 25) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-gray-300 text-sm">entries</span>
                        </form>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                        <span class="text-gray-300 text-sm">Search:</span>
                        <form method="GET" action="{{ route('member.topup.history') }}" class="flex items-center gap-2">
                            @if(request('entries'))
                                <input type="hidden" name="entries" value="{{ request('entries') }}">
                            @endif
                            <input 
                                type="text" 
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari topup..."
                                class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                            >
                            <button type="submit" class="px-3 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-sm transition-colors duration-200">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="overflow-x-auto animate-on-scroll-slide-up">
                <table class="w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                    <thead>
                        <tr class="border-b border-gray-700/30">
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">ID TopUp</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Jumlah</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Metode Pembayaran</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($topups as $index => $topup)
                        <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                            <td class="px-6 py-4 text-sm text-white font-mono">
                                <a href="{{ route('member.topup.invoice', $topup->topup_id) }}" class="text-purple-400 hover:text-purple-300 transition-colors duration-200 cursor-pointer">
                                    {{ $topup->topup_id }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-white">
                                Rp {{ number_format($topup->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-white">{{ $topup->payment_method ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($topup->status === 'pending')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
                                @elseif($topup->status === 'success')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Success</span>
                                @elseif($topup->status === 'failed')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Failed</span>
                                @elseif($topup->status === 'cancelled')
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400 border border-gray-500/30">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-white">
                                {{ $topup->tanggal->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr class="animate-on-scroll-zoom">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-lg font-medium animate-on-scroll">Belum ada riwayat topup</span>
                                    <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Anda belum melakukan topup saldo</span>
                                    <a href="{{ route('member.topup-saldo') }}" class="text-green-400 hover:text-green-300 mt-2 animate-on-scroll animate-on-scroll-delay-2">
                                        TopUp sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($topups->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 animate-on-scroll-zoom">
                    <div class="text-sm text-gray-300 animate-on-scroll animate-on-scroll-delay-1">
                        Showing {{ $topups->firstItem() ?? 0 }} to {{ $topups->lastItem() ?? 0 }} of {{ $topups->total() }} entries
                    </div>
                    
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                        {{-- Previous Button --}}
                        @if($topups->onFirstPage())
                            <button class="px-3 py-2 text-sm text-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Previous
                            </button>
                        @else
                            <a href="{{ $topups->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">
                                Previous
                            </a>
                        @endif
                        
                        {{-- Page Numbers --}}
                        <div class="flex items-center gap-1">
                            @foreach($topups->getUrlRange(1, min(5, $topups->lastPage())) as $page => $url)
                                @if($page == $topups->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium bg-purple-500 text-white rounded-lg">{{ $page }}</span>
                                @else
                                    <a href="{{ $topups->appends(request()->query())->url($page) }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                            
                            @if($topups->lastPage() > 5)
                                @if($topups->currentPage() < $topups->lastPage() - 2)
                                    <span class="px-3 py-2 text-sm text-gray-300">...</span>
                                @endif
                                
                                @if($topups->currentPage() < $topups->lastPage() - 1)
                                    <a href="{{ $topups->appends(request()->query())->url($topups->lastPage()) }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">{{ $topups->lastPage() }}</a>
                                @endif
                            @endif
                        </div>
                        
                        {{-- Next Button --}}
                        @if($topups->hasMorePages())
                            <a href="{{ $topups->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">
                                Next
                            </a>
                        @else
                            <button class="px-3 py-2 text-sm text-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Desktop Layout - Sidebar + Main Content -->
        <div class="hidden lg:flex">
            <!-- Desktop Sidebar -->
            <div class="w-64 flex-shrink-0 p-6 animate-on-scroll-fade">
                <nav class="space-y-2">
                    <a href="{{ route('member.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-1">
                        <i class="fas fa-home-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('member.purchases.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-2">
                        <i class="fas fa-clipboard-list w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        Pesanan Saya
                    </a>
                    
                    <a href="{{ route('member.topup-saldo') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-3">
                        <i class="fas fa-credit-card w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        TopUp Saldo
                    </a>
                    
                    <a href="{{ route('member.pengaturan-akun') }}" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800/50 rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-4">
                        <i class="fas fa-user-cog w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                        Pengaturan Akun
                    </a>
                    
                    <form method="POST" action="{{ route('member.logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-all duration-200 group animate-on-scroll animate-on-scroll-delay-4">
                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3 group-hover:text-purple-400 transition-colors duration-200"></i>
                            Log Out
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 p-8 hidden lg:block animate-on-scroll-fade">
                <!-- Header Section -->
                <div class="mb-8 animate-on-scroll-bounce">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('member.topup-saldo') }}" class="text-gray-400 hover:text-white transition-colors duration-200 animate-on-scroll">
                                <i class="fas fa-arrow-left text-lg"></i>
                            </a>
                            <h1 class="text-3xl font-bold text-white animate-on-scroll animate-on-scroll-delay-1">Riwayat TopUp</h1>
                        </div>
                    </div>
                    
                    <!-- Controls -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-on-scroll-fade">
                        <!-- Entries Control -->
                        <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-1">
                            <span class="text-gray-300 text-sm">Show</span>
                            <form method="GET" action="{{ route('member.topup.history') }}" class="flex items-center gap-2">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                <select name="entries" onchange="this.form.submit()" class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60">
                                    <option value="10" {{ request('entries', 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('entries', 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('entries', 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('entries', 25) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-gray-300 text-sm">entries</span>
                            </form>
                        </div>
                        
                        <!-- Search Bar -->
                        <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                            <span class="text-gray-300 text-sm">Search:</span>
                            <form method="GET" action="{{ route('member.topup.history') }}" class="flex items-center gap-2">
                                @if(request('entries'))
                                    <input type="hidden" name="entries" value="{{ request('entries') }}">
                                @endif
                                <input 
                                    type="text" 
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Cari topup..."
                                    class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                                >
                                <button type="submit" class="px-3 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-sm transition-colors duration-200">
                                    Cari
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Data Table -->
                <div class="overflow-x-auto animate-on-scroll-slide-up">
                    <table class="w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                        <thead>
                            <tr class="border-b border-gray-700/30">
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">ID TopUp</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Jumlah</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Metode Pembayaran</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/30">
                            @forelse($topups as $index => $topup)
                            <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                                <td class="px-6 py-4 text-sm text-white font-mono">
                                    <a href="{{ route('member.topup.invoice', $topup->topup_id) }}" class="text-purple-400 hover:text-purple-300 transition-colors duration-200 cursor-pointer">
                                        {{ $topup->topup_id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    Rp {{ number_format($topup->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-white">{{ $topup->payment_method ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    @if($topup->status === 'pending')
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
                                    @elseif($topup->status === 'success')
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Success</span>
                                    @elseif($topup->status === 'failed')
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Failed</span>
                                    @elseif($topup->status === 'cancelled')
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400 border border-gray-500/30">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $topup->tanggal->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr class="animate-on-scroll-zoom">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-lg font-medium animate-on-scroll">Belum ada riwayat topup</span>
                                        <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Anda belum melakukan topup saldo</span>
                                        <a href="{{ route('member.topup-saldo') }}" class="text-green-400 hover:text-green-300 mt-2 animate-on-scroll animate-on-scroll-delay-2">
                                            TopUp sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($topups->hasPages())
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 animate-on-scroll-zoom">
                        <div class="text-sm text-gray-300 animate-on-scroll animate-on-scroll-delay-1">
                            Showing {{ $topups->firstItem() ?? 0 }} to {{ $topups->lastItem() ?? 0 }} of {{ $topups->total() }} entries
                        </div>
                        
                        <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                            {{-- Previous Button --}}
                            @if($topups->onFirstPage())
                                <button class="px-3 py-2 text-sm text-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    Previous
                                </button>
                            @else
                                <a href="{{ $topups->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">
                                    Previous
                                </a>
                            @endif
                            
                            {{-- Page Numbers --}}
                            <div class="flex items-center gap-1">
                                @foreach($topups->getUrlRange(1, min(5, $topups->lastPage())) as $page => $url)
                                    @if($page == $topups->currentPage())
                                        <span class="px-3 py-2 text-sm font-medium bg-purple-500 text-white rounded-lg">{{ $page }}</span>
                                    @else
                                        <a href="{{ $topups->appends(request()->query())->url($page) }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">{{ $page }}</a>
                                    @endif
                                @endforeach
                                
                                @if($topups->lastPage() > 5)
                                    @if($topups->currentPage() < $topups->lastPage() - 2)
                                        <span class="px-3 py-2 text-sm text-gray-300">...</span>
                                    @endif
                                    
                                    @if($topups->currentPage() < $topups->lastPage() - 1)
                                        <a href="{{ $topups->appends(request()->query())->url($topups->lastPage()) }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">{{ $topups->lastPage() }}</a>
                                    @endif
                                @endif
                            </div>
                            
                            {{-- Next Button --}}
                            @if($topups->hasMorePages())
                                <a href="{{ $topups->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">
                                    Next
                                </a>
                            @else
                                <button class="px-3 py-2 text-sm text-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    Next
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/riwayat-topup.js') }}"></script>
@endsection
