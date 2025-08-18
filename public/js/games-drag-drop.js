// Drag & Drop functionality for Games
class GamesDragDrop {
    constructor() {
        this.draggedElement = null;
        this.draggedIndex = null;
        this.originalOrder = [];
        this.tableBody = document.querySelector('tbody');
        
        this.init();
    }
    
    init() {
        this.initializeDragAndDrop();
    }
    
    initializeDragAndDrop() {
        const gameRows = document.querySelectorAll('.game-row');
        
        gameRows.forEach((row, index) => {
            // Store original order
            this.originalOrder[index] = {
                id: row.dataset.gameId,
                order: parseInt(row.dataset.order)
            };
            
            // Mouse events for entire row
            row.addEventListener('mousedown', this.startDrag.bind(this));
            row.addEventListener('touchstart', this.startDrag.bind(this), { passive: false });
            
            // Prevent drag on action buttons
            const actionButtons = row.querySelectorAll('a, button');
            actionButtons.forEach(button => {
                button.addEventListener('mousedown', (e) => e.stopPropagation());
                button.addEventListener('touchstart', (e) => e.stopPropagation(), { passive: false });
            });
        });
    }
    
    startDrag(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const row = e.target.closest('.game-row');
        if (!row) return;
        
        // Don't start drag if clicking on action buttons
        if (e.target.closest('a, button')) return;
        
        this.draggedElement = row;
        this.draggedIndex = Array.from(this.tableBody.querySelectorAll('.game-row')).indexOf(row);
        
        // Add dragging class
        row.classList.add('dragging');
        
        // Add event listeners for drag
        document.addEventListener('mousemove', this.onDrag.bind(this));
        document.addEventListener('touchmove', this.onDrag.bind(this), { passive: false });
        document.addEventListener('mouseup', this.stopDrag.bind(this));
        document.addEventListener('touchend', this.stopDrag.bind(this));
        
        // Prevent text selection
        document.body.style.userSelect = 'none';
    }
    
    onDrag(e) {
        e.preventDefault();
        if (!this.draggedElement) return;
        
        const clientY = e.type === 'mousemove' ? e.clientY : e.touches[0].clientY;
        const rows = Array.from(this.tableBody.querySelectorAll('.game-row'));
        
        // Remove drag-over class from all rows
        rows.forEach(row => row.classList.remove('drag-over'));
        
        // Find the row we're hovering over
        const targetRow = document.elementFromPoint(e.clientX || e.touches[0].clientX, clientY)?.closest('.game-row');
        
        if (targetRow && targetRow !== this.draggedElement) {
            // Add drag-over class to target row
            targetRow.classList.add('drag-over');
            
            const targetIndex = rows.indexOf(targetRow);
            
            // Move the dragged element
            if (targetIndex > this.draggedIndex) {
                targetRow.parentNode.insertBefore(this.draggedElement, targetRow.nextSibling);
            } else {
                targetRow.parentNode.insertBefore(this.draggedElement, targetRow);
            }
            
            // Update indices
            const newRows = Array.from(this.tableBody.querySelectorAll('.game-row'));
            this.draggedIndex = newRows.indexOf(this.draggedElement);
        }
    }
    
    stopDrag() {
        if (!this.draggedElement) return;
        
        // Remove dragging class and drag-over classes
        this.draggedElement.classList.remove('dragging');
        document.querySelectorAll('.game-row').forEach(row => row.classList.remove('drag-over'));
        
        // Remove event listeners
        document.removeEventListener('mousemove', this.onDrag.bind(this));
        document.removeEventListener('touchmove', this.onDrag.bind(this));
        document.removeEventListener('mouseup', this.stopDrag.bind(this));
        document.removeEventListener('touchend', this.stopDrag.bind(this));
        
        // Restore text selection
        document.body.style.userSelect = '';
        
        // Update order in database
        this.updateOrderInDatabase();
        
        this.draggedElement = null;
        this.draggedIndex = null;
    }
    
    updateOrderInDatabase() {
        const rows = Array.from(this.tableBody.querySelectorAll('.game-row'));
        const orders = rows.map((row, index) => ({
            id: row.dataset.gameId,
            order: index + 1
        }));
        
        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'fixed top-4 right-4 bg-purple-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan urutan...';
        document.body.appendChild(loadingIndicator);
        
        fetch(window.gamesUpdateOrderRoute || '/admin/games/update-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ orders: orders })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update data attributes
                rows.forEach((row, index) => {
                    row.dataset.order = index + 1;
                });
                
                // Show success message
                loadingIndicator.innerHTML = '<i class="fas fa-check mr-2"></i>Urutan berhasil disimpan!';
                loadingIndicator.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                
                setTimeout(() => {
                    document.body.removeChild(loadingIndicator);
                }, 2000);
            } else {
                throw new Error(data.message || 'Gagal menyimpan urutan');
            }
        })
        .catch(error => {
            console.error('Error updating order:', error);
            
            // Show error message
            loadingIndicator.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Gagal menyimpan urutan!';
            loadingIndicator.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            
            setTimeout(() => {
                document.body.removeChild(loadingIndicator);
            }, 3000);
            
            // Revert to original order
            this.revertToOriginalOrder();
        });
    }
    
    revertToOriginalOrder() {
        const rows = Array.from(this.tableBody.querySelectorAll('.game-row'));
        
        // Sort rows back to original order
        rows.sort((a, b) => {
            const aId = a.dataset.gameId;
            const bId = b.dataset.gameId;
            const aOriginal = this.originalOrder.find(item => item.id == aId);
            const bOriginal = this.originalOrder.find(item => item.id == bId);
            return aOriginal.order - bOriginal.order;
        });
        
        // Re-append rows in correct order
        rows.forEach(row => {
            this.tableBody.appendChild(row);
        });
    }
    
    // Method to reinitialize after AJAX content update
    reinitialize() {
        this.originalOrder = [];
        this.initializeDragAndDrop();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.gamesDragDrop = new GamesDragDrop();
});
