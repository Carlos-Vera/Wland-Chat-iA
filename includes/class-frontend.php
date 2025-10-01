<?php
namespace WlandChat;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gestión del frontend
 */
class Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        // NUEVO: Hook para renderizar el chat globalmente
        add_action('wp_footer', array($this, 'render_global_chat'), 100);
    }
    
    /**
     * Encolar assets del frontend
     */
    public function enqueue_assets() {
        // Solo cargar si el chat debe mostrarse
        if (!Helpers::should_display_chat()) {
            return;
        }
        
        // NUEVO: Solo cargar si está habilitado globalmente O si hay un bloque en la página
        $global_enable = get_option('wland_chat_global_enable', false);
        $has_block = $this->page_has_chat_block();
        
        if (!$global_enable && !$has_block) {
            return;
        }
        
        // Lottie
        wp_enqueue_script(
            'lottie-player',
            'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js',
            array(),
            '5.12.2',
            true
        );
        
        $display_mode = get_option('wland_chat_display_mode', 'modal');
        
        // CSS
        wp_enqueue_style(
            'wland-chat-frontend',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/wland-chat-block-' . $display_mode . '.css',
            array(),
            WLAND_CHAT_VERSION
        );
        
        // JS
        wp_enqueue_script(
            'wland-chat-frontend',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/wland-chat-block-' . $display_mode . '.js',
            array('lottie-player'),
            WLAND_CHAT_VERSION,
            true
        );
        
        // Localizar script con la ruta correcta de la animación
        wp_localize_script('wland-chat-frontend', 'wlandChatData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wland_chat_nonce'),
            'animationPath' => WLAND_CHAT_PLUGIN_URL . 'assets/media/chat.json',
            'isAvailable' => !Helpers::is_availability_enabled() || Helpers::is_within_availability_hours(),
        ));
    }
    
    /**
     * NUEVO: Renderizar chat globalmente si está habilitado
     */
    public function render_global_chat() {
        // Solo renderizar si está habilitado globalmente
        $global_enable = get_option('wland_chat_global_enable', false);
        
        if (!$global_enable) {
            return;
        }
        
        // Verificar si el chat debe mostrarse
        if (!Helpers::should_display_chat()) {
            return;
        }
        
        // Verificar que no haya un bloque ya en la página
        if ($this->page_has_chat_block()) {
            return; // Si ya hay un bloque, no renderizar globalmente
        }
        
        // Obtener los atributos por defecto de las opciones
        $attributes = array(
            'webhookUrl' => get_option('wland_chat_webhook_url'),
            'headerTitle' => get_option('wland_chat_header_title'),
            'headerSubtitle' => get_option('wland_chat_header_subtitle'),
            'welcomeMessage' => Helpers::get_welcome_message(),
            'position' => get_option('wland_chat_position', 'bottom-right'),
            'displayMode' => get_option('wland_chat_display_mode', 'modal'),
        );
        
        // Renderizar el widget
        echo self::render_chat_widget($attributes);
    }
    
    /**
     * NUEVO: Verificar si la página actual tiene el bloque de chat
     */
    private function page_has_chat_block() {
        global $post;
        
        if (!$post) {
            return false;
        }
        
        // Verificar si el contenido tiene el bloque de chat
        if (has_block('wland/chat-widget', $post)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Renderizar widget de chat
     */
    public static function render_chat_widget($attributes = array()) {
        // Verificar si debe mostrarse
        if (!Helpers::should_display_chat()) {
            return '';
        }
        
        // Sanitizar y mezclar con valores por defecto
        $attributes = Helpers::sanitize_block_attributes($attributes);
        
        // Generar ID único
        $unique_id = Helpers::generate_unique_id();
        
        // Extraer variables
        extract($attributes);
        
        $webhook_url = $webhookUrl;
        $header_title = $headerTitle;
        $header_subtitle = $headerSubtitle;
        $welcome_message = $welcomeMessage;
        $position = $attributes['position'];
        $display_mode = $attributes['displayMode'];
        
        // Buffer de salida
        ob_start();
        
        // Cargar plantilla correspondiente
        if ($display_mode === 'fullscreen') {
            include WLAND_CHAT_PLUGIN_DIR . 'templates/screen.php';
        } else {
            include WLAND_CHAT_PLUGIN_DIR . 'templates/modal.php';
        }
        
        return ob_get_clean();
    }
}