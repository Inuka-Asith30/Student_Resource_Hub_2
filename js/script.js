document.addEventListener('DOMContentLoaded', function () {
  const isPublicPage = window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('register.html') || window.location.pathname === '/' || window.location.pathname.endsWith('/');
  
  if (isPublicPage) {
    localStorage.removeItem('isLoggedIn'); // Clear login state when on login/register page
  } else {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    
    // 1. Only prevent upload.html from loading if not logged in
    if (window.location.pathname.endsWith('upload.html') && !isLoggedIn) {
      window.location.href = 'index.html';
      return; 
    }

    // 2. Hide/Show Menu Items dynamically based on login state
    const navLinks = document.querySelectorAll('.nav-link');
    let uploadLi = null, loginLi = null, registerLi = null;

    navLinks.forEach(link => {
      if (link.getAttribute('href') === 'upload.html') uploadLi = link.closest('li');
      if (link.getAttribute('href') === 'index.html') loginLi = link.closest('li');
      if (link.getAttribute('href') === 'register.html') registerLi = link.closest('li');
    });

    const logoutDiv = document.querySelector('.sidebar-logout');
    const userChip = document.querySelector('.user-chip');

    if (isLoggedIn) {
      // If User is LOGGED IN: Hide Login/Register, Show Upload/Logout
      if (loginLi) loginLi.style.display = 'none';
      if (registerLi) registerLi.style.display = 'none';
      if (uploadLi) uploadLi.style.display = '';
      if (logoutDiv) logoutDiv.style.display = '';
      if (userChip) userChip.innerHTML = '<span class="avatar">US</span><span>User</span>';
    } else {
      // If User is NOT LOGGED IN: Hide Upload/Logout, Show Login/Register
      if (uploadLi) uploadLi.style.display = 'none';
      if (logoutDiv) logoutDiv.style.display = 'none';
      if (loginLi) loginLi.style.display = '';
      if (registerLi) registerLi.style.display = '';
      if (userChip) userChip.innerHTML = '<span class="avatar" style="background:#6c757d;">G</span><span>Guest User</span>';
    }
  }

  initAnimations();
  initNotesSearch();
  initLoginValidation();
  initRegisterValidation();
  initUploadValidation();
});

function initNotesSearch() {
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const grid = document.getElementById('papersGrid');
  const noResults = document.getElementById('noResults');

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

    if (noResults) {
      noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  }

  searchInput.addEventListener('input', filterCards);
  if (searchBtn) searchBtn.addEventListener('click', filterCards);

  searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); filterCards(); }
  });
}

function initAnimations() {
  injectAnimationStyles();
  const autoFadeSelectors = ['.login-card', '.welcome-banner', '.stat-card', '.action-card', '.paper-card', '.upload-card', '.image-slider'];
  const entranceEls = [];
  
  autoFadeSelectors.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) {
      el.classList.add('fade-slide-in');
      entranceEls.push(el);
    });
  });

  entranceEls.forEach(function (el, index) {
    el.style.animationDelay = Math.min(index * 60, 480) + 'ms';
  });

  requestAnimationFrame(function () {
    requestAnimationFrame(function () {
      entranceEls.forEach(function (el) { el.classList.add('is-visible'); });
    });
  });

  document.querySelectorAll('.btn-primary, .download-btn, .search-bar button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      btn.classList.remove('pulse-anim'); void btn.offsetWidth; btn.classList.add('pulse-anim');
    });
  });
}

function injectAnimationStyles() {
  if (document.getElementById('animationStyles')) return;
  const style = document.createElement('style');
  style.id = 'animationStyles';
  style.textContent = `
    .fade-slide-in { opacity: 0; transform: translateY(18px); transition: opacity 0.5s ease, transform 0.5s ease; }
    .fade-slide-in.is-visible { opacity: 1; transform: translateY(0); }
    @keyframes pulseAnim { 0% { transform: scale(1); } 40% { transform: scale(0.94); } 100% { transform: scale(1); } }
    .pulse-anim { animation: pulseAnim 0.28s ease; }
    @keyframes shakeAnim { 0%, 100% { transform: translateX(0); } 20% { transform: translateX(-4px); } 40% { transform: translateX(4px); } 60% { transform: translateX(-3px); } 80% { transform: translateX(3px); } }
    .input-error { animation: shakeAnim 0.35s ease; }
  `;
  document.head.appendChild(style);
}

function initLoginValidation() {
  const form = document.querySelector('.login-card form');
  if (!form) return;
  const emailInput = form.querySelector('#email');
  const passwordInput = form.querySelector('#password');
  if (!emailInput || !passwordInput) return;

  const emailError = createErrorElement(emailInput);
  const passwordError = createErrorElement(passwordInput);
  const emailPattern = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;

  function validateEmail() {
    const value = emailInput.value.trim();
    if (value === '') { showError(emailInput, emailError, 'Student email is required.'); return false; }
    if (!emailPattern.test(value)) { showError(emailInput, emailError, 'Please enter a valid email address.'); return false; }
    clearError(emailInput, emailError); return true;
  }

  function validatePassword() {
    const value = passwordInput.value;
    if (value === '') { showError(passwordInput, passwordError, 'Password is required.'); return false; }
    if (value.length < 6) { showError(passwordInput, passwordError, 'Password must be at least 6 characters.'); return false; }
    clearError(passwordInput, passwordError); return true;
  }

  emailInput.addEventListener('input', validateEmail);
  emailInput.addEventListener('blur', validateEmail);
  passwordInput.addEventListener('input', validatePassword);
  passwordInput.addEventListener('blur', validatePassword);

  form.addEventListener('submit', function (e) {
    if (!validateEmail() || !validatePassword()) { 
      e.preventDefault(); 
    } else {
      // Set logged in state for static HTML navigation
      localStorage.setItem('isLoggedIn', 'true');
    }
  });
}

function initUploadValidation() {
  const form = document.getElementById('uploadForm');
  if (!form) return;
  const subjectInput = document.getElementById('subjectName');
  const yearInput = document.getElementById('NoteYear');
  const fileInput = document.getElementById('NoteFile');
  if (!subjectInput || !yearInput || !fileInput) return;

  const subjectError = createErrorElement(subjectInput);
  const yearError = createErrorElement(yearInput);
  const fileError = createErrorElement(fileInput);

  function validateSubject() {
    const value = subjectInput.value.trim();
    if (value === '') { showError(subjectInput, subjectError, 'Subject name is required.'); return false; }
    clearError(subjectInput, subjectError); return true;
  }

  function validateYear() {
    const value = yearInput.value.trim();
    const currentYear = new Date().getFullYear();
    if (value === '') { showError(yearInput, yearError, 'Year is required.'); return false; }
    const numericYear = Number(value);
    if (numericYear < 2000 || numericYear > currentYear) { showError(yearInput, yearError, 'Enter a valid year.'); return false; }
    clearError(yearInput, yearError); return true;
  }

  function validateFile() {
    const files = fileInput.files;
    if (!files || files.length === 0) { showError(fileInput, fileError, 'Please choose a PDF file.'); return false; }
    clearError(fileInput, fileError); return true;
  }

  subjectInput.addEventListener('input', validateSubject);
  yearInput.addEventListener('input', validateYear);
  fileInput.addEventListener('change', validateFile);

  form.addEventListener('submit', function (e) {
    // Only prevent form submission if validation FAILS. Otherwise, let PHP handle it.
    if (!validateSubject() || !validateYear() || !validateFile()) {
      e.preventDefault(); 
    }
  });
}

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
  style.textContent = \`
    .field-error { display: block; min-height: 16px; font-size: 12px; color: #dc2626; margin-top: 4px; }
    .input-error { border-color: #dc2626 !important; box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12) !important; }
  \`;
  document.head.appendChild(style);
}

function initRegisterValidation() {
  const form = document.getElementById('registerForm');
  if (!form) return;
  const usernameInput = form.querySelector('#username');
  const emailInput = form.querySelector('#email');
  const passwordInput = form.querySelector('#password');
  if (!usernameInput || !emailInput || !passwordInput) return;

  const usernameError = createErrorElement(usernameInput);
  const emailError = createErrorElement(emailInput);
  const passwordError = createErrorElement(passwordInput);
  const emailPattern = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;

  function validateUsername() {
    if (usernameInput.value.trim() === '') { showError(usernameInput, usernameError, 'Username is required.'); return false; }
    clearError(usernameInput, usernameError); return true;
  }

  function validateEmail() {
    const value = emailInput.value.trim();
    if (value === '') { showError(emailInput, emailError, 'Email is required.'); return false; }
    if (!emailPattern.test(value)) { showError(emailInput, emailError, 'Invalid email.'); return false; }
    clearError(emailInput, emailError); return true;
  }

  function validatePassword() {
    if (passwordInput.value.length < 6) { showError(passwordInput, passwordError, 'Password must be at least 6 chars.'); return false; }
    clearError(passwordInput, passwordError); return true;
  }

  usernameInput.addEventListener('input', validateUsername);
  emailInput.addEventListener('input', validateEmail);
  passwordInput.addEventListener('input', validatePassword);

  form.addEventListener('submit', function (e) {
    if (!validateUsername() || !validateEmail() || !validatePassword()) {
      e.preventDefault();
    } else {
      localStorage.setItem('isLoggedIn', 'true');
    }
  });
}
