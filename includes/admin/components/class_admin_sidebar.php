<?php
/**
 * Componente Sidebar del Admin
 *
 * Renderiza la navegación lateral con tabs
 *
 * @package WlandChat
 * @version 1.2.0
 */

namespace WlandChat\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Admin_Sidebar {

    /**
     * Instancia única (Singleton)
     *
     * @var Admin_Sidebar
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @return Admin_Sidebar
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        // Inicialización si es necesaria
    }

    /**
     * Renderizar sidebar
     *
     * @param string $current_page Página actual
     * @return void
     */
    public function render($current_page = '') {
        if (empty($current_page)) {
            $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'wland-chat-ia';
        }

        $menu_items = $this->get_menu_items();

        ?>
        <nav class="wland-admin-sidebar">
            <div class="wland-admin-sidebar__inner">
                <?php foreach ($menu_items as $item): ?>
                    <?php $this->render_menu_item($item, $current_page); ?>
                <?php endforeach; ?>
            </div>

            <?php
            /**
             * Hook para agregar items adicionales al sidebar
             *
             * @param string $current_page Página actual
             */
            do_action('wland_chat_admin_sidebar_items', $current_page);
            ?>
        </nav>
        <?php
    }

    /**
     * Obtener items del menú
     *
     * @return array Items del menú
     */
    private function get_menu_items() {
        $items = array(
            array(
                'id' => 'dashboard',
                'label' => __('Dashboard', 'wland-chat'),
                'url' => admin_url('admin.php?page=wland-chat-ia'),
                'page_slug' => 'wland-chat-ia',
                'icon' => $this->get_icon_svg('dashboard'),
            ),
            array(
                'id' => 'settings',
                'label' => __('Ajustes', 'wland-chat'),
                'url' => admin_url('admin.php?page=wland-chat-settings'),
                'page_slug' => 'wland-chat-settings',
                'icon' => $this->get_icon_svg('settings'),
            ),
            array(
                'id' => 'appearance',
                'label' => __('Apariencia', 'wland-chat'),
                'url' => admin_url('admin.php?page=wland-chat-appearance'),
                'page_slug' => 'wland-chat-appearance',
                'icon' => $this->get_icon_svg('appearance'),
            ),
            array(
                'id' => 'availability',
                'label' => __('Horarios', 'wland-chat'),
                'url' => admin_url('admin.php?page=wland-chat-availability'),
                'page_slug' => 'wland-chat-availability',
                'icon' => $this->get_icon_svg('availability'),
            ),
            array(
                'id' => 'gdpr',
                'label' => __('GDPR', 'wland-chat'),
                'url' => admin_url('admin.php?page=wland-chat-gdpr'),
                'page_slug' => 'wland-chat-gdpr',
                'icon' => $this->get_icon_svg('gdpr'),
            ),
        );

        /**
         * Filtro para modificar items del menú
         *
         * @param array $items Items del menú
         */
        return apply_filters('wland_chat_admin_menu_items', $items);
    }

    /**
     * Renderizar un item del menú
     *
     * @param array $item Item del menú
     * @param string $current_page Página actual
     * @return void
     */
    private function render_menu_item($item, $current_page) {
        $is_active = ($item['page_slug'] === $current_page);
        $item_class = 'wland-admin-sidebar__item';

        if ($is_active) {
            $item_class .= ' wland-admin-sidebar__item--active';
        }

        ?>
        <a href="<?php echo esc_url($item['url']); ?>"
           class="<?php echo esc_attr($item_class); ?>"
           data-page="<?php echo esc_attr($item['page_slug']); ?>">
            <span class="wland-admin-sidebar__icon">
                <?php echo $item['icon']; ?>
            </span>
            <span class="wland-admin-sidebar__label">
                <?php echo esc_html($item['label']); ?>
            </span>
        </a>
        <?php
    }

    /**
     * Obtener SVG de icono
     *
     * @param string $icon_name Nombre del icono
     * @return string SVG del icono
     */
    private function get_icon_svg($icon_name) {
        $icons = array(
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
            </svg>',
            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" fill="currentColor"/>
            </svg>',
            'appearance' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" fill="currentColor"/>
            </svg>',
            'availability' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm3.3 14.71L11 12.41V7h2v4.59l3.71 3.71-1.42 1.41z" fill="currentColor"/>
            </svg>',
            'gdpr' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9z" fill="currentColor"/>
            </svg>',
        );

        return isset($icons[$icon_name]) ? $icons[$icon_name] : '';
    }
}
