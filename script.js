// =============================
// Sunny Smile Dental - script.js
// =============================

// --- Mobile navigation toggle ---
const toggle = document.querySelector('.nav__toggle');
const nav = document.querySelector('#navmenu');

if (toggle && nav) {
  toggle.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!open));
    nav.classList.toggle('show');
  });
}

// --- Dynamic footer year ---
const yearEl = document.getElementById('year');
if (yearEl) yearEl.textContent = new Date().getFullYear();

// --- Contact form logic ---
const form = document.getElementById('contactForm');
const formMsg = document.getElementById('formMsg');

function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

if (form) {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    formMsg.hidden = true;
    formMsg.textContent = '';

    const data = new FormData(form);

    // Honeypot spam prevention
    if (data.get('website')) return;

    // Required field validation
    if (!data.get('name') || !data.get('phone') || !form.consent.checked) {
      formMsg.hidden = false;
      formMsg.textContent = 'Please fill your name, phone, and consent.';
      formMsg.style.color = '#b91c1c'; // red-700
      return;
    }

    const email = data.get('email');
    if (email && !validateEmail(email)) {
      formMsg.hidden = false;
      formMsg.textContent = 'Please enter a valid email or leave it blank.';
      formMsg.style.color = '#b91c1c';
      return;
    }

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        body: data
      });

      const json = await res.json();

      formMsg.hidden = false;
      formMsg.textContent = json.message || 'Thanks! We will contact you shortly.';
      formMsg.style.color = res.ok ? '#0c4a6e' : '#b91c1c';

      if (res.ok) {
        form.reset();
      }
    } catch (err) {
      formMsg.hidden = false;
      formMsg.textContent = 'Something went wrong. Please call us at +359 88 123 4567.';
      formMsg.style.color = '#b91c1c';
    }
  });
}
