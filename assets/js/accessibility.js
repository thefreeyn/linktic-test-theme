/**
 * LinkTIC – Accessibility Controls
 *
 * - Zoom in / Zoom out (3 niveles: normal, +1, +2)
 * - Toggle alto contraste
 * - Persistencia en localStorage
 * - Manejo del formulario de contacto via AJAX
 */
(function () {
    'use strict';

    var STORAGE_KEYS = {
        zoom: 'linktic_zoom_level',
        contrast: 'linktic_high_contrast'
    };

    var MAX_ZOOM = 2;
    var zoomLevel = parseInt(localStorage.getItem(STORAGE_KEYS.zoom) || '0', 10);
    var highContrast = localStorage.getItem(STORAGE_KEYS.contrast) === 'true';

    /**
     * Apply saved zoom & contrast preferences to the DOM.
     */
    function applyPreferences() {
        var body = document.body;

        // Zoom classes
        body.classList.remove('linktic-zoom-1', 'linktic-zoom-2');
        if (zoomLevel > 0 && zoomLevel <= MAX_ZOOM) {
            body.classList.add('linktic-zoom-' + zoomLevel);
        }

        // Contrast class
        if (highContrast) {
            body.classList.add('high-contrast');
        } else {
            body.classList.remove('high-contrast');
        }

        // Update button states
        var contrastBtn = document.getElementById('linktic-contrast-toggle');
        if (contrastBtn) {
            contrastBtn.classList.toggle('active', highContrast);
            contrastBtn.setAttribute('aria-pressed', String(highContrast));
        }
    }

    /**
     * Initialize all accessibility controls.
     */
    function initAccessibility() {
        applyPreferences();

        // ── Zoom In ──
        var zoomInBtn = document.getElementById('linktic-zoom-in');
        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', function () {
                if (zoomLevel < MAX_ZOOM) {
                    zoomLevel++;
                    localStorage.setItem(STORAGE_KEYS.zoom, String(zoomLevel));
                    applyPreferences();
                }
            });
        }

        // ── Zoom Out ──
        var zoomOutBtn = document.getElementById('linktic-zoom-out');
        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function () {
                if (zoomLevel > 0) {
                    zoomLevel--;
                    localStorage.setItem(STORAGE_KEYS.zoom, String(zoomLevel));
                    applyPreferences();
                }
            });
        }

        // ── Contrast Toggle ──
        var contrastBtn = document.getElementById('linktic-contrast-toggle');
        if (contrastBtn) {
            contrastBtn.addEventListener('click', function () {
                highContrast = !highContrast;
                localStorage.setItem(STORAGE_KEYS.contrast, String(highContrast));
                applyPreferences();
            });
        }
    }

    /**
     * Show a message inside the contact form.
     * Uses only CSS classes to control visibility (no inline style override).
     */
    function showFormMessage(messageEl, text, type) {
        messageEl.textContent = text;
        // Remove inline style so CSS classes take full control
        messageEl.removeAttribute('style');
        messageEl.className = 'linktic-form-message ' + type;
        messageEl.focus();
    }

    /**
     * Initialize contact form AJAX handler.
     */
    function initContactForm() {
        var form = document.getElementById('linktic-contact-form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var submitBtn = form.querySelector('.form-submit');
            var messageEl = form.querySelector('.linktic-form-message');

            if (!submitBtn || !messageEl) return;

            // ── Client-side validation ──────────────────────────────────
            var nameVal    = (form.querySelector('[name="name"]').value || '').trim();
            var emailVal   = (form.querySelector('[name="email"]').value || '').trim();
            var messageVal = (form.querySelector('[name="message"]').value || '').trim();

            if (!nameVal) {
                showFormMessage(messageEl, 'Por favor ingresa tu nombre.', 'error');
                form.querySelector('[name="name"]').focus();
                return;
            }
            if (!emailVal) {
                showFormMessage(messageEl, 'Por favor ingresa tu correo electrónico.', 'error');
                form.querySelector('[name="email"]').focus();
                return;
            }
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailVal)) {
                showFormMessage(messageEl, 'El correo electrónico no es válido.', 'error');
                form.querySelector('[name="email"]').focus();
                return;
            }
            if (!messageVal) {
                showFormMessage(messageEl, 'Por favor escribe tu mensaje.', 'error');
                form.querySelector('[name="message"]').focus();
                return;
            }

            // ── Reset message & disable button ──────────────────────────
            messageEl.className = 'linktic-form-message';
            messageEl.removeAttribute('style');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            // ── Build form data ─────────────────────────────────────────
            var formData = new FormData();
            formData.append('action', 'linktic_contact_form');
            formData.append('nonce', (typeof linkticAjax !== 'undefined') ? linkticAjax.nonce : '');
            formData.append('name', nameVal);
            formData.append('email', emailVal);
            formData.append('phone', (form.querySelector('[name="phone"]').value || '').trim());
            formData.append('company', (form.querySelector('[name="company"]').value || '').trim());
            formData.append('message', messageVal);

            var ajaxUrl = (typeof linkticAjax !== 'undefined') ? linkticAjax.ajaxUrl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                var text = (data && data.data && data.data.message)
                    ? data.data.message
                    : (data.success ? '¡Mensaje enviado correctamente!' : 'Error al enviar.');
                showFormMessage(messageEl, text, data.success ? 'success' : 'error');
                if (data.success) {
                    form.reset();
                }
            })
            .catch(function () {
                showFormMessage(messageEl, 'Error de conexión. Intenta de nuevo.', 'error');
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar mensaje';
            });
        });
    }

    // ── Boot ──
    document.addEventListener('DOMContentLoaded', function () {
        initAccessibility();
        initContactForm();
    });

})();
