<?php
/**
 * Controlador principal del panel de administración
 *
 * Clase base que coordina todos los componentes del admin
 * Diseño Bentō moderno para panel de administración
 *
 * @package WlandChat
 * @version 1.2.0
 */

namespace WlandChat\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Admin_Controller {

    /**
     * Instancia única (Singleton)
     *
     * @var Admin_Controller
     */
    private static $instance = null;

    /**
     * Componentes del admin
     *
     * @var array
     */
    private $components = array();

    /**
     * Obtener instancia única
     *
     * @return Admin_Controller
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
        $this->load_dependencies();
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Cargar archivos de dependencias
     *
     * @return void
     */
    private function load_dependencies() {
        // Componentes principales
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/admin/components/class_admin_header.php';
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/admin/components/class_admin_sidebar.php';
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/admin/components/class_admin_content.php';

        // Templates helper
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/admin/class_template_helpers.php';
    }

    /**
     * Inicializar hooks de WordPress
     *
     * @return void
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_head', array($this, 'add_menu_icon_active_styles'));
    }

    /**
     * Agregar estilos y script para icono de menú activo en páginas sin parent_slug
     *
     * @return void
     */
    public function add_menu_icon_active_styles() {
        $screen = get_current_screen();

        // Solo aplicar en páginas del plugin que NO activan wp-has-current-submenu automáticamente
        $wland_pages = array('admin_page_wland-chat-settings', 'admin_page_wland-chat-appearance', 'admin_page_wland-chat-availability', 'admin_page_wland-chat-gdpr', 'admin_page_wland-chat-about');

        if (!$screen || !in_array($screen->id, $wland_pages)) {
            return;
        }

        ?>
        <script>
            // Agregar la clase wp-has-current-submenu para que WordPress aplique estilos correctos
            document.addEventListener('DOMContentLoaded', function() {
                var menuItem = document.getElementById('toplevel_page_wland-chat-ia');
                if (menuItem) {
                    menuItem.classList.add('wp-has-current-submenu', 'wp-menu-open');
                    menuItem.classList.remove('wp-not-current-submenu');
                }
            });
        </script>
        <?php
    }

    /**
     * Inicializar componentes
     *
     * @return void
     */
    private function init_components() {
        $this->components['header'] = Admin_Header::get_instance();
        $this->components['sidebar'] = Admin_Sidebar::get_instance();
        $this->components['content'] = Admin_Content::get_instance();
    }

    /**
     * Registrar menú de administración
     *
     * @return void
     */
    public function register_admin_menu() {
        // Obtener icono SVG
        $icon_svg = $this->get_menu_icon_svg();

        // Menú principal
        add_menu_page(
            __('Wland Chat iA', 'wland-chat'),
            __('Wland Chat iA', 'wland-chat'),
            'manage_options',
            'wland-chat-ia',
            array($this, 'render_dashboard_page'),
            $icon_svg,
            58
        );

        // Ocultar el submenu por defecto de WordPress
        // Solo se muestra "Wland Chat iA" en el menu principal
        // La navegación se hace por el sidebar personalizado

        // Registrar páginas sin mostrarlas en el submenu
        add_submenu_page(
            null, // parent_slug = null = página oculta en menu
            __('Resumen', 'wland-chat'),
            __('Resumen', 'wland-chat'),
            'manage_options',
            'wland-chat-ia',
            array($this, 'render_dashboard_page')
        );

        add_submenu_page(
            null,
            __('Ajustes', 'wland-chat'),
            __('Ajustes', 'wland-chat'),
            'manage_options',
            'wland-chat-settings',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            null,
            __('Apariencia', 'wland-chat'),
            __('Apariencia', 'wland-chat'),
            'manage_options',
            'wland-chat-appearance',
            array($this, 'render_appearance_page')
        );

        add_submenu_page(
            null,
            __('Horarios', 'wland-chat'),
            __('Horarios', 'wland-chat'),
            'manage_options',
            'wland-chat-availability',
            array($this, 'render_availability_page')
        );

        add_submenu_page(
            null,
            __('GDPR', 'wland-chat'),
            __('GDPR', 'wland-chat'),
            'manage_options',
            'wland-chat-gdpr',
            array($this, 'render_gdpr_page')
        );

        add_submenu_page(
            null,
            __('Acerca de', 'wland-chat'),
            __('Acerca de', 'wland-chat'),
            'manage_options',
            'wland-chat-about',
            array($this, 'render_about_page')
        );
    }

    /**
     * Obtener icono SVG del menú
     *
     * @return string Data URI del SVG o dashicon
     */
    private function get_menu_icon_svg() {
        $svg_path = WLAND_CHAT_PLUGIN_DIR . 'assets/media/menu-icon.svg';

        if (file_exists($svg_path)) {
            $svg_content = file_get_contents($svg_path);
            return 'data:image/svg+xml;base64,' . base64_encode($svg_content);
        }

        // Fallback a dashicon
        return 'dashicons-format-chat';
    }

    /**
     * Renderizar página de Dashboard
     *
     * @return void
     */
    public function render_dashboard_page() {
        $current_page = 'wland-chat-ia';

        // Template del dashboard
        include WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/dashboard.php';
    }

    /**
     * Renderizar página de Settings (Configuración General)
     *
     * @return void
     */
    public function render_settings_page() {
        $current_page = 'wland-chat-settings';
        include WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/settings.php';
    }

    /**
     * Renderizar página de Apariencia
     *
     * @return void
     */
    public function render_appearance_page() {
        $current_page = 'wland-chat-appearance';
        include WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/appearance.php';
    }

    /**
     * Renderizar página de Horarios
     *
     * @return void
     */
    public function render_availability_page() {
        $current_page = 'wland-chat-availability';
        include WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/availability.php';
    }

    /**
     * Renderizar página de GDPR
     *
     * @return void
     */
    public function render_gdpr_page() {
        $current_page = 'wland-chat-gdpr';
        include WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/gdpr.php';
    }

    /**
     * Renderizar página de Acerca de
     *
     * @return void
     */
    public function render_about_page() {
        $current_page = 'wland-chat-about';
        include WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/about.php';
    }

    /**
     * Encolar assets del admin
     *
     * @param string $hook Página actual del admin
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        // Solo cargar en páginas de Wland Chat
        if (!$this->is_wland_admin_page($hook)) {
            return;
        }

        // WordPress Components
        wp_enqueue_style('wp-components');

        // Estilos del admin
        wp_enqueue_style(
            'wland-admin-variables',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/admin/variables.css',
            array(),
            WLAND_CHAT_VERSION
        );

        wp_enqueue_style(
            'wland-admin-base',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/admin/base.css',
            array('wland-admin-variables'),
            WLAND_CHAT_VERSION
        );

        wp_enqueue_style(
            'wland-admin-components',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/admin/components.css',
            array('wland-admin-base'),
            WLAND_CHAT_VERSION
        );

        wp_enqueue_style(
            'wland-admin-dashboard',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/admin/dashboard.css',
            array('wland-admin-components'),
            WLAND_CHAT_VERSION
        );

        wp_enqueue_style(
            'wland-admin-settings',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/admin/settings.css',
            array('wland-admin-dashboard'),
            WLAND_CHAT_VERSION
        );

        // JavaScript del admin settings (para auto-hide de notificaciones)
        wp_enqueue_script(
            'wland-admin-settings',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/admin_settings.js',
            array(),
            WLAND_CHAT_VERSION,
            true
        );

        // Script de selector de iconos y color picker (solo en página de Apariencia)
        if ($hook === 'admin_page_wland-chat-appearance') {
            wp_enqueue_script(
                'wland-icon-selector',
                WLAND_CHAT_PLUGIN_URL . 'assets/js/icon_selector.js',
                array(),
                WLAND_CHAT_VERSION,
                true
            );

            wp_enqueue_script(
                'wland-color-picker',
                WLAND_CHAT_PLUGIN_URL . 'assets/js/color_picker.js',
                array(),
                WLAND_CHAT_VERSION,
                true
            );
        }

        // JavaScript del admin (si existe)
        if (file_exists(WLAND_CHAT_PLUGIN_DIR . 'assets/js/admin/dashboard.js')) {
            wp_enqueue_script(
                'wland-admin-dashboard',
                WLAND_CHAT_PLUGIN_URL . 'assets/js/admin/dashboard.js',
                array('jquery'),
                WLAND_CHAT_VERSION,
                true
            );
        }

        // Localizar datos
        $this->localize_admin_data();
    }

    /**
     * Verificar si estamos en una página del admin de Wland
     *
     * @param string $hook Página actual
     * @return bool
     */
    private function is_wland_admin_page($hook) {
        $wland_pages = array(
            'toplevel_page_wland-chat-ia',
            'admin_page_wland-chat-settings',
            'admin_page_wland-chat-appearance',
            'admin_page_wland-chat-availability',
            'admin_page_wland-chat-gdpr',
            'admin_page_wland-chat-about',
        );

        return in_array($hook, $wland_pages);
    }

    /**
     * Pasar datos a JavaScript
     *
     * @return void
     */
    private function localize_admin_data() {
        $config = array(
            'siteUrl' => esc_url(home_url()),
            'adminUrl' => esc_url(admin_url()),
            'settingsUrl' => esc_url(admin_url('admin.php?page=wland-chat-settings')),
            'dashboardUrl' => esc_url(admin_url('admin.php?page=wland-chat-ia')),
            'customizeUrl' => esc_url(admin_url('customize.php')),
            'pluginVersion' => WLAND_CHAT_VERSION,
            'isConfigured' => !empty(get_option('wland_chat_webhook_url')),
            'nonce' => wp_create_nonce('wland_chat_admin'),
        );

        wp_localize_script('wp-components', 'wlandAdminConfig', $config);
    }

    /**
     * Obtener componente específico
     *
     * @param string $name Nombre del componente
     * @return object|null
     */
    public function get_component($name) {
        return isset($this->components[$name]) ? $this->components[$name] : null;
    }
}

// Inicializar el controlador
Admin_Controller::get_instance();
