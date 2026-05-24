/**
 * BoekControle.nl — Frontend JavaScript
 * Live search, mobile menu, drag & drop upload, lightbox, filters, AJAX form submit
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', init);

  function init() {
    initMobileMenu();
    initSearch();
    initUpload();
    initLightbox();
    initFilters();
    initFormAjax();
    initNewBookToggle();
  }

  /* ==========================================================
     1. MOBILE MENU
     ========================================================== */
  function initMobileMenu() {
    const toggle = document.getElementById('bc-menu-toggle');
    const nav = document.getElementById('bc-mobile-nav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen);
      toggle.innerHTML = isOpen ? '✕' : '☰';
    });
  }

  /* ==========================================================
     2. LIVE SEARCH (Homepage)
     ========================================================== */
  function initSearch() {
    const input = document.getElementById('bc-search');
    const clearBtn = document.getElementById('bc-search-clear');
    const grid = document.getElementById('bc-book-grid');
    const noResults = document.getElementById('bc-no-results');
    if (!input || !grid) return;

    const cards = Array.from(grid.querySelectorAll('.bc-card'));

    function filterCards() {
      const query = input.value.toLowerCase().trim();
      let visibleCount = 0;

      cards.forEach(card => {
        const title = (card.dataset.title || '').toLowerCase();
        const author = (card.dataset.author || '').toLowerCase();
        const match = !query || title.includes(query) || author.includes(query);
        card.style.display = match ? '' : 'none';
        if (match) visibleCount++;
      });

      if (clearBtn) {
        clearBtn.classList.toggle('is-visible', query.length > 0);
      }
      if (noResults) {
        noResults.classList.toggle('is-visible', visibleCount === 0 && query.length > 0);
      }
    }

    input.addEventListener('input', filterCards);

    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        input.value = '';
        filterCards();
        input.focus();
      });
    }
  }

  /* ==========================================================
     3. DRAG & DROP UPLOAD
     ========================================================== */
  function initUpload() {
    const zone = document.getElementById('bc-upload-zone');
    const input = document.getElementById('bc-upload-input');
    const preview = document.getElementById('bc-upload-preview');
    const previewImg = document.getElementById('bc-upload-preview-img');
    if (!zone || !input) return;

    // Click to open file picker
    zone.addEventListener('click', (e) => {
      if (e.target !== input) input.click();
    });

    // Drag events
    ['dragenter', 'dragover'].forEach(evt => {
      zone.addEventListener(evt, (e) => {
        e.preventDefault();
        zone.classList.add('is-dragover');
      });
    });

    ['dragleave', 'drop'].forEach(evt => {
      zone.addEventListener(evt, (e) => {
        e.preventDefault();
        zone.classList.remove('is-dragover');
      });
    });

    zone.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files.length) {
        input.files = files;
        showPreview(files[0]);
      }
    });

    input.addEventListener('change', () => {
      if (input.files.length) showPreview(input.files[0]);
    });

    function showPreview(file) {
      if (!file.type.startsWith('image/')) return;
      if (!preview || !previewImg) return;

      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        preview.classList.add('is-visible');
      };
      reader.readAsDataURL(file);
    }
  }

  /* ==========================================================
     4. IMAGE LIGHTBOX
     ========================================================== */
  function initLightbox() {
    const lightbox = document.getElementById('bc-lightbox');
    const lightboxImg = document.getElementById('bc-lightbox-img');
    if (!lightbox || !lightboxImg) return;

    // Open on thumbnail click
    document.addEventListener('click', (e) => {
      const thumb = e.target.closest('.bc-table__thumbnail, .bc-correction-card img');
      if (!thumb) return;
      e.preventDefault();
      const src = thumb.dataset.full || thumb.src;
      lightboxImg.src = src;
      lightbox.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    });

    // Close on click
    lightbox.addEventListener('click', closeLightbox);

    // Close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox();
    });

    function closeLightbox() {
      lightbox.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  }

  /* ==========================================================
     5. CORRECTION FILTERS (single-book page)
     ========================================================== */
  function initFilters() {
    const filterBar = document.getElementById('bc-filter-bar');
    if (!filterBar) return;

    const buttons = filterBar.querySelectorAll('.bc-filter-btn');
    const items = document.querySelectorAll('[data-correction-type]');

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const type = btn.dataset.filter;

        // Toggle active
        buttons.forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        // Filter items
        items.forEach(item => {
          if (type === 'alle' || item.dataset.correctionType === type) {
            item.style.display = '';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  }

  /* ==========================================================
     6. AJAX FORM SUBMIT
     ========================================================== */
  function initFormAjax() {
    const form = document.getElementById('bc-correction-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const submitBtn = form.querySelector('[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '⏳ Bezig met versturen...';

      const formData = new FormData(form);
      formData.append('action', 'bc_submit_correction');
      formData.append('bc_nonce', (typeof bcAjax !== 'undefined') ? bcAjax.nonce : '');

      try {
        const response = await fetch((typeof bcAjax !== 'undefined') ? bcAjax.ajaxUrl : '/wp-admin/admin-ajax.php', {
          method: 'POST',
          body: formData,
        });

        const result = await response.json();

        // Remove existing alerts
        form.querySelectorAll('.bc-alert').forEach(a => a.remove());

        const alert = document.createElement('div');
        if (result.success) {
          alert.className = 'bc-alert bc-alert--success';
          alert.innerHTML = '✅ ' + result.data.message;
          form.reset();
          // Reset upload preview
          const preview = document.getElementById('bc-upload-preview');
          if (preview) preview.classList.remove('is-visible');
          // Close new-book fields
          const slideContent = document.getElementById('bc-new-book-fields');
          if (slideContent) slideContent.classList.remove('is-open');
        } else {
          alert.className = 'bc-alert bc-alert--error';
          alert.innerHTML = '❌ ' + (result.data?.message || 'Er ging iets mis.');
        }

        form.prepend(alert);
        alert.scrollIntoView({ behavior: 'smooth', block: 'center' });

      } catch (err) {
        const alert = document.createElement('div');
        alert.className = 'bc-alert bc-alert--error';
        alert.innerHTML = '❌ Netwerkfout. Probeer het opnieuw.';
        form.prepend(alert);
      }

      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  }

  /* ==========================================================
     7. NEW BOOK TOGGLE (correction form)
     ========================================================== */
  function initNewBookToggle() {
    const select = document.getElementById('book_select');
    const fields = document.getElementById('bc-new-book-fields');
    if (!select || !fields) return;

    select.addEventListener('change', () => {
      if (select.value === 'nieuw') {
        fields.classList.add('is-open');
      } else {
        fields.classList.remove('is-open');
      }
    });
  }

})();
