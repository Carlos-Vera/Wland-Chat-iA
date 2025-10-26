<?php
/**
 * Funciones helper para templates del admin
 *
 * Funciones auxiliares estáticas para renderizado rápido de componentes
 *
 * @package WlandChat
 * @since 1.2.0
 * @version 1.2.0
 */

namespace WlandChat\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Template_Helpers
 *
 * Proporciona métodos estáticos para renderizado de componentes del admin
 *
 * @since 1.2.0
 */
class Template_Helpers {

    /**
     * Renderizar un card rápidamente
     *
     * @since 1.2.0
     * @param array $args Argumentos del card
     * @return void
     */
    public static function card($args = array()) {
        $content = Admin_Content::get_instance();
        $content->render_card($args);
    }

    /**
     * Renderizar una sección rápidamente
     *
     * @since 1.2.0
     * @param array $args Argumentos de la sección
     * @return void
     */
    public static function section($args = array()) {
        $content = Admin_Content::get_instance();
        $content->render_section($args);
    }

    /**
     * Renderizar un toggle rápidamente
     *
     * @since 1.2.0
     * @param array $args Argumentos del toggle
     * @return void
     */
    public static function toggle($args = array()) {
        $content = Admin_Content::get_instance();
        $content->render_toggle($args);
    }

    /**
     * Renderizar botón de acción rápida
     *
     * @since 1.2.0
     * @param array $args Argumentos del botón
     * @return void
     */
    public static function quick_action($args = array()) {
        $content = Admin_Content::get_instance();
        $content->render_quick_action($args);
    }

    /**
     * Renderizar grid de cards
     *
     * @since 1.2.0
     * @param array $cards Array de configuración de cards
     * @param int   $columns Número de columnas (2, 3 o 4)
     * @return void
     */
    public static function card_grid($cards = array(), $columns = 3) {
        $content = Admin_Content::get_instance();
        $content->render_card_grid($cards, $columns);
    }

    /**
     * Obtener icono SVG por nombre
     *
     * @since 1.2.0
     * @param string $name  Nombre del icono
     * @param string $color Color del icono (opcional)
     * @return string SVG del icono
     */
    public static function get_icon($name, $color = 'currentColor') {
        $icons = array(
            'chat' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'settings' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'docs' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'webhook' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'check' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'warning' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'verified' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M23 12l-2.44-2.79.34-3.69-3.61-.82-1.89-3.2L12 2.96 8.6 1.5 6.71 4.69 3.1 5.5l.34 3.7L1 12l2.44 2.79-.34 3.7 3.61.82L8.6 22.5l3.4-1.47 3.4 1.46 1.89-3.19 3.61-.82-.34-3.69L23 12zm-12.91 4.72l-3.8-3.81 1.48-1.48 2.32 2.33 5.85-5.87 1.48 1.48-7.33 7.35z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'logo_dev' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h4l3 3 3-3h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3.3c1.49 0 2.7 1.21 2.7 2.7 0 1.49-1.21 2.7-2.7 2.7-1.49 0-2.7-1.21-2.7-2.7 0-1.49 1.21-2.7 2.7-2.7zM18 16H6v-.9c0-2 4-3.1 6-3.1s6 1.1 6 3.1v.9z" fill="' . esc_attr($color) . '"/>
            </svg>',

            'business_center' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M10 16v-1H3.01L3 19c0 1.11.89 2 2 2h14c1.11 0 2-.89 2-2v-4h-7v1h-4zm10-9h-4.01V5l-2-2h-4l-2 2v2H4c-1.1 0-2 .9-2 2v3c0 1.11.89 2 2 2h6v-2h4v2h6c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm-6 0h-4V5h4v2z" fill="' . esc_attr($color) . '"/>
            </svg>',
        );

        return isset($icons[$name]) ? $icons[$name] : '';
    }

    /**
     * Renderizar notice/alert
     *
     * @since 1.2.0
     * @param string $message Mensaje a mostrar
     * @param string $type    Tipo de alerta: success, warning, error, info
     * @return void
     */
    public static function notice($message, $type = 'info') {
        $notice_class = 'wland-notice wland-notice--' . sanitize_html_class($type);

        ?>
        <div class="<?php echo esc_attr($notice_class); ?>">
            <div class="wland-notice__content">
                <?php echo wp_kses_post($message); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Obtener estado de configuración del plugin
     *
     * @since 1.2.0
     * @return array Estado de configuración con claves: is_configured, webhook_url, global_enabled, display_mode
     */
    public static function get_config_status() {
        $webhook_url = get_option('wland_chat_webhook_url');
        $is_configured = !empty($webhook_url);

        return array(
            'is_configured' => $is_configured,
            'webhook_url' => $webhook_url,
            'global_enabled' => get_option('wland_chat_global_enable', false),
            'display_mode' => get_option('wland_chat_display_mode', 'modal'),
        );
    }
}
