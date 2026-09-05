/**
 * home.js — comportements spécifiques à la page d'accueil :
 * slider hero, compte à rebours du deal du jour, onglets meilleures ventes,
 * chargement AJAX des "consultés récemment", inscription newsletter.
 */

document.addEventListener('DOMContentLoaded', function () {
    initHeroSlider();
    initDealCountdown();
    initBestsellerTabs();
    loadRecentlyViewed();
    initNewsletterForm();
});

/* ---------- Hero slider ---------- */
function initHeroSlider() {
    var slider = document.querySelector('[data-hero-slider]');
    if (!slider) return;

    var slides = slider.querySelectorAll('.hero-slide');
    var dotsWrap = slider.querySelector('[data-hero-dots]');
    if (!slides.length) return;

    var current = 0;
    var dots = [];

    slides.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'hero-slider__dot' + (i === 0 ? ' is-active' : '');
        dot.setAttribute('aria-label', 'Diapositive ' + (i + 1));
        dot.addEventListener('click', function () { goTo(i); });
        dotsWrap.appendChild(dot);
        dots.push(dot);
    });

    function goTo(index) {
        slides[current].classList.remove('is-active');
        dots[current].classList.remove('is-active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('is-active');
        dots[current].classList.add('is-active');
    }

    var timer = setInterval(function () { goTo(current + 1); }, 6000);

    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', function () {
        timer = setInterval(function () { goTo(current + 1); }, 6000);
    });
}

/* ---------- Compte à rebours du deal du jour ---------- */
function initDealCountdown() {
    var el = document.querySelector('[data-deal-countdown]');
    if (!el) return;

    var deadline = new Date(el.getAttribute('data-deadline')).getTime();
    var hoursEl = el.querySelector('[data-countdown-hours]');
    var minutesEl = el.querySelector('[data-countdown-minutes]');
    var secondsEl = el.querySelector('[data-countdown-seconds]');

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        var diff = deadline - Date.now();

        if (diff <= 0) {
            hoursEl.textContent = minutesEl.textContent = secondsEl.textContent = '00';
            clearInterval(interval);
            return;
        }

        var hours = Math.floor(diff / 3600000);
        var minutes = Math.floor((diff % 3600000) / 60000);
        var seconds = Math.floor((diff % 60000) / 1000);

        secondsEl.classList.remove('is-ticking');
        void secondsEl.offsetWidth; // relance l'animation CSS à chaque tick
        secondsEl.classList.add('is-ticking');

        hoursEl.textContent = pad(hours);
        minutesEl.textContent = pad(minutes);
        secondsEl.textContent = pad(seconds);
    }

    tick();
    var interval = setInterval(tick, 1000);
}

/* ---------- Onglets meilleures ventes (par catégorie) ---------- */
function initBestsellerTabs() {
    var tabs = document.querySelectorAll('[data-bestseller-tab]');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var key = tab.getAttribute('data-bestseller-tab');

            document.querySelectorAll('[data-bestseller-tab]').forEach(function (t) { t.classList.remove('is-active'); });
            document.querySelectorAll('[data-bestseller-panel]').forEach(function (p) { p.classList.remove('is-active'); });

            tab.classList.add('is-active');
            var panel = document.querySelector('[data-bestseller-panel="' + key + '"]');
            if (panel) panel.classList.add('is-active');
        });
    });
}

/* ---------- Consultés récemment (AJAX depuis localStorage) ---------- */
function loadRecentlyViewed() {
    var section = document.querySelector('[data-recently-viewed-section]');
    var grid = document.querySelector('[data-recently-viewed-grid]');
    if (!section || !grid || !window.getRecentlyViewedIds) return;

    var ids = window.getRecentlyViewedIds();
    if (!ids.length) return;

    fetch('/produits/consultes-recemment?ids=' + ids.join(','), {
        headers: { 'Accept': 'text/html' }
    })
        .then(function (res) { return res.text(); })
        .then(function (html) {
            if (!html.trim()) return;
            grid.innerHTML = html;
            section.style.display = '';
            section.classList.add('is-visible');
        })
        .catch(function () { /* section reste masquée en cas d'échec */ });
}

/* ---------- Newsletter (AJAX) ---------- */
function initNewsletterForm() {
    var form = document.querySelector('[data-newsletter-form]');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var email = form.querySelector('input[name="email"]').value;

        window.requestJSON(form.getAttribute('action'), 'POST', { email: email })
            .then(function (json) {
                window.showToast(json.message, 'success');
                form.reset();
            })
            .catch(function (err) {
                window.showToast(err.message || 'Une erreur est survenue.', 'error');
            });
    });
}
