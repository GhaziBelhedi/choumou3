/**
 * filters.js — panneau de filtres du catalogue en version mobile (drawer),
 * et soumission automatique du formulaire de filtres au changement.
 */

document.addEventListener('DOMContentLoaded', function () {
    initFiltersDrawer();
    initAutoSubmitFilters();
});

function initFiltersDrawer() {
    var toggle = document.querySelector('[data-filters-toggle]');
    var drawer = document.querySelector('[data-filters-drawer]');
    if (!toggle || !drawer) return;

    var close = function () { drawer.classList.remove('is-open'); document.body.style.overflow = ''; };
    var open = function () { drawer.classList.add('is-open'); document.body.style.overflow = 'hidden'; };

    toggle.addEventListener('click', open);

    var overlay = drawer.querySelector('[data-filters-overlay]');
    if (overlay) overlay.addEventListener('click', close);

    var closeBtn = drawer.querySelector('[data-filters-close]');
    if (closeBtn) closeBtn.addEventListener('click', close);
}

function initAutoSubmitFilters() {
    var form = document.querySelector('[data-filters-form]');
    if (!form) return;

    form.querySelectorAll('input[type="checkbox"], input[type="radio"], select').forEach(function (el) {
        el.addEventListener('change', function () {
            form.submit();
        });
    });
}
