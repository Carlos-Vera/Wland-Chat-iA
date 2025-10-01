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
    }
    
    /**
     * Encolar assets del frontend
     */
    public function enqueue_assets() {
        // Solo cargar si el chat debe mostrarse
        if (!Helpers::should_display_chat()) {
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
        
        // Localizar script
        wp_localize_script('wland-chat-frontend', 'wlandChatData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wland_chat_nonce'),
            'animationPath' => WLAND_CHAT_PLUGIN_URL . 'assets/media/chat.json',
            'isAvailable' => !Helpers::is_availability_enabled() || Helpers::is_within_availability_hours(),
        ));
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