@extends('layouts.app')

@section('title', 'Pesanan Saya')

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

            <!-- Desktop Main Content -->
            <div class="flex-1 p-8 animate-on-scroll-fade">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg animate-on-scroll-zoom">
                        <p class="text-green-400">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Header Section -->
                <div class="mb-8 animate-on-scroll-bounce">
                    <h1 class="text-3xl font-bold text-white mb-6 animate-on-scroll">Pesanan Saya</h1>
                    
                    <!-- Controls -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-on-scroll-fade">
                        <!-- Entries Control -->
                        <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-1">
                            <span class="text-gray-300 text-sm">Show</span>
                            <select id="entriesSelect" class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60">
                                <option value="10" {{ request('entries') == '10' ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('entries', '10') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('entries') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('entries') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-gray-300 text-sm">entries</span>
                        </div>
                        
                        <!-- Search Bar -->
                        <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                            <span class="text-gray-300 text-sm">Search:</span>
                            <input 
                                type="text" 
                                id="searchInput"
                                value="{{ request('search') }}"
                                placeholder="Cari pesanan..."
                                class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Data Table -->
                <div class="overflow-x-auto animate-on-scroll-slide-up">
                    <table class="min-w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                        <thead>
                            <tr class="border-b border-gray-700/30">
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">No</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Order ID</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Produk</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Harga</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/30">
                            @forelse($purchases as $index => $purchase)
                            <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-sm text-white font-mono">
                                    {{ $purchase->order_id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="text-white font-medium">{{ $purchase->product->name ?? '-' }}</div>
                                        <div class="text-gray-400 text-xs">{{ $purchase->product->game->name ?? '-' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full border {{ $purchase->status_badge }}">
                                        {{ $purchase->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    <div>
                                        <div>{{ $purchase->created_at->format('d/m/Y') }}</div>
                                        <div class="text-gray-400 text-xs">{{ $purchase->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="animate-on-scroll-zoom">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-lg font-medium animate-on-scroll">Belum ada pesanan</span>
                                        <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Anda belum melakukan pembelian</span>
                                        <a href="/" class="text-green-400 hover:text-green-300 mt-2 animate-on-scroll animate-on-scroll-delay-2">
                                            Beli sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($purchases->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 animate-on-scroll-zoom">
                    <div class="text-sm text-gray-300 animate-on-scroll animate-on-scroll-delay-1">
                        Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} entries
                    </div>
                    
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                        {{ $purchases->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile Main Content -->
        <div class="lg:hidden p-4 animate-on-scroll-fade">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg animate-on-scroll-zoom">
                    <p class="text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Header Section -->
            <div class="mb-8 animate-on-scroll-bounce">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-white animate-on-scroll">Pesanan Saya</h1>
                </div>
                
                <!-- Controls -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-on-scroll-fade">
                    <!-- Entries Control -->
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-1">
                        <span class="text-gray-300 text-sm">Show</span>
                        <form method="GET" action="{{ route('member.purchases.index') }}" class="flex items-center gap-2">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="entries" onchange="this.form.submit()" class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60">
                                <option value="10" {{ request('entries', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('entries', 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('entries', 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('entries', 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-gray-300 text-sm">entries</span>
                        </form>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                        <span class="text-gray-300 text-sm">Search:</span>
                        <form method="GET" action="{{ route('member.purchases.index') }}" class="flex items-center gap-2">
                            @if(request('entries'))
                                <input type="hidden" name="entries" value="{{ request('entries') }}">
                            @endif
                            <input 
                                type="text" 
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari pesanan..."
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
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">Order ID</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Produk</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Harga</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($purchases as $index => $purchase)
                        <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                            <td class="px-6 py-4 text-sm text-white font-mono">
                                {{ $purchase->order_id }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-white font-medium">{{ $purchase->product->name ?? '-' }}</div>
                                    <div class="text-gray-400 text-xs">{{ $purchase->product->game->name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-white">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full border {{ $purchase->status_badge }}">
                                    {{ $purchase->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-white">
                                {{ $purchase->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr class="animate-on-scroll-zoom">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-lg font-medium animate-on-scroll">Belum ada pesanan</span>
                                    <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Anda belum melakukan pembelian</span>
                                    <a href="/" class="text-green-400 hover:text-green-300 mt-2 animate-on-scroll animate-on-scroll-delay-2">
                                        Beli sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($purchases->hasPages())
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 animate-on-scroll-zoom">
                    <div class="text-sm text-gray-300 animate-on-scroll animate-on-scroll-delay-1">
                        Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} entries
                    </div>
                    
                    <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-2">
                        {{-- Previous Button --}}
                        @if($purchases->onFirstPage())
                            <button class="px-3 py-2 text-sm text-gray-300 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Previous
                            </button>
                        @else
                            <a href="{{ $purchases->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">
                                Previous
                            </a>
                        @endif
                        
                        {{-- Page Numbers --}}
                        <div class="flex items-center gap-1">
                            @foreach($purchases->getUrlRange(1, min(5, $purchases->lastPage())) as $page => $url)
                                @if($page == $purchases->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium bg-purple-500 text-white rounded-lg">{{ $page }}</span>
                                @else
                                    <a href="{{ $purchases->appends(request()->query())->url($page) }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                            
                            @if($purchases->lastPage() > 5)
                                @if($purchases->currentPage() < $purchases->lastPage() - 2)
                                    <span class="px-3 py-2 text-sm text-gray-300">...</span>
                                @endif
                                
                                @if($purchases->currentPage() < $purchases->lastPage() - 1)
                                    <a href="{{ $purchases->appends(request()->query())->url($purchases->lastPage()) }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">{{ $purchases->lastPage() }}</a>
                                @endif
                            @endif
                        </div>
                        
                        {{-- Next Button --}}
                        @if($purchases->hasMorePages())
                            <a href="{{ $purchases->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-300 hover:text-white transition-colors duration-200">
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


@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Entries select functionality
    const entriesSelect = document.getElementById('entriesSelect');
    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('entries', this.value);
            window.location.href = url.toString();
        });
    }

    // Search input functionality with debounce
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location);
                if (this.value.trim()) {
                    url.searchParams.set('search', this.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }, 500);
        });
    }
});
</script>
