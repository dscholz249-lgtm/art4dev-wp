/* ============================================================
   Arts for Global Development — Site JS
   ------------------------------------------------------------
   Lightweight vanilla JS for interactions.
   Headless WP note: this is presentation only — content
   comes from WP REST/GraphQL elsewhere.
   ============================================================ */

(function () {
  'use strict';

  // ---------- Mobile menu toggle ---------------------------
  const menuToggle = document.querySelector('.menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  const menuClose  = document.querySelector('.mobile-menu__close');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      mobileMenu.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    });
  }
  if (menuClose && mobileMenu) {
    menuClose.addEventListener('click', () => {
      mobileMenu.classList.remove('is-open');
      document.body.style.overflow = '';
    });
  }
  if (mobileMenu) {
    mobileMenu.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        mobileMenu.classList.remove('is-open');
        document.body.style.overflow = '';
      });
    });
  }

  // ---------- Scroll reveal --------------------------------
  const inViewEls = document.querySelectorAll('.in-view');
  if (inViewEls.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add('is-visible');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -80px 0px' });
    inViewEls.forEach(el => io.observe(el));
  } else {
    inViewEls.forEach(el => el.classList.add('is-visible'));
  }

  // ---------- Tag selector (contact form) ------------------
  document.querySelectorAll('.tag-option').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      btn.classList.toggle('is-active');
    });
  });

  // ---------- Year in footer -------------------------------
  document.querySelectorAll('[data-year]').forEach(el => {
    el.textContent = new Date().getFullYear();
  });

})();
