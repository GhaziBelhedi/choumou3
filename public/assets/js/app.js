/**
 * app.js — comportements globaux du site (menu mobile, dropdowns, toasts).
 * JavaScript vanilla, aucune librairie externe.
 */

document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initDropdowns();
    autoDismissAlerts();
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

/* ---------- Auto-dismiss des messages flash ---------- */
function autoDismissAlerts() {
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 300ms ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 300);
        }, 4000);
    });
}

/* ---------- Helper fetch JSON (utilisé par cart.js, wishlist.js...) ---------- */
window.postJSON = function (url, data) {
    var token = document.querySelector('meta[name="csrf-token"]');
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
        },
        body: JSON.stringify(data || {})
    }).then(function (res) {
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        return res.json();
    });
};
