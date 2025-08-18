// Social Media Management JavaScript
const SocialMediaPage = {
    // Modal elements
    modal: null,
    modalBackground: null,
    modalCloseBtn: null,
    modalCancelBtn: null,
    modalSaveBtn: null,
    modalTitle: null,
    modalIcon: null,
    modalLink: null,
    modalSocialMediaId: null,
    
    // Form elements
    addSocialMediaBtn: null,
    searchInput: null,
    entriesSelect: null,
    
    // State
    isEditMode: false,
    
    init() {
        this.initializeElements();
        this.bindEvents();
    },
    
    initializeElements() {
        // Modal elements
        this.modal = document.getElementById('socialMediaModal');
        this.modalBackground = document.getElementById('modalBackground');
        this.modalCloseBtn = document.getElementById('modalCloseBtn');
        this.modalCancelBtn = document.getElementById('modalCancelBtn');
        this.modalSaveBtn = document.getElementById('modalSaveBtn');
        this.modalTitle = document.getElementById('modalTitle');
        this.modalIcon = document.getElementById('modalIcon');
        this.modalLink = document.getElementById('modalLink');
        this.modalSocialMediaId = document.getElementById('modalSocialMediaId');
        
        // Form elements
        this.addSocialMediaBtn = document.getElementById('addSocialMediaBtn');
        this.searchInput = document.getElementById('searchInput');
        this.entriesSelect = document.getElementById('entriesSelect');
    },
    
    bindEvents() {
        // Add button
        if (this.addSocialMediaBtn) {
            this.addSocialMediaBtn.addEventListener('click', () => this.openModal());
        }
        
        // Modal close events
        if (this.modalCloseBtn) {
            this.modalCloseBtn.addEventListener('click', () => this.closeModal());
        }
        
        if (this.modalCancelBtn) {
            this.modalCancelBtn.addEventListener('click', () => this.closeModal());
        }
        
        if (this.modalBackground) {
            this.modalBackground.addEventListener('click', () => this.closeModal());
        }
        
        // Modal save
        if (this.modalSaveBtn) {
            this.modalSaveBtn.addEventListener('click', () => this.saveSocialMedia());
        }
        
        // Keyboard events
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal && !this.modal.classList.contains('hidden')) {
                this.closeModal();
            }
        });
    },
    
    openModal() {
        this.isEditMode = false;
        this.resetForm();
        this.modalTitle.textContent = 'Tambah Sosial Media';
        this.modal.classList.remove('hidden');
        this.modalIcon.focus();
    },
    
    closeModal() {
        this.modal.classList.add('hidden');
        this.resetForm();
    },
    
    resetForm() {
        this.modalIcon.value = '';
        this.modalLink.value = '';
        this.modalSocialMediaId.value = '';
        this.isEditMode = false;
    },
    
    editSocialMedia(id, icon, link) {
        this.isEditMode = true;
        this.modalTitle.textContent = 'Edit Sosial Media';
        this.modalSocialMediaId.value = id;
        this.modalIcon.value = icon;
        this.modalLink.value = link;
        this.modal.classList.remove('hidden');
        this.modalIcon.focus();
    },
    
    saveSocialMedia() {
        const icon = this.modalIcon.value.trim();
        const link = this.modalLink.value.trim();
        const id = this.modalSocialMediaId.value;
        
        if (!icon || !link) {
            alert('Icon dan Link tidak boleh kosong');
            return;
        }
        
        const url = this.isEditMode 
            ? `/admin/social-media/${id}`
            : '/admin/social-media';
        
        const method = this.isEditMode ? 'PUT' : 'POST';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const requestBody = {
            icon: icon,
            link: link
        };
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (typeof loadSocialMedia === 'function') {
                    loadSocialMedia();
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan sosial media');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat menyimpan sosial media. Silakan coba lagi.');
        });
    },
    
    deleteSocialMedia(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus sosial media ini?')) {
            return;
        }
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/admin/social-media/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (typeof loadSocialMedia === 'function') {
                    loadSocialMedia();
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Terjadi kesalahan saat menghapus sosial media');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat menghapus sosial media. Silakan coba lagi.');
        });
    },
    

};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    SocialMediaPage.init();
});
