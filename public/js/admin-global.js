// Admin Global JavaScript - Modal Konfirmasi Hapus
class AdminGlobal {
    constructor() {
        this.initDeleteModal();
    }

    initDeleteModal() {
        // Tambahkan modal HTML ke body jika belum ada
        if (!document.getElementById('deleteConfirmModal')) {
            this.createDeleteModal();
        }

        // Event listener untuk tombol hapus
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-delete') || e.target.closest('.btn-delete')) {
                e.preventDefault();
                const button = e.target.classList.contains('btn-delete') ? e.target : e.target.closest('.btn-delete');
                this.showDeleteModal(button);
            }
        });
    }

    createDeleteModal() {
        const modalHTML = `
            <div id="deleteConfirmModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                    
                    <!-- Modal panel -->
                    <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl">
                        <!-- Modal header -->
                        <div class="mb-6">
                            <h3 id="deleteModalTitle" class="text-lg font-semibold text-white">Konfirmasi Hapus</h3>
                        </div>
                        
                        <!-- Modal body -->
                        <div class="mb-6">
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-300 text-center" id="deleteModalMessage">
                                Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                        
                        <!-- Modal footer -->
                        <div class="flex justify-end gap-3">
                            <button 
                                id="deleteModalCancel"
                                class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-colors duration-200 cursor-pointer"
                            >
                                Batal
                            </button>
                            <button 
                                id="deleteModalConfirm"
                                class="bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-6 py-2 rounded-full font-medium transition-all duration-200 cursor-pointer"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    showDeleteModal(button) {
        const modal = document.getElementById('deleteConfirmModal');
        const title = document.getElementById('deleteModalTitle');
        const message = document.getElementById('deleteModalMessage');
        const cancelBtn = document.getElementById('deleteModalCancel');
        const confirmBtn = document.getElementById('deleteModalConfirm');

        // Ambil data dari tombol
        const itemName = button.getAttribute('data-item-name') || 'item ini';
        const customMessage = button.getAttribute('data-message');
        const deleteUrl = button.getAttribute('data-url') || button.href;

        // Set judul dan pesan
        title.textContent = `Konfirmasi Hapus ${itemName}`;
        message.textContent = customMessage || `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`;

        // Tampilkan modal
        modal.classList.remove('hidden');

        // Event listener untuk tombol batal
        const handleCancel = () => {
            modal.classList.add('hidden');
            cancelBtn.removeEventListener('click', handleCancel);
            confirmBtn.removeEventListener('click', handleConfirm);
        };

        // Event listener untuk tombol konfirmasi
        const handleConfirm = () => {
            // Kirim request hapus
            this.performDelete(deleteUrl);
            modal.classList.add('hidden');
            cancelBtn.removeEventListener('click', handleCancel);
            confirmBtn.removeEventListener('click', handleConfirm);
        };

        cancelBtn.addEventListener('click', handleCancel);
        confirmBtn.addEventListener('click', handleConfirm);

        // Tutup modal jika klik di luar modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                handleCancel();
            }
        });
    }

    performDelete(url) {
        // Tentukan method berdasarkan URL
        let method = 'DELETE';
        let body = null;
        
        // Jika URL mengandung toggle-status, gunakan PUT
        if (url.includes('toggle-status')) {
            method = 'PUT';
            body = JSON.stringify({ status: 'toggle' });
        }
        
        // Kirim request menggunakan fetch
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: body
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                this.showNotification(data.message || 'Operasi berhasil', 'success');
                
                // Reload page or data
                if (typeof window.loadAdmins === 'function') {
                    window.loadAdmins(); // For admin pages with AJAX
                } else if (typeof window.loadMembers === 'function') {
                    window.loadMembers(); // For member pages with AJAX
                } else if (typeof window.loadCategories === 'function') {
                    window.loadCategories(); // For category pages with AJAX
                } else if (typeof window.loadTargets === 'function') {
                    window.loadTargets(); // For target pages with AJAX
                } else if (typeof window.loadProducts === 'function') {
                    window.loadProducts(); // For product pages with AJAX
                } else if (typeof window.loadGames === 'function') {
                    window.loadGames(); // For game pages with AJAX
                } else if (typeof window.loadTopups === 'function') {
                    window.loadTopups(); // For topup pages with AJAX
                } else if (typeof window.loadPurchases === 'function') {
                    window.loadPurchases(); // For purchase pages with AJAX
                } else if (typeof window.loadPaymentMethods === 'function') {
                    window.loadPaymentMethods(); // For payment method pages with AJAX
                } else if (typeof window.loadSocialMedia === 'function') {
                    window.loadSocialMedia(); // For social media pages with AJAX
                } else if (typeof window.loadFaqs === 'function') {
                    window.loadFaqs(); // For FAQ pages with AJAX
                } else {
                    window.location.reload(); // For other pages
                }
            } else {
                this.showNotification('Gagal melakukan operasi: ' + (data.message || 'Terjadi kesalahan'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Terjadi kesalahan saat menghapus', 'error');
        });
    }

    // Method untuk menampilkan notifikasi
    showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
        
        // Set background color based on type
        switch (type) {
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
        
        // Add to page
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
}

// Inisialisasi ketika DOM sudah siap
document.addEventListener('DOMContentLoaded', () => {
    window.adminGlobal = new AdminGlobal();
});
