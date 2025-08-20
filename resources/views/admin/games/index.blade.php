@extends('admin.layouts.app')

@section('title', 'Kelola Games - Admin Panel')

@section('content')
<style>
    .game-row {
        transition: all 0.2s ease;
        cursor: grab;
    }
    
    .game-row:hover {
        background-color: rgba(55, 65, 81, 0.3) !important;
    }
    
    .game-row:active {
        cursor: grabbing;
    }
    
    .game-row.dragging {
        opacity: 0.8;
        transform: rotate(1deg);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        cursor: grabbing !important;
    }
    
    .game-row.drag-over {
        border-top: 2px solid #8b5cf6;
        background-color: rgba(147, 51, 234, 0.1) !important;
    }
    
    /* Prevent text selection during drag */
    .game-row.dragging * {
        user-select: none;
        pointer-events: none;
    }
</style>
<div class="w-full px-4 sm:px-6 lg:px-8">
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

    <!-- Header Section -->
    <div class="mb-8">
        <!-- Title and Add Button -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Kelola Games</h1>
            <a href="{{ route('admin.games.create') }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-8 py-3 rounded-full font-medium transition-all duration-200">
                Tambah
            </a>
        </div>
        
        <!-- Controls -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <!-- Entries Control -->
            <div class="flex items-center gap-2">
                <span class="text-gray-300 text-sm">Show</span>
                <select id="entriesSelect" class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60">
                    <option value="10" {{ request('entries') == '10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('entries', '25') == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('entries') == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('entries') == '100' ? 'selected' : '' }}>100</option>
                </select>
                <span class="text-gray-300 text-sm">entries</span>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <!-- Category Filter -->
                <div class="flex items-center gap-2">
                    <span class="text-gray-300 text-sm">Kategori:</span>
                    <select id="categoryFilter" class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 min-w-[150px]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Search Bar -->
                <div class="flex items-center gap-2">
                    <span class="text-gray-300 text-sm">Search:</span>
                    <input 
                        type="text" 
                        id="searchInput"
                        value="{{ request('search') }}"
                        placeholder="Cari games..."
                        class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                    >
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
            <thead>
                <tr class="border-b border-gray-700/30">
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Gambar</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Games</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Sub Judul</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/30">
                @include('admin.games.partials.table-rows')
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
        <div class="text-sm text-gray-300 bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-lg px-4 py-2">
            <span class="text-purple-400 font-medium">Showing</span> 
            <span class="text-white font-semibold">{{ $games->firstItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">to</span> 
            <span class="text-white font-semibold">{{ $games->lastItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">of</span> 
            <span class="text-white font-semibold">{{ $games->total() }}</span> 
            <span class="text-purple-400 font-medium">entries</span>
        </div>
        
        <div class="flex items-center gap-2 pagination-container">
            @if($games->hasPages())
                {{ $games->appends(request()->query())->links('vendor.pagination.tailwind') }}
            @else
                <div class="text-sm text-gray-400">No pagination needed</div>
            @endif
        </div>
    </div>
    

</div>

<script>
// Pass the route URL to the JavaScript file
window.gamesUpdateOrderRoute = '{{ route("admin.games.update-order") }}';
</script>
<script src="{{ asset('js/games-drag-drop.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const entriesSelect = document.getElementById('entriesSelect');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.querySelector('.pagination-container');
    
    // Handle entries change
    entriesSelect.addEventListener('change', function() {
        loadGames();
    });
    
    // Handle category filter change
    categoryFilter.addEventListener('change', function() {
        loadGames();
    });
    
    // Handle search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadGames();
        }, 500);
    });
    
    window.loadGames = function() {
        const entries = entriesSelect.value;
        const search = searchInput.value;
        const category = categoryFilter.value;
        
        // Build query parameters
        const params = new URLSearchParams();
        if (entries) params.append('entries', entries);
        if (search) params.append('search', search);
        if (category) params.append('category', category);
        
        // Make AJAX request
        fetch(`/admin/games?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update table content
                tableBody.innerHTML = data.html;
                
                // Update pagination
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination;
                }
                
                // Delete buttons are automatically handled by admin-global.js
                
                // Reinitialize drag & drop
                if (window.gamesDragDrop) {
                    window.gamesDragDrop.reinitialize();
                }
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-400">Gagal memuat data</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-400">Terjadi kesalahan saat memuat data</td></tr>';
        });
    }
    
    function initializeDeleteButtons() {
        // Delete buttons are now handled by admin-global.js
        // No need to add event listeners here as admin-global.js handles all .btn-delete elements
    }
    
    // Delete buttons are now handled by admin-global.js automatically
    
    // Handle pagination clicks with event delegation
    document.addEventListener('click', function(e) {
        // Check for pagination links (both initial and dynamically added)
        const paginationLink = e.target.matches('.pagination-link') ? e.target : e.target.closest('.pagination-link');
        
        if (paginationLink) {
            e.preventDefault();
            e.stopPropagation();
            
            const url = new URL(paginationLink.href);
            const params = new URLSearchParams(url.search);
            
            // Update current parameters
            if (entriesSelect.value) params.set('entries', entriesSelect.value);
            if (searchInput.value) params.set('search', searchInput.value);
            if (categoryFilter.value) params.set('category', categoryFilter.value);
            
            // Make AJAX request
            fetch(`/admin/games?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tableBody.innerHTML = data.html;
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                    // Delete buttons are automatically handled by admin-global.js
                    
                    // Reinitialize drag & drop
                    if (window.gamesDragDrop) {
                        window.gamesDragDrop.reinitialize();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});
</script>
@endsection 