function checkStatus(topupId) {
    fetch(`/api/topup/status/${topupId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Status TopUp ${topupId}: ${data.data.status.toUpperCase()}`);
            } else {
                alert('Gagal mendapatkan status topup');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengecek status');
        });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchInputMobile = document.getElementById('searchInputMobile');
    
    function filterTable(searchTerm) {
        const tableRows = document.querySelectorAll('tbody tr');
        const mobileCards = document.querySelectorAll('.space-y-4 > div');
        
        // Filter desktop table
        tableRows.forEach(row => {
            const topupId = row.querySelector('td:first-child a')?.textContent || '';
            const isVisible = topupId.toLowerCase().includes(searchTerm.toLowerCase());
            row.style.display = isVisible ? '' : 'none';
        });
        
        // Filter mobile cards
        mobileCards.forEach(card => {
            const topupId = card.querySelector('a')?.textContent || '';
            const isVisible = topupId.toLowerCase().includes(searchTerm.toLowerCase());
            card.style.display = isVisible ? '' : 'none';
        });
    }
    
    // Desktop search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTable(this.value);
        });
    }
    
    // Mobile search
    if (searchInputMobile) {
        searchInputMobile.addEventListener('input', function() {
            filterTable(this.value);
        });
    }
});
