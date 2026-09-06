/* ==========================================================================
   Student Notes Management System — script.js
   Shared by all pages (index.html, dashboard.html, pastpapers.html, upload.html)

   This project uses Bootstrap 5 (CSS + JS bundle, loaded via CDN in every
   page) for its navbar, cards, carousel, modals, and dropdown components.
   This file handles everything Bootstrap doesn't do out of the box:

   Implements the required JavaScript features:
   1. Dynamic Content Updates  -> live subject search/filter on pastpapers.html
   2. Interactive Image Slider -> the Bootstrap carousel on dashboard.html is
                                   driven entirely by its own data-bs-* attributes;
                                   this file only wires the download-confirmation
                                   and upload-success Bootstrap modals
   3. Form Validation          -> real-time validation on the login & upload forms
   4. Custom Animations        -> fade-in / slide-up page entrance animations
                                   and scroll-triggered reveal animations

   Every function checks that its target elements exist before running, so
   this single file can safely be included on every page without errors.
   Note: this file relies on the Bootstrap JS bundle (bootstrap.bundle.min.js)
   being loaded on the page BEFORE script.js for the `bootstrap.Modal` calls
   used in initDownloadModal() and initUploadValidation() to work.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  initLoginValidation();
  initUploadValidation();
  initNotesSearch();
  initDownloadModal();
  initAnimations();
});


/* ==========================================================================
   FEATURE 1: DYNAMIC CONTENT UPDATES
   Live filtering of the subject cards on pastpapers.html.
   Matches typed text against each card's data-subject attribute and its
   heading text, hiding non-matching cards and showing a "no results"
   message when nothing matches.
   ========================================================================== */
function initNotesSearch() {
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const grid = document.getElementById('papersGrid');
  const noResults = document.getElementById('noResults');

  // Bail out silently if this page doesn't have the search UI
  if (!searchInput || !grid) return;

  const cards = Array.from(grid.querySelectorAll('.paper-card'));

  function filterCards() {
    const query = searchInput.value.trim().toLowerCase();
    let visibleCount = 0;

    cards.forEach(function (card) {
      const subject = (card.dataset.subject || '').toLowerCase();
      const heading = card.querySelector('h3') ? card.querySelector('h3').textContent.toLowerCase() : '';
      const isMatch = query === '' || subject.includes(query) || heading.includes(query);

      card.style.display = isMatch ? '' : 'none';
      if (isMatch) visibleCount++;
    });

    // Toggle the "No Notes matched your search" message dynamically
    if (noResults) {
      noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  }

  // Live filtering as the user types (dynamic content update)
  searchInput.addEventListener('input', filterCards);

  // Also support clicking the Search button
  if (searchBtn) {
    searchBtn.addEventListener('click', filterCards);
  }

  // Support pressing Enter inside the search box
  searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      filterCards();
    }
  });
}


/* ==========================================================================
   BOOTSTRAP MODAL WIRING — Download confirmation (pastpapers.html)
   Each subject card's "Download" button opens a shared Bootstrap modal.
   Before showing it, we fill in the subject name so the modal reads
   "Download Physics?" etc. Confirming is just a demo (no real file
   exists to download), so it simply closes the modal via data-bs-dismiss.
   ========================================================================== */
function initDownloadModal() {
  const modalEl = document.getElementById('downloadModal');
  const subjectNameEl = document.getElementById('downloadSubjectName');
  const downloadButtons = document.querySelectorAll('.download-btn');

  // Bail out silently if this page doesn't have the download modal
  if (!modalEl || typeof bootstrap === 'undefined' || downloadButtons.length === 0) return;

  const modal = new bootstrap.Modal(modalEl);

  downloadButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const card = btn.closest('.paper-card');
      const heading = card ? card.querySelector('h3') : null;
      if (subjectNameEl) {
        subjectNameEl.textContent = heading ? heading.textContent : 'this note';
      }
      modal.show();
    });
  });
}


/* ==========================================================================
   FEATURE 4: CUSTOM ANIMATIONS
   Adds fade-in / slide-up entrance animations to key elements when a page
   loads, plus a scroll-triggered "reveal" animation (using
   IntersectionObserver) for elements further down the page, and a subtle
   animated feedback pulse on form errors. All animations are injected as a
   single stylesheet, and elements are animated by toggling classes rather
   than inline styles, so they stay easy to tweak.
   ========================================================================== */
function initAnimations() {
  injectAnimationStyles();

  // ---- Page entrance animation ----
  // Any element on the page can opt in just by having one of these classes
  // already in the markup; we also auto-tag common containers below so
  // every page gets a nice entrance without needing HTML changes.
  const autoFadeSelectors = [
    '.login-card',
    '.welcome-banner',
    '.stat-card',
    '.action-card',
    '.paper-card',
    '.upload-card',
    '#dashboardCarousel'
  ];

  const entranceEls = [];
  autoFadeSelectors.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) {
      el.classList.add('fade-slide-in');
      entranceEls.push(el);
    });
  });

  // Stagger the entrance animation slightly for groups of elements
  // (e.g. the 4 stat cards, the grid of paper cards) so they cascade in
  // rather than all popping in at once.
  entranceEls.forEach(function (el, index) {
    el.style.animationDelay = Math.min(index * 60, 480) + 'ms';
  });

  // Trigger the CSS animation on the next frame (elements start hidden
  // via the .fade-slide-in class, then this class flips them visible)
  requestAnimationFrame(function () {
    requestAnimationFrame(function () {
      entranceEls.forEach(function (el) {
        el.classList.add('is-visible');
      });
    });
  });

  // ---- Scroll-triggered reveal animation ----
  // Sidebar nav links and download buttons get a gentle reveal as the
  // user scrolls the main content into view (mainly noticeable on long
  // pages / smaller screens where content overflows).
  const revealTargets = document.querySelectorAll('.paper-card, .stat-card, .action-card');

  if ('IntersectionObserver' in window && revealTargets.length) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    revealTargets.forEach(function (el) {
      observer.observe(el);
    });
  }

  // ---- Sidebar link hover/active transition ----
  // Adds a smooth animated underline-style transition class; the actual
  // transition timing lives in the injected stylesheet below.
  document.querySelectorAll('.sidebar .navbar-nav .nav-link, .sidebar-logout .nav-link').forEach(function (link) {
    link.classList.add('nav-link-animated');
  });

  // ---- Button press "ripple-free" pulse feedback ----
  // A lightweight animated pulse whenever a primary button is clicked,
  // giving instant visual feedback without needing a full ripple effect.
  document.querySelectorAll('.btn-primary, .download-btn, .search-bar button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      btn.classList.remove('pulse-anim'); // reset so repeat clicks re-trigger
      void btn.offsetWidth; // force reflow so the animation restarts
      btn.classList.add('pulse-anim');
    });
  });
}

function injectAnimationStyles() {
  if (document.getElementById('animationStyles')) return;

  const style = document.createElement('style');
  style.id = 'animationStyles';
  style.textContent = `
    /* Entrance animation: starts faded-out and shifted down, then
       transitions to fully visible once .is-visible is added */
    .fade-slide-in {
      opacity: 0;
      transform: translateY(18px);
      transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .fade-slide-in.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Animated underline transition for navbar links */
    .nav-link-animated {
      position: relative;
      transition: background 0.2s ease, color 0.2s ease, padding-left 0.2s ease;
    }
    .nav-link-animated:hover {
      padding-left: 18px;
    }

    /* Short pulse used for click feedback on buttons */
    @keyframes pulseAnim {
      0%   { transform: scale(1); }
      40%  { transform: scale(0.94); }
      100% { transform: scale(1); }
    }
    .pulse-anim {
      animation: pulseAnim 0.28s ease;
    }

    /* Gentle shake animation used when a form field fails validation */
    @keyframes shakeAnim {
      0%, 100% { transform: translateX(0); }
      20%      { transform: translateX(-4px); }
      40%      { transform: translateX(4px); }
      60%      { transform: translateX(-3px); }
      80%      { transform: translateX(3px); }
    }
    .input-error {
      animation: shakeAnim 0.35s ease;
    }

    @media (prefers-reduced-motion: reduce) {
      .fade-slide-in, .pulse-anim, .input-error {
        animation: none !important;
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
      }
    }
  `;
  document.head.appendChild(style);
}


/* ==========================================================================
   FEATURE 3: FORM VALIDATION (part A) — Login form on index.html
   Validates the student email format and the required password field,
   giving real-time feedback as the user types/leaves each field, and
   blocking submission until both fields are valid.
   ========================================================================== */
function initLoginValidation() {
  const form = document.querySelector('.login-card form');
  if (!form) return;

  const emailInput = form.querySelector('#email');
  const passwordInput = form.querySelector('#password');
  if (!emailInput || !passwordInput) return;

  const emailError = createErrorElement(emailInput);
  const passwordError = createErrorElement(passwordInput);

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function validateEmail() {
    const value = emailInput.value.trim();
    if (value === '') {
      showError(emailInput, emailError, 'Student email is required.');
      return false;
    }
    if (!emailPattern.test(value)) {
      showError(emailInput, emailError, 'Please enter a valid email address.');
      return false;
    }
    clearError(emailInput, emailError);
    return true;
  }

  function validatePassword() {
    const value = passwordInput.value;
    if (value === '') {
      showError(passwordInput, passwordError, 'Password is required.');
      return false;
    }
    if (value.length < 6) {
      showError(passwordInput, passwordError, 'Password must be at least 6 characters.');
      return false;
    }
    clearError(passwordInput, passwordError);
    return true;
  }

  // Real-time feedback: validate as the user types and when they leave a field
  emailInput.addEventListener('input', validateEmail);
  emailInput.addEventListener('blur', validateEmail);
  passwordInput.addEventListener('input', validatePassword);
  passwordInput.addEventListener('blur', validatePassword);

  form.addEventListener('submit', function (e) {
    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();

    if (!isEmailValid || !isPasswordValid) {
      e.preventDefault(); // stop the redirect to dashboard.html until valid
    }
  });
}


/* ==========================================================================
   FEATURE 3: FORM VALIDATION (part B) — Upload form on upload.html
   Validates subject name, year, and file (must be a .pdf), with real-time
   feedback, then shows the Bootstrap "Upload Successful" modal and resets
   the form once the (simulated) upload passes validation.
   ========================================================================== */
function initUploadValidation() {
  const form = document.getElementById('uploadForm');
  if (!form) return;

  const subjectInput = document.getElementById('subjectName');
  const yearInput = document.getElementById('NoteYear');
  const fileInput = document.getElementById('NoteFile');
  const successModalEl = document.getElementById('uploadSuccessModal');
  const successModal = (successModalEl && typeof bootstrap !== 'undefined')
    ? new bootstrap.Modal(successModalEl)
    : null;

  if (!subjectInput || !yearInput || !fileInput) return;

  const subjectError = createErrorElement(subjectInput);
  const yearError = createErrorElement(yearInput);
  const fileError = createErrorElement(fileInput);

  const MAX_FILE_SIZE_MB = 10;

  function validateSubject() {
    const value = subjectInput.value.trim();
    if (value === '') {
      showError(subjectInput, subjectError, 'Subject name is required.');
      return false;
    }
    if (value.length < 2) {
      showError(subjectInput, subjectError, 'Subject name is too short.');
      return false;
    }
    clearError(subjectInput, subjectError);
    return true;
  }

  function validateYear() {
    const value = yearInput.value.trim();
    const currentYear = new Date().getFullYear();

    if (value === '') {
      showError(yearInput, yearError, 'Year is required.');
      return false;
    }
    const numericYear = Number(value);
    if (!Number.isInteger(numericYear) || numericYear < 2000 || numericYear > currentYear) {
      showError(yearInput, yearError, 'Enter a valid year between 2000 and ' + currentYear + '.');
      return false;
    }
    clearError(yearInput, yearError);
    return true;
  }

  function validateFile() {
    const files = fileInput.files;
    if (!files || files.length === 0) {
      showError(fileInput, fileError, 'Please choose a PDF file to upload.');
      return false;
    }
    const file = files[0];
    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
    if (!isPdf) {
      showError(fileInput, fileError, 'Only PDF files are allowed.');
      return false;
    }
    const sizeMb = file.size / (1024 * 1024);
    if (sizeMb > MAX_FILE_SIZE_MB) {
      showError(fileInput, fileError, 'File must be smaller than ' + MAX_FILE_SIZE_MB + 'MB.');
      return false;
    }
    clearError(fileInput, fileError);
    return true;
  }

  // Real-time feedback
  subjectInput.addEventListener('input', validateSubject);
  yearInput.addEventListener('input', validateYear);
  fileInput.addEventListener('change', validateFile);

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // this is a front-end demo only, nothing is actually sent

    const isSubjectValid = validateSubject();
    const isYearValid = validateYear();
    const isFileValid = validateFile();

    if (isSubjectValid && isYearValid && isFileValid) {
      if (successModal) {
        successModal.show();
      }
      form.reset();
      // Clear any validation error styling left over after a successful reset
      [subjectInput, yearInput, fileInput].forEach(function (input) {
        input.classList.remove('input-error');
      });
    }
  });
}


/* ==========================================================================
   SHARED VALIDATION HELPERS
   Small utilities used by both forms above to show/hide inline error text
   next to a field and to toggle an "input-error" class for red-border
   styling (styles injected once, reused by every field).
   ========================================================================== */
function createErrorElement(input) {
  const error = document.createElement('span');
  error.className = 'field-error';
  input.insertAdjacentElement('afterend', error);
  ensureValidationStylesInjected();
  return error;
}

function showError(input, errorEl, message) {
  input.classList.add('input-error');
  errorEl.textContent = message;
}

function clearError(input, errorEl) {
  input.classList.remove('input-error');
  errorEl.textContent = '';
}

function ensureValidationStylesInjected() {
  if (document.getElementById('validationStyles')) return;

  const style = document.createElement('style');
  style.id = 'validationStyles';
  style.textContent = `
    .field-error {
      display: block;
      min-height: 16px;
      font-size: 12px;
      color: #dc2626;
      margin-top: 4px;
    }
    .input-error {
      border-color: #dc2626 !important;
      box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12) !important;
    }
  `;
  document.head.appendChild(style);
}
