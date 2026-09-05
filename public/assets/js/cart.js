/**
 * cart.js — panier en AJAX (ajout / quantité / suppression / coupon),
 * sans rechargement de page. Se dégrade en formulaires classiques si JS
 * est indisponible (chaque form garde son action/method HTML normal).
 */

document.addEventListener('DOMContentLoaded', function () {
    initAddToCartForms();
    initCartUpdateForms();
    initCartRemoveForms();
    initCouponForms();
});

function updateCartBadge(count) {
    document.querySelectorAll('[data-cart-badge]').forEach(function (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.hidden = false;
        } else {
            badge.hidden = true;
        }
    });
}

function updateCartTotals(totals) {
    if (!totals) return;
    document.querySelectorAll('[data-cart-subtotal]').forEach(function (el) { el.textContent = totals.subtotal + ' DT'; });
    document.querySelectorAll('[data-cart-total]').forEach(function (el) { el.textContent = totals.total + ' DT'; });
}

function submitFormAsJSON(form) {
    var url = form.getAttribute('action');
    var method = (form.querySelector('input[name="_method"]') || {}).value || form.getAttribute('method') || 'POST';
    var data = {};

    new FormData(form).forEach(function (value, key) {
        if (key === '_token' || key === '_method') return;
        data[key] = value;
    });

    return window.requestJSON(url, method.toUpperCase(), data);
}

/* ---------- Ajout au panier (product-card + fiche produit) ---------- */
function initAddToCartForms() {
    document.querySelectorAll('[data-add-to-cart-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var button = form.querySelector('button[type="submit"]');
            // Ne touche au texte du bouton que s'il a explicitement du texte à
            // remplacer pendant le chargement (boutons icône seule = juste disabled).
            var hasLabel = button && button.hasAttribute('data-loading-text');
            var originalText = hasLabel ? button.textContent : null;

            if (button) {
                button.disabled = true;
                button.classList.add('is-loading');
                if (hasLabel) button.textContent = button.getAttribute('data-loading-text');
            }

            submitFormAsJSON(form)
                .then(function (json) {
                    window.showToast(json.message, json.success ? 'success' : 'error');
                    if (json.success) {
                        updateCartBadge(json.cart_count);
                        if (button) button.classList.add('is-added');
                    }
                })
                .catch(function (err) {
                    window.showToast(err.message || 'Une erreur est survenue.', 'error');
                })
                .finally(function () {
                    if (button) {
                        button.disabled = false;
                        button.classList.remove('is-loading');
                        if (hasLabel) button.textContent = originalText;
                        setTimeout(function () { button.classList.remove('is-added'); }, 1200);
                    }
                });
        });
    });
}

/* ---------- Mise à jour quantité (page panier) ---------- */
function initCartUpdateForms() {
    document.querySelectorAll('[data-cart-update-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            submitFormAsJSON(form)
                .then(function (json) {
                    if (json.success && json.item) {
                        var row = form.closest('[data-cart-row]');
                        if (row) {
                            var subtotalEl = row.querySelector('[data-row-subtotal]');
                            if (subtotalEl) subtotalEl.textContent = json.item.subtotal + ' DT';
                        }
                        updateCartBadge(json.cart_count);
                        updateCartTotals(json.totals);
                    }
                    window.showToast(json.message, json.success ? 'success' : 'error');
                })
                .catch(function (err) {
                    window.showToast(err.message || 'Une erreur est survenue.', 'error');
                });
        });
    });
}

/* ---------- Suppression d'un article du panier ---------- */
function initCartRemoveForms() {
    document.querySelectorAll('[data-cart-remove-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!confirm('Retirer ce livre du panier ?')) return;

            submitFormAsJSON(form)
                .then(function (json) {
                    var row = form.closest('[data-cart-row]');
                    if (row) {
                        row.style.transition = 'opacity 200ms ease';
                        row.style.opacity = '0';
                        setTimeout(function () {
                            row.remove();
                            if (json.cart_count === 0) window.location.reload();
                        }, 200);
                    }
                    updateCartBadge(json.cart_count);
                    updateCartTotals(json.totals);
                    window.showToast(json.message, 'success');
                })
                .catch(function (err) {
                    window.showToast(err.message || 'Une erreur est survenue.', 'error');
                });
        });
    });
}

/* ---------- Coupon (appliquer / retirer) ---------- */
function initCouponForms() {
    document.querySelectorAll('[data-coupon-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            submitFormAsJSON(form)
                .then(function (json) {
                    window.showToast(json.message, json.success ? 'success' : 'error');
                    if (json.success) {
                        updateCartTotals(json.totals);
                        setTimeout(function () { window.location.reload(); }, 700);
                    }
                })
                .catch(function (err) {
                    window.showToast(err.message || 'Une erreur est survenue.', 'error');
                });
        });
    });
}
