<?php
namespace WlandChat;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase para gestionar la configuración del plugin
 */
class Settings {
    
    private static $instance = null;
    private $option_prefix = 'wland_chat_';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Agregar página de administración
     */
    public function add_admin_menu() {
        add_options_page(
            __('Wland Chat iA', 'wland-chat'),
            __('Wland Chat iA', 'wland-chat'),
            'manage_options',
            'wland-chat-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Registrar configuraciones
     */
    public function register_settings() {
        // Sección General
        add_settings_section(
            'wland_chat_general_section',
            __('Configuración General', 'wland-chat'),
            array($this, 'general_section_callback'),
            'wland-chat-settings'
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
            'wland-chat-settings',
            'wland_chat_general_section'
        );
        
        // Título del header
        register_setting('wland_chat_settings', $this->option_prefix . 'header_title', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('BravesLab AI Assistant', 'wland-chat')
        ));
        
        add_settings_field(
            'header_title',
            __('Título del Header', 'wland-chat'),
            array($this, 'header_title_callback'),
            'wland-chat-settings',
            'wland_chat_general_section'
        );
        
        // Subtítulo del header
        register_setting('wland_chat_settings', $this->option_prefix . 'header_subtitle', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('Artificial Intelligence Marketing Agency', 'wland-chat')
        ));
        
        add_settings_field(
            'header_subtitle',
            __('Subtítulo del Header', 'wland-chat'),
            array($this, 'header_subtitle_callback'),
            'wland-chat-settings',
            'wland_chat_general_section'
        );
        
        // Mensaje de bienvenida
        register_setting('wland_chat_settings', $this->option_prefix . 'welcome_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?', 'wland-chat')
        ));
        
        add_settings_field(
            'welcome_message',
            __('Mensaje de Bienvenida', 'wland-chat'),
            array($this, 'welcome_message_callback'),
            'wland-chat-settings',
            'wland_chat_general_section'
        );
        
        // Posición
        register_setting('wland_chat_settings', $this->option_prefix . 'position', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_position'),
            'default' => 'bottom-right'
        ));
        
        add_settings_field(
            'position',
            __('Posición del Chat', 'wland-chat'),
            array($this, 'position_callback'),
            'wland-chat-settings',
            'wland_chat_general_section'
        );
        
        // Modo de visualización
        register_setting('wland_chat_settings', $this->option_prefix . 'display_mode', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_display_mode'),
            'default' => 'modal'
        ));
        
        add_settings_field(
            'display_mode',
            __('Modo de Visualización', 'wland-chat'),
            array($this, 'display_mode_callback'),
            'wland-chat-settings',
            'wland_chat_general_section'
        );
        
        // Sección de Páginas Excluidas
        add_settings_section(
            'wland_chat_exclusions_section',
            __('Páginas Excluidas', 'wland-chat'),
            array($this, 'exclusions_section_callback'),
            'wland-chat-settings'
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
            'wland-chat-settings',
            'wland_chat_exclusions_section'
        );
        
        // Sección de Disponibilidad
        add_settings_section(
            'wland_chat_availability_section',
            __('Horarios de Disponibilidad', 'wland-chat'),
            array($this, 'availability_section_callback'),
            'wland-chat-settings'
        );
        
        // Habilitar horarios
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => false
        ));
        
        add_settings_field(
            'availability_enabled',
            __('Habilitar Horarios', 'wland-chat'),
            array($this, 'availability_enabled_callback'),
            'wland-chat-settings',
            'wland_chat_availability_section'
        );
        
        // Hora de inicio
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_start', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_time'),
            'default' => '09:00'
        ));
        
        add_settings_field(
            'availability_start',
            __('Hora de Inicio', 'wland-chat'),
            array($this, 'availability_start_callback'),
            'wland-chat-settings',
            'wland_chat_availability_section'
        );
        
        // Hora de fin
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_end', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_time'),
            'default' => '18:00'
        ));
        
        add_settings_field(
            'availability_end',
            __('Hora de Fin', 'wland-chat'),
            array($this, 'availability_end_callback'),
            'wland-chat-settings',
            'wland_chat_availability_section'
        );
        
        // Zona horaria
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_timezone', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Europe/Madrid'
        ));
        
        add_settings_field(
            'availability_timezone',
            __('Zona Horaria', 'wland-chat'),
            array($this, 'availability_timezone_callback'),
            'wland-chat-settings',
            'wland_chat_availability_section'
        );
        
        // Mensaje fuera de horario
        register_setting('wland_chat_settings', $this->option_prefix . 'availability_message', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('Nuestro horario de atención es de 9:00 a 18:00. Déjanos tu mensaje y te responderemos lo antes posible.', 'wland-chat')
        ));
        
        add_settings_field(
            'availability_message',
            __('Mensaje Fuera de Horario', 'wland-chat'),
            array($this, 'availability_message_callback'),
            'wland-chat-settings',
            'wland_chat_availability_section'
        );
    }
    
    /**
     * Callbacks de secciones
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure los ajustes generales del chat.', 'wland-chat') . '</p>';
    }
    
    public function exclusions_section_callback() {
        echo '<p>' . __('Seleccione las páginas donde NO desea mostrar el widget de chat.', 'wland-chat') . '</p>';
    }
    
    public function availability_section_callback() {
        echo '<p>' . __('Configure los horarios de disponibilidad del chat.', 'wland-chat') . '</p>';
    }
    
    /**
     * Callbacks de campos
     */
    public function webhook_url_callback() {
        $value = get_option($this->option_prefix . 'webhook_url');
        printf(
            '<input type="url" name="%s" value="%s" class="regular-text" required />',
            esc_attr($this->option_prefix . 'webhook_url'),
            esc_attr($value)
        );
        echo '<p class="description">' . __('URL del webhook de N8N para procesar los mensajes.', 'wland-chat') . '</p>';
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
        $value = get_option($this->option_prefix . 'availability_enabled');
        printf(
            '<input type="checkbox" name="%s" value="1" %s />',
            esc_attr($this->option_prefix . 'availability_enabled'),
            checked($value, 1, false)
        );
        echo ' <label>' . __('Activar restricción por horarios', 'wland-chat') . '</label>';
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
            '<textarea name="%s" rows="3" class="large-text">%s</textarea>',
            esc_attr($this->option_prefix . 'availability_message'),
            esc_textarea($value)
        );
    }
    
    /**
     * Funciones de sanitización
     */
    public function sanitize_position($value) {
        $allowed = array('bottom-right', 'bottom-left', 'center');
        return in_array($value, $allowed) ? $value : 'bottom-right';
    }
    
    public function sanitize_display_mode($value) {
        $allowed = array('modal', 'fullscreen');
        return in_array($value, $allowed) ? $value : 'modal';
    }
    
    public function sanitize_excluded_pages($value) {
        if (!is_array($value)) {
            return array();
        }
        return array_map('absint', $value);
    }
    
    public function sanitize_checkbox($value) {
        return $value == 1 ? 1 : 0;
    }
    
    public function sanitize_time($value) {
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return $value;
        }
        return '09:00';
    }
    
    /**
     * Renderizar página de configuración
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Verificar si se guardaron los cambios
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'wland_chat_messages',
                'wland_chat_message',
                __('Configuración guardada correctamente.', 'wland-chat'),
                'updated'
            );
        }
        
        settings_errors('wland_chat_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="wland-chat-admin-header">
                <p><?php _e('Configure el widget de chat con IA de BravesLab para su sitio web.', 'wland-chat'); ?></p>
            </div>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('wland_chat_settings');
                do_settings_sections('wland-chat-settings');
                submit_button(__('Guardar Cambios', 'wland-chat'));
                ?>
            </form>
            
            <div class="wland-chat-admin-footer">
                <hr>
                <p>
                    <strong><?php _e('Wland Chat iA', 'wland-chat'); ?></strong> - 
                    <?php printf(
                        __('Versión %s | Desarrollado por %s', 'wland-chat'),
                        WLAND_CHAT_VERSION,
                        '<a href="https://weblandia.es" target="_blank">Weblandia.es</a>'
                    ); ?>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Encolar assets del admin
     */
    public function enqueue_admin_assets($hook) {
        if ('settings_page_wland-chat-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'wland-chat-admin',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WLAND_CHAT_VERSION
        );
        
        wp_enqueue_script(
            'wland-chat-admin',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            WLAND_CHAT_VERSION,
            true
        );
    }
}