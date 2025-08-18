// Cek Pesanan JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('#search-form');
    const orderIdInput = document.querySelector('#order_id');
    const searchButton = document.querySelector('#search-button');

    if (searchForm && orderIdInput && searchButton) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchOrder();
        });

        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            searchOrder();
        });

        // Enter key press
        orderIdInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchOrder();
            }
        });
    }

    function searchOrder() {
        const orderId = orderIdInput.value.trim();
        
        if (!orderId) {
            showMessage('Masukkan Order ID terlebih dahulu', 'error');
            return;
        }

        // Tampilkan loading
        showMessage('Mencari pesanan...', 'info');
        
        // Kirim request ke API
        fetch('/api/cek-pesanan/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect ke halaman payment confirmation
                window.location.href = `/checkout/payment/${data.data.order_id}`;
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Terjadi kesalahan saat mencari pesanan', 'error');
        });
    }



    function showMessage(message, type) {
        // Hapus pesan sebelumnya jika ada
        const existingMessage = document.querySelector('.message-toast');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageHtml = `
            <div class="message-toast fixed top-4 right-4 z-50">
                <div class="bg-gray-800 border border-gray-600 rounded-lg p-4 shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            ${type === 'error' ? 
                                '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                                type === 'info' ?
                                '<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>' :
                                '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                            }
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-white">${message}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', messageHtml);
        
        // Hapus pesan setelah 3 detik
        setTimeout(() => {
            const messageElement = document.querySelector('.message-toast');
            if (messageElement) {
                messageElement.remove();
            }
        }, 3000);
    }
});

 