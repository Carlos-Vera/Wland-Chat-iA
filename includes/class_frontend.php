<?php
/**
 * Gestión del frontend
 *
 * Maneja carga de assets y renderizado del chat en el frontend
 *
 * @package WlandChat
 * @since 1.0.0
 * @version 1.2.2
 */

namespace WlandChat;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Frontend
 *
 * Gestiona la visualización y funcionalidad del chat en el frontend
 *
 * @since 1.0.0
 */
class Frontend {

    /**
     * Instancia única (patrón Singleton)
     *
     * @since 1.0.0
     * @var Frontend|null
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @since 1.0.0
     * @return Frontend Instancia única de la clase
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado (patrón Singleton)
     *
     * Inicializa hooks de WordPress
     *
     * @since 1.0.0
     */
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_footer', array($this, 'render_global_chat'), 100);
        add_action('wp_head', array($this, 'inject_custom_colors'), 100);
    }

    /**
     * Encolar assets CSS y JS del frontend
     *
     * Carga condicional de scripts y estilos solo donde se necesita (WPO)
     * Incluye token N8N para autenticación de webhook
     *
     * @since 1.0.0
     * @since 1.2.2 Añadido token N8N en configuración
     * @return void
     */
    public function enqueue_assets() {
        // TAREA 1: Verificación condicional usando Helper
        if (!Helpers::should_display_chat()) {
            // Desencolar assets si el chat no debe mostrarse
            $this->dequeue_all_assets();
            return;
        }
        
        // Verificar si está habilitado globalmente O si hay un bloque en la página
        $global_enable = get_option('wland_chat_global_enable', false);
        $has_block = $this->page_has_chat_block();
        
        if (!$global_enable && !$has_block) {
            // Desencolar assets si no cumple las condiciones
            $this->dequeue_all_assets();
            return;
        }
        
        // Si pasa las verificaciones, encolar los assets

        // Sistema de fingerprinting y cookies (cargar primero, sin dependencias)
        wp_enqueue_script(
            'wland-chat-fingerprint',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/wland_fingerprint.js',
            array(),
            WLAND_CHAT_VERSION,
            false // Cargar en head para que esté disponible inmediatamente
        );

        // Pasar configuración GDPR al script de fingerprinting
        wp_localize_script('wland-chat-fingerprint', 'wlandGDPRConfig', array(
            'enabled' => (bool) get_option('wland_chat_gdpr_enabled', false),
            'message' => get_option('wland_chat_gdpr_message', __('Este sitio utiliza cookies para mejorar tu experiencia y proporcionar un servicio de chat personalizado. Al continuar navegando, aceptas nuestra política de cookies.', 'wland-chat')),
            'accept_text' => get_option('wland_chat_gdpr_accept_text', __('Aceptar', 'wland-chat')),
            'cookie_name' => 'wland_chat_session',
            'cookie_duration' => YEAR_IN_SECONDS
        ));

        // CSS del banner GDPR
        wp_enqueue_style(
            'wland-chat-gdpr-banner',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/wland_gdpr_banner.css',
            array(),
            WLAND_CHAT_VERSION
        );
        
        $display_mode = get_option('wland_chat_display_mode', 'modal');
        
        // CSS condicional según modo de visualización
        wp_enqueue_style(
            'wland-chat-frontend',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/wland_chat_block_' . $display_mode . '.css',
            array(),
            WLAND_CHAT_VERSION
        );

        // JS condicional según modo de visualización
        wp_enqueue_script(
            'wland-chat-frontend',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/wland_chat_block_' . $display_mode . '.js',
            array('wp-i18n'),
            WLAND_CHAT_VERSION,
            true
        );

        // TAREA 2B: Localizar script con configuración completa incluyendo token de autenticación
        $chat_config = Helpers::get_chat_config();

        wp_localize_script('wland-chat-frontend', 'WlandChatConfig', array(
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('wland_chat_nonce'),
            'webhook_url'   => $chat_config['webhook_url'],
            'auth_token'    => get_option('wland_chat_n8n_auth_token', ''),
            'isAvailable'   => $chat_config['is_available'],
        ));

        // Configurar traducciones para JavaScript
        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations(
                'wland-chat-frontend',
                'wland-chat',
                WLAND_CHAT_PLUGIN_DIR . 'languages'
            );
        }
    }
    
    /**
     * Desencolar todos los assets del chat
     *
     * Función auxiliar para optimización WPO - elimina scripts y estilos innecesarios
     *
     * @since 1.2.0
     * @return void
     */
    private function dequeue_all_assets() {
        // Desencolar estilos
        wp_dequeue_style('wland-chat-block-modal-css');
        wp_dequeue_style('wland-chat-block-screen-css');
        wp_dequeue_style('wland-chat-frontend');

        // Desencolar scripts
        wp_dequeue_script('wland-chat-block-modal-js');
        wp_dequeue_script('wland-chat-block-screen-css');
        wp_dequeue_script('wland-chat-frontend');
    }

    /**
     * Renderizar chat globalmente si está habilitado
     *
     * Muestra el widget en todas las páginas si la opción global está activa
     *
     * @since 1.0.0
     * @return void
     */
    public function render_global_chat() {
        $global_enable = get_option('wland_chat_global_enable', false);
        
        if (!$global_enable) {
            return;
        }
        
        if (!Helpers::should_display_chat()) {
            return;
        }
        
        if ($this->page_has_chat_block()) {
            return; // No renderizar si ya hay un bloque
        }
        
        // Obtener atributos por defecto
        $attributes = array(
            'webhookUrl'      => get_option('wland_chat_webhook_url'),
            'headerTitle'     => get_option('wland_chat_header_title'),
            'headerSubtitle'  => get_option('wland_chat_header_subtitle'),
            'welcomeMessage'  => Helpers::get_welcome_message(),
            'position'        => get_option('wland_chat_position', 'bottom-right'),
            'displayMode'     => get_option('wland_chat_display_mode', 'modal'),
        );
        
        echo self::render_chat_widget($attributes);
    }
    
    /**
     * Verificar si la página actual tiene el bloque de chat
     *
     * @since 1.0.0
     * @return bool True si la página contiene el bloque Gutenberg del chat
     */
    private function page_has_chat_block() {
        global $post;

        if (!$post) {
            return false;
        }

        return has_block('wland/chat-widget', $post);
    }

    /**
     * Renderizar widget de chat
     *
     * Genera el HTML del widget con los atributos proporcionados
     *
     * @since 1.0.0
     * @param array $attributes Atributos del widget
     * @return string HTML del widget
     */
    public static function render_chat_widget($attributes = array()) {
        if (!Helpers::should_display_chat()) {
            return '';
        }
        
        $attributes = Helpers::sanitize_block_attributes($attributes);
        $unique_id = Helpers::generate_unique_id();
        
        extract($attributes);
        
        $webhook_url = $webhookUrl;
        $header_title = $headerTitle;
        $header_subtitle = $headerSubtitle;
        $welcome_message = $welcomeMessage;
        $position = $attributes['position'];
        $display_mode = $attributes['displayMode'];
        
        ob_start();
        
        if ($display_mode === 'fullscreen') {
            include WLAND_CHAT_PLUGIN_DIR . 'templates/screen.php';
        } else {
            include WLAND_CHAT_PLUGIN_DIR . 'templates/modal.php';
        }
        
        return ob_get_clean();
    }

    /**
     * Inyectar colores personalizados en el frontend
     *
     * Genera CSS inline con variables CSS personalizadas para colores del chat
     *
     * @since 1.2.4
     * @return void
     */
    public function inject_custom_colors() {
        // Solo inyectar si el chat debe mostrarse
        if (!Helpers::should_display_chat()) {
            return;
        }

        $global_enable = get_option('wland_chat_global_enable', false);
        $has_block = $this->page_has_chat_block();

        if (!$global_enable && !$has_block) {
            return;
        }

        // Obtener colores personalizados
        $bubble_color = get_option('wland_chat_bubble_color', '#01B7AF');
        $primary_color = get_option('wland_chat_primary_color', '#01B7AF');
        $background_color = get_option('wland_chat_background_color', '#FFFFFF');
        $text_color = get_option('wland_chat_text_color', '#333333');

        // Calcular color secundario (tono más claro del color primario)
        $secondary_color = $this->lighten_color($primary_color, 20);

        ?>
        <style id="wland-chat-custom-colors">
            /* Colores personalizados del chat - Wland Chat iA */

            /* Color de la burbuja flotante (sin gradiente, color sólido) */
            body #braveslab-chat-container #chat-toggle {
                background: <?php echo esc_attr($bubble_color); ?> !important;
                box-shadow: 0 4px 20px <?php echo esc_attr($bubble_color); ?>66 !important;
            }

            body #braveslab-chat-container #chat-toggle:hover {
                box-shadow: 0 6px 25px <?php echo esc_attr($bubble_color); ?>99 !important;
            }

            /* Color del header */
            body #braveslab-chat-container #chat-header {
                background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%) !important;
            }

            /* Color de fondo del chat */
            body #braveslab-chat-container #chat-window {
                background: <?php echo esc_attr($background_color); ?> !important;
            }

            body #braveslab-chat-container #chat-messages {
                background: <?php echo esc_attr($background_color); ?> !important;
            }

            /* Color de mensajes del bot (sin borde izquierdo) */
            body #braveslab-chat-container .message.bot .message-bubble {
                background: <?php echo esc_attr($primary_color); ?>1A !important;
                border-left: none !important;
                color: <?php echo esc_attr($text_color); ?> !important;
            }

            /* Color de mensajes del usuario (sin borde izquierdo) */
            body #braveslab-chat-container .message.user .message-bubble {
                background: <?php echo esc_attr($primary_color); ?> !important;
                color: white !important;
                border-left: none !important;
            }

            /* Caja de escritura - input */
            body #braveslab-chat-container #chat-input {
                color: <?php echo esc_attr($text_color); ?> !important;
                border-color: <?php echo esc_attr($primary_color); ?>40 !important;
            }

            body #braveslab-chat-container #chat-input:focus {
                border-color: <?php echo esc_attr($primary_color); ?> !important;
                outline-color: <?php echo esc_attr($primary_color); ?>40 !important;
            }

            /* Contenedor de input */
            body #braveslab-chat-container #chat-input-container {
                border-top-color: <?php echo esc_attr($primary_color); ?>20 !important;
            }

            /* Botón de enviar */
            body #braveslab-chat-container #send-button {
                background: <?php echo esc_attr($primary_color); ?> !important;
            }

            body #braveslab-chat-container #send-button:hover:not(:disabled) {
                background: <?php echo esc_attr($this->darken_color($primary_color, 10)); ?> !important;
            }

            /* Indicador de escritura */
            body #braveslab-chat-container .typing-indicator .typing-dot {
                background: <?php echo esc_attr($primary_color); ?> !important;
            }

            /* Tiempo de mensajes */
            body #braveslab-chat-container .message-time {
                color: <?php echo esc_attr($text_color); ?>80 !important;
            }
        </style>
        <?php
    }

    /**
     * Aclarar un color hexadecimal
     *
     * @since 1.2.4
     * @param string $hex Color hexadecimal (#RRGGBB)
     * @param int $percent Porcentaje para aclarar (0-100)
     * @return string Color aclarado en formato hexadecimal
     */
    private function lighten_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = min(255, $r + ($percent / 100 * (255 - $r)));
        $g = min(255, $g + ($percent / 100 * (255 - $g)));
        $b = min(255, $b + ($percent / 100 * (255 - $b)));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Oscurecer un color hexadecimal
     *
     * @since 1.2.4
     * @param string $hex Color hexadecimal (#RRGGBB)
     * @param int $percent Porcentaje para oscurecer (0-100)
     * @return string Color oscurecido en formato hexadecimal
     */
    private function darken_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, $r - ($percent / 100 * $r));
        $g = max(0, $g - ($percent / 100 * $g));
        $b = max(0, $b - ($percent / 100 * $b));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}