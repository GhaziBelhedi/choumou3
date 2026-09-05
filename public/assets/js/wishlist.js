/**
 * wishlist.js — toggle liste de souhaits.
 * NOTE: la persistance côté serveur (WishlistController) arrive en Phase 4.
 * Pour l'instant, le bouton redirige les invités vers la connexion et fait
 * un toggle visuel simple pour les utilisateurs connectés.
 */

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-wishlist-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            if (document.body.getAttribute('data-authenticated') !== '1') {
                window.location.href = '/connexion';
                return;
            }

            btn.classList.toggle('is-active');
        });
    });
});
