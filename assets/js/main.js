/**
 * FUNKTOGRAPHER - Ultra-Fast Vanilla JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Sticky Header Scroll Effect (Zero forced reflow)
  const header = document.querySelector('.site-header');
  if (header) {
    let ticking = false;
    const updateHeader = () => {
      header.classList.toggle('scrolled', window.scrollY > 40);
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        window.requestAnimationFrame(updateHeader);
        ticking = true;
      }
    }, { passive: true });

    // Run asynchronously after first paint to eliminate forced layout calculation
    if ('requestIdleCallback' in window) {
      requestIdleCallback(updateHeader);
    } else {
      setTimeout(updateHeader, 60);
    }
  }

  // 2. Off-canvas Drawer Navigation
  const drawerBtn = document.getElementById('drawerOpenBtn');
  const drawerClose = document.getElementById('drawerCloseBtn');
  const drawerOverlay = document.getElementById('drawerOverlay');

  function openDrawer() {
    if (drawerOverlay) {
      drawerOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeDrawer() {
    if (drawerOverlay) {
      drawerOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (drawerBtn) drawerBtn.addEventListener('click', openDrawer);
  if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
  if (drawerOverlay) {
    drawerOverlay.addEventListener('click', (e) => {
      if (e.target === drawerOverlay) closeDrawer();
    });
  }

  // 3. Category Filter (Home Gallery & Projects)
  const filterButtons = document.querySelectorAll('.filter-btn');
  const galleryItems = document.querySelectorAll('.gallery-item');
  const projectCards = document.querySelectorAll('.project-card');

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filter = (btn.getAttribute('data-filter') || 'all').toLowerCase();

      // Filter gallery items
      if (galleryItems.length > 0) {
        galleryItems.forEach(item => {
          const itemCat = (item.getAttribute('data-category') || '').toLowerCase();
          const matches = (filter === 'all' || itemCat === filter || itemCat.includes(filter) || filter.includes(itemCat));
          if (matches) {
            item.style.display = 'block';
            setTimeout(() => { item.style.opacity = '1'; item.style.transform = 'scale(1)'; }, 20);
          } else {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.95)';
            setTimeout(() => { item.style.display = 'none'; }, 250);
          }
        });
      }

      // Filter project cards
      if (projectCards.length > 0) {
        projectCards.forEach(card => {
          const cardCat = (card.getAttribute('data-category') || '').toLowerCase();
          const matches = (filter === 'all' || cardCat === filter || cardCat.includes(filter) || filter.includes(cardCat));
          if (matches) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      }
    });
  });

  // 4. Native Pure Lightbox
  const lightboxModal = document.getElementById('lightboxModal');
  const lightboxImg = document.getElementById('lightboxImg');
  const lightboxCaption = document.getElementById('lightboxCaption');
  const lightboxClose = document.getElementById('lightboxClose');
  const lightboxPrev = document.getElementById('lightboxPrev');
  const lightboxNext = document.getElementById('lightboxNext');

  let activeImages = [];
  let currentIndex = 0;

  function collectVisibleImages() {
    activeImages = [];
    const items = document.querySelectorAll('.gallery-item[data-full-src]');
    items.forEach(item => {
      if (item.style.display !== 'none') {
        activeImages.push({
          src: item.getAttribute('data-full-src'),
          title: item.getAttribute('data-title') || ''
        });
      }
    });
  }

  function showLightboxIndex(idx) {
    if (activeImages.length === 0) return;
    if (idx < 0) idx = activeImages.length - 1;
    if (idx >= activeImages.length) idx = 0;
    currentIndex = idx;

    const current = activeImages[currentIndex];
    if (lightboxImg) lightboxImg.src = current.src;
    if (lightboxCaption) lightboxCaption.textContent = current.title;
  }

  function openLightbox(src, title) {
    collectVisibleImages();
    currentIndex = activeImages.findIndex(img => img.src === src);
    if (currentIndex === -1) {
      activeImages.push({ src, title });
      currentIndex = activeImages.length - 1;
    }

    showLightboxIndex(currentIndex);
    if (lightboxModal) {
      lightboxModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeLightbox() {
    if (lightboxModal) {
      lightboxModal.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  // Attach click to gallery items
  document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      const src = item.getAttribute('data-full-src');
      const title = item.getAttribute('data-title') || '';
      if (src) openLightbox(src, title);
    });
  });

  if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
  if (lightboxPrev) lightboxPrev.addEventListener('click', () => showLightboxIndex(currentIndex - 1));
  if (lightboxNext) lightboxNext.addEventListener('click', () => showLightboxIndex(currentIndex + 1));
  if (lightboxModal) {
    lightboxModal.addEventListener('click', (e) => {
      if (e.target === lightboxModal || e.target.classList.contains('lightbox-content')) {
        closeLightbox();
      }
    });
  }

  // Keyboard navigation for lightbox
  window.addEventListener('keydown', (e) => {
    if (!lightboxModal || !lightboxModal.classList.contains('active')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') showLightboxIndex(currentIndex - 1);
    if (e.key === 'ArrowRight') showLightboxIndex(currentIndex + 1);
  });
});
