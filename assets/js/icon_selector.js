/**
 * Icon Selector - Tabs Bentō
 * @since 1.2.3
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.wland-icon-tab');

        if (!tabs.length) return;

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });
    });
})();
