/**
 * app.js — comportements globaux du site (menu mobile, dropdowns, toasts).
 * JavaScript vanilla, aucune librairie externe.
 */

document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initDropdowns();
    initQuantityStepper();
    initToasts();
    initScrollReveal();
});

/* ---------- Menu mobile ---------- */
function initMobileMenu() {
    var toggle = document.querySelector('[data-menu-toggle]');
    var menu = document.querySelector('[data-mobile-menu]');
    if (!toggle || !menu) return;

    var close = function () {
        menu.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };
    var open = function () {
        menu.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    };

    toggle.addEventListener('click', function () {
        menu.classList.contains('is-open') ? close() : open();
    });

    var overlay = menu.querySelector('[data-menu-overlay]');
    if (overlay) overlay.addEventListener('click', close);

    var closeBtn = menu.querySelector('[data-menu-close]');
    if (closeBtn) closeBtn.addEventListener('click', close);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });
}

/* ---------- Dropdowns génériques (compte, tri, etc.) ---------- */
function initDropdowns() {
    var toggles = document.querySelectorAll('[data-dropdown-toggle]');

    toggles.forEach(function (toggle) {
        var targetId = toggle.getAttribute('data-dropdown-toggle');
        var panel = document.getElementById(targetId);
        if (!panel) return;

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = panel.classList.contains('is-open');
            closeAllDropdowns();
            if (!isOpen) {
                panel.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', closeAllDropdowns);
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-panel.is-open').forEach(function (panel) {
        panel.classList.remove('is-open');
    });
    document.querySelectorAll('[data-dropdown-toggle]').forEach(function (t) {
        t.setAttribute('aria-expanded', 'false');
    });
}

/* ---------- Toasts ---------- */

/**
 * Affiche un toast (notification flottante), sans reload de page.
 * type: 'success' | 'error' | 'info'
 */
window.showToast = function (message, type) {
    if (!message) return;
    type = type || 'success';

    var stack = document.querySelector('[data-toast-stack]');
    if (!stack) {
        stack = document.createElement('div');
        stack.className = 'toast-stack';
        stack.setAttribute('data-toast-stack', '');
        stack.setAttribute('aria-live', 'polite');
        document.body.appendChild(stack);
    }

    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    stack.appendChild(toast);

    setTimeout(function () {
        toast.classList.add('is-leaving');
        setTimeout(function () { toast.remove(); }, 250);
    }, 4000);
};

/**
 * Convertit les messages flash serveur (session success/error, portés par
 * des attributs data-* sur <body>) en toasts au chargement de la page —
 * plus de bandeau qui n'apparaît qu'après le rendu complet de la page.
 */
function initToasts() {
    var body = document.body;
    var success = body.getAttribute('data-flash-success');
    var error = body.getAttribute('data-flash-error');

    if (success) window.showToast(success, 'success');
    if (error) window.showToast(error, 'error');
}

/* ---------- Scroll reveal (fade/slide-in des sections au scroll) ---------- */
function initScrollReveal() {
    var targets = document.querySelectorAll('[data-reveal]');
    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(function (el) { observer.observe(el); });
}

/* ---------- Stepper quantité (fiche produit, panier) ---------- */
function initQuantityStepper() {
    document.querySelectorAll('[data-qty-stepper]').forEach(function (stepper) {
        var input = stepper.querySelector('input');
        var decrement = stepper.querySelector('[data-qty-decrement]');
        var increment = stepper.querySelector('[data-qty-increment]');
        var max = parseInt(input.getAttribute('max') || '99', 10);

        var clamp = function (value) {
            return Math.min(Math.max(value, 1), max);
        };

        if (decrement) {
            decrement.addEventListener('click', function () {
                input.value = clamp((parseInt(input.value, 10) || 1) - 1);
                input.dispatchEvent(new Event('change'));
            });
        }

        if (increment) {
            increment.addEventListener('click', function () {
                input.value = clamp((parseInt(input.value, 10) || 1) + 1);
                input.dispatchEvent(new Event('change'));
            });
        }

        input.addEventListener('change', function () {
            input.value = clamp(parseInt(input.value, 10) || 1);
            if (stepper.hasAttribute('data-auto-submit')) {
                var form = stepper.closest('form');
                // requestSubmit() déclenche le vrai événement 'submit' (intercepté par
                // cart.js pour l'AJAX) — contrairement à submit() qui le contourne.
                if (form.requestSubmit) {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        });
    });
}

/* ---------- Helper fetch JSON (utilisé par cart.js, wishlist.js, home.js...) ---------- */
window.requestJSON = function (url, method, data) {
    var token = document.querySelector('meta[name="csrf-token"]');

    return fetch(url, {
        method: method || 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
        },
        body: JSON.stringify(data || {})
    }).then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (json) {
            if (!res.ok) {
                var err = new Error(json.message || 'Request failed: ' + res.status);
                err.payload = json;
                throw err;
            }
            return json;
        });
    });
};

// Alias rétro-compatible.
window.postJSON = function (url, data) {
    return window.requestJSON(url, 'POST', data);
};
