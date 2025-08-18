@extends('admin.layouts.app')

@section('title', 'Kelola Pembelian - Admin Panel')

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
        <!-- Title and Configuration Button -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Kelola Pembelian</h1>
            <button 
                id="configBtn"
                class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-8 py-3 rounded-full font-medium transition-all duration-200 cursor-pointer"
            >
                Konfigurasi
            </button>
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
                     placeholder="Cari pembelian..."
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
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/30">
                @include('admin.purchases.partials.table-rows')
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
        <div class="text-sm text-gray-300 bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-lg px-4 py-2">
            <span class="text-purple-400 font-medium">Showing</span> 
            <span class="text-white font-semibold">{{ $purchases->firstItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">to</span> 
            <span class="text-white font-semibold">{{ $purchases->lastItem() ?? 0 }}</span> 
            <span class="text-purple-400 font-medium">of</span> 
            <span class="text-white font-semibold">{{ $purchases->total() }}</span> 
            <span class="text-purple-400 font-medium">entries</span>
        </div>
        
        <div class="flex items-center gap-2 pagination-container">
            @if($purchases->hasPages())
                {{ $purchases->appends(request()->query())->links('vendor.pagination.tailwind') }}
            @else
                <div class="text-sm text-gray-400">No pagination needed</div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-white mb-4">Update Status Pembelian</h3>
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-purple-400" placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 text-gray-300 hover:text-white transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Configuration Modal -->
<div id="configModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div id="configModalBackground" class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
        
        <!-- Modal panel -->
        <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl">
            <!-- Modal header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">Konfigurasi Pembelian</h3>
                <button id="configModalCloseBtn" class="text-gray-400 hover:text-white transition-colors duration-200 cursor-pointer">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal body -->
            <form id="configForm" method="POST" action="{{ route('admin.purchases.config') }}">
                @csrf
                <div class="mb-6">
                    <label for="orderPrefix" class="block text-sm font-medium text-gray-300 mb-2">Prefix Order ID</label>
                    <input 
                        type="text" 
                        id="orderPrefix"
                        name="order_prefix"
                        value="{{ \App\Models\Configuration::getValue('order_prefix', 'ORD') }}"
                        placeholder="Masukkan prefix order ID..."
                        class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
                    >
                </div>
                
                <div class="mb-6">
                    <label for="invoiceDuration" class="block text-sm font-medium text-gray-300 mb-2">Durasi Invoice (Menit)</label>
                    <input 
                        type="number" 
                        id="invoiceDuration"
                        name="invoice_duration"
                        value="{{ \App\Models\Configuration::getValue('invoice_duration', '30') }}"
                        placeholder="Masukkan durasi invoice..."
                        min="1"
                        max="1440"
                        class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 cursor-text"
                    >
                </div>
                
                <!-- Modal footer -->
                <div class="flex justify-end gap-3">
                    <button 
                        type="button"
                        id="configModalCancelBtn"
                        class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-colors duration-200 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        id="configModalSaveBtn"
                        class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 disabled:from-gray-500 disabled:to-gray-400 disabled:cursor-not-allowed text-white px-6 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer"
                    >
                        Simpan
                    </button>
                </div>
            </form>
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
        loadPurchases();
    });
    
    // Handle search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadPurchases();
        }, 500);
    });
    
    window.loadPurchases = function() {
        const entries = entriesSelect.value;
        const search = searchInput.value;
        
        // Build query parameters
        const params = new URLSearchParams();
        if (entries) params.append('entries', entries);
        if (search) params.append('search', search);
        
        // Make AJAX request
        fetch(`/admin/purchases?${params.toString()}`, {
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
                
                // Reinitialize delete buttons
                initializeDeleteButtons();
            } else {
                tableBody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-red-400">Gagal memuat data</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-red-400">Terjadi kesalahan saat memuat data</td></tr>';
        });
    }
    
    function initializeDeleteButtons() {
        // Reinitialize delete buttons after content update
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                const itemName = this.getAttribute('data-item-name');
                const message = this.getAttribute('data-message');
                
                if (confirm(message)) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success notification
                            alert(data.message || 'Pembelian berhasil dihapus');
                            setTimeout(() => {
                                loadPurchases(); // Reload data after successful deletion
                            }, 1000);
                        } else {
                            alert('Gagal menghapus ' + itemName + ': ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus ' + itemName);
                    });
                }
            });
        });
    }
    
    // Initialize delete buttons on page load
    initializeDeleteButtons();
    
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
            fetch(`/admin/purchases?${params.toString()}`, {
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
                    initializeDeleteButtons();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Configuration Modal
    const configBtn = document.getElementById('configBtn');
    const configModal = document.getElementById('configModal');
    const configModalCloseBtn = document.getElementById('configModalCloseBtn');
    const configModalCancelBtn = document.getElementById('configModalCancelBtn');
    const configModalBackground = document.getElementById('configModalBackground');

    // Open config modal
    configBtn.addEventListener('click', function() {
        configModal.classList.remove('hidden');
    });

    // Close config modal
    function closeConfigModal() {
        configModal.classList.add('hidden');
    }

    configModalCloseBtn.addEventListener('click', closeConfigModal);
    configModalCancelBtn.addEventListener('click', closeConfigModal);
    configModalBackground.addEventListener('click', closeConfigModal);

    // Handle form submission
    const configForm = document.getElementById('configForm');
    const configModalSaveBtn = document.getElementById('configModalSaveBtn');

    configForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable save button
        configModalSaveBtn.disabled = true;
        configModalSaveBtn.textContent = 'Menyimpan...';
        
        // Get form data
        const formData = new FormData(this);
        
        // Add CSRF token to form data
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);
        
        // Submit form using fetch
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400';
                successMessage.textContent = data.message || 'Konfigurasi berhasil disimpan';
                
                // Insert success message at the top of the page
                const container = document.querySelector('.w-full.px-4');
                container.insertBefore(successMessage, container.firstChild);
                
                // Remove success message after 3 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
                
                // Close modal
                closeConfigModal();
            } else {
                // Show error message
                alert(data.message || 'Terjadi kesalahan saat menyimpan konfigurasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan konfigurasi. Silakan coba lagi.');
        })
        .finally(() => {
            // Re-enable save button
            configModalSaveBtn.disabled = false;
            configModalSaveBtn.textContent = 'Simpan';
        });
    });
});

function updateStatus(purchaseId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    
    form.action = `/admin/purchases/${purchaseId}/status`;
    modal.classList.remove('hidden');
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});

// Function to delete purchase
function deletePurchase(purchaseId) {
    if (confirm('Apakah Anda yakin ingin menghapus pembelian ini?')) {
        fetch(`/admin/purchases/${purchaseId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Pembelian berhasil dihapus');
                if (typeof loadPurchases === 'function') {
                    loadPurchases();
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Gagal menghapus pembelian');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus pembelian');
        });
    }
}
</script>
@endsection
