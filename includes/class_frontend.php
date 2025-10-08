<?php
/**
 * Gestión del frontend
 * 
 * @package WlandChat
 * @version 1.0.0
 * MODIFICADO: Implementadas mejoras WPO y Seguridad N8N
 */

namespace WlandChat;

if (!defined('ABSPATH')) {
    exit;
}

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
        add_action('wp_footer', array($this, 'render_global_chat'), 100);
    }
    
    /**
     * Encolar assets del frontend
     * 
     * ========== TAREA 1: CARGA CONDICIONAL (WPO) ==========
     * ========== TAREA 2B: PASAR TOKEN A JAVASCRIPT ==========
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
        
        // Lottie Player
        wp_enqueue_script(
            'lottie-player',
            'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js',
            array(),
            '5.12.2',
            true
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
            array('lottie-player'),
            WLAND_CHAT_VERSION,
            true
        );
        
        // TAREA 2B: Localizar script con configuración completa incluyendo token de autenticación
        $chat_config = Helpers::get_chat_config();
        
        wp_localize_script('wland-chat-frontend', 'WlandChatConfig', array(
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('wland_chat_nonce'),
            'webhook_url'   => $chat_config['webhook_url'],
            'auth_token'    => get_option('wland_chat_n8n_auth_token', ''), // NUEVO: Token N8N
            'animationPath' => WLAND_CHAT_PLUGIN_URL . 'assets/media/chat.json',
            'isAvailable'   => $chat_config['is_available'],
        ));
    }
    
    /**
     * TAREA 1: Desencolar todos los assets del chat
     * Función auxiliar para mejorar la legibilidad
     */
    private function dequeue_all_assets() {
        // Desencolar estilos
        wp_dequeue_style('wland-chat-block-modal-css');
        wp_dequeue_style('wland-chat-block-screen-css');
        wp_dequeue_style('wland-chat-frontend');

        // Desencolar scripts
        wp_dequeue_script('wland-chat-block-modal-js');
        wp_dequeue_script('wland-chat-block-screen-js');
        wp_dequeue_script('wland-chat-frontend');
        wp_dequeue_script('lottie-player');
    }
    
    /**
     * Renderizar chat globalmente si está habilitado
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
}