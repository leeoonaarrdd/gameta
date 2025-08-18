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
        // Kirim request DELETE menggunakan fetch
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
                this.showNotification(data.message, 'success');
                
                // Reload page or data
                if (typeof window.loadAdmins === 'function') {
                    window.loadAdmins(); // For admin pages with AJAX
                } else {
                    window.location.reload(); // For other pages
                }
            } else {
                this.showNotification('Gagal menghapus: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Terjadi kesalahan saat menghapus', 'error');
        });
    }

    // Method untuk menampilkan notifikasi
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'warning' ? 'bg-yellow-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Hapus notifikasi setelah 3 detik
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Inisialisasi ketika DOM sudah siap
document.addEventListener('DOMContentLoaded', () => {
    window.adminGlobal = new AdminGlobal();
});
