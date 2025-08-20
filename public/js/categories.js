// Global functions for category management
window.CategoryManager = {
    /**
     * Get CSRF token safely
     * @returns {string} CSRF token
     */
    getCsrfToken: function() {
        // Try to get from meta tag first
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            const token = metaTag.getAttribute('content');
            if (token) {
                return token;
            }
        }
        
        // Try to get from input field
        const inputTag = document.querySelector('input[name="_token"]');
        if (inputTag) {
            const token = inputTag.value;
            if (token) {
                return token;
            }
        }
        
        // Try to get from cookie
        const token = this.getCookie('XSRF-TOKEN');
        if (token) {
            return decodeURIComponent(token);
        }
        
        // Fallback to empty string
        return '';
    },

    /**
     * Get cookie value by name
     * @param {string} name - Cookie name
     * @returns {string|null} Cookie value
     */
    getCookie: function(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    },

    /**
     * Delete a category
     * @param {number} categoryId - The ID of the category to delete
     */
    deleteCategory: function(categoryId) {
        // Fungsi ini tidak lagi digunakan karena menggunakan modal konfirmasi dari admin-global.js
        // Tombol delete sekarang menggunakan class btn-delete yang di-handle oleh admin-global.js
    },

    /**
     * Save a category (add or edit)
     * @param {string} categoryName - The name of the category
     * @param {number|null} editingCategoryId - The ID of the category being edited (null for new)
     */
    saveCategory: function(categoryName, editingCategoryId = null) {
        if (!categoryName.trim()) {
            if (typeof CategoryPage !== 'undefined' && CategoryPage.showNotification) {
                CategoryPage.showNotification('Nama kategori tidak boleh kosong', 'error');
            } else {
                alert('Nama kategori tidak boleh kosong');
            }
            return;
        }
        
        const url = editingCategoryId ? `/admin/categories/${editingCategoryId}` : '/admin/categories';
        const method = editingCategoryId ? 'PUT' : 'POST';
        
        // Get CSRF token safely
        const csrfToken = this.getCsrfToken();
        
        if (!csrfToken) {
            if (typeof CategoryPage !== 'undefined' && CategoryPage.showNotification) {
                CategoryPage.showNotification('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.', 'error');
            } else {
                alert('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.');
            }
            return;
        }
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: categoryName
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success notification
                if (typeof CategoryPage !== 'undefined' && CategoryPage.showNotification) {
                    CategoryPage.showNotification(data.message, 'success');
                } else {
                    alert(data.message);
                }
                if (typeof loadCategories === 'function') {
                    loadCategories();
                } else {
                    window.location.reload();
                }
            } else {
                if (typeof CategoryPage !== 'undefined' && CategoryPage.showNotification) {
                    CategoryPage.showNotification(data.message || 'Terjadi kesalahan saat menyimpan kategori', 'error');
                } else {
                    alert(data.message || 'Terjadi kesalahan saat menyimpan kategori');
                }
            }
        })
        .catch(error => {
            if (typeof CategoryPage !== 'undefined' && CategoryPage.showNotification) {
                CategoryPage.showNotification('Terjadi kesalahan saat menyimpan kategori. Silakan coba lagi.', 'error');
            } else {
                alert('Terjadi kesalahan saat menyimpan kategori. Silakan coba lagi.');
            }
        });
    },

    /**
     * Get all categories for dropdown/select
     * @returns {Promise} Promise that resolves to categories array
     */
    getCategories: function() {
        return fetch('/admin/categories-list', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message);
            }
        });
    },

    /**
     * Populate a select element with categories
     * @param {string} selectId - The ID of the select element
     * @param {string} placeholder - Placeholder text for the select
     */
    populateCategorySelect: function(selectId, placeholder = 'Pilih Kategori') {
        this.getCategories().then(categories => {
            const select = document.getElementById(selectId);
            if (select) {
                select.innerHTML = `<option value="">${placeholder}</option>`;
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });
            }
        }).catch(error => {
            // Silent fail for category loading
        });
    }
};

// Category Page Controller
window.CategoryPage = {
    // State variables
    showAddForm: false,
    categoryName: '',
    editingCategory: null,
    searchTerm: '',
    entriesPerPage: '25',

    /**
     * Initialize the category page
     */
    init: function() {
        this.bindEvents();
        this.loadInitialState();
    },

    /**
     * Load initial state from URL parameters
     */
    loadInitialState: function() {
        const urlParams = new URLSearchParams(window.location.search);
        this.searchTerm = urlParams.get('search') || '';
        this.entriesPerPage = urlParams.get('entries') || '25';
        
        // Update UI elements
        const searchInput = document.getElementById('searchInput');
        const entriesSelect = document.getElementById('entriesSelect');
        
        if (searchInput) searchInput.value = this.searchTerm;
        if (entriesSelect) entriesSelect.value = this.entriesPerPage;
    },

    /**
     * Bind all event listeners
     */
    bindEvents: function() {
        // Add category button
        const addButton = document.getElementById('addCategoryBtn');
        if (addButton) {
            addButton.addEventListener('click', () => this.toggleModal());
        }

        // Modal close button
        const closeButton = document.getElementById('modalCloseBtn');
        if (closeButton) {
            closeButton.addEventListener('click', () => this.closeModal());
        }

        // Modal cancel button
        const cancelButton = document.getElementById('modalCancelBtn');
        if (cancelButton) {
            cancelButton.addEventListener('click', () => this.closeModal());
        }

        // Modal save button
        const saveButton = document.getElementById('modalSaveBtn');
        if (saveButton) {
            saveButton.addEventListener('click', () => this.saveCategory());
        }

        // Modal background click
        const modalBackground = document.getElementById('modalBackground');
        if (modalBackground) {
            modalBackground.addEventListener('click', () => this.closeModal());
        }

        // Search and entries functionality now handled in the main page script

        // Category name input
        const categoryNameInput = document.getElementById('modalCategoryName');
        if (categoryNameInput) {
            categoryNameInput.addEventListener('input', (e) => {
                this.categoryName = e.target.value;
                this.updateSaveButton();
            });
        }
    },

    /**
     * Toggle modal visibility
     */
    toggleModal: function() {
        this.showAddForm = !this.showAddForm;
        this.updateModalVisibility();
        
        if (!this.showAddForm) {
            this.resetForm();
        }
    },

    /**
     * Close modal
     */
    closeModal: function() {
        this.showAddForm = false;
        this.updateModalVisibility();
        this.resetForm();
    },

    /**
     * Update modal visibility
     */
    updateModalVisibility: function() {
        const modal = document.getElementById('categoryModal');
        if (modal) {
            if (this.showAddForm) {
                modal.style.display = 'block';
                document.getElementById('modalCategoryName').focus();
            } else {
                modal.style.display = 'none';
            }
        }
    },

    /**
     * Reset form data
     */
    resetForm: function() {
        this.categoryName = '';
        this.editingCategory = null;
        const input = document.getElementById('modalCategoryName');
        if (input) input.value = '';
        this.updateModalTitle();
        this.updateSaveButton();
    },

    /**
     * Update modal title based on editing state
     */
    updateModalTitle: function() {
        const title = document.getElementById('modalTitle');
        if (title) {
            title.textContent = this.editingCategory ? 'Edit Kategori' : 'Tambah Kategori Baru';
        }
    },

    /**
     * Update save button state
     */
    updateSaveButton: function() {
        const saveButton = document.getElementById('modalSaveBtn');
        if (saveButton) {
            saveButton.disabled = !this.categoryName.trim();
        }
    },

    /**
     * Save category
     */
    saveCategory: function() {
        if (!this.categoryName.trim()) {
            if (typeof CategoryPage !== 'undefined' && CategoryPage.showNotification) {
                CategoryPage.showNotification('Nama kategori tidak boleh kosong', 'error');
            } else {
                alert('Nama kategori tidak boleh kosong');
            }
            return;
        }

        CategoryManager.saveCategory(this.categoryName, this.editingCategory);
        this.closeModal();
    },

    /**
     * Edit category
     */
    editCategory: function(categoryId, categoryName) {
        this.editingCategory = categoryId;
        this.categoryName = categoryName;
        const input = document.getElementById('modalCategoryName');
        if (input) input.value = categoryName;
        this.updateModalTitle();
        this.updateSaveButton();
        this.toggleModal();
    },

    // Search and entries functionality now handled in the main page script
    
    /**
     * Reload page data
     */
    reloadPage: function() {
        // Trigger AJAX reload if loadCategories function exists
        if (typeof loadCategories === 'function') {
            loadCategories();
        } else {
            window.location.reload();
        }
    },
    
    /**
     * Show notification
     */
    showNotification: function(message, type = 'info') {
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
document.addEventListener('DOMContentLoaded', function() {
    CategoryPage.init();
});