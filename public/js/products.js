function deleteProduct(productId) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        fetch(`/admin/products/${productId}`, {
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
                // Reload halaman setelah berhasil dihapus
                window.location.reload();
            } else {
                alert('Gagal menghapus produk: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus produk');
        });
    }
}

// Auto calculate margins when prices change
document.addEventListener('DOMContentLoaded', function() {
    const originalPriceInput = document.getElementById('original_price');
    const priceTamuInput = document.getElementById('price_tamu');
    const priceMemberInput = document.getElementById('price_member');
    const marginTamuInput = document.getElementById('margin_tamu');
    const marginMemberInput = document.getElementById('margin_member');

    if (originalPriceInput && priceTamuInput && priceMemberInput && marginTamuInput && marginMemberInput) {
        // Calculate margins when original price changes
        originalPriceInput.addEventListener('input', function() {
            calculateMargins();
        });

        // Calculate margins when tamu price changes
        priceTamuInput.addEventListener('input', function() {
            calculateMargins();
        });

        // Calculate margins when member price changes
        priceMemberInput.addEventListener('input', function() {
            calculateMargins();
        });

        // Calculate margins when margin inputs change
        marginTamuInput.addEventListener('input', function() {
            calculatePricesFromMargins();
        });

        marginMemberInput.addEventListener('input', function() {
            calculatePricesFromMargins();
        });

        function calculateMargins() {
            const originalPrice = parseInt(originalPriceInput.value) || 0;
            const priceTamu = parseInt(priceTamuInput.value) || 0;
            const priceMember = parseInt(priceMemberInput.value) || 0;

            const marginTamu = Math.max(0, priceTamu - originalPrice);
            const marginMember = Math.max(0, priceMember - originalPrice);

            marginTamuInput.value = marginTamu;
            marginMemberInput.value = marginMember;
        }

        function calculatePricesFromMargins() {
            const originalPrice = parseInt(originalPriceInput.value) || 0;
            const marginTamu = parseInt(marginTamuInput.value) || 0;
            const marginMember = parseInt(marginMemberInput.value) || 0;

            const priceTamu = originalPrice + marginTamu;
            const priceMember = originalPrice + marginMember;

            priceTamuInput.value = priceTamu;
            priceMemberInput.value = priceMember;
        }

        // Initial calculation
        calculateMargins();
    }
});
