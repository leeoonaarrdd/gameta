// Payment Confirmation Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    let checkStatusInterval;
    let retryCount = 0;
    const maxRetries = 3;

    // Countdown Timer
    function startCountdown() {
        // Check if we have expired_at from database
        const expiredAtElement = document.querySelector('[data-expired-at]');
        let countdownTime;
        
        if (expiredAtElement && expiredAtElement.dataset.expiredAt) {
            // Use expired_at from database
            countdownTime = new Date(expiredAtElement.dataset.expiredAt).getTime();
        } else {
            // Fallback to 30 minutes from now (for session data)
            countdownTime = new Date().getTime() + (30 * 60 * 1000);
        }
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = countdownTime - now;
            
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const minutesElement = document.getElementById('minutes');
            const secondsElement = document.getElementById('seconds');
            
            if (minutesElement && secondsElement) {
                minutesElement.textContent = minutes;
                secondsElement.textContent = seconds;
            }
            
            if (distance < 0) {
                clearInterval(timer);
                const countdownElement = document.getElementById('countdown');
                if (countdownElement) {
                    countdownElement.innerHTML = '<span class="text-red-400">Waktu Habis</span>';
                }
                
                // Stop checking payment status when expired
                if (checkStatusInterval) {
                    clearInterval(checkStatusInterval);
                }
                
                showNotification('Waktu pembayaran telah habis. Pesanan akan dibatalkan otomatis.', 'error');
            }
        }, 1000);
    }

    // Copy to clipboard function
    function copyToClipboard(text) {
        if (!text) {
            showNotification('Tidak ada teks untuk disalin', 'error');
            return;
        }

        navigator.clipboard.writeText(text).then(function() {
            showNotification('Berhasil disalin ke clipboard!', 'success');
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            showNotification('Gagal menyalin ke clipboard', 'error');
        });
    }

    // Check payment status with retry mechanism
    function checkPaymentStatus() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');
        
        // Get order ID from the page
        const orderIdElement = document.getElementById('order-id');
        const orderId = orderIdElement ? orderIdElement.textContent.trim() : null;
        
        if (!orderId) {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
            showNotification('Order ID tidak ditemukan', 'error');
            return;
        }

        // Check if this is a Tripay payment
        const tripayReference = document.querySelector('[data-tripay-reference]');
        
        if (tripayReference && tripayReference.dataset.tripayReference) {
            // Check Tripay status
            checkTripayStatus(tripayReference.dataset.tripayReference, loadingOverlay);
        } else {
            // Fallback to regular status check
            setTimeout(() => {
                if (loadingOverlay) loadingOverlay.classList.add('hidden');
                showNotification('Status pembayaran sedang diperiksa...', 'info');
            }, 2000);
        }
    }

    // Check Tripay status specifically
    function checkTripayStatus(reference, loadingOverlay) {
        fetch('/api/tripay/check-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reference: reference
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
            
            if (data.success) {
                const status = data.data.status;
                handlePaymentStatus(status, data.data);
            } else {
                handlePaymentError(data.message);
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            
            // Retry mechanism for network errors
            if (retryCount < maxRetries) {
                retryCount++;
                showNotification(`Gagal memeriksa status pembayaran. Mencoba lagi... (${retryCount}/${maxRetries})`, 'warning');
                
                setTimeout(() => {
                    checkTripayStatus(reference, loadingOverlay);
                }, 3000); // Wait 3 seconds before retry
            } else {
                if (loadingOverlay) loadingOverlay.classList.add('hidden');
                showNotification('Gagal memeriksa status pembayaran setelah beberapa percobaan', 'error');
                retryCount = 0; // Reset retry count
            }
        });
    }

    // Handle payment status response
    function handlePaymentStatus(status, data) {
        switch (status) {
            case 'PAID':
                showNotification('Pembayaran berhasil! Pesanan akan diproses segera.', 'success');
                // Reload page after 3 seconds to show updated status
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
                break;
            case 'EXPIRED':
                showNotification('Pembayaran telah kedaluwarsa', 'error');
                break;
            case 'FAILED':
                showNotification('Pembayaran gagal', 'error');
                break;
            case 'CANCELLED':
                showNotification('Pembayaran dibatalkan', 'error');
                break;
            case 'UNPAID':
            case 'PENDING':
                showNotification('Pembayaran masih dalam proses', 'info');
                break;
            default:
                showNotification('Status pembayaran: ' + status, 'info');
        }
    }

    // Handle payment error
    function handlePaymentError(message) {
        showNotification('Gagal memeriksa status pembayaran: ' + message, 'error');
    }

    // Auto-check payment status every 30 seconds
    function startAutoCheckStatus() {
        // Check if this is a Tripay payment
        const tripayReference = document.querySelector('[data-tripay-reference]');
        
        if (tripayReference && tripayReference.dataset.tripayReference) {
            checkStatusInterval = setInterval(() => {
                // Only check if page is visible
                if (!document.hidden) {
                    checkPaymentStatus();
                }
            }, 30000); // Check every 30 seconds
        }
    }

    // Notification function with enhanced styling
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
        
        // Set background color based on type
        switch (type) {
            case 'success':
                notification.className += ' bg-green-600 text-white';
                break;
            case 'error':
                notification.className += ' bg-red-600 text-white';
                break;
            case 'warning':
                notification.className += ' bg-yellow-600 text-white';
                break;
            default:
                notification.className += ' bg-blue-600 text-white';
        }
        
        // Set content
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    ${getNotificationIcon(type)}
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

    // Get notification icon based on type
    function getNotificationIcon(type) {
        switch (type) {
            case 'success':
                return '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            case 'error':
                return '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            case 'warning':
                return '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            default:
                return '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }
    }

    // Make functions globally available
    window.copyToClipboard = copyToClipboard;
    window.checkPaymentStatus = checkPaymentStatus;
    window.showNotification = showNotification;

    // Start countdown when page loads
    startCountdown();
    
    // Start auto-check payment status
    startAutoCheckStatus();
    
    // Check payment status when page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Check status when page becomes visible
            setTimeout(() => {
                checkPaymentStatus();
            }, 1000);
        }
    });
});
