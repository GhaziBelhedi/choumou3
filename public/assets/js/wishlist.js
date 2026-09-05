/**
 * wishlist.js — toggle liste de souhaits en AJAX, sans rechargement de page.
 * Les invités sont redirigés vers la connexion (lien classique, pas de form).
 */

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-wishlist-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var button = form.querySelector('[data-wishlist-btn]');
            var url = form.getAttribute('action');

            window.requestJSON(url, 'POST', {})
                .then(function (json) {
                    if (!button) return;

                    button.classList.toggle('is-active', json.is_wishlisted);
                    var svg = button.querySelector('svg');
                    if (svg) svg.setAttribute('fill', json.is_wishlisted ? 'currentColor' : 'none');
                    button.setAttribute(
                        'aria-label',
                        json.is_wishlisted ? 'Retirer de la liste de souhaits' : 'Ajouter à la liste de souhaits'
                    );

                    button.classList.add('is-pulsing');
                    setTimeout(function () { button.classList.remove('is-pulsing'); }, 350);

                    window.showToast(json.message, 'success');

                    // Sur la page "Ma liste de souhaits", un retrait fait disparaître la carte.
                    if (!json.is_wishlisted && document.body.hasAttribute('data-wishlist-page')) {
                        var card = form.closest('.product-card');
                        if (card) {
                            card.style.transition = 'opacity 200ms ease';
                            card.style.opacity = '0';
                            setTimeout(function () { card.remove(); }, 200);
                        }
                    }
                })
                .catch(function (err) {
                    window.showToast(err.message || 'Une erreur est survenue.', 'error');
                });
        });
    });
});
