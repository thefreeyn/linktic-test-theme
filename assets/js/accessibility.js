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

            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            messageEl.style.display = 'none';
            messageEl.className = 'linktic-form-message';

            // Build form data
            var formData = new FormData();
            formData.append('action', 'linktic_contact_form');
            formData.append('nonce', (typeof linkticAjax !== 'undefined') ? linkticAjax.nonce : '');
            formData.append('name', form.querySelector('[name="name"]').value);
            formData.append('email', form.querySelector('[name="email"]').value);
            formData.append('phone', form.querySelector('[name="phone"]').value || '');
            formData.append('company', form.querySelector('[name="company"]').value || '');
            formData.append('message', form.querySelector('[name="message"]').value);

            var ajaxUrl = (typeof linkticAjax !== 'undefined') ? linkticAjax.ajaxUrl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data && data.data && data.data.message) {
                    messageEl.textContent = data.data.message;
                } else {
                    messageEl.textContent = data.success ? '¡Enviado!' : 'Error al enviar.';
                }
                messageEl.className = 'linktic-form-message ' + (data.success ? 'success' : 'error');

                if (data.success) {
                    form.reset();
                }
            })
            .catch(function () {
                messageEl.textContent = 'Error de conexión. Intenta de nuevo.';
                messageEl.className = 'linktic-form-message error';
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
