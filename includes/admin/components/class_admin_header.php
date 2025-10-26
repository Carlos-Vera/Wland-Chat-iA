<?php
/**
 * Componente Header del Admin
 *
 * Renderiza el header con logo y navegación superior
 *
 * @package WlandChat
 * @version 1.2.0
 */

namespace WlandChat\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Admin_Header {

    /**
     * Instancia única (Singleton)
     *
     * @var Admin_Header
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @return Admin_Header
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
     * Renderizar header
     *
     * @param array $args Argumentos opcionales
     * @return void
     */
    public function render($args = array()) {
        $defaults = array(
            'show_logo' => true,
            'show_version' => true,
            'custom_class' => '',
        );

        $args = wp_parse_args($args, $defaults);

        ?>
        <div class="wland-admin-header <?php echo esc_attr($args['custom_class']); ?>">
            <div class="wland-admin-header__inner">
                <?php if ($args['show_logo']): ?>
                <div class="wland-admin-header__logo">
                    <?php $this->render_logo(); ?>
                </div>
                <?php endif; ?>

                <?php if ($args['show_version']): ?>
                <div class="wland-admin-header__version">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wland-chat-about')); ?>"
                       class="wland-badge wland-badge--primary wland-badge--clickable"
                       title="<?php echo esc_attr__('Ver información del plugin', 'wland-chat'); ?>">
                        <?php echo esc_html('v' . WLAND_CHAT_VERSION); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renderizar logo
     *
     * @return void
     */
    private function render_logo() {
        $logo_path = WLAND_CHAT_PLUGIN_DIR . 'assets/media/wland-logo.svg';

        if (file_exists($logo_path)) {
            // Renderizar SVG directamente
            echo file_get_contents($logo_path);
        } else {
            // Fallback a texto
            echo '<span class="wland-admin-header__logo-text">';
            echo esc_html__('Wland Chat iA', 'wland-chat');
            echo '</span>';
        }
    }

    /**
     * Obtener HTML del header sin renderizarlo
     *
     * @param array $args Argumentos opcionales
     * @return string HTML del header
     */
    public function get_html($args = array()) {
        ob_start();
        $this->render($args);
        return ob_get_clean();
    }
}
