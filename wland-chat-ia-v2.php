<?php
/**
 * Plugin Name: Wland Chat iA
 * Plugin URI: https://github.com/Carlos-Vera/Wland-Chat-Block-iA
 * Description: Integración profesional de chat con IA mediante bloque Gutenberg, con horarios personalizables y páginas excluidas. Desarrollado por BravesLab para Weblandia.es
 * Version: 1.0.0
 * Author: Carlos Vera, Mikel Marqués
 * Author URI: https://weblandia.es
 * Text Domain: wland-chat
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: Commercial
 * License URI: LICENSE
 * 
 * GitHub Plugin URI: Carlos-Vera/Wland-Chat-Block-iA
 * GitHub Branch: main
 */

namespace WlandChat;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('WLAND_CHAT_VERSION', '1.0.0');
define('WLAND_CHAT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WLAND_CHAT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WLAND_CHAT_PLUGIN_FILE', __FILE__);
define('WLAND_CHAT_TEXT_DOMAIN', 'wland-chat');

/**
 * Clase principal del plugin
 */
class WlandChatIA {
    
    /**
     * Instancia única del plugin
     */
    private static $instance = null;
    
    /**
     * Obtener instancia única
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor privado para patrón Singleton
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Cargar dependencias
     */
    private function load_dependencies() {
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class-helpers.php';
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class-settings.php';
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class-customizer.php';
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class-block.php';
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class-frontend.php';
    }
    
    /**
     * Inicializar hooks
     */
    private function init_hooks() {
        // Activación y desactivación
        register_activation_hook(WLAND_CHAT_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(WLAND_CHAT_PLUGIN_FILE, array($this, 'deactivate'));
        
        // Cargar traducciones
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Inicializar componentes
        add_action('plugins_loaded', array($this, 'init_components'));
        
        // Agregar enlaces en la página de plugins
        add_filter('plugin_action_links_' . plugin_basename(WLAND_CHAT_PLUGIN_FILE), array($this, 'add_action_links'));
    }
    
    /**
     * Activación del plugin
     */
    public function activate() {
        // Establecer opciones por defecto
        $defaults = array(
            'webhook_url' => 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat',
            'header_title' => __('BravesLab AI Assistant', 'wland-chat'),
            'header_subtitle' => __('Artificial Intelligence Marketing Agency', 'wland-chat'),
            'welcome_message' => __('¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?', 'wland-chat'),
            'position' => 'bottom-right',
            'excluded_pages' => array(),
            'availability_enabled' => false,
            'availability_start' => '09:00',
            'availability_end' => '18:00',
            'availability_timezone' => 'Europe/Madrid',
            'availability_message' => __('Nuestro horario de atención es de 9:00 a 18:00. Déjanos tu mensaje y te responderemos lo antes posible.', 'wland-chat'),
            'display_mode' => 'modal',
        );
        
        foreach ($defaults as $key => $value) {
            if (false === get_option('wland_chat_' . $key)) {
                add_option('wland_chat_' . $key, $value);
            }
        }
        
        // Crear directorios necesarios
        $upload_dir = wp_upload_dir();
        $wland_dir = $upload_dir['basedir'] . '/wland-chat';
        if (!file_exists($wland_dir)) {
            wp_mkdir_p($wland_dir);
        }
        
        // Guardar versión
        update_option('wland_chat_version', WLAND_CHAT_VERSION);
        
        flush_rewrite_rules();
    }
    
    /**
     * Desactivación del plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Cargar traducciones
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'wland-chat',
            false,
            dirname(plugin_basename(WLAND_CHAT_PLUGIN_FILE)) . '/languages'
        );
    }
    
    /**
     * Inicializar componentes
     */
    public function init_components() {
        Settings::get_instance();
        Customizer::get_instance();
        Block::get_instance();
        Frontend::get_instance();
    }
    
    /**
     * Agregar enlaces de acción
     */
    public function add_action_links($links) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url('options-general.php?page=wland-chat-settings'),
            __('Ajustes', 'wland-chat')
        );
        
        array_unshift($links, $settings_link);
        
        return $links;
    }
}

/**
 * Inicializar el plugin
 */
function wland_chat_init() {
    return WlandChatIA::get_instance();
}

// Iniciar el plugin
wland_chat_init();