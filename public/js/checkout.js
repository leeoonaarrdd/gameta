// Checkout Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    let selectedProduct = null;
    let selectedPaymentMethod = null;
    
    // Initialize nickname cache for better performance
    if (!window.nicknameCache) {
        window.nicknameCache = {};
    }
    
    // Notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
        
        // Set background color based on type
        if (type === 'success') {
            notification.className += ' bg-green-600 text-white';
        } else if (type === 'error') {
            notification.className += ' bg-red-600 text-white';
        } else {
            notification.className += ' bg-blue-600 text-white';
        }
        
        // Set content
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    ${type === 'success' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' : 
                      type === 'error' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                      '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
    
    // Product selection
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove active class from all cards
            document.querySelectorAll('.product-card').forEach(c => {
                c.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-600/20');
            });
            
            // Add active class to clicked card
            this.classList.add('ring-2', 'ring-purple-500', 'bg-purple-600/20');
            
            // Get price based on login status
            const isMemberLoggedIn = checkMemberLoginStatus();
            const productPrice = isMemberLoggedIn ? this.dataset.productPriceMember : this.dataset.productPriceTamu;
            
            // Update selected product
            selectedProduct = {
                id: this.dataset.productId,
                name: this.dataset.productName,
                price: productPrice
            };
            
            // Update display
            const formProductIdEl = document.getElementById('form-product-id');
            
            if (formProductIdEl) formProductIdEl.value = selectedProduct.id;
            
            updateCheckoutButton();
            
            // Auto scroll to player input section
            setTimeout(() => {
                const playerInputSection = document.getElementById('player-input-section');
                
                if (playerInputSection) {
                    playerInputSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    
                    // Add highlight effect to player input section
                    playerInputSection.classList.add('ring-2', 'ring-purple-500', 'ring-opacity-50');
                    setTimeout(() => {
                        playerInputSection.classList.remove('ring-2', 'ring-purple-500', 'ring-opacity-50');
                    }, 2000);
                }
            }, 300);
        });
    });
    
    // Function to check if member is logged in
    function checkMemberLoginStatus() {
        // Check if there's a member login indicator in the page
        const memberIndicator = document.querySelector('[data-member-logged-in]');
        if (memberIndicator) {
            return memberIndicator.dataset.memberLoggedIn === 'true';
        }
        
        // Fallback: Check if there's a member dashboard link visible (indicating logged in)
        const memberDashboardLink = document.querySelector('a[href*="member/dashboard"]');
        if (memberDashboardLink && !memberDashboardLink.classList.contains('hidden')) {
            return true;
        }
        
        // Fallback: Check if there's a login link visible (indicating not logged in)
        const loginLink = document.querySelector('a[href*="member/login"]');
        if (loginLink && !loginLink.classList.contains('hidden')) {
            return false;
        }
        
        // Default to false if we can't determine
        return false;
    }
    
    // Payment method selection
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-600/20');
            });
            
            // Add active class to clicked method
            this.classList.add('ring-2', 'ring-purple-500', 'bg-purple-600/20');
            
            // Update selected payment method
            selectedPaymentMethod = {
                id: this.dataset.methodId,
                name: this.dataset.methodName
            };
            
            // Update display
            const formPaymentMethodIdEl = document.getElementById('form-payment-method-id');
            
            if (formPaymentMethodIdEl) formPaymentMethodIdEl.value = selectedPaymentMethod.id;
            
            updateCheckoutButton();
        });
    });
    
    // Dynamic input fields handling
    function setupInputFields() {
        // Handle player input fields
        document.querySelectorAll('input[name="player_fields[]"]').forEach((input, index) => {
            input.addEventListener('input', function() {
                validateField(this);
                updateCheckoutButton();
                // Preload nickname when user types
                preloadNickname();
            });
            
            // Add blur event for validation
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        // Handle option fields
        document.querySelectorAll('select[name="option_fields[]"]').forEach((select, index) => {
            select.addEventListener('change', function() {
                updateCheckoutButton();
                // Preload nickname when user selects option
                preloadNickname();
            });
        });
        
        // Handle fallback player ID input (if exists)
        const playerIdInput = document.getElementById('player-id');
        if (playerIdInput) {
            playerIdInput.addEventListener('input', function() {
                updateCheckoutButton();
                // Preload nickname when user types
                preloadNickname();
            });
        }
    }
    
    // Field validation function
    function validateField(field) {
        const validationType = field.dataset.validation;
        if (!validationType) return true;
        
        const value = field.value.trim();
        const label = field.previousElementSibling?.textContent || 'Field';
        
        // Remove existing error styling
        field.classList.remove('border-red-500', 'border-green-500');
        
        // Remove existing error message
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Required validation - semua field wajib diisi
        if (!value) {
            showFieldError(field, `${label} harus diisi`);
            return false;
        }
        
        // Type-specific validation
        switch (validationType) {
            case 'angka':
                if (!/^\d+$/.test(value)) {
                    showFieldError(field, `${label} harus berupa angka`);
                    return false;
                }
                break;
                
            case 'email':
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    showFieldError(field, `${label} format email tidak valid`);
                    return false;
                }
                break;
                
            case 'password':
                if (value.length < 6) {
                    showFieldError(field, `${label} minimal 6 karakter`);
                    return false;
                }
                break;
                
            case 'teks':
            default:
                // Teks validation - minimal 1 karakter
                if (value.length < 1) {
                    showFieldError(field, `${label} harus diisi`);
                    return false;
                }
                break;
        }
        
        // If all validations pass, show success styling
        if (value) {
            field.classList.add('border-green-500');
        }
        
        return true;
    }
    
    // Show field error
    function showFieldError(field, message) {
        field.classList.add('border-red-500');
        
        // Remove existing error message
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-400 text-xs mt-1';
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
    }
    
    // WhatsApp input
    const whatsappInput = document.getElementById('whatsapp');
    if (whatsappInput) {
        whatsappInput.addEventListener('input', function() {
            const formWhatsappEl = document.getElementById('form-whatsapp');
            if (formWhatsappEl) formWhatsappEl.value = this.value;
            updateCheckoutButton();
        });
    }
    
    // Update checkout button state
    function updateCheckoutButton() {
        const btn = document.getElementById('btn-checkout');
        if (!btn) return;
        
        // Check if all required fields are filled
        let allFieldsFilled = true;
        
        // Check player fields
        const playerFields = document.querySelectorAll('input[name="player_fields[]"]');
        if (playerFields.length > 0) {
            playerFields.forEach(field => {
                if (!field.value.trim()) {
                    allFieldsFilled = false;
                }
            });
        } else {
            // Fallback to single player ID input
            const playerIdInput = document.getElementById('player-id');
            if (playerIdInput && !playerIdInput.value.trim()) {
                allFieldsFilled = false;
            }
        }
        
        // Check option fields
        const optionFields = document.querySelectorAll('select[name="option_fields[]"]');
        optionFields.forEach(field => {
            if (!field.value.trim()) {
                allFieldsFilled = false;
            }
        });
        
        // Check WhatsApp
        const whatsapp = whatsappInput ? whatsappInput.value.trim() : '';
        if (!whatsapp) {
            allFieldsFilled = false;
        }
        
        if (selectedProduct && selectedPaymentMethod && allFieldsFilled) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }
    
    // Checkout button click
    const checkoutBtn = document.getElementById('btn-checkout');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            if (this.disabled) return;
            
            // Validate inputs
            const whatsapp = whatsappInput ? whatsappInput.value.trim() : '';
            
            if (!whatsapp) {
                showNotification('Mohon masukkan nomor WhatsApp', 'error');
                return;
            }
            
            // Basic WhatsApp number validation
            const whatsappRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
            if (!whatsappRegex.test(whatsapp.replace(/\s/g, ''))) {
                showNotification('Mohon masukkan nomor WhatsApp yang valid', 'error');
                return;
            }
            
            // Validate player fields
            const playerFields = document.querySelectorAll('input[name="player_fields[]"]');
            if (playerFields.length > 0) {
                for (let i = 0; i < playerFields.length; i++) {
                    const field = playerFields[i];
                    if (!validateField(field)) {
                        return; // Stop validation if any field fails
                    }
                }
            } else {
                // Fallback validation
                const playerIdInput = document.getElementById('player-id');
                if (playerIdInput && !playerIdInput.value.trim()) {
                    showNotification('Mohon masukkan ID Player', 'error');
                    return;
                }
            }
            
            // Validate option fields
            const optionFields = document.querySelectorAll('select[name="option_fields[]"]');
            for (let i = 0; i < optionFields.length; i++) {
                const field = optionFields[i];
                if (!field.value.trim()) {
                    const label = field.previousElementSibling?.textContent || `Option ${i + 1}`;
                    showNotification(`Mohon pilih ${label}`, 'error');
                    return;
                }
            }
            
            // Show confirmation modal
            showConfirmationModal();
        });
    }
    
    // Function to show confirmation modal
    function showConfirmationModal() {
        const modal = document.getElementById('confirmation-modal');
        if (!modal) return;
        
        // Get game name from page title or data attribute
        const gameName = document.querySelector('h1')?.textContent || 'Game';
        
        // Get player data
        let playerId = '-';
        let playerData = []; // Define playerData in the correct scope
        
        // Collect input fields
        const playerFields = document.querySelectorAll('input[name="player_fields[]"]');
        if (playerFields.length > 0) {
            playerFields.forEach(field => {
                if (field.value.trim()) {
                    playerData.push(field.value.trim());
                }
            });
        }
        
        // Collect option fields and add to playerData
        const optionFields = document.querySelectorAll('select[name="option_fields[]"]');
        optionFields.forEach(field => {
            if (field.value.trim()) {
                playerData.push(field.value.trim());
            }
        });
        
        // Format player ID display
        if (playerData.length > 0) {
            if (playerData.length === 1) {
                playerId = playerData[0];
            } else {
                playerId = playerData[0] + ' (' + playerData.slice(1).join(' - ') + ')';
            }
        } else {
            // Fallback to single player ID input
            const playerIdInput = document.getElementById('player-id');
            if (playerIdInput && playerIdInput.value.trim()) {
                playerId = playerIdInput.value.trim();
                playerData = [playerId]; // Add to playerData for nickname check
            }
        }
        
        // Calculate admin fee (2% of price)
        const price = parseInt(selectedProduct.price);
        const adminFee = Math.ceil(price * 0.02); // 2% admin fee
        const total = price + adminFee;
        
        // Fill modal data
        document.getElementById('modal-player-id').textContent = playerId;
        document.getElementById('modal-game-name').textContent = gameName;
        document.getElementById('modal-product-name').textContent = selectedProduct.name;
        document.getElementById('modal-whatsapp').textContent = whatsappInput ? whatsappInput.value.trim() : '';
        document.getElementById('modal-payment-method').textContent = selectedPaymentMethod.name;
        document.getElementById('modal-price').textContent = `Rp ${price.toLocaleString('id-ID')}`;
        document.getElementById('modal-admin-fee').textContent = `Rp ${adminFee.toLocaleString('id-ID')}`;
        document.getElementById('modal-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;

        // Show modal with fade in effect
        modal.classList.remove('hidden');
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95)';
        
        // Animate modal appearance
        setTimeout(() => {
            modal.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            modal.style.opacity = '1';
            modal.style.transform = 'scale(1)';
        }, 50);
        
        // Add event listeners for modal buttons
        setupModalEventListeners();
        
        // Get nickname immediately without delay
        getNicknameOptimized(playerData);
    }
    
    // Function to setup modal event listeners
    function setupModalEventListeners() {
        const modal = document.getElementById('confirmation-modal');
        const btnCancel = document.getElementById('btn-cancel');
        const btnConfirmPayment = document.getElementById('btn-confirm-payment');
        
        if (!modal || !btnCancel || !btnConfirmPayment) return;
        
        // Remove existing event listeners
        btnCancel.replaceWith(btnCancel.cloneNode(true));
        btnConfirmPayment.replaceWith(btnConfirmPayment.cloneNode(true));
        
        // Get fresh references
        const newBtnCancel = document.getElementById('btn-cancel');
        const newBtnConfirmPayment = document.getElementById('btn-confirm-payment');
        
        // Cancel button
        newBtnCancel.addEventListener('click', function() {
            // Cancel any ongoing nickname request
            if (window.currentNicknameRequest) {
                window.currentNicknameRequest.abort();
                window.currentNicknameRequest = null;
            }
            modal.classList.add('hidden');
        });
        
        // Confirm payment button
        newBtnConfirmPayment.addEventListener('click', function() {
            // Cancel any ongoing nickname request
            if (window.currentNicknameRequest) {
                window.currentNicknameRequest.abort();
                window.currentNicknameRequest = null;
            }
            modal.classList.add('hidden');
            processCheckout();
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                // Cancel any ongoing nickname request
                if (window.currentNicknameRequest) {
                    window.currentNicknameRequest.abort();
                    window.currentNicknameRequest = null;
                }
                modal.classList.add('hidden');
            }
        });
    }
    
    // Function to process checkout (moved from original click handler)
    function processCheckout() {
        // Show loading overlay
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');
        
        // Disable button to prevent double submission
        const checkoutBtn = document.getElementById('btn-checkout');
        if (checkoutBtn) checkoutBtn.disabled = true;
        
        const loadingIcon = document.getElementById('loading-icon');
        const btnText = document.getElementById('btn-text');
        if (loadingIcon) loadingIcon.classList.remove('hidden');
        if (btnText) btnText.textContent = 'Memproses...';
        
        // Collect form data
        const formData = new FormData();
        formData.append('product_id', selectedProduct.id);
        formData.append('payment_method_id', selectedPaymentMethod.id);
        formData.append('whatsapp', whatsappInput ? whatsappInput.value.trim() : '');
        
        // Collect all fields data (input + option) into player_fields
        const allFieldsData = [];
        
        // Add input fields
        const playerFields = document.querySelectorAll('input[name="player_fields[]"]');
        playerFields.forEach(field => {
            if (field.value.trim()) {
                allFieldsData.push(field.value.trim());
            }
        });
        
        // Add option fields
        document.querySelectorAll('select[name="option_fields[]"]').forEach(field => {
            if (field.value.trim()) {
                allFieldsData.push(field.value.trim());
            }
        });
        
        formData.append('player_fields', JSON.stringify(allFieldsData));
        formData.append('option_fields', JSON.stringify([])); // Empty for now
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }
        
        // Submit to backend
        fetch('/checkout/process', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
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
                const successMessage = data.message || 'Pesanan berhasil dibuat!';
                showNotification(successMessage, 'success');
                
                // Check if payment is completed (InnerPay)
                if (data.payment_completed) {
                    // Show special message for completed InnerPay payment
                    showNotification('Pembayaran berhasil! Pesanan akan segera diproses.', 'success');
                    
                    // Redirect to payment page to show completion status
                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 2000);
                    }
                } else {
                    // Redirect to payment page if URL is provided
                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1500);
                    } else {
                        // Reset form if no redirect
                        playerFields.forEach(field => {
                            field.value = '';
                            field.classList.remove('border-red-500', 'border-green-500');
                            const errorDiv = field.parentElement.querySelector('.field-error');
                            if (errorDiv) errorDiv.remove();
                        });
                        document.querySelectorAll('select[name="option_fields[]"]').forEach(field => {
                            field.value = '';
                            field.classList.remove('border-red-500', 'border-green-500');
                            const errorDiv = field.parentElement.querySelector('.field-error');
                            if (errorDiv) errorDiv.remove();
                        });
                        if (whatsappInput) whatsappInput.value = '';
                    }
                    
                    // Clear selections
                    document.querySelectorAll('.product-card').forEach(c => {
                        c.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-600/20');
                    });
                    document.querySelectorAll('.payment-method').forEach(m => {
                        m.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-600/20');
                    });
                    

                    
                    // Reset variables
                    selectedProduct = null;
                    selectedPaymentMethod = null;
                    
                    // Update button state
                    updateCheckoutButton();
                }
            } else {
                const errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                showNotification(errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan pada server. Silakan coba lagi nanti.', 'error');
        })
        .finally(() => {
            // Hide loading overlay
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
            
            // Re-enable button
            const checkoutBtn = document.getElementById('btn-checkout');
            if (checkoutBtn) checkoutBtn.disabled = false;
            const loadingIcon = document.getElementById('loading-icon');
            const btnText = document.getElementById('btn-text');
            if (loadingIcon) loadingIcon.classList.add('hidden');
            if (btnText) btnText.textContent = 'Konfirmasi Pembelian';
        });
    }

    /**
     * Function to preload nickname in background while user is typing
     */
    function preloadNickname() {
        // Get current player data
        const playerData = [];
        
        // Collect input fields
        const playerFields = document.querySelectorAll('input[name="player_fields[]"]');
        if (playerFields.length > 0) {
            playerFields.forEach(field => {
                if (field.value.trim()) {
                    playerData.push(field.value.trim());
                }
            });
        }
        
        // Collect option fields
        const optionFields = document.querySelectorAll('select[name="option_fields[]"]');
        optionFields.forEach(field => {
            if (field.value.trim()) {
                playerData.push(field.value.trim());
            }
        });
        
        // Fallback to single player ID input
        if (playerData.length === 0) {
            const playerIdInput = document.getElementById('player-id');
            if (playerIdInput && playerIdInput.value.trim()) {
                playerData.push(playerIdInput.value.trim());
            }
        }
        
        // Only preload if we have data, product is selected, and preloading is enabled
        if (playerData.length > 0 && selectedProduct && window.preloadEnabled !== false) {
            // Use setTimeout to debounce the preload
            clearTimeout(window.preloadTimeout);
            window.preloadTimeout = setTimeout(() => {
                // Use requestIdleCallback if available for better performance
                if (window.requestIdleCallback) {
                    window.requestIdleCallback(() => {
                        preloadNicknameData(playerData);
                    }, { timeout: 1000 });
                } else {
                    preloadNicknameData(playerData);
                }
            }, 500); // Wait 500ms after user stops typing
        }
    }
    
    /**
     * Function to preload nickname data in background
     */
    async function preloadNicknameData(playerData) {
        const gameId = document.querySelector('[data-game-id]')?.dataset.gameId;
        if (!gameId) return;
        
        // Create cache key
        const cacheKey = `${gameId}_${playerData.join('_')}`;
        
        // Skip if already cached
        if (window.nicknameCache && window.nicknameCache[cacheKey]) return;
        
        // Prepare data for API call
        const formData = new FormData();
        formData.append('game_id', gameId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
        
        // Add player fields
        playerData.forEach((field, index) => {
            formData.append(`player_fields[${index}]`, field);
        });
        
        // Add option fields
        const optionFields = document.querySelectorAll('select[name="option_fields[]"]');
        optionFields.forEach((field, index) => {
            if (field.value.trim()) {
                formData.append(`player_fields[${playerData.length + index}]`, field.value.trim());
            }
        });
        
        try {
            const response = await fetch('/checkout/check-nickname', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            const data = await response.json();
            
            // Cache the result silently
            if (!window.nicknameCache) window.nicknameCache = {};
            if (data.success && data.nickname) {
                window.nicknameCache[cacheKey] = {
                    nickname: data.nickname,
                    className: 'text-white text-sm font-medium'
                };
            } else {
                window.nicknameCache[cacheKey] = {
                    nickname: data.message || 'Tidak ditemukan',
                    className: 'text-red-400 text-sm'
                };
            }
        } catch (error) {
            // Silent fail for preloading
            console.log('Preload nickname failed:', error);
        }
    }
    
    /**
     * Function to get nickname optimized - langsung tampil hasil tanpa loading state
     */
    async function getNicknameOptimized(playerData) {
        if (!playerData || playerData.length === 0) {
            const nicknameElement = document.getElementById('modal-nickname');
            nicknameElement.textContent = 'Tidak ada data player';
            nicknameElement.className = 'text-red-400 text-sm';
            return;
        }

        const gameId = document.querySelector('[data-game-id]')?.dataset.gameId;
        if (!gameId) {
            const nicknameElement = document.getElementById('modal-nickname');
            nicknameElement.textContent = 'Game tidak ditemukan';
            nicknameElement.className = 'text-red-400 text-sm';
            return;
        }

        // Create cache key for this request
        const cacheKey = `${gameId}_${playerData.join('_')}`;
        
        // Check if we have cached result - INSTANT DISPLAY
        if (window.nicknameCache && window.nicknameCache[cacheKey]) {
            const cachedResult = window.nicknameCache[cacheKey];
            const nicknameElement = document.getElementById('modal-nickname');
            nicknameElement.textContent = cachedResult.nickname;
            nicknameElement.className = cachedResult.className;
            return;
        }

        // Prepare data for API call
        const formData = new FormData();
        formData.append('game_id', gameId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
        
        // Add player fields (input fields)
        playerData.forEach((field, index) => {
            formData.append(`player_fields[${index}]`, field);
        });
        
        // Add option fields to player_fields array
        const optionFields = document.querySelectorAll('select[name="option_fields[]"]');
        optionFields.forEach((field, index) => {
            if (field.value.trim()) {
                formData.append(`player_fields[${playerData.length + index}]`, field.value.trim());
            }
        });

        // Create AbortController for request cancellation
        const controller = new AbortController();
        
        // Store controller for potential cancellation
        window.currentNicknameRequest = controller;

        try {
            // Call API to get nickname with optimized performance
            const response = await fetch('/checkout/check-nickname', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
                signal: controller.signal
            });

            const data = await response.json();
            const nicknameElement = document.getElementById('modal-nickname');
            
            if (data.success && data.nickname) {
                // Nickname found - langsung tampil
                nicknameElement.textContent = data.nickname;
                nicknameElement.className = 'text-white text-sm font-medium';
                
                // Cache the result
                if (!window.nicknameCache) window.nicknameCache = {};
                window.nicknameCache[cacheKey] = {
                    nickname: data.nickname,
                    className: 'text-white text-sm font-medium'
                };
            } else {
                // Nickname not found - langsung tampil
                nicknameElement.textContent = data.message || 'Tidak ditemukan';
                nicknameElement.className = 'text-red-400 text-sm';
                
                // Cache the result
                if (!window.nicknameCache) window.nicknameCache = {};
                window.nicknameCache[cacheKey] = {
                    nickname: data.message || 'Tidak ditemukan',
                    className: 'text-red-400 text-sm'
                };
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                // Request was cancelled, do nothing
                return;
            }
            
            console.error('Error getting nickname:', error);
            const nicknameElement = document.getElementById('modal-nickname');
            nicknameElement.textContent = 'Gagal dicek';
            nicknameElement.className = 'text-red-400 text-sm';
        } finally {
            // Clear the current request reference
            if (window.currentNicknameRequest === controller) {
                window.currentNicknameRequest = null;
            }
        }
    }
    
    /**
     * Function to clean up nickname cache periodically
     */
    function cleanupNicknameCache() {
        if (window.nicknameCache) {
            const cacheKeys = Object.keys(window.nicknameCache);
            if (cacheKeys.length > 50) { // Keep only last 50 entries
                const keysToDelete = cacheKeys.slice(0, cacheKeys.length - 50);
                keysToDelete.forEach(key => {
                    delete window.nicknameCache[key];
                });
            }
        }
    }
    
    // Clean up cache every 5 minutes
    setInterval(cleanupNicknameCache, 5 * 60 * 1000);
    
    /**
     * Setup preload optimization using IntersectionObserver
     */
    function setupPreloadOptimization() {
        // Only preload when form is visible
        const playerInputSection = document.getElementById('player-input-section');
        if (playerInputSection && window.IntersectionObserver) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Form is visible, enable preloading
                        window.preloadEnabled = true;
                    } else {
                        // Form is not visible, disable preloading
                        window.preloadEnabled = false;
                    }
                });
            }, { threshold: 0.1 });
            
            observer.observe(playerInputSection);
        } else {
            // Fallback: always enable preloading
            window.preloadEnabled = true;
        }
    }
    
    // Initialize input fields
    setupInputFields();
    
    // Setup intersection observer for preloading optimization
    setupPreloadOptimization();
});
