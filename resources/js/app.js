// Import dependencies
import './bootstrap';

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initializeSearch();
    initializeGameCards();
});

// Search functionality
function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[placeholder*="Cari"]');
    
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    console.log('Searching for:', searchTerm);
                }
            }
        });
    });
}

// Game cards hover effects
function initializeGameCards() {
    const gameCards = document.querySelectorAll('.bg-gray-800, .bg-gray-700');
    gameCards.forEach(card => {
        card.addEventListener('click', function() {
            const gameName = this.querySelector('h3')?.textContent;
            if (gameName) {
                console.log('Selected game:', gameName);
            }
        });
    });
}

