<?php
/**
 * Gestión del bloque de Gutenberg
 *
 * Maneja el registro y renderizado del bloque de chat para Gutenberg
 *
 * @package BravesChat
 * @since 1.0.0
 * @version 1.0.0
 */

namespace BravesChat;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Block
 *
 * Gestiona el bloque de Gutenberg del chat
 *
 * @since 1.0.0
 */
class Block {

    /**
     * Instancia única (patrón Singleton)
     *
     * @since 1.0.0
     * @var Block|null
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @since 1.0.0
     * @return Block Instancia única de la clase
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
     * @since 1.0.0
     */
    private function __construct() {
        add_action('init', array($this, 'register_block'));
    }

    /**
     * Registrar bloque de Gutenberg
     *
     * Registra los assets del bloque y el bloque 'braves/chat-widget'
     * a partir de block.json (apiVersion 3)
     *
     * @since 1.0.0
     * @return void
     */
    public function register_block() {
        // Registrar assets antes del bloque: block.json los referencia por handle
        wp_register_script(
            'braves-chat-block-editor',
            BRAVES_CHAT_PLUGIN_URL . 'assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'),
            BRAVES_CHAT_VERSION,
            true
        );

        // Localizar datos para el bloque
        wp_localize_script('braves-chat-block-editor', 'bravesChatBlock', array(
            'defaultWelcomeMessage' => Helpers::get_welcome_message(),
        ));

        wp_register_style(
            'braves-chat-block-editor-style',
            BRAVES_CHAT_PLUGIN_URL . 'assets/css/block_editor.css',
            array(),
            BRAVES_CHAT_VERSION
        );

        wp_register_style(
            'braves-chat-block-style',
            BRAVES_CHAT_PLUGIN_URL . 'assets/css/block_style.css',
            array(),
            BRAVES_CHAT_VERSION
        );

        register_block_type(BRAVES_CHAT_PLUGIN_DIR . 'block.json', array(
            'render_callback' => array($this, 'render_block'),
        ));
    }
    
    /**
     * Renderizar bloque en el frontend
     *
     * Callback para renderizar el HTML del bloque en la página
     *
     * @since 1.0.0
     * @param array $attributes Atributos del bloque
     * @return string HTML del bloque
     */
    public function render_block($attributes) {
        // Verificar si debe mostrarse el chat
        if (!Helpers::should_display_chat()) {
            return '';
        }
        
        // Usar configuración global del plugin. Si el bloque tiene mensaje de
        // bienvenida propio, lo sobreescribe; el resto siempre viene del panel.
        $global = Helpers::sanitize_block_attributes(array());

        $webhook_url    = $global['webhookUrl'];
        $header_title   = $global['headerTitle'];
        $header_subtitle = $global['headerSubtitle'];
        $position       = $global['position'];
        $chat_skin      = $global['chatSkin'];
        $bubble_image   = $global['bubbleImage'];
        $bubble_text    = $global['bubbleText'];
        $avatar_url     = $bubble_image; // Imagen del avatar en los mensajes del bot

        // Mensaje de bienvenida: del bloque si está definido, si no el global
        $block_message = isset($attributes['welcomeMessage']) ? trim($attributes['welcomeMessage']) : '';
        $welcome_message = $block_message !== '' ? sanitize_textarea_field($block_message) : $global['welcomeMessage'];

        // El bloque siempre renderiza en modo pantalla completa
        $display_mode = 'fullscreen';

        // Generar ID único
        $unique_id = Helpers::generate_unique_id();

        // Forzar el CSS de pantalla completa si enqueue_assets() cargó el modal por error
        wp_enqueue_style(
            'braves-chat-frontend',
            BRAVES_CHAT_PLUGIN_URL . 'assets/css/braves_chat_block_screen.css',
            array(),
            BRAVES_CHAT_VERSION
        );

        ob_start();
        include BRAVES_CHAT_PLUGIN_DIR . 'templates/screen.php';
        return ob_get_clean();
    }
}