const TopupPage = {
    currentTopupId: null,
    
    init() {
        this.bindEvents();
    },
    
    bindEvents() {
        // View Modal Events
        document.getElementById('viewModalCloseBtn')?.addEventListener('click', () => this.closeViewModal());
        document.getElementById('viewModalBackground')?.addEventListener('click', () => this.closeViewModal());
        
        // Create/Edit Modal Events
        document.getElementById('modalCloseBtn')?.addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancelBtn')?.addEventListener('click', () => this.closeModal());
        document.getElementById('modalBackground')?.addEventListener('click', () => this.closeModal());
        document.getElementById('modalSaveBtn')?.addEventListener('click', () => this.saveTopup());
        document.getElementById('modalTerimaBtn')?.addEventListener('click', () => this.acceptTopup());
        
        // Delete Modal Events
        document.getElementById('deleteModalCancelBtn')?.addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('deleteModalBackground')?.addEventListener('click', () => this.closeDeleteModal());
        document.getElementById('deleteModalConfirmBtn')?.addEventListener('click', () => this.confirmDelete());
        
        // Configuration Modal Events
        document.getElementById('configBtn')?.addEventListener('click', () => this.showConfigModal());
        document.getElementById('configModalCloseBtn')?.addEventListener('click', () => this.closeConfigModal());
        document.getElementById('configModalCancelBtn')?.addEventListener('click', () => this.closeConfigModal());
        document.getElementById('configModalBackground')?.addEventListener('click', () => this.closeConfigModal());
        document.getElementById('configForm')?.addEventListener('submit', (e) => this.handleConfigSubmit(e));
    },
    

    
    async viewTopup(topupId) {
        try {
            const response = await fetch(`/admin/topups/${topupId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const topup = await response.json();
            this.showTopupDetails(topup);
        } catch (error) {
            console.error('Error fetching topup details:', error);
            this.showNotification('Gagal memuat detail topup', 'error');
        }
    },
    
    showTopupDetails(topup) {
        const topupDetails = document.getElementById('topupDetails');
        if (topupDetails) {
            const statusBadge = this.getStatusBadge(topup.status);
            const formattedDate = topup.tanggal ? new Date(topup.tanggal).toLocaleString('id-ID') : '-';
            
            topupDetails.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                        <p class="text-white font-mono">${topup.username || '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Topup ID</label>
                        <p class="text-white font-mono">${topup.topup_id || '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Jumlah</label>
                        <p class="text-white">Rp ${this.formatNumber(topup.jumlah)}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                        <div class="mt-1">${statusBadge}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal</label>
                        <p class="text-white">${formattedDate}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Dibuat</label>
                        <p class="text-white">${topup.created_at ? new Date(topup.created_at).toLocaleString('id-ID') : '-'}</p>
                    </div>
                </div>
            `;
        }
        
        this.showViewModal();
    },
    
    getStatusBadge(status) {
        switch(status) {
            case 'success':
                return '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Success</span>';
            case 'pending':
                return '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>';
            case 'failed':
                return '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Failed</span>';
            case 'cancelled':
                return '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400 border border-gray-500/30">Cancelled</span>';
            default:
                return '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400 border border-gray-500/30">Unknown</span>';
        }
    },
    
    formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    },
    
    createTopup() {
        this.currentTopupId = null;
        this.resetForm();
        document.getElementById('modalTitle').textContent = 'Tambah Topup';
        document.getElementById('topupId').value = '';
        document.getElementById('topup_id').value = '';
        
        // Pastikan field tidak readonly saat create
        document.getElementById('username').readOnly = false;
        document.getElementById('topup_id').readOnly = false;
        document.getElementById('username').classList.remove('bg-gray-700/50', 'cursor-not-allowed');
        document.getElementById('topup_id').classList.remove('bg-gray-700/50', 'cursor-not-allowed');
        
        this.showModal();
    },
    
    async editTopup(topupId) {
        try {
            const response = await fetch(`/admin/topups/${topupId}/edit`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const topup = await response.json();
            this.populateForm(topup);
            this.currentTopupId = topupId;
            document.getElementById('modalTitle').textContent = 'Edit Topup';
            this.showModal();
        } catch (error) {
            console.error('Error fetching topup for edit:', error);
            this.showNotification('Gagal memuat data topup', 'error');
        }
    },
    
    populateForm(topup) {
        document.getElementById('username').value = topup.username || '';
        document.getElementById('topup_id').value = topup.topup_id || '';
        document.getElementById('jumlah').value = topup.jumlah || '';
        document.getElementById('status').value = topup.status || 'pending';
        document.getElementById('topupId').value = topup.id || '';
        
        // Set username dan topup_id menjadi readonly saat edit
        document.getElementById('username').readOnly = true;
        document.getElementById('topup_id').readOnly = true;
        
        // Tambahkan styling untuk readonly fields
        document.getElementById('username').classList.add('bg-gray-700/50', 'cursor-not-allowed');
        document.getElementById('topup_id').classList.add('bg-gray-700/50', 'cursor-not-allowed');
        
        // Show/hide terima button based on payment provider and status
        this.toggleTerimaButton(topup);
    },
    
    resetForm() {
        document.getElementById('topupForm').reset();
        document.getElementById('topupId').value = '';
        document.getElementById('topup_id').value = '';
        
        // Reset readonly dan styling untuk username dan topup_id
        document.getElementById('username').readOnly = false;
        document.getElementById('topup_id').readOnly = false;
        
        // Hapus styling readonly
        document.getElementById('username').classList.remove('bg-gray-700/50', 'cursor-not-allowed');
        document.getElementById('topup_id').classList.remove('bg-gray-700/50', 'cursor-not-allowed');
        
        // Hide terima button on reset
        this.hideTerimaButton();
    },
    
    async saveTopup() {
        const form = document.getElementById('topupForm');
        const formData = new FormData(form);
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);
        
        // Add _method for PUT requests
        if (this.currentTopupId) {
            formData.append('_method', 'PUT');
        }
        
        const url = this.currentTopupId 
            ? `/admin/topups/${this.currentTopupId}`
            : '/admin/topups';
        
        const method = 'POST'; // Always use POST, Laravel will handle PUT via _method
        
        try {
            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            const result = await response.json();
            
            if (response.ok) {
                this.showNotification(result.message || 'Topup berhasil disimpan', 'success');
                this.closeModal();
                setTimeout(() => {
                    if (typeof loadTopups === 'function') {
                        loadTopups();
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                this.showNotification(result.message || 'Gagal menyimpan topup', 'error');
            }
        } catch (error) {
            console.error('Error saving topup:', error);
            this.showNotification('Gagal menyimpan topup', 'error');
        }
    },
    
    async acceptTopup() {
        if (!this.currentTopupId) return;
        
        if (!confirm('Apakah Anda yakin ingin menerima topup ini? Saldo member akan ditambahkan secara otomatis.')) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            const response = await fetch(`/admin/topups/${this.currentTopupId}/accept`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                this.showNotification(result.message || 'Topup berhasil diterima', 'success');
                this.closeModal();
                setTimeout(() => {
                    if (typeof loadTopups === 'function') {
                        loadTopups();
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                this.showNotification(result.message || 'Gagal menerima topup', 'error');
            }
        } catch (error) {
            console.error('Error accepting topup:', error);
            this.showNotification('Gagal menerima topup', 'error');
        }
    },
    
    toggleTerimaButton(topup) {
        const terimaBtn = document.getElementById('modalTerimaBtn');
        if (!terimaBtn) return;
        
        // Show terima button only if:
        // 1. Payment provider is manual (case insensitive)
        // 2. Status is pending
        const paymentProvider = topup.payment_provider ? topup.payment_provider.toLowerCase() : '';
        
        if (paymentProvider === 'manual' && topup.status === 'pending') {
            terimaBtn.classList.remove('hidden');
        } else {
            terimaBtn.classList.add('hidden');
        }
    },
    
    hideTerimaButton() {
        const terimaBtn = document.getElementById('modalTerimaBtn');
        if (terimaBtn) {
            terimaBtn.classList.add('hidden');
        }
    },
    
    deleteTopup(topupId) {
        this.currentTopupId = topupId;
        this.showDeleteModal();
    },
    
    async confirmDelete() {
        if (!this.currentTopupId) return;
        
        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', 'DELETE');
            
            const response = await fetch(`/admin/topups/${this.currentTopupId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            
            if (response.ok) {
                this.showNotification('Topup berhasil dihapus', 'success');
                this.closeDeleteModal();
                setTimeout(() => {
                    if (typeof loadTopups === 'function') {
                        loadTopups();
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                const result = await response.json();
                this.showNotification(result.message || 'Gagal menghapus topup', 'error');
            }
        } catch (error) {
            console.error('Error deleting topup:', error);
            this.showNotification('Gagal menghapus topup', 'error');
        }
    },
    
    showViewModal() {
        document.getElementById('viewTopupModal').classList.remove('hidden');
    },
    
    closeViewModal() {
        document.getElementById('viewTopupModal').classList.add('hidden');
    },
    
    showModal() {
        document.getElementById('topupModal').classList.remove('hidden');
    },
    
    closeModal() {
        document.getElementById('topupModal').classList.add('hidden');
        this.resetForm();
    },
    
    showDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    },
    
    closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        this.currentTopupId = null;
    },
    
    showConfigModal() {
        document.getElementById('configModal').classList.remove('hidden');
    },
    
    closeConfigModal() {
        document.getElementById('configModal').classList.add('hidden');
    },
    
    async handleConfigSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const saveBtn = document.getElementById('configModalSaveBtn');
        
        // Disable save button
        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                this.showNotification(result.message || 'Konfigurasi berhasil disimpan', 'success');
                this.closeConfigModal();
            } else {
                this.showNotification(result.message || 'Gagal menyimpan konfigurasi', 'error');
            }
        } catch (error) {
            console.error('Error saving config:', error);
            this.showNotification('Gagal menyimpan konfigurasi', 'error');
        } finally {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan';
        }
    },
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
        
        // Set background color based on type
        switch(type) {
            case 'success':
                notification.className += ' bg-green-500';
                break;
            case 'error':
                notification.className += ' bg-red-500';
                break;
            case 'warning':
                notification.className += ' bg-yellow-500';
                break;
            default:
                notification.className += ' bg-blue-500';
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    TopupPage.init();
});
