<?php
/**
 * Gestión de configuración del plugin
 *
 * Maneja el registro y renderizado de opciones del plugin
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
 * Clase Settings
 *
 * Gestiona la configuración y opciones del plugin en WordPress
 *
 * @since 1.0.0
 */
class Settings {

    /**
     * Instancia única (patrón Singleton)
     *
     * @since 1.0.0
     * @var Settings|null
     */
    private static $instance = null;

    /**
     * Prefijo para las opciones de WordPress
     *
     * @since 1.0.0
     * @var string
     */
    private $option_prefix = 'wland_chat_';

    /**
     * Obtener instancia única
     *
     * @since 1.0.0
     * @return Settings Instancia única de la clase
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
        // NOTE: Menu is now handled by Admin_Controller class (v1.2.0)
        // add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_head', array($this, 'add_menu_icon_styles'));
    }

    /**
     * Renderizar campos ocultos para preservar opciones no mostradas
     *
     * @param array $exclude_fields Campos a excluir (los que están visibles en el formulario)
     * @return void
     */
    public function render_hidden_fields($exclude_fields = array()) {
        $all_fields = array(
            'global_enable',
            'webhook_url',
            'n8n_auth_token',
            'header_title',
            'header_subtitle',
            'welcome_message',
            'position',
            'display_mode',
            'chat_icon',
            'bubble_color',
            'primary_color',
            'background_color',
            'text_color',
            'excluded_pages',
            'availability_enabled',
            'availability_start',
            'availability_end',
            'availability_timezone',
            'availability_message',
            'gdpr_enabled',
            'gdpr_message',
            'gdpr_accept_text',
        );

        foreach ($all_fields as $field) {
            if (in_array($field, $exclude_fields)) {
                continue; // Saltar campos visibles
            }

            $option_name = $this->option_prefix . $field;
            $value = get_option($option_name);

            if (is_array($value)) {
                // Para arrays como excluded_pages
                foreach ($value as $item) {
                    echo '<input type="hidden" name="' . esc_attr($option_name) . '[]" value="' . esc_attr($item) . '">';
                }
            } elseif (is_bool($value) || $value === '1' || $value === '0') {
                // Para checkboxes
                echo '<input type="hidden" name="' . esc_attr($option_name) . '" value="' . ($value ? '1' : '0') . '">';
            } else {
                // Para campos de texto normales
                echo '<input type="hidden" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '">';
            }
        }
    }

    /**
     * Agregar menú principal en el admin de WordPress
     *
     * DEPRECATED: Ahora se maneja desde Admin_Controller (v1.2.0)
     *
     * @since 1.0.0
     * @deprecated 1.2.0 Usar Admin_Controller en su lugar
     * @return void
     */
    public function add_settings_page() {
        // Determinar la ruta del icono (SVG o PNG)
        $icon_url = $this->get_menu_icon();

        // Agregar página principal del menú
        add_menu_page(
            __('Wland Chat iA', 'wland-chat'),           // page_title
            __('Wland Chat iA', 'wland-chat'),           // menu_title
            'manage_options',                             // capability
            'wland-chat-ia',                              // menu_slug
            array($this, 'render_settings_page'),         // callback
            $icon_url,                                    // icon_url
            58                                            // position (después de Apariencia=60, antes de Plugins=65)
        );

        // Agregar submenú "Dashboard" (renombra el primer item)
        add_submenu_page(
            'wland-chat-ia',                              // parent_slug
            __('Dashboard', 'wland-chat'),                // page_title
            __('Dashboard', 'wland-chat'),                // menu_title
            'manage_options',                             // capability
            'wland-chat-ia',                              // menu_slug (mismo que parent para ser el primero)
            array($this, 'render_settings_page')          // callback
        );

        // Agregar submenú "Ajustes"
        add_submenu_page(
            'wland-chat-ia',                              // parent_slug
            __('Ajustes', 'wland-chat'),                  // page_title
            __('Ajustes', 'wland-chat'),                  // menu_title
            'manage_options',                             // capability
            'wland-chat-settings',                        // menu_slug
            array($this, 'render_settings_page')          // callback
        );
    }

    /**
     * Obtener URL del icono del menú
     *
     * Busca primero SVG, luego PNG, sino usa dashicon por defecto
     *
     * @since 1.0.0
     * @return string URL del icono o dashicon
     */
    private function get_menu_icon() {
        // Intentar cargar SVG
        $svg_path = WLAND_CHAT_PLUGIN_DIR . 'assets/media/menu-icon.svg';
        if (file_exists($svg_path)) {
            $svg_content = file_get_contents($svg_path);
            // Codificar SVG como data URI
            return 'data:image/svg+xml;base64,' . base64_encode($svg_content);
        }

        // Intentar cargar PNG
        $png_url = WLAND_CHAT_PLUGIN_URL . 'assets/media/menu-icon.png';
        $png_path = WLAND_CHAT_PLUGIN_DIR . 'assets/media/menu-icon.png';
        if (file_exists($png_path)) {
            return $png_url;
        }

        // Fallback a dashicon
        return 'dashicons-format-chat';
    }

    /**
     * Estilos personalizados para el icono del menú
     *
     * Agrega estilos CSS inline para el icono del menú de WordPress
     *
     * @since 1.0.0
     * @return void
     */
    public function add_menu_icon_styles() {
        ?>
        <style>
            /* Estilos del icono del menú Wland Chat iA */
            #toplevel_page_wland-chat-ia .wp-menu-image img {
                width: 20px;
                height: 20px;
                padding: 6px 0;
                opacity: 0.6;
                transition: opacity 0.2s ease;
            }

            #toplevel_page_wland-chat-ia:hover .wp-menu-image img,
            #toplevel_page_wland-chat-ia.current .wp-menu-image img,
            #toplevel_page_wland-chat-ia.wp-has-current-submenu .wp-menu-image img {
                opacity: 1;
            }

            /* Para SVG con currentColor */
            #toplevel_page_wland-chat-ia .wp-menu-image svg {
                width: 20px;
                height: 20px;
                fill: #a7aaad;
                transition: fill 0.2s ease;
            }

            #toplevel_page_wland-chat-ia:hover .wp-menu-image svg {
                fill: #00a0d2;
            }

            /* Cuando el menú está activo (cualquier subpágina) */
            #toplevel_page_wland-chat-ia.wp-has-current-submenu .wp-menu-image svg,
            #toplevel_page_wland-chat-ia.current .wp-menu-image svg {
                fill: #ffffff !important;
            }

            /* Color del badge de notificación (para futuras funcionalidades) */
            #toplevel_page_wland-chat-ia .update-plugins {
                background-color: #00a0d2;
            }
        </style>
        <?php
    }

    /**
     * Registrar todas las opciones del plugin en WordPress
     *
     * Registra secciones y campos de configuración usando Settings API
     *
     * @since 1.0.0
     * @since 1.2.2 Añadido campo token N8N
     * @return void
     */
    public function register_settings() {
        // ==================== SECCIÓN GENERAL ====================
        add_settings_section(
            'wland_chat_general_section',
            __('Configuración General', 'wland-chat'),
            array($this, 'general_section_callback'),
            'wland_chat_settings'
        );
        
        // Global Enable
        register_setting('wland_chat_settings', $this->option_prefix . 'global_enable', array(
            'type' => 'boolean',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => false
        ));
        
        add_settings_field(
            'global_enable',
            __('Mostrar en toda la web', 'wland-chat'),
            array($this, 'global_enable_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        
        // Webhook URL
        register_setting('wland_chat_settings', $this->option_prefix . 'webhook_url', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat'
        ));
        
        add_settings_field(
            'webhook_url',
            __('URL del Webhook', 'wland-chat'),
            array($this, 'webhook_url_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        
        // ========== TAREA 2A: CAMPO TOKEN DE AUTENTICACIÓN N8N ==========
        register_setting('wland_chat_settings', $this->option_prefix . 'n8n_auth_token', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field', // Sanitización automática
            'default' => ''
        ));
        
        add_settings_field(
            'n8n_auth_token',
            __('Token de Autenticación N8N', 'wland-chat'),
            array($this, 'n8n_auth_token_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        // ========== FIN TAREA 2A ==========
        
        // Header Title
        register_setting('wland_chat_settings', $this->option_prefix . 'header_title', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('BravesLab AI Assistant', 'wland-chat')
        ));
        
        add_settings_field(
            'header_title',
            __('Título del Header', 'wland-chat'),
            array($this, 'header_title_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        
        // Header Subtitle
        register_setting('wland_chat_settings', $this->option_prefix . 'header_subtitle', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('Artificial Intelligence Marketing Agency', 'wland-chat')
        ));
        
        add_settings_field(
            'header_subtitle',
            __('Subtítulo del Header', 'wland-chat'),
            array($this, 'header_subtitle_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        
        // Welcome Message
        register_setting('wland_chat_settings', $this->option_prefix . 'welcome_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?', 'wland-chat')
        ));
        
        add_settings_field(
            'welcome_message',
            __('Mensaje de Bienvenida', 'wland-chat'),
            array($this, 'welcome_message_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        
        // Position
        register_setting('wland_chat_settings', $this->option_prefix . 'position', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_position'),
            'default' => 'bottom-right'
        ));
        
        add_settings_field(
            'position',
            __('Posición del Chat', 'wland-chat'),
            array($this, 'position_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );
        
        // Display Mode
        register_setting('wland_chat_settings', $this->option_prefix . 'display_mode', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_display_mode'),
            'default' => 'modal'
        ));

        // Chat Icon
        register_setting('wland_chat_settings', $this->option_prefix . 'chat_icon', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_chat_icon'),
            'default' => 'robot-chat'
        ));

        add_settings_field(
            'display_mode',
            __('Modo de Visualización', 'wland-chat'),
            array($this, 'display_mode_callback'),
            'wland_chat_settings',
            'wland_chat_general_section'
        );

        // Bubble Color
        register_setting('wland_chat_settings', $this->option_prefix . 'bubble_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#01B7AF'
        ));

        // Primary Color
        register_setting('wland_chat_settings', $this->option_prefix . 'primary_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#01B7AF'
        ));

        // Background Color
        register_setting('wland_chat_settings', $this->option_prefix . 'background_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ));

        // Text Color
        register_setting('wland_chat_settings', $this->option_prefix . 'text_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#333333'
        ));
        
        // ==================== SECCIÓN PÁGINAS EXCLUIDAS ====================
        add_settings_section(
            'wland_chat_exclusions_section',
            __('Páginas Excluidas', 'wland-chat'),
            array($this, 'exclusions_section_callback'),
            'wland_chat_settings'
        );
        
        register_setting('wland_chat_settings', $this->option_prefix . 'excluded_pages', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_excluded_pages'),
            'default' => array()
        ));
        
        add_settings_field(
            'excluded_pages',
            __('Páginas donde NO mostrar el chat', 'wland-chat'),
            array($this, 'excluded_pages_callback'),
            'wland_chat_settings',
            'wland_chat_exclusions_section'
        );
        
        // ==================== SECCIÓN DISPONIBILIDAD ====================
        add_settings_section(
            'wland_chat_availability_section',
            __('Horarios de Disponibilidad', 'wland-chat'),
            array($this, 'availability_section_callback'),
            'wland_chat_settings'
        );
        
        // Availability Enabled
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => false
        ));
        
        add_settings_field(
            'availability_enabled',
            __('Habilitar Horarios', 'wland-chat'),
            array($this, 'availability_enabled_callback'),
            'wland_chat_settings',
            'wland_chat_availability_section'
        );
        
        // Availability Start
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_start', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_time'),
            'default' => '09:00'
        ));
        
        add_settings_field(
            'availability_start',
            __('Hora de Inicio', 'wland-chat'),
            array($this, 'availability_start_callback'),
            'wland_chat_settings',
            'wland_chat_availability_section'
        );
        
        // Availability End
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_end', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_time'),
            'default' => '18:00'
        ));
        
        add_settings_field(
            'availability_end',
            __('Hora de Fin', 'wland-chat'),
            array($this, 'availability_end_callback'),
            'wland_chat_settings',
            'wland_chat_availability_section'
        );
        
        // Availability Timezone
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_timezone', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Europe/Madrid'
        ));
        
        add_settings_field(
            'availability_timezone',
            __('Zona Horaria', 'wland-chat'),
            array($this, 'availability_timezone_callback'),
            'wland_chat_settings',
            'wland_chat_availability_section'
        );
        
        // Availability Message
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('Nuestro horario de atención es de 9:00 a 18:00. Déjanos tu mensaje y te responderemos lo antes posible.', 'wland-chat')
        ));
        
        add_settings_field(
            'availability_message',
            __('Mensaje Fuera de Horario', 'wland-chat'),
            array($this, 'availability_message_callback'),
            'wland_chat_settings',
            'wland_chat_availability_section'
        );

        // ==================== SECCIÓN GDPR/COOKIES ====================
        add_settings_section(
            'wland_chat_gdpr_section',
            __('Compliance GDPR / Cookies', 'wland-chat'),
            array($this, 'gdpr_section_callback'),
            'wland_chat_settings'
        );

        // GDPR Enabled
        register_setting('wland_chat_settings', $this->option_prefix . 'gdpr_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => false
        ));

        add_settings_field(
            'gdpr_enabled',
            __('Habilitar Banner GDPR', 'wland-chat'),
            array($this, 'gdpr_enabled_callback'),
            'wland_chat_settings',
            'wland_chat_gdpr_section'
        );

        // GDPR Message
        register_setting('wland_chat_settings', $this->option_prefix . 'gdpr_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('Este sitio utiliza cookies para mejorar tu experiencia y proporcionar un servicio de chat personalizado. Al continuar navegando, aceptas nuestra política de cookies.', 'wland-chat')
        ));

        add_settings_field(
            'gdpr_message',
            __('Mensaje del Banner', 'wland-chat'),
            array($this, 'gdpr_message_callback'),
            'wland_chat_settings',
            'wland_chat_gdpr_section'
        );

        // GDPR Accept Text
        register_setting('wland_chat_settings', $this->option_prefix . 'gdpr_accept_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('Aceptar', 'wland-chat')
        ));

        add_settings_field(
            'gdpr_accept_text',
            __('Texto del Botón de Aceptar', 'wland-chat'),
            array($this, 'gdpr_accept_text_callback'),
            'wland_chat_settings',
            'wland_chat_gdpr_section'
        );
    }
    
    // ==================== CALLBACKS DE SECCIONES ====================

    /**
     * Callback para sección General
     *
     * @since 1.0.0
     * @return void
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure los ajustes generales del chat.', 'wland-chat') . '</p>';
    }

    /**
     * Callback para sección Exclusiones
     *
     * @since 1.0.0
     * @return void
     */
    public function exclusions_section_callback() {
        echo '<p>' . __('Seleccione las páginas donde NO desea mostrar el widget de chat.', 'wland-chat') . '</p>';
    }

    /**
     * Callback para sección Disponibilidad
     *
     * @since 1.0.0
     * @return void
     */
    public function availability_section_callback() {
        echo '<p>' . __('Configure los horarios de disponibilidad del chat.', 'wland-chat') . '</p>';
    }

    /**
     * Callback para sección GDPR
     *
     * @since 1.1.0
     * @return void
     */
    public function gdpr_section_callback() {
        echo '<p>' . __('Configure el banner de consentimiento de cookies para cumplir con las regulaciones GDPR. El sistema utiliza cookies persistentes con fingerprinting del navegador para identificar usuarios únicamente.', 'wland-chat') . '</p>';
    }
    
    // ==================== CALLBACKS DE CAMPOS ====================
    
    public function global_enable_callback() {
        $value = get_option($this->option_prefix . 'global_enable', false);
        printf(
            '<label><input type="checkbox" name="%s" value="1" %s /> %s</label>',
            esc_attr($this->option_prefix . 'global_enable'),
            checked(1, $value, false),
            __('Mostrar el chat en todas las páginas del sitio', 'wland-chat')
        );
    }
    
    public function webhook_url_callback() {
        $value = get_option($this->option_prefix . 'webhook_url');
        printf(
            '<input type="url" name="%s" value="%s" class="regular-text" placeholder="https://flow.braveslab.com/webhook/..." /><p class="description">%s</p>',
            esc_attr($this->option_prefix . 'webhook_url'),
            esc_attr($value),
            __('URL del webhook de N8N para procesar los mensajes.', 'wland-chat')
        );
    }
    
    /**
     * ========== TAREA 2A: CALLBACK PARA TOKEN N8N ==========
     */
    public function n8n_auth_token_callback() {
        $value = get_option($this->option_prefix . 'n8n_auth_token', '');
        ?>
        <input 
            type="password" 
            name="<?php echo esc_attr($this->option_prefix . 'n8n_auth_token'); ?>" 
            value="<?php echo esc_attr($value); ?>" 
            class="regular-text"
            autocomplete="new-password"
            placeholder="••••••••••••••••"
        />
        <p class="description">
            <?php _e('Token secreto para autenticar las peticiones al webhook de N8N (Header Auth X-N8N-Auth). Déjalo vacío si no usas autenticación.', 'wland-chat'); ?>
        </p>
        <p class="description">
            <strong><?php _e('Importante:', 'wland-chat'); ?></strong>
            <?php _e('Este token se enviará en cada petición al webhook para verificar que proviene del plugin.', 'wland-chat'); ?>
        </p>
        <?php
    }
    
    public function header_title_callback() {
        $value = get_option($this->option_prefix . 'header_title');
        printf(
            '<input type="text" name="%s" value="%s" class="regular-text" />',
            esc_attr($this->option_prefix . 'header_title'),
            esc_attr($value)
        );
    }
    
    public function header_subtitle_callback() {
        $value = get_option($this->option_prefix . 'header_subtitle');
        printf(
            '<input type="text" name="%s" value="%s" class="regular-text" />',
            esc_attr($this->option_prefix . 'header_subtitle'),
            esc_attr($value)
        );
    }
    
    public function welcome_message_callback() {
        $value = get_option($this->option_prefix . 'welcome_message');
        printf(
            '<textarea name="%s" rows="4" class="large-text">%s</textarea>',
            esc_attr($this->option_prefix . 'welcome_message'),
            esc_textarea($value)
        );
    }
    
    public function position_callback() {
        $value = get_option($this->option_prefix . 'position');
        $positions = array(
            'bottom-right' => __('Abajo Derecha', 'wland-chat'),
            'bottom-left' => __('Abajo Izquierda', 'wland-chat'),
            'center' => __('Centro', 'wland-chat')
        );
        
        echo '<select name="' . esc_attr($this->option_prefix . 'position') . '">';
        foreach ($positions as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }
    
    public function display_mode_callback() {
        $value = get_option($this->option_prefix . 'display_mode');
        $modes = array(
            'modal' => __('Modal (Ventana emergente)', 'wland-chat'),
            'fullscreen' => __('Pantalla completa', 'wland-chat')
        );
        
        echo '<select name="' . esc_attr($this->option_prefix . 'display_mode') . '">';
        foreach ($modes as $key => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }
    
    public function excluded_pages_callback() {
        $excluded = get_option($this->option_prefix . 'excluded_pages', array());
        $pages = get_pages();
        
        echo '<select name="' . esc_attr($this->option_prefix . 'excluded_pages') . '[]" multiple size="10" style="width: 300px;">';
        foreach ($pages as $page) {
            printf(
                '<option value="%d" %s>%s</option>',
                esc_attr($page->ID),
                in_array($page->ID, (array)$excluded) ? 'selected' : '',
                esc_html($page->post_title)
            );
        }
        echo '</select>';
        echo '<p class="description">' . __('Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples páginas.', 'wland-chat') . '</p>';
    }
    
    public function availability_enabled_callback() {
        $value = get_option($this->option_prefix . 'availability_enabled', false);
        printf(
            '<label><input type="checkbox" name="%s" value="1" %s /> %s</label>',
            esc_attr($this->option_prefix . 'availability_enabled'),
            checked(1, $value, false),
            __('Activar restricción por horarios', 'wland-chat')
        );
    }
    
    public function availability_start_callback() {
        $value = get_option($this->option_prefix . 'availability_start');
        printf(
            '<input type="time" name="%s" value="%s" />',
            esc_attr($this->option_prefix . 'availability_start'),
            esc_attr($value)
        );
    }
    
    public function availability_end_callback() {
        $value = get_option($this->option_prefix . 'availability_end');
        printf(
            '<input type="time" name="%s" value="%s" />',
            esc_attr($this->option_prefix . 'availability_end'),
            esc_attr($value)
        );
    }
    
    public function availability_timezone_callback() {
        $value = get_option($this->option_prefix . 'availability_timezone');
        $timezones = timezone_identifiers_list();
        
        echo '<select name="' . esc_attr($this->option_prefix . 'availability_timezone') . '">';
        foreach ($timezones as $timezone) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($timezone),
                selected($value, $timezone, false),
                esc_html($timezone)
            );
        }
        echo '</select>';
    }
    
    public function availability_message_callback() {
        $value = get_option($this->option_prefix . 'availability_message');
        printf(
            '<textarea name="%s" rows="4" class="large-text">%s</textarea>',
            esc_attr($this->option_prefix . 'availability_message'),
            esc_textarea($value)
        );
    }

    public function gdpr_enabled_callback() {
        $value = get_option($this->option_prefix . 'gdpr_enabled', false);
        printf(
            '<label><input type="checkbox" name="%s" value="1" %s /> %s</label><p class="description">%s</p>',
            esc_attr($this->option_prefix . 'gdpr_enabled'),
            checked(1, $value, false),
            __('Mostrar banner de consentimiento de cookies', 'wland-chat'),
            __('Cuando está habilitado, se mostrará un banner solicitando consentimiento antes de crear cookies. El consentimiento se guarda en localStorage.', 'wland-chat')
        );
    }

    public function gdpr_message_callback() {
        $value = get_option($this->option_prefix . 'gdpr_message');
        printf(
            '<textarea name="%s" rows="4" class="large-text">%s</textarea><p class="description">%s</p>',
            esc_attr($this->option_prefix . 'gdpr_message'),
            esc_textarea($value),
            __('Mensaje que se mostrará en el banner de cookies. Explica el uso de cookies para la sesión del chat.', 'wland-chat')
        );
    }

    public function gdpr_accept_text_callback() {
        $value = get_option($this->option_prefix . 'gdpr_accept_text');
        printf(
            '<input type="text" name="%s" value="%s" class="regular-text" /><p class="description">%s</p>',
            esc_attr($this->option_prefix . 'gdpr_accept_text'),
            esc_attr($value),
            __('Texto del botón para aceptar las cookies (ej: "Aceptar", "Entendido", "Acepto").', 'wland-chat')
        );
    }

    // ==================== FUNCIONES DE SANITIZACIÓN ====================

    /**
     * Sanitizar campo de posición del chat
     *
     * @since 1.0.0
     * @param string $value Valor a sanitizar
     * @return string Valor sanitizado
     */
    public function sanitize_position($value) {
        $allowed = array('bottom-right', 'bottom-left', 'center');
        return in_array($value, $allowed) ? $value : 'bottom-right';
    }

    /**
     * Sanitizar modo de visualización
     *
     * @since 1.0.0
     * @param string $value Valor a sanitizar
     * @return string Valor sanitizado
     */
    public function sanitize_display_mode($value) {
        $allowed = array('modal', 'fullscreen');
        return in_array($value, $allowed) ? $value : 'modal';
    }

    /**
     * Sanitizar icono del chat
     *
     * @since 1.2.3
     * @param string $value Valor a sanitizar
     * @return string Icono sanitizado
     */
    public function sanitize_chat_icon($value) {
        $allowed = array('robot-chat', 'chat-circle', 'chat-happy', 'chat-burbble');
        return in_array($value, $allowed) ? $value : 'robot-chat';
    }

    /**
     * Sanitizar páginas excluidas
     *
     * @since 1.0.0
     * @param array|mixed $value Valor a sanitizar
     * @return array Array de IDs de páginas sanitizados
     */
    public function sanitize_excluded_pages($value) {
        if (!is_array($value)) {
            return array();
        }
        return array_map('absint', $value);
    }

    /**
     * Sanitizar checkbox (convertir a 1 o 0)
     *
     * @since 1.0.0
     * @param mixed $value Valor a sanitizar
     * @return int 1 si está marcado, 0 en caso contrario
     */
    public function sanitize_checkbox($value) {
        return $value == 1 ? 1 : 0;
    }

    /**
     * Sanitizar hora en formato HH:MM
     *
     * @since 1.0.0
     * @param string $value Hora a sanitizar
     * @return string Hora sanitizada en formato HH:MM
     */
    public function sanitize_time($value) {
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return $value;
        }
        return '09:00';
    }
    
    // ==================== RENDERIZADO DE PÁGINA ====================

    /**
     * Renderizar página de ajustes
     *
     * Carga el template de ajustes con diseño Bentō
     *
     * @since 1.0.0
     * @return void
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Cargar template de settings con diseño Bentō
        require_once WLAND_CHAT_PLUGIN_DIR . 'includes/admin/templates/settings.php';
    }

    /**
     * Encolar assets CSS y JS del admin
     *
     * Carga estilos y scripts solo en páginas de Wland Chat
     *
     * @since 1.0.0
     * @param string $hook Identificador de la página actual del admin
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        // Load on both dashboard and settings pages
        if (!in_array($hook, array('toplevel_page_wland-chat-ia', 'wland-chat-ia_page_wland-chat-settings'))) {
            return;
        }

        // Enqueue WordPress components styles
        wp_enqueue_style('wp-components');

        // Enqueue nuevo sistema CSS modular (v1.2.0)
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

        // Enqueue admin settings script (for all plugin pages)
        wp_enqueue_script(
            'wland-admin-settings',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/admin_settings.js',
            array(),
            WLAND_CHAT_VERSION,
            true
        );

        // Enqueue settings-specific styles
        if ('wland-chat-ia_page_wland-chat-settings' === $hook) {
            wp_enqueue_style(
                'wland-admin-settings',
                WLAND_CHAT_PLUGIN_URL . 'assets/css/admin/settings.css',
                array('wland-admin-dashboard'),
                WLAND_CHAT_VERSION
            );
        }

        // Keep existing admin script if needed
        if (file_exists(WLAND_CHAT_PLUGIN_DIR . 'assets/js/admin.js')) {
            wp_enqueue_script(
                'wland-chat-admin',
                WLAND_CHAT_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                WLAND_CHAT_VERSION,
                true
            );
        }
    }
}