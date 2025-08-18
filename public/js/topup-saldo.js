document.addEventListener('DOMContentLoaded', function() {
    let selectedPaymentMethod = null;
    let selectedPaymentMethodMobile = null;
    let selectedPaymentMethodName = null;
    let selectedPaymentMethodMobileName = null;

    // Desktop payment method selection
    const paymentButtons = document.querySelectorAll('.payment-method-btn');
    paymentButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state from all buttons
            paymentButtons.forEach(btn => {
                btn.classList.remove('bg-purple-600/50', 'border-purple-500');
                btn.classList.add('bg-gray-800/50', 'border-gray-600/30');
            });
            
            // Add active state to clicked button
            this.classList.remove('bg-gray-800/50', 'border-gray-600/30');
            this.classList.add('bg-purple-600/50', 'border-purple-500');
            
            selectedPaymentMethod = this.getAttribute('data-method');
            selectedPaymentMethodName = this.getAttribute('data-method-name');
        });
    });

    // Mobile payment method selection
    const paymentButtonsMobile = document.querySelectorAll('.payment-method-btn-mobile');
    paymentButtonsMobile.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state from all buttons
            paymentButtonsMobile.forEach(btn => {
                btn.classList.remove('bg-purple-600/50', 'border-purple-500');
                btn.classList.add('bg-gray-800/50', 'border-gray-600/30');
            });
            
            // Add active state to clicked button
            this.classList.remove('bg-gray-800/50', 'border-gray-600/30');
            this.classList.add('bg-purple-600/50', 'border-purple-500');
            
            selectedPaymentMethodMobile = this.getAttribute('data-method');
            selectedPaymentMethodMobileName = this.getAttribute('data-method-name');
        });
    });

    // Topup button click handlers
    const topupBtn = document.getElementById('topup-btn');
    const topupBtnMobile = document.getElementById('topup-btn-mobile');
    
    if (topupBtn) {
        topupBtn.addEventListener('click', function() {
            const amount = document.getElementById('topup-amount').value;
            handleTopup(amount, selectedPaymentMethod);
        });
    }

    if (topupBtnMobile) {
        topupBtnMobile.addEventListener('click', function() {
            const amount = document.getElementById('topup-amount-mobile').value;
            handleTopup(amount, selectedPaymentMethodMobile);
        });
    }

    // Cancel button click handlers
    const cancelBtn = document.getElementById('cancel-btn');
    const cancelBtnMobile = document.getElementById('cancel-btn-mobile');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            window.location.href = '/member/dashboard';
        });
    }

    if (cancelBtnMobile) {
        cancelBtnMobile.addEventListener('click', function() {
            window.location.href = '/member/dashboard';
        });
    }

    function handleTopup(amount, paymentMethod) {
        if (!amount || amount < 10000) {
            alert('Minimal topup saldo Rp 10.000');
            return;
        }

        if (!paymentMethod) {
            alert('Silakan pilih metode pembayaran');
            return;
        }

        // Get payment method name for display
        const selectedMethodName = selectedPaymentMethodName || selectedPaymentMethodMobileName;

        // Show loading state
        const topupBtn = document.getElementById('topup-btn');
        const topupBtnMobile = document.getElementById('topup-btn-mobile');
        
        if (topupBtn) {
            topupBtn.disabled = true;
            topupBtn.textContent = 'Memproses...';
        }
        
        if (topupBtnMobile) {
            topupBtnMobile.disabled = true;
            topupBtnMobile.textContent = 'Memproses...';
        }

        // Make AJAX call to process topup
        fetch('/api/topup/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                amount: amount,
                payment_method_id: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to invoice page
                window.location.href = '/invoice-topup/' + data.data.topup_id;
            } else {
                alert(data.message || 'Terjadi kesalahan saat memproses topup');
                
                // Reset button state
                if (topupBtn) {
                    topupBtn.disabled = false;
                    topupBtn.textContent = 'Topup Saldo';
                }
                
                if (topupBtnMobile) {
                    topupBtnMobile.disabled = false;
                    topupBtnMobile.textContent = 'Topup Saldo';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses topup');
            
            // Reset button state
            if (topupBtn) {
                topupBtn.disabled = false;
                topupBtn.textContent = 'Topup Saldo';
            }
            
            if (topupBtnMobile) {
                topupBtnMobile.disabled = false;
                topupBtnMobile.textContent = 'Topup Saldo';
            }
        });
    }

    // Format amount input with thousand separator
    const amountInputs = document.querySelectorAll('#topup-amount, #topup-amount-mobile');
    amountInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
                this.value = value;
            }
        });
        
        input.addEventListener('blur', function() {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                this.value = parseInt(value);
            }
        });
    });
});
