const AdminPage = {
    currentAdminId: null,
    isEditMode: false,

    init() {
        this.bindEvents();
    },

    bindEvents() {
        // Add admin button
        document.getElementById('addAdminBtn').addEventListener('click', () => {
            this.showModal();
        });

        // Modal close button
        document.getElementById('modalCloseBtn').addEventListener('click', () => {
            this.hideModal();
        });

        // Modal cancel button
        document.getElementById('modalCancelBtn').addEventListener('click', () => {
            this.hideModal();
        });

        // Modal background click
        document.getElementById('modalBackground').addEventListener('click', () => {
            this.hideModal();
        });

        // Modal save button
        document.getElementById('modalSaveBtn').addEventListener('click', () => {
            this.saveAdmin();
        });

        // Modal input enter key
        document.getElementById('modalAdminPasswordConfirmation').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.saveAdmin();
            }
        });
    },

    showModal(adminData = null) {
        this.isEditMode = adminData !== null;
        this.currentAdminId = adminData ? adminData.id : null;

        const modal = document.getElementById('adminModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalSaveBtn = document.getElementById('modalSaveBtn');

        // Reset form
        this.resetForm();

        if (this.isEditMode) {
            modalTitle.textContent = 'Edit Admin';
            modalSaveBtn.textContent = 'Update';
            this.populateForm(adminData);
        } else {
            modalTitle.textContent = 'Tambah Admin Baru';
            modalSaveBtn.textContent = 'Simpan';
        }

        modal.classList.remove('hidden');
        document.getElementById('modalAdminName').focus();
    },

    hideModal() {
        document.getElementById('adminModal').classList.add('hidden');
        this.resetForm();
        this.isEditMode = false;
        this.currentAdminId = null;
    },

    resetForm() {
        document.getElementById('modalAdminName').value = '';
        document.getElementById('modalAdminUsername').value = '';
        document.getElementById('modalAdminStatus').value = 'active';
        document.getElementById('modalAdminPassword').value = '';
        document.getElementById('modalAdminPasswordConfirmation').value = '';

        // Remove error states
        this.clearErrors();
    },

    populateForm(adminData) {
        document.getElementById('modalAdminName').value = adminData.name || '';
        document.getElementById('modalAdminUsername').value = adminData.username || '';
        document.getElementById('modalAdminStatus').value = adminData.status || 'active';
        
        // Clear password fields for edit mode
        document.getElementById('modalAdminPassword').value = '';
        document.getElementById('modalAdminPasswordConfirmation').value = '';
    },

    clearErrors() {
        const inputs = [
            'modalAdminName',
            'modalAdminUsername', 
            'modalAdminStatus',
            'modalAdminPassword',
            'modalAdminPasswordConfirmation'
        ];

        inputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            input.classList.add('border-gray-600/30', 'focus:border-purple-400/60', 'focus:ring-purple-400/50');
        });

        // Remove error messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
    },

    showErrors(errors) {
        this.clearErrors();

        Object.keys(errors).forEach(field => {
            const inputId = this.getInputId(field);
            const input = document.getElementById(inputId);
            
            if (input) {
                input.classList.remove('border-gray-600/30', 'focus:border-purple-400/60', 'focus:ring-purple-400/50');
                input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

                // Add error message
                const errorMessage = document.createElement('p');
                errorMessage.className = 'error-message text-red-400 text-xs mt-1';
                errorMessage.textContent = errors[field][0];
                input.parentNode.appendChild(errorMessage);
            }
        });
    },

    getInputId(field) {
        const fieldMap = {
            'name': 'modalAdminName',
            'username': 'modalAdminUsername',
            'status': 'modalAdminStatus',
            'password': 'modalAdminPassword',
            'password_confirmation': 'modalAdminPasswordConfirmation'
        };
        return fieldMap[field] || field;
    },

    getFormData() {
        const formData = {
            name: document.getElementById('modalAdminName').value.trim(),
            username: document.getElementById('modalAdminUsername').value.trim(),
            status: document.getElementById('modalAdminStatus').value,
            password: document.getElementById('modalAdminPassword').value,
            password_confirmation: document.getElementById('modalAdminPasswordConfirmation').value
        };

        // Remove password fields if empty in edit mode
        if (this.isEditMode && !formData.password) {
            delete formData.password;
            delete formData.password_confirmation;
        }

        return formData;
    },

    async saveAdmin() {
        const formData = this.getFormData();
        const saveBtn = document.getElementById('modalSaveBtn');
        
        // Disable save button
        saveBtn.disabled = true;
        saveBtn.textContent = this.isEditMode ? 'Updating...' : 'Saving...';

        try {
            const url = this.isEditMode 
                ? `/admin/admins/${this.currentAdminId}` 
                : '/admin/admins';
            
            const method = this.isEditMode ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');
                this.hideModal();
                this.reloadPage();
            } else {
                if (result.errors) {
                    this.showErrors(result.errors);
                } else {
                    this.showNotification(result.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
        } finally {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = this.isEditMode ? 'Update' : 'Simpan';
        }
    },

    editAdmin(id, name, username, status) {
        this.showModal({
            id: id,
            name: name,
            username: username,
            status: status
        });
    },

    // Delete functionality now handled by the delete buttons in the table

    // Search and entries functionality now handled in the main page script

    reloadPage() {
        // Trigger AJAX reload if loadAdmins function exists
        if (typeof loadAdmins === 'function') {
            loadAdmins();
        } else {
            window.location.reload();
        }
    },

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        notification.classList.add(bgColor, 'text-white');
        
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="mr-2">
                    ${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}
                </span>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    AdminPage.init();
});
