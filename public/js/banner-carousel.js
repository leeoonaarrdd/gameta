;(function () {
  const container = document.getElementById('banner-carousel')
  if (!container) return

  const slides = Array.from(container.querySelectorAll('.banner-slide'))
  if (slides.length === 0) return

  const dotsContainer = document.getElementById('banner-dots')
  const progressEl = document.getElementById('banner-progress')
  const frameEl = document.getElementById('banner-frame')

  let currentIndex = 0
  const intervalMs = 5000
  let timer = null
  let rafId = null
  let progressStartTs = 0

  function updateDots() {
    if (!dotsContainer) return
    const dotButtons = Array.from(dotsContainer.querySelectorAll('button'))
    dotButtons.forEach((btn, i) => {
      if (i === currentIndex) {
        btn.style.backgroundColor = 'rgba(255,255,255,0.95)'
        btn.style.width = '14px'
        btn.style.height = '6px'
        btn.style.borderRadius = '9999px'
      } else {
        btn.style.backgroundColor = 'rgba(255,255,255,0.45)'
        btn.style.width = '10px'
        btn.style.height = '10px'
        btn.style.borderRadius = '9999px'
      }
    })
  }

  // Modern coverflow-like layout
  function applyCoverflow() {
    const total = slides.length
    slides.forEach((slide, index) => {
      let offset = (index - currentIndex + total) % total
      if (offset > total / 2) offset -= total
      const abs = Math.abs(offset)

      // Sedikit lebih rapat di mobile agar tidak terpotong
      const isMobile = window.matchMedia('(max-width: 640px)').matches
      const translateX = offset * (isMobile ? 28 : 40)
      const translateZ = -Math.min(abs * 80, 400)
      const rotateY = offset * -12
      const scale = 1 - Math.min(abs * 0.08, 0.4)
      const opacity = 1 - Math.min(abs * 0.18, 0.6)

      slide.style.transform = `translateX(${translateX}%) translateZ(${translateZ}px) rotateY(${rotateY}deg) scale(${scale})`
      slide.style.opacity = String(opacity)
      slide.style.zIndex = String(100 - abs)
      slide.style.filter = abs === 0 ? 'none' : 'brightness(0.85)'
      slide.style.transition = 'transform 700ms cubic-bezier(0.22, 1, 0.36, 1), opacity 500ms ease'
      
      // Add hover effect for center slide
      if (abs === 0) {
        slide.style.cursor = 'pointer'
        slide.setAttribute('data-center-slide', 'true')
      } else {
        slide.style.cursor = 'default'
        slide.removeAttribute('data-center-slide')
      }

      // Pastikan gambar tengah tampil utuh (tanpa terpotong)
      const fg = slide.querySelector('img.banner-foreground')
      if (fg) {
        // Selalu tampil utuh
        fg.style.objectFit = 'contain'
        fg.style.backgroundColor = 'transparent'
        // Sesuaikan tinggi frame terhadap rasio gambar, agar tidak ada ruang kosong
        if (abs === 0 && frameEl) {
          const img = fg
          // Jika dimensi asli belum tersedia, gunakan naturalWidth/Height
          const w = img.naturalWidth || img.width
          const h = img.naturalHeight || img.height
          if (w > 0 && h > 0) {
            // Lebar kartu tengah mengikuti utilitas Tailwind pada slide: w-[80%]/85%/70%
            const slideWidthRatio = isMobile ? 0.8 : (window.matchMedia('(min-width: 640px) and (max-width: 767px)').matches ? 0.85 : 0.7)
            const viewportW = container.clientWidth * slideWidthRatio
            const targetHeight = Math.min(viewportW * (h / w), window.innerHeight * 0.5)
            const minHeight = isMobile ? 160 : 240
            frameEl.style.height = `${Math.max(minHeight, Math.round(targetHeight))}px`
          }
        }
      }
    })
    updateDots()
  }

  function goTo(next) {
    currentIndex = (next + slides.length) % slides.length
    applyCoverflow()
    restartProgress()
  }

  // Initialize styles
  slides.forEach((slide) => {
    slide.style.left = '0'
    slide.style.right = '0'
    slide.style.top = '0'
    slide.style.bottom = '0'
    slide.style.margin = 'auto'
    slide.style.pointerEvents = 'auto'
    slide.style.willChange = 'transform, opacity'
    slide.style.cursor = 'pointer'
  })
  applyCoverflow()

  function tickProgress(ts) {
    if (!progressEl) return
    if (progressStartTs === 0) progressStartTs = ts
    const elapsed = ts - progressStartTs
    const ratio = Math.max(0, Math.min(1, elapsed / intervalMs))
    progressEl.style.width = `${ratio * 100}%`
    if (ratio >= 1) {
      progressStartTs = 0
      goTo(currentIndex + 1)
      return
    }
    rafId = requestAnimationFrame(tickProgress)
  }

  function start() {
    stop()
    progressStartTs = 0
    rafId = requestAnimationFrame(tickProgress)
    timer = setInterval(() => goTo(currentIndex + 1), intervalMs)
  }

  function stop() {
    if (timer) clearInterval(timer)
    if (rafId) cancelAnimationFrame(rafId)
    timer = null
    rafId = null
  }

  function restartProgress() {
    if (!progressEl) return
    progressEl.style.width = '0%'
    progressStartTs = 0
  }

  // Auto start
  start()

  // Re-apply layout setelah gambar betul-betul termuat
  slides.forEach((slide) => {
    const img = slide.querySelector('img.banner-foreground')
    if (!img) return
    if (img.complete) {
      applyCoverflow()
    } else {
      img.addEventListener('load', applyCoverflow, { once: true })
    }
  })

  // Re-apply saat resize agar ukuran frame konsisten di mobile/desktop
  window.addEventListener('resize', () => {
    applyCoverflow()
  })

  // Pause on hover/touch for better UX
  container.addEventListener('mouseenter', () => {
    stop()
  })
  container.addEventListener('mouseleave', () => {
    start()
  })

  // Swipe support
  let startX = null
  container.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX
    stop()
  })
  container.addEventListener('touchend', (e) => {
    if (startX == null) return
    const delta = e.changedTouches[0].clientX - startX
    if (Math.abs(delta) > 30) {
      if (delta < 0) goTo(currentIndex + 1)
      else goTo(currentIndex - 1)
    } else {
      applyCoverflow()
    }
    startX = null
    start()
  })

  // Dot navigation
  if (dotsContainer) {
    dotsContainer.addEventListener('click', (e) => {
      const target = e.target
      if (!(target instanceof HTMLElement)) return
      const indexStr = target.getAttribute('data-index')
      if (!indexStr) return
      const idx = parseInt(indexStr, 10)
      if (Number.isNaN(idx)) return
      goTo(idx)
    })
  }

  // Click on center banner to go to next slide
  container.addEventListener('click', (e) => {
    const clickedSlide = e.target.closest('.banner-slide')
    if (!clickedSlide) return
    
    // Check if the clicked slide is the center one (current index)
    const slideIndex = slides.indexOf(clickedSlide)
    if (slideIndex === currentIndex) {
      // Go to next slide
      goTo(currentIndex + 1)
    }
  })
})()
