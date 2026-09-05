/**
 * gallery.js — galerie d'images de la fiche produit (miniatures -> image principale)
 * et onglets (description / infos bibliographiques / avis).
 */

document.addEventListener('DOMContentLoaded', function () {
    initProductGallery();
    initTabs();
    initQuantityStepper();
});

function initProductGallery() {
    var mainImg = document.querySelector('[data-gallery-main] img');
    var thumbs = document.querySelectorAll('[data-gallery-thumb]');
    if (!mainImg || !thumbs.length) return;

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            var fullSrc = thumb.getAttribute('data-full-src');
            if (!fullSrc) return;
            mainImg.setAttribute('src', fullSrc);
            thumbs.forEach(function (t) { t.classList.remove('is-active'); });
            thumb.classList.add('is-active');
        });
    });
}

function initTabs() {
    var tabs = document.querySelectorAll('[data-tab]');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var targetId = tab.getAttribute('data-tab');

            document.querySelectorAll('[data-tab]').forEach(function (t) { t.classList.remove('is-active'); });
            document.querySelectorAll('[data-tab-panel]').forEach(function (p) { p.classList.remove('is-active'); });

            tab.classList.add('is-active');
            var panel = document.querySelector('[data-tab-panel="' + targetId + '"]');
            if (panel) panel.classList.add('is-active');
        });
    });
}

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
        });
    });
}
