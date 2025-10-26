/**
 * Color Picker Sync - Wland Chat iA
 *
 * Sincroniza input type="color" con input type="text" y paleta de colores predefinidos
 *
 * @package WlandChat
 * @since 1.2.4
 */

(function() {
    'use strict';

    /**
     * Inicializar sincronización de color pickers
     */
    function init_color_pickers() {
        const color_pickers = document.querySelectorAll('.wland-color-picker');

        color_pickers.forEach(function(picker) {
            const text_input = picker.parentElement.querySelector('.wland-color-text');

            if (!text_input) {
                return;
            }

            // Sincronizar cuando cambia el color picker
            picker.addEventListener('input', function() {
                text_input.value = picker.value.toUpperCase();
            });

            // Sincronizar cuando cambia el texto (opcional, pero útil)
            text_input.addEventListener('input', function() {
                const color = text_input.value.trim();
                // Validar formato hexadecimal
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    picker.value = color;
                }
            });
        });
    }

    /**
     * Inicializar botones de paleta de colores predefinidos
     */
    function init_color_presets() {
        const preset_buttons = document.querySelectorAll('.wland-color-preset');

        preset_buttons.forEach(function(button) {
            // Agregar efecto hover
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.borderColor = '#9CA3AF';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.borderColor = '#E5E7EB';
            });

            // Aplicar color al hacer clic
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const color = this.getAttribute('data-color');
                const target_id = this.getAttribute('data-target');

                if (!color || !target_id) {
                    return;
                }

                // Actualizar el color picker
                const color_picker = document.getElementById(target_id);
                if (color_picker) {
                    color_picker.value = color;

                    // Disparar evento input para sincronizar con el campo de texto
                    const event = new Event('input', { bubbles: true });
                    color_picker.dispatchEvent(event);
                }

                // Efecto visual de selección
                this.style.transform = 'scale(0.95)';
                setTimeout(function() {
                    button.style.transform = 'scale(1)';
                }, 100);
            });
        });
    }

    /**
     * Inicializar toggles de paleta de colores
     */
    function init_palette_toggles() {
        const toggle_buttons = document.querySelectorAll('.wland-palette-toggle');

        toggle_buttons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const palette_id = this.getAttribute('data-palette-target');
                const palette = document.getElementById(palette_id);

                if (!palette) {
                    return;
                }

                // Toggle clases
                const is_expanded = this.classList.contains('wland-palette-toggle--expanded');

                if (is_expanded) {
                    // Colapsar
                    this.classList.remove('wland-palette-toggle--expanded');
                    palette.classList.remove('wland-color-palette--expanded');
                    palette.classList.add('wland-color-palette--collapsed');
                } else {
                    // Expandir
                    this.classList.add('wland-palette-toggle--expanded');
                    palette.classList.remove('wland-color-palette--collapsed');
                    palette.classList.add('wland-color-palette--expanded');
                }
            });
        });
    }

    /**
     * Inicializar todo
     */
    function init() {
        init_color_pickers();
        init_color_presets();
        init_palette_toggles();
    }

    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
