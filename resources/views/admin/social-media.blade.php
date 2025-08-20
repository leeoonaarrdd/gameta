@extends('admin.layouts.app')

@section('title', 'Kelola Sosial Media - Admin Panel')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <!-- Title and Add Button -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Kelola Sosial Media</h1>
            <button 
                id="addSocialMediaBtn"
                class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-8 py-3 rounded-full font-medium transition-all duration-200 cursor-pointer"
            >
                Tambah
            </button>
        </div>
        
        <!-- Add Social Media Modal -->
        <div id="socialMediaModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div id="modalBackground" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                
                <!-- Modal panel -->
                <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Tambah Sosial Media</h3>
                        <button id="modalCloseBtn" class="text-gray-400 hover:text-white transition-colors duration-200 cursor-pointer">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Modal body -->
                    <form id="socialMediaForm">
                        @csrf
                        <input type="hidden" id="modalSocialMediaId" name="id">
                        
                        <div class="mb-6">
                            <label for="modalIcon" class="block text-sm font-medium text-gray-300 mb-2">Icon FontAwesome</label>
                            <input 
                                type="text" 
                                id="modalIcon"
                                name="icon"
                                placeholder="Contoh: fab fa-facebook"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
                            >
                        </div>
                        
                        <div class="mb-6">
                            <label for="modalLink" class="block text-sm font-medium text-gray-300 mb-2">Link URL</label>
                            <input 
                                type="url" 
                                id="modalLink"
                                name="link"
                                class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
                            >
                        </div>
                    </form>
                    
                    <!-- Modal footer -->
                    <div class="flex justify-end gap-3">
                        <button 
                            id="modalCancelBtn"
                            class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-colors duration-200 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            id="modalSaveBtn"
                            class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 disabled:from-gray-500 disabled:to-gray-400 disabled:cursor-not-allowed text-white px-6 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <!-- Entries Control -->
            <div class="flex items-center gap-2">
                <span class="text-gray-300 text-sm">Show</span>
                <select 
                    id="entriesSelect"
                    class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-pointer"
                >
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
                    placeholder="Cari sosial media..."
                    class="bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
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
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Icon</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Link</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/30">
                @include('admin.social-media.partials.table-rows')
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
        <div class="text-sm text-gray-300 bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-lg px-4 py-2">
            <span class="text-purple-400 font-medium">Showing</span> 
            <span class="text-white font-semibold">{{ $socialMedia->firstItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">to</span> 
            <span class="text-white font-semibold">{{ $socialMedia->lastItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">of</span> 
            <span class="text-white font-semibold">{{ $socialMedia->total() }}</span> 
            <span class="text-purple-400 font-medium">entries</span>
        </div>
        
        <div class="flex items-center gap-2 pagination-container">
            @if($socialMedia->hasPages())
                {{ $socialMedia->appends(request()->query())->links('vendor.pagination.tailwind') }}
            @else
                <div class="text-sm text-gray-400">No pagination needed</div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const entriesSelect = document.getElementById('entriesSelect');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const paginationContainer = document.querySelector('.pagination-container');
    
    // Handle entries change
    entriesSelect.addEventListener('change', function() {
        loadSocialMedia();
    });
    
    // Handle search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadSocialMedia();
        }, 500);
    });
    
    window.loadSocialMedia = function() {
        const entries = entriesSelect.value;
        const search = searchInput.value;
        
        // Build query parameters
        const params = new URLSearchParams();
        if (entries) params.append('entries', entries);
        if (search) params.append('search', search);
        
        // Make AJAX request
        fetch(`/admin/social-media?${params.toString()}`, {
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
                tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-400">Gagal memuat data</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-400">Terjadi kesalahan saat memuat data</td></tr>';
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
            fetch(`/admin/social-media?${params.toString()}`, {
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
</script>

@push('scripts')
<script src="{{ asset('js/social-media.js') }}"></script>
@endpush
@endsection
