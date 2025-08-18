class RealtimeSearch {
    constructor() {
        this.searchInput = null;
        this.searchResults = null;
        this.searchTimeout = null;
        this.isLoading = false;
        this.currentQuery = '';
        
        this.init();
    }

    init() {
        // Desktop search
        this.searchInput = document.querySelector('.search-input-desktop');
        this.searchResults = document.querySelector('.search-results-desktop');
        
        if (this.searchInput && this.searchResults) {
            this.setupSearch(this.searchInput, this.searchResults);
        }

        // Mobile search
        const mobileSearchInput = document.querySelector('.search-input-mobile');
        const mobileSearchResults = document.querySelector('.search-results-mobile');
        
        if (mobileSearchInput && mobileSearchResults) {
            this.setupSearch(mobileSearchInput, mobileSearchResults);
        }

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideAllResults();
            }
        });

        // Handle keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideAllResults();
            }
        });

        // Hide results on scroll or resize
        window.addEventListener('scroll', () => {
            this.hideAllResults();
        });

        window.addEventListener('resize', () => {
            this.hideAllResults();
        });
    }

    setupSearch(input, resultsContainer) {
        // Create results container if it doesn't exist
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.className = 'search-results fixed top-20 left-1/2 transform -translate-x-1/2 w-80 lg:w-96 bg-gray-900/95 backdrop-blur-md border border-gray-700/50 rounded-lg shadow-2xl z-[9999] max-h-96 overflow-y-auto';
            document.body.appendChild(resultsContainer);
        }

        // Add event listeners
        input.addEventListener('input', (e) => {
            this.handleSearch(e.target.value, resultsContainer);
        });

        input.addEventListener('focus', () => {
            if (input.value.length >= 2) {
                this.showResults(resultsContainer);
            }
        });

        input.addEventListener('blur', () => {
            // Delay hiding to allow clicking on results
            setTimeout(() => {
                if (!resultsContainer.contains(document.activeElement)) {
                    this.hideResults(resultsContainer);
                }
            }, 200);
        });
    }

    async handleSearch(query, resultsContainer) {
        this.currentQuery = query.trim();
        
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }

        // Hide results if query is too short
        if (this.currentQuery.length < 2) {
            this.hideResults(resultsContainer);
            return;
        }

        // Set loading state
        this.setLoading(resultsContainer, true);

        // Debounce search
        this.searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(this.currentQuery)}`);
                const data = await response.json();

                if (data.success) {
                    this.displayResults(data.data, resultsContainer);
                } else {
                    this.showError(resultsContainer, 'Terjadi kesalahan saat mencari');
                }
            } catch (error) {
                console.error('Search error:', error);
                this.showError(resultsContainer, 'Terjadi kesalahan saat mencari');
            } finally {
                this.setLoading(resultsContainer, false);
            }
        }, 300);
    }

    displayResults(data, resultsContainer) {
        const { games, categories } = data;
        
        if (games.length === 0 && categories.length === 0) {
            this.showNoResults(resultsContainer);
            return;
        }

        let html = '';

        // Display games
        if (games.length > 0) {
            html += `
                <div class="p-3 border-b border-gray-700/50">
                    <h3 class="text-sm font-semibold text-purple-400 mb-2">Games (${games.length})</h3>
                    <div class="space-y-2">
            `;
            
            games.forEach(game => {
                html += `
                    <a href="${game.url}" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-800/50 transition-colors group">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-gray-700/50">
                            ${game.gambar ? 
                                `<img src="${game.gambar}" alt="${game.name}" class="w-full h-full object-cover">` :
                                `<div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>`
                            }
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white group-hover:text-purple-400 transition-colors truncate">${game.name}</p>
                            <p class="text-xs text-gray-400 truncate">${game.sub_judul || 'Game'}</p>
                            ${game.category ? `<p class="text-xs text-purple-400/70">${game.category}</p>` : ''}
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }

        // Display categories
        if (categories.length > 0) {
            html += `
                <div class="p-3">
                    <h3 class="text-sm font-semibold text-purple-400 mb-2">Kategori (${categories.length})</h3>
                    <div class="space-y-2">
            `;
            
            categories.forEach(category => {
                html += `
                    <div class="p-2 rounded-lg hover:bg-gray-800/50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-white">${category.name}</h4>
                            <span class="text-xs text-gray-400">${category.game_count} games</span>
                        </div>
                        
                        ${category.games.length > 0 ? `
                            <div class="flex space-x-2 overflow-x-auto">
                                ${category.games.map(game => `
                                    <a href="${game.url}" class="flex-shrink-0 group">
                                        <div class="w-8 h-8 rounded overflow-hidden bg-gray-700/50">
                                            ${game.gambar ? 
                                                `<img src="${game.gambar}" alt="${game.name}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">` :
                                                `<div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>`
                                            }
                                        </div>
                                    </a>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }

        resultsContainer.innerHTML = html;
        this.showResults(resultsContainer);
    }

    setLoading(resultsContainer, loading) {
        this.isLoading = loading;
        
        if (loading) {
            resultsContainer.innerHTML = `
                <div class="p-4 text-center">
                    <div class="inline-flex items-center space-x-2">
                        <svg class="animate-spin w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-400">Mencari...</span>
                    </div>
                </div>
            `;
            this.showResults(resultsContainer);
        }
    }

    showNoResults(resultsContainer) {
        resultsContainer.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm text-gray-400">Tidak ada hasil ditemukan</p>
                <p class="text-xs text-gray-500 mt-1">Coba kata kunci lain</p>
            </div>
        `;
        this.showResults(resultsContainer);
    }

    showError(resultsContainer, message) {
        resultsContainer.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-400">${message}</p>
            </div>
        `;
        this.showResults(resultsContainer);
    }

    showResults(resultsContainer) {
        resultsContainer.classList.remove('hidden');
        resultsContainer.classList.add('block');
    }

    hideResults(resultsContainer) {
        resultsContainer.classList.add('hidden');
        resultsContainer.classList.remove('block');
    }

    hideAllResults() {
        const allResults = document.querySelectorAll('.search-results');
        allResults.forEach(results => {
            this.hideResults(results);
        });
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new RealtimeSearch();
});
