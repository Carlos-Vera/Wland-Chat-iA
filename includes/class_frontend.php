<?php
/**
 * Gestión del frontend
 *
 * Maneja carga de assets y renderizado del chat en el frontend
 *
 * @package BravesChat
 * @since 1.0.0
 * @version 1.2.5
 */

namespace BravesChat;

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
        add_action('wp_enqueue_scripts', array($this, 'inject_custom_colors'), 20);
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
        $global_enable = get_option('braves_chat_global_enable', false);
        $has_block = $this->page_has_chat_block();

        if (!$global_enable && !$has_block) {
            // Desencolar assets si no cumple las condiciones
            $this->dequeue_all_assets();
            return;
        }

        // El modo pantalla completa solo funciona vía bloque de Gutenberg:
        // no se carga en páginas que no lo tengan aunque global_enable esté activo.
        // El modo mixto sí carga assets en páginas sin bloque (muestra burbuja flotante).
        $display_mode_setting = get_option('braves_chat_display_mode', 'modal');
        if ($display_mode_setting === 'fullscreen' && !$has_block) {
            $this->dequeue_all_assets();
            return;
        }
        
        // Si pasa las verificaciones, encolar los assets

        // Sistema de fingerprinting y cookies (cargar primero, sin dependencias)
        wp_enqueue_script(
            'braves-chat-fingerprint',
            BRAVES_CHAT_PLUGIN_URL . 'assets/js/braves_fingerprint.js',
            array(),
            BRAVES_CHAT_VERSION,
            false // Cargar en head para que esté disponible inmediatamente
        );

        // Pasar configuración GDPR al script de fingerprinting
        wp_localize_script('braves-chat-fingerprint', 'bravesGDPRConfig', array(
            'enabled' => (bool) get_option('braves_chat_gdpr_enabled', false),
            'message' => get_option('braves_chat_gdpr_message', __('This site uses cookies to improve your experience and provide a personalized chat service. By continuing to browse, you accept our cookie policy.', 'braveschat')),
            'accept_text' => get_option('braves_chat_gdpr_accept_text', __('Accept', 'braveschat')),
            'cookie_name' => 'braves_chat_session',
            'cookie_duration' => YEAR_IN_SECONDS
        ));

        // CSS del banner GDPR
        wp_enqueue_style(
            'braves-chat-gdpr-banner',
            BRAVES_CHAT_PLUGIN_URL . 'assets/css/braves_gdpr_banner.css',
            array(),
            BRAVES_CHAT_VERSION
        );

        // Fuente Montserrat (Local)
        wp_enqueue_style(
            'braves-chat-fonts',
            BRAVES_CHAT_PLUGIN_URL . 'assets/css/braves_fonts.css',
            array(),
            BRAVES_CHAT_VERSION
        );

        // Si hay un bloque en la página, siempre carga assets de screen (el bloque es siempre fullscreen).
        // Si no hay bloque, usa el modo configurado globalmente.
        // En modo mixto sin bloque → modal (burbuja flotante).
        if ($has_block) {
            $asset_mode = 'screen';
        } else {
            $display_mode = get_option('braves_chat_display_mode', 'modal');
            if ($display_mode === 'fullscreen') {
                $asset_mode = 'screen';
            } elseif ($display_mode === 'mixed') {
                $asset_mode = 'modal';
            } else {
                $asset_mode = $display_mode;
            }
        }

        // CSS condicional según modo de visualización
        wp_enqueue_style(
            'braves-chat-frontend',
            BRAVES_CHAT_PLUGIN_URL . 'assets/css/braves_chat_block_' . $asset_mode . '.css',
            array(),
            BRAVES_CHAT_VERSION
        );

        // CSS del Skin seleccionado
        $chat_config = Helpers::get_chat_config();
        if ($chat_config['skin'] !== 'default' && $asset_mode !== 'screen') {
            wp_enqueue_style(
                'braves-chat-skin-' . $chat_config['skin'],
                BRAVES_CHAT_PLUGIN_URL . 'assets/css/skins/' . $chat_config['skin'] . '.css',
                array('braves-chat-frontend'),
                BRAVES_CHAT_VERSION
            );
        }

        // RedirectHandler - debe cargarse antes de los scripts del chat
        wp_enqueue_script(
            'braves-chat-redirect-handler',
            BRAVES_CHAT_PLUGIN_URL . 'assets/js/redirect_handler.js',
            array(),
            BRAVES_CHAT_VERSION,
            true
        );

        // JS condicional según modo de visualización (depende de redirect_handler)
        wp_enqueue_script(
            'braves-chat-frontend',
            BRAVES_CHAT_PLUGIN_URL . 'assets/js/braves_chat_block_' . $asset_mode . '.js',
            array('wp-i18n', 'braves-chat-redirect-handler'),
            BRAVES_CHAT_VERSION,
            true
        );

        // TAREA 2B: Localizar script con configuración completa incluyendo token de autenticación
        $chat_config = Helpers::get_chat_config();

        wp_localize_script('braves-chat-frontend', 'BravesChatConfig', array(
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('braves_chat_nonce'),
            'isAvailable'  => $chat_config['is_available'],
            'skin'         => $chat_config['skin'],
            'bubbleImage'  => $chat_config['bubble_image'],
            'typing_speed' => (int) get_option('braves_chat_typing_speed', 30),
        ));

        // Configurar traducciones para JavaScript
        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations(
                'braves-chat-frontend',
                'braveschat',
                BRAVES_CHAT_PLUGIN_DIR . 'languages'
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
        wp_dequeue_style('braves-chat-block-modal-css');
        wp_dequeue_style('braves-chat-block-screen-css');
        wp_dequeue_style('braves-chat-frontend');

        // Desencolar scripts
        wp_dequeue_script('braves-chat-block-modal-js');
        wp_dequeue_script('braves-chat-block-screen-css');
        wp_dequeue_script('braves-chat-frontend');
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
        $global_enable = get_option('braves_chat_global_enable', false);

        if (!$global_enable) {
            return;
        }

        // El modo pantalla completa solo se activa vía bloque de Gutenberg,
        // nunca de forma global (cada página con bloque lo renderiza por sí sola).
        // El modo mixto sí renderiza la burbuja globalmente; en páginas con bloque
        // la burbuja se omite por el check de page_has_chat_block() más abajo.
        $display_mode_setting = get_option('braves_chat_display_mode', 'modal');
        if ($display_mode_setting === 'fullscreen') {
            return;
        }
        
        if (!Helpers::should_display_chat()) {
            return;
        }
        
        if ($this->page_has_chat_block()) {
            return; // No renderizar si ya hay un bloque
        }
        
        // Obtener atributos por defecto
        // En modo mixto, el render global siempre es burbuja flotante (modal).
        $raw_display_mode = get_option('braves_chat_display_mode', 'modal');
        $effective_display_mode = ($raw_display_mode === 'mixed') ? 'modal' : $raw_display_mode;

        $attributes = array(
            'webhookUrl'      => get_option('braves_chat_webhook_url'),
            'headerTitle'     => get_option('braves_chat_header_title'),
            'headerSubtitle'  => get_option('braves_chat_header_subtitle'),
            'welcomeMessage'  => Helpers::get_welcome_message(),
            'position'        => get_option('braves_chat_position', 'bottom-right'),
            'displayMode'     => $effective_display_mode,
            'chatSkin'        => get_option('braves_chat_chat_skin', 'default'),
            'bubbleImage'     => get_option('braves_chat_bubble_image', ''),
            'bubbleText'      => get_option('braves_chat_bubble_text', __('Voice chat', 'braveschat')),
        );
        
        echo self::render_chat_widget($attributes); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_chat_widget() builds safe internal HTML.
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

        return has_block('braves/chat-widget', $post);
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
        
        $webhook_url = $attributes['webhookUrl'];
        $header_title = $attributes['headerTitle'];
        $header_subtitle = $attributes['headerSubtitle'];
        $welcome_message = $attributes['welcomeMessage'];
        $position = $attributes['position'];
        $display_mode = $attributes['displayMode'];
        $chat_skin = isset($attributes['chatSkin']) ? $attributes['chatSkin'] : 'default';
        $bubble_image = isset($attributes['bubbleImage']) ? $attributes['bubbleImage'] : '';
        $bubble_text = isset($attributes['bubbleText']) ? $attributes['bubbleText'] : __('Voice chat', 'braveschat');
        
        // Avatar URL - usar bubble_image por defecto para el avatar del header
        $avatar_url = $bubble_image;

        
        ob_start();
        
        if ($display_mode === 'fullscreen') {
            include BRAVES_CHAT_PLUGIN_DIR . 'templates/screen.php';
        } else {
            include BRAVES_CHAT_PLUGIN_DIR . 'templates/modal.php';
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

        $global_enable = get_option('braves_chat_global_enable', false);
        $has_block = $this->page_has_chat_block();

        if (!$global_enable && !$has_block) {
            return;
        }

        // No inyectar colores en páginas sin bloque cuando el modo es fullscreen
        $display_mode_setting = get_option('braves_chat_display_mode', 'modal');
        if ($display_mode_setting === 'fullscreen' && !$has_block) {
            return;
        }

        // Obtener colores personalizados
        $bubble_color = get_option('braves_chat_bubble_color', '#01B7AF');
        $icon_color = get_option('braves_chat_icon_color', '#ffffff');
        $primary_color = get_option('braves_chat_primary_color', '#01B7AF');
        $background_color = get_option('braves_chat_background_color', '#FFFFFF');
        $text_color = get_option('braves_chat_text_color', '#333333');

        // Calcular color secundario (tono más claro del color primario)
        $secondary_color = $this->lighten_color($primary_color, 20);

        ob_start();
        ?>
            /* Colores personalizados del chat - BravesChat */
            :root {
                --braves-primary: <?php echo esc_attr($primary_color); ?>;
            }

            /* Color de la burbuja flotante (sin gradiente, color sólido) */
            body #braveslab-chat-container #chat-toggle {
                background: <?php echo esc_attr($bubble_color); ?> !important;
                box-shadow: 0 4px 20px <?php echo esc_attr($bubble_color); ?>66 !important;
            }

            body #braveslab-chat-container #chat-toggle:hover {
                box-shadow: 0 6px 25px <?php echo esc_attr($bubble_color); ?>99 !important;
            }

            /* Color del icono SVG */
            body #braveslab-chat-container #chat-toggle #chat-icon {
                filter: brightness(0) invert(1);
            }

            body #braveslab-chat-container #chat-toggle #chat-icon g {
                fill: <?php echo esc_attr($icon_color); ?> !important;
            }

            <?php 
            // Obtener configuración para verificar el skin
            $chat_config = Helpers::get_chat_config();
            ?>
            /* Color del header */
            <?php if ($chat_config['skin'] === 'braves') : ?>
            body .braves-skin-braves #chat-header {
                background: transparent !important;
            }
            body .braves-skin-braves #chat-header .chat-header-badge {
                background: transparent !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
            }
            body .braves-skin-braves #chat-header .chat-header-info {
                background: <?php echo esc_attr($primary_color); ?> !important;
                border-radius: 50px !important;
                padding: 6px 16px !important;
                display: flex !important;
                align-items: center !important;
            }
            body .braves-skin-braves #chat-header .chat-status-text {
                color: #ffffff !important;
                font-weight: 500 !important;
                font-size: 14px !important;
                line-height: 1 !important;
            }
            /* Asegurar que el avatar tenga el tamaño correcto dentro del pill */
            body .braves-skin-braves #chat-header .chat-header-badge img.chat-avatar,
            body .braves-skin-braves #chat-header .chat-header-badge .chat-avatar {
                width: 32px !important;
                height: 32px !important;
                margin: 0 !important;
            }
            <?php else : ?>
            body #braveslab-chat-container #chat-header {
                background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%) !important;
            }
            <?php endif; ?>

            <?php 
            // Modo display para el widget global o bloques (en el plugin usa modo o modal/fullscreen)
            $display_mode_global = get_option('braves_chat_display_mode', 'modal');
            ?>
            <?php if ($display_mode_global !== 'fullscreen' && !$has_block) : ?>
            /* Color de fondo del chat */
            body #braveslab-chat-container #chat-window {
                background: <?php echo esc_attr($background_color); ?> !important;
            }

            body #braveslab-chat-container #chat-messages {
                background: <?php echo esc_attr($background_color); ?> !important;
            }
            <?php endif; ?>

            <?php if ($display_mode_global !== 'fullscreen' && !$has_block) : ?>
            /* Color de mensajes del bot (sin borde izquierdo) */
            body #braveslab-chat-container .message.bot .message-bubble {
                background: transparent !important;
                border-left: none !important;
                color: <?php echo esc_attr($text_color); ?> !important;
            }
            <?php else : ?>
            /* Color de mensajes del bot (sin borde izquierdo) */
            body #braveslab-chat-container .message.bot .message-bubble {
                background: transparent !important;
                border-left: none !important;
            }
            <?php endif; ?>

            <?php if ($display_mode_global !== 'fullscreen' && !$has_block) : ?>
            /* Color de mensajes del usuario (sin borde izquierdo) */
            body #braveslab-chat-container .message.user .message-bubble {
                background: <?php echo esc_attr($primary_color); ?> !important;
                color: white !important;
                border-left: none !important;
            }
            <?php endif; ?>

            /* Caja de escritura - input */
            <?php if ($display_mode_global !== 'fullscreen' && !$has_block) : ?>
            body #braveslab-chat-container #chat-input {
                color: <?php echo esc_attr($text_color); ?> !important;
                border-color: <?php echo esc_attr($primary_color); ?>40 !important;
            }
            <?php else : ?>
            body #braveslab-chat-container #chat-input {
                border-color: <?php echo esc_attr($primary_color); ?>40 !important;
            }
            <?php endif; ?>

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
        <?php
        $css = ob_get_clean();
        wp_add_inline_style( 'braves-chat-frontend', $css );
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

        return '#' . str_pad(dechex((int)$r), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex((int)$g), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex((int)$b), 2, '0', STR_PAD_LEFT);
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

        return '#' . str_pad(dechex((int)$r), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex((int)$g), 2, '0', STR_PAD_LEFT)
                  . str_pad(dechex((int)$b), 2, '0', STR_PAD_LEFT);
    }
}