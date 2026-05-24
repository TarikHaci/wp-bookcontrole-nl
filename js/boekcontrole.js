/**
 * BoekControle.nl — Frontend JavaScript
 * Mobile menu, live search, drag & drop upload, lightbox, filters, AJAX form
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
    initWelcomeToggle();
  }

  /* ── 1. Mobile Menu ── */
  function initMobileMenu() {
    const toggle = document.getElementById('bc-menu-toggle');
    const nav = document.getElementById('bc-mobile-nav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen);
      toggle.textContent = isOpen ? '✕' : '☰';
    });
  }

  /* ── 2. Live Search ── */
  function initSearch() {
    const input = document.getElementById('bc-search');
    const clearBtn = document.getElementById('bc-search-clear');
    const grid = document.getElementById('bc-book-grid');
    const noResults = document.getElementById('bc-no-results');
    if (!input || !grid) return;

    const cards = Array.from(grid.querySelectorAll('[data-title]'));

    function filter() {
      const q = input.value.toLowerCase().trim();
      let count = 0;

      cards.forEach(card => {
        const title = (card.dataset.title || '').toLowerCase();
        const author = (card.dataset.author || '').toLowerCase();
        const match = !q || title.includes(q) || author.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) count++;
      });

      if (clearBtn) clearBtn.classList.toggle('is-visible', q.length > 0);
      if (noResults) noResults.classList.toggle('is-visible', count === 0 && q.length > 0);
    }

    input.addEventListener('input', filter);
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        input.value = '';
        filter();
        input.focus();
      });
    }
  }

  /* ── 3. Drag & Drop Upload ── */
  function initUpload() {
    const zone = document.getElementById('bc-upload-zone');
    const input = document.getElementById('bc-upload-input');
    const preview = document.getElementById('bc-upload-preview');
    const previewImg = document.getElementById('bc-upload-preview-img');
    if (!zone || !input) return;

    zone.addEventListener('click', (e) => {
      if (e.target !== input) input.click();
    });

    ['dragenter', 'dragover'].forEach(evt => {
      zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.add('is-dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
      zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.remove('is-dragover'); });
    });

    zone.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files.length) { input.files = files; showPreview(files[0]); }
    });

    input.addEventListener('change', () => {
      if (input.files.length) showPreview(input.files[0]);
    });

    function showPreview(file) {
      if (!file.type.startsWith('image/') || !preview || !previewImg) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        preview.classList.add('is-visible');
      };
      reader.readAsDataURL(file);
    }
  }

  /* ── 4. Lightbox ── */
  function initLightbox() {
    const lightbox = document.getElementById('bc-lightbox');
    const lbImg = document.getElementById('bc-lightbox-img');
    if (!lightbox || !lbImg) return;

    document.addEventListener('click', (e) => {
      const thumb = e.target.closest('[data-lightbox]');
      if (!thumb) return;
      e.preventDefault();
      lbImg.src = thumb.dataset.full || thumb.src;
      lightbox.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    });

    lightbox.addEventListener('click', close);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

    function close() {
      lightbox.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  }

  /* ── 5. Correction Filters ── */
  function initFilters() {
    const bar = document.getElementById('bc-filter-bar');
    if (!bar) return;

    const buttons = bar.querySelectorAll('[data-filter]');
    const items = document.querySelectorAll('[data-correction-type]');

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const type = btn.dataset.filter;
        buttons.forEach(b => b.classList.remove('bg-white', 'shadow-sm', 'text-emerald-700', 'border-emerald-300'));
        buttons.forEach(b => b.classList.add('text-gray-500'));
        btn.classList.remove('text-gray-500');
        btn.classList.add('bg-white', 'shadow-sm', 'text-emerald-700', 'border-emerald-300');

        items.forEach(item => {
          item.style.display = (type === 'alle' || item.dataset.correctionType === type) ? '' : 'none';
        });
      });
    });
  }

  /* ── 6. AJAX Form Submit ── */
  function initFormAjax() {
    const form = document.getElementById('bc-correction-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = form.querySelector('[type="submit"]');
      const original = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Bezig met versturen...';
      btn.classList.add('opacity-70', 'cursor-not-allowed');

      const fd = new FormData(form);
      fd.append('action', 'bc_submit_correction');
      fd.append('bc_nonce', typeof bcAjax !== 'undefined' ? bcAjax.nonce : '');

      try {
        const res = await fetch(typeof bcAjax !== 'undefined' ? bcAjax.ajaxUrl : '/wp-admin/admin-ajax.php', {
          method: 'POST', body: fd,
        });
        const data = await res.json();

        form.querySelectorAll('.bc-alert').forEach(a => a.remove());
        const alert = document.createElement('div');

        if (data.success) {
          alert.className = 'bc-alert p-4 rounded-xl text-sm font-medium flex items-start gap-3 mb-6 bg-emerald-50 text-emerald-800 border border-emerald-200 animate-slide-down';
          alert.innerHTML = '✅ ' + data.data.message;
          form.reset();
          const preview = document.getElementById('bc-upload-preview');
          if (preview) preview.classList.remove('is-visible');
          const slide = document.getElementById('bc-new-book-fields');
          if (slide) slide.classList.remove('is-open');
        } else {
          alert.className = 'bc-alert p-4 rounded-xl text-sm font-medium flex items-start gap-3 mb-6 bg-red-50 text-red-700 border border-red-200 animate-slide-down';
          alert.innerHTML = '❌ ' + (data.data?.message || 'Er ging iets mis.');
        }

        form.prepend(alert);
        alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } catch {
        const alert = document.createElement('div');
        alert.className = 'bc-alert p-4 rounded-xl text-sm font-medium mb-6 bg-red-50 text-red-700 border border-red-200 animate-slide-down';
        alert.innerHTML = '❌ Netwerkfout. Probeer het opnieuw.';
        form.prepend(alert);
      }

      btn.disabled = false;
      btn.innerHTML = original;
      btn.classList.remove('opacity-70', 'cursor-not-allowed');
    });
  }

  /* ── 7. New Book Toggle ── */
  function initNewBookToggle() {
    const select = document.getElementById('book_select');
    const fields = document.getElementById('bc-new-book-fields');
    if (!select || !fields) return;

    select.addEventListener('change', () => {
      fields.classList.toggle('is-open', select.value === 'nieuw');
    });
  }

  /* ── 8. Welcome Section Toggle ── */
  function initWelcomeToggle() {
    const toggleBtn = document.getElementById('bc-welcome-toggle');
    const content = document.getElementById('bc-welcome-content');
    const fade = document.getElementById('bc-welcome-fade');
    if (!toggleBtn || !content || !fade) return;

    let isOpen = false;

    toggleBtn.addEventListener('click', () => {
      isOpen = !isOpen;
      
      if (isOpen) {
        content.style.maxHeight = content.scrollHeight + 'px';
        fade.style.opacity = '0';
        toggleBtn.querySelector('span').textContent = 'Klap in';
        toggleBtn.querySelector('svg').style.transform = 'rotate(180deg)';
      } else {
        content.style.maxHeight = '48px';
        fade.style.opacity = '1';
        toggleBtn.querySelector('span').textContent = 'Lees verder';
        toggleBtn.querySelector('svg').style.transform = 'rotate(0deg)';
      }
    });
  }

})();
