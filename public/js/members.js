const MemberPage = {
    currentMemberId: null,
    
    init() {
        this.bindEvents();
    },
    
    bindEvents() {
        // View Modal Events
        document.getElementById('viewModalCloseBtn2')?.addEventListener('click', () => this.closeViewModal());
        document.getElementById('viewModalBackground')?.addEventListener('click', () => this.closeViewModal());
        
        // Edit Modal Events
        document.getElementById('editModalCloseBtn')?.addEventListener('click', () => this.closeEditModal());
        document.getElementById('editModalCancelBtn')?.addEventListener('click', () => this.closeEditModal());
        document.getElementById('editModalBackground')?.addEventListener('click', () => this.closeEditModal());
        document.getElementById('editModalSaveBtn')?.addEventListener('click', () => this.saveMember());
    },
    
    // Search and entries functionality now handled in the main page script
    
    async viewMember(memberId) {
        try {
            const response = await fetch(`/admin/members/${memberId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const member = await response.json();
            this.showMemberDetails(member);
        } catch (error) {
            console.error('Error fetching member details:', error);
            this.showNotification('Gagal memuat detail member', 'error');
        }
    },
    
    showMemberDetails(member) {
        const memberDetails = document.getElementById('memberDetails');
        if (memberDetails) {
            memberDetails.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                        <p class="text-white">${member.username || '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">No. WhatsApp</label>
                        <p class="text-white">${member.phone || '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <p class="text-white">${member.email || '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Saldo</label>
                        <p class="text-white">Rp ${member.balance ? new Intl.NumberFormat('id-ID').format(member.balance) : '0'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Status Verifikasi</label>
                        <p class="text-white">
                            ${member.phone_verified_at ? 
                                '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Terverifikasi</span>' : 
                                '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Belum Verifikasi</span>'
                            }
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                        <p class="text-white">
                            ${member.status === 'active' ? 
                                '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>' : 
                                '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Tidak Aktif</span>'
                            }
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tanggal Registrasi</label>
                        <p class="text-white">${member.created_at ? new Date(member.created_at).toLocaleDateString('id-ID') : '-'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Terakhir Login</label>
                        <p class="text-white">${member.last_login_at ? new Date(member.last_login_at).toLocaleDateString('id-ID') : '-'}</p>
                    </div>
                </div>
            `;
        }
        
        this.openViewModal();
    },
    
    openViewModal() {
        const modal = document.getElementById('viewMemberModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    },
    
    closeViewModal() {
        const modal = document.getElementById('viewMemberModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    },
    
    async editMember(memberId) {
        this.currentMemberId = memberId;
        
        try {
            const response = await fetch(`/admin/members/${memberId}/edit`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const member = await response.json();
            this.populateEditForm(member);
            this.openEditModal();
        } catch (error) {
            console.error('Error fetching member for edit:', error);
            this.showNotification('Gagal memuat data member', 'error');
        }
    },
    
    populateEditForm(member) {
        document.getElementById('editMemberUsername').value = member.username || '';
        document.getElementById('editMemberPhone').value = member.phone || '';
        document.getElementById('editMemberBalance').value = member.balance || 0;
        document.getElementById('editMemberStatus').value = member.status || 'active';
        // Set verification status based on phone_verified_at
        document.getElementById('editMemberVerification').value = member.phone_verified_at ? 'verified' : 'unverified';
    },
    
    openEditModal() {
        const modal = document.getElementById('editMemberModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    },
    
    closeEditModal() {
        const modal = document.getElementById('editMemberModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        this.currentMemberId = null;
    },
    
    async saveMember() {
        if (!this.currentMemberId) return;
        
        const saveBtn = document.getElementById('editModalSaveBtn');
        const originalText = saveBtn.textContent;
        
        try {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Menyimpan...';
            
            const formData = {
                username: document.getElementById('editMemberUsername').value,
                phone: document.getElementById('editMemberPhone').value,
                balance: document.getElementById('editMemberBalance').value,
                status: document.getElementById('editMemberStatus').value,
                verification_status: document.getElementById('editMemberVerification').value,
            };
            
            const response = await fetch(`/admin/members/${this.currentMemberId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Member berhasil diperbarui', 'success');
                this.closeEditModal();
                setTimeout(() => {
                    if (typeof loadMembers === 'function') {
                        loadMembers();
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                this.showNotification(result.message || 'Gagal memperbarui member', 'error');
            }
        } catch (error) {
            console.error('Error updating member:', error);
            this.showNotification('Gagal memperbarui member', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        }
    },
    
    async toggleStatus(memberId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const action = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';
        
        if (!confirm(`Apakah Anda yakin ingin ${action} member ini?`)) {
            return;
        }
        
        try {
            const response = await fetch(`/admin/members/${memberId}/toggle-status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message, 'success');
                setTimeout(() => {
                    if (typeof loadMembers === 'function') {
                        loadMembers();
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                this.showNotification(result.message || 'Gagal memperbarui status member', 'error');
            }
        } catch (error) {
            console.error('Error toggling member status:', error);
            this.showNotification('Gagal memperbarui status member', 'error');
        }
    },

    async deleteMember(memberId) {
        if (!confirm('Apakah Anda yakin ingin menghapus member ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }
        
        try {
            const response = await fetch(`/admin/members/${memberId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Member berhasil dihapus', 'success');
                setTimeout(() => {
                    if (typeof loadMembers === 'function') {
                        loadMembers();
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                this.showNotification(result.message || 'Gagal menghapus member', 'error');
            }
        } catch (error) {
            console.error('Error deleting member:', error);
            this.showNotification('Gagal menghapus member', 'error');
        }
    },
    
    showNotification(message, type = 'info') {
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
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    MemberPage.init();
});
