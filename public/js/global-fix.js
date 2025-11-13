/**
 * Global Fix - Correcciones globales del sistema
 */

(function() {
    'use strict';

    // Inicializar tooltips de Bootstrap si existen
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Manejo de errores global
    window.addEventListener('error', function(event) {
        console.error('Error global:', event.error);
    });

    // Logout automático si se recibe 401 (no autenticado)
    document.addEventListener('DOMContentLoaded', function() {
        // Aquí pueden ir otras inicializaciones globales
    });
})();
