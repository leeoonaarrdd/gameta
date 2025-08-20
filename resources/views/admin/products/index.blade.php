@extends('admin.layouts.app')

@section('title', 'Kelola Produk - Admin Panel')

@section('content')
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
        <!-- Title -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Kelola Produk</h1>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center justify-between mb-6">
            <!-- Left Side Buttons -->
            <div class="flex items-center gap-3">
                <button 
                    onclick="getProductsFromDigiflazz()" 
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-6 py-3 rounded-full font-medium transition-all duration-200 flex items-center gap-2"
                >
                    Get Produk
                </button>
                <button 
                    onclick="updateProductPrices()" 
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-6 py-3 rounded-full font-medium transition-all duration-200 flex items-center gap-2"
                >
                    Update Harga
                </button>
                <a href="{{ route('admin.icons.index') }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-8 py-3 rounded-full font-medium transition-all duration-200">
                    Icon
                </a>
            </div>
            
            <!-- Right Side Button -->
            <div class="flex items-center">
                <a href="{{ route('admin.products.create') }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-8 py-3 rounded-full font-medium transition-all duration-200">
                    Tambah
                </a>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
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
            
            <!-- Search Bar -->
            <div class="flex items-center gap-2">
                <span class="text-gray-300 text-sm">Search:</span>
                <input 
                    type="text" 
                    id="searchInput"
                    value="{{ request('search') }}"
                    placeholder="Cari produk..."
                    class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                >
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
            <thead>
                <tr class="border-b border-gray-700/30">
                                         <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">No</th>
                     <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Produk</th>
                     <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Games</th>
                     <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Harga Tamu</th>
                     <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Harga Member</th>
                     <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                     <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/30">
                @include('admin.products.partials.table-rows')
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($products->hasPages())
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
        <div class="text-sm text-gray-300 bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-lg px-4 py-2">
            <span class="text-purple-400 font-medium">Showing</span> 
            <span class="text-white font-semibold">{{ $products->firstItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">to</span> 
            <span class="text-white font-semibold">{{ $products->lastItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">of</span> 
            <span class="text-white font-semibold">{{ $products->total() }}</span> 
            <span class="text-purple-400 font-medium">entries</span>
        </div>
        
        <div class="flex items-center gap-2 pagination-container">
            {{ $products->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
    </div>
    @endif
</div>

<script src="/js/products.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const entriesSelect = document.getElementById('entriesSelect');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.querySelector('.pagination-container');
    
    // Handle entries change
    entriesSelect.addEventListener('change', function() {
        loadProducts();
    });
    
    // Handle search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadProducts();
        }, 500);
    });
    
    window.loadProducts = function() {
        const entries = entriesSelect.value;
        const search = searchInput.value;
        
        // Build query parameters
        const params = new URLSearchParams();
        if (entries) params.append('entries', entries);
        if (search) params.append('search', search);
        
        // Make AJAX request
        fetch(`/admin/products?${params.toString()}`, {
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
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-red-400">Gagal memuat data</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-red-400">Terjadi kesalahan saat memuat data</td></tr>';
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
            
            // Make AJAX request
            fetch(`/admin/products?${params.toString()}`, {
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
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});

// Function to get products from Digiflazz
function getProductsFromDigiflazz() {
    if (confirm('Apakah Anda yakin ingin mengambil data produk dari Digiflazz? Ini akan mengambil data terbaru dari provider Digiflazz.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> Mengambil Data...';
        button.disabled = true;
        
        // Make API call to get products from Digiflazz
        fetch('/admin/products/get-from-digiflazz', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Gagal mengambil data: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Function to update product prices
function updateProductPrices() {
    if (confirm('Apakah Anda yakin ingin update harga produk dari Digiflazz? Ini akan mengupdate harga produk yang memiliki auto update aktif.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i> Update Harga...';
        button.disabled = true;
        
        // Make API call to update product prices
        fetch('/admin/products/update-prices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Gagal update harga: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}
</script>
@endsection
