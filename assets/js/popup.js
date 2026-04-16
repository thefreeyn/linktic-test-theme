/**
 * LinkTIC – Popup
 *
 * Muestra un popup con el nombre del postulante 3 segundos después de cargar la página.
 * Usa sessionStorage para no volver a mostrar en la misma sesión.
 */
(function () {
    'use strict';

    var POPUP_SHOWN_KEY = 'linktic_popup_shown';

    document.addEventListener('DOMContentLoaded', function () {
        var overlay  = document.getElementById('linktic-popup-overlay');
        var closeBtn = document.getElementById('linktic-popup-close');

        if (!overlay) return;

        // If already shown this session, remove and exit.
        if (sessionStorage.getItem(POPUP_SHOWN_KEY)) {
            overlay.parentNode.removeChild(overlay);
            return;
        }

        /**
         * Close the popup with animation.
         */
        function closePopup() {
            overlay.classList.add('linktic-popup-hidden');
            sessionStorage.setItem(POPUP_SHOWN_KEY, 'true');

            // Remove from DOM after animation completes.
            setTimeout(function () {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 450);
        }

        // Show popup after 3 seconds.
        setTimeout(function () {
            overlay.classList.remove('linktic-popup-hidden');

            // Focus close button for keyboard accessibility.
            if (closeBtn) {
                closeBtn.focus();
            }
        }, 3000);

        // Close button click.
        if (closeBtn) {
            closeBtn.addEventListener('click', closePopup);
        }

        // Close on overlay background click.
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                closePopup();
            }
        });

        // Close on Escape key.
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay && !overlay.classList.contains('linktic-popup-hidden')) {
                closePopup();
            }
        });
    });

})();
