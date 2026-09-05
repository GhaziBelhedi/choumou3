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
    initChatbot();
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

/* ---------- Historique "consultés récemment" (localStorage, pas de session serveur) ---------- */
var RECENTLY_VIEWED_KEY = 'choumou3_recently_viewed';
var RECENTLY_VIEWED_MAX = 8;

window.trackRecentlyViewed = function (productId) {
    try {
        var ids = JSON.parse(localStorage.getItem(RECENTLY_VIEWED_KEY) || '[]');
        ids = ids.filter(function (id) { return id !== productId; });
        ids.unshift(productId);
        ids = ids.slice(0, RECENTLY_VIEWED_MAX);
        localStorage.setItem(RECENTLY_VIEWED_KEY, JSON.stringify(ids));
    } catch (e) {
        // localStorage indisponible (navigation privée stricte...) — on ignore silencieusement.
    }
};

window.getRecentlyViewedIds = function () {
    try {
        return JSON.parse(localStorage.getItem(RECENTLY_VIEWED_KEY) || '[]');
    } catch (e) {
        return [];
    }
};

/* ---------- Chatbot Widget ---------- */
function initChatbot() {
    var widget = document.querySelector('[data-chatbot]');
    if (!widget) return;

    var toggleBtn = widget.querySelector('[data-chatbot-toggle]');
    var closeBtn = widget.querySelector('[data-chatbot-close]');
    var windowPanel = widget.querySelector('[data-chatbot-window]');
    var messagesContainer = widget.querySelector('[data-chatbot-messages]');
    var form = widget.querySelector('[data-chatbot-form]');
    var input = widget.querySelector('[data-chatbot-input]');
    var suggestionChips = widget.querySelectorAll('[data-chatbot-suggest]');
    var badge = widget.querySelector('.chatbot-toggle-badge');

    var isOpen = false;

    // Toggle Chat window
    function toggleChat() {
        isOpen = !isOpen;
        if (isOpen) {
            windowPanel.removeAttribute('hidden');
            setTimeout(function() {
                windowPanel.classList.add('is-open');
                input.focus();
            }, 10);
            if (badge) badge.style.display = 'none';
        } else {
            windowPanel.classList.remove('is-open');
            setTimeout(function() {
                if (!isOpen) windowPanel.setAttribute('hidden', '');
            }, 300);
        }
    }

    if (toggleBtn) toggleBtn.addEventListener('click', toggleChat);
    if (closeBtn) closeBtn.addEventListener('click', toggleChat);

    // Append Message helper
    function appendMessage(text, sender) {
        var msgDiv = document.createElement('div');
        msgDiv.className = 'chatbot-message chatbot-message--' + sender;
        
        var textDiv = document.createElement('div');
        textDiv.className = 'chatbot-message__text';
        textDiv.innerHTML = text;
        
        msgDiv.appendChild(textDiv);
        messagesContainer.appendChild(msgDiv);
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Typing Indicator helper
    var activeTypingIndicator = null;
    function showTypingIndicator() {
        if (activeTypingIndicator) return;
        
        var msgDiv = document.createElement('div');
        msgDiv.className = 'chatbot-message chatbot-message--bot';
        msgDiv.id = 'chatbot-typing-indicator';
        
        var textDiv = document.createElement('div');
        textDiv.className = 'chatbot-message__text';
        
        var typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-typing';
        typingDiv.innerHTML = '<span></span><span></span><span></span>';
        
        textDiv.appendChild(typingDiv);
        msgDiv.appendChild(textDiv);
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        activeTypingIndicator = msgDiv;
    }

    function removeTypingIndicator() {
        if (activeTypingIndicator) {
            activeTypingIndicator.remove();
            activeTypingIndicator = null;
        }
    }

    // Bot Response Logic
    function handleBotReply(userQuery) {
        showTypingIndicator();
        
        var query = userQuery.toLowerCase().trim();
        var reply = "";
        
        // Match responses
        if (query.indexOf('livraison') > -1 || query.indexOf('tarif') > -1 || query.indexOf('frais') > -1) {
            reply = "Nous livrons partout en Tunisie. 🚚<br><br>" +
                    "• **Tarif standard** : 7 DT fixe.<br>" +
                    "• **Livraison gratuite** : à partir de 80 DT d'achats !<br>" +
                    "• **Délai** : 24h à 72h ouvrables selon votre région.";
        } else if (query.indexOf('suivi') > -1 || query.indexOf('suivre') > -1 || query.indexOf('commande') > -1) {
            reply = "Vous pouvez suivre le statut de votre colis en temps réel ! 📦<br><br>" +
                    "Rendez-vous sur notre page [Suivi de commande](/suivi-commande) et entrez votre numéro de référence reçu par SMS ou par email.";
        } else if (query.indexOf('contact') > -1 || query.indexOf('horaire') > -1 || query.indexOf('téléphone') > -1 || query.indexOf('telephone') > -1) {
            reply = "Notre équipe est disponible pour vous accompagner ! 📞<br><br>" +
                    "• **Téléphone** : +216 71 000 000 (Lun-Ven, 8h30-17h30)<br>" +
                    "• **Email** : contact@choumou3.test<br>" +
                    "• **Adresse** : Avenue Habib Bourguiba, Tunis.";
        } else if (query.indexOf('retour') > -1 || query.indexOf('remboursement') > -1 || query.indexOf('échange') > -1) {
            reply = "Vous changez d'avis ? Pas de souci ! ♻️<br><br>" +
                    "Vous disposez de **14 jours** après réception pour retourner un article non ouvert/non utilisé. Contactez le service client pour organiser le ramassage.";
        } else if (query.indexOf('bonjour') > -1 || query.indexOf('salut') > -1 || query.indexOf('hi') > -1) {
            reply = "Bonjour ! Comment puis-je vous aider aujourd'hui ? Je peux vous parler de nos tarifs de livraison, du suivi de commande, ou de nos horaires. 😊";
        } else {
            reply = "Je suis ravi de vous aider ! Pour toute question spécifique sur un produit, un achat en gros ou un problème de paiement, vous pouvez aussi contacter directement nos conseillers au +216 71 000 000. 📚✨";
        }

        setTimeout(function() {
            removeTypingIndicator();
            appendMessage(reply, 'bot');
        }, 1200);
    }

    // Submit form handler
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var text = input.value.trim();
            if (!text) return;
            
            appendMessage(text, 'user');
            input.value = '';
            
            handleBotReply(text);
        });
    }

    // Click on suggestion chips
    suggestionChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            var text = chip.getAttribute('data-chatbot-suggest') || chip.textContent;
            appendMessage(text, 'user');
            handleBotReply(text);
        });
    });
}
