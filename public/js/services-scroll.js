// Remove no-js class when JavaScript is available
document.documentElement.classList.remove('no-js');

function initServicesScroll() {
    const container = document.querySelector('.services-scroll-container');
    const track = document.querySelector('.services-scroll-track');
    
    if (!container || !track) return;
    
    // Remove no-js class
    container.classList.remove('no-js');
    
    const items = track.querySelectorAll('.service-item');
    if (items.length === 0) return;
    
    // Calculate total width of first set of items
    let totalWidth = 0;
    items.forEach((item, index) => {
        if (index < items.length / 2) { // Only count first set
            totalWidth += item.offsetWidth + 16; // 16px for gap
        }
    });
    
    if (totalWidth === 0) return;
    
    let position = 0;
    let speed = 1;
    let animationId = null;
    let isPaused = false;
    
    function animate() {
        if (!isPaused) {
            position -= speed;
            
            // Reset when we've scrolled half the total width
            if (Math.abs(position) >= totalWidth) {
                position = 0;
            }
            
            track.style.transform = `translateX(${position}px)`;
        }
        
        animationId = requestAnimationFrame(animate);
    }
    
    // Start animation
    animate();
    
    // Pause on hover
    container.addEventListener('mouseenter', () => isPaused = true);
    container.addEventListener('mouseleave', () => isPaused = false);
    
    // Adjust speed based on screen size
    function updateSpeed() {
        const width = window.innerWidth;
        if (width < 768) {
            speed = 0.5;
        } else if (width < 1024) {
            speed = 0.8;
        } else {
            speed = 1;
        }
    }
    
    updateSpeed();
    window.addEventListener('resize', updateSpeed);
    
    // Cleanup
    window.addEventListener('beforeunload', () => {
        if (animationId) {
            cancelAnimationFrame(animationId);
        }
    });
}

// Initialize when ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initServicesScroll);
} else {
    initServicesScroll();
}
