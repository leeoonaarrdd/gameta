// Animasi On Scroll
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Tambahkan class animate-in saat elemen terlihat
                entry.target.classList.add('animate-in');
            } else {
                // Hapus class animate-in saat elemen keluar dari viewport
                // Ini akan membuat animasi berulang setiap kali elemen masuk lagi
                // Kecuali jika elemen memiliki class animate-once
                if (!entry.target.classList.contains('animate-once')) {
                    entry.target.classList.remove('animate-in');
                }
            }
        });
    }, observerOptions);

    // Observe semua elemen dengan class animate-on-scroll
    const animatedElements = document.querySelectorAll(`
        .animate-on-scroll, 
        .animate-on-scroll-left, 
        .animate-on-scroll-right, 
        .animate-on-scroll-scale,
        .animate-on-scroll-fade,
        .animate-on-scroll-bounce,
        .animate-on-scroll-rotate,
        .animate-on-scroll-bottom,
        .animate-on-scroll-top,
        .animate-on-scroll-zoom,
        .animate-on-scroll-slide-up,
        .animate-on-scroll-flip
    `);
    
    animatedElements.forEach(el => {
        observer.observe(el);
    });

    // Tambahan: Animasi untuk elemen yang sudah terlihat saat halaman dimuat
    setTimeout(() => {
        const visibleElements = document.querySelectorAll(`
            .animate-on-scroll, 
            .animate-on-scroll-left, 
            .animate-on-scroll-right, 
            .animate-on-scroll-scale,
            .animate-on-scroll-fade,
            .animate-on-scroll-bounce,
            .animate-on-scroll-rotate,
            .animate-on-scroll-bottom,
            .animate-on-scroll-top,
            .animate-on-scroll-zoom,
            .animate-on-scroll-slide-up,
            .animate-on-scroll-flip
        `);
        
        visibleElements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                el.classList.add('animate-in');
            }
        });
    }, 100);
});
