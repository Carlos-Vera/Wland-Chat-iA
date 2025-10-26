<?php
/**
 * Integración con WordPress Customizer
 *
 * Registra opciones del chat en el Customizer de WordPress
 *
 * @package WlandChat
 * @since 1.0.0
 * @version 1.0.0
 */

namespace WlandChat;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Customizer
 *
 * Integra el plugin con el Customizer de WordPress
 *
 * @since 1.0.0
 */
class Customizer {

    /**
     * Instancia única (patrón Singleton)
     *
     * @since 1.0.0
     * @var Customizer|null
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @since 1.0.0
     * @return Customizer Instancia única de la clase
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
        add_action('customize_register', array($this, 'register_customizer_settings'));
    }

    /**
     * Registrar configuraciones en el Customizer
     *
     * Añade panel, secciones y controles del chat al Customizer
     *
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Objeto del Customizer
     * @return void
     */
    public function register_customizer_settings($wp_customize) {
        // Agregar panel
        $wp_customize->add_panel('wland_chat_panel', array(
            'title' => __('Wland Chat iA', 'wland-chat'),
            'description' => __('Personalice la apariencia y comportamiento del chat con IA.', 'wland-chat'),
            'priority' => 160,
        ));
        
        // Sección de Apariencia
        $wp_customize->add_section('wland_chat_appearance', array(
            'title' => __('Apariencia', 'wland-chat'),
            'panel' => 'wland_chat_panel',
            'priority' => 10,
        ));
        
        // Título del header
        $wp_customize->add_setting('wland_chat_header_title', array(
            'default' => __('BravesLab AI Assistant', 'wland-chat'),
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('wland_chat_header_title', array(
            'label' => __('Título del Header', 'wland-chat'),
            'section' => 'wland_chat_appearance',
            'type' => 'text',
        ));
        
        // Subtítulo del header
        $wp_customize->add_setting('wland_chat_header_subtitle', array(
            'default' => __('Artificial Intelligence Marketing Agency', 'wland-chat'),
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('wland_chat_header_subtitle', array(
            'label' => __('Subtítulo del Header', 'wland-chat'),
            'section' => 'wland_chat_appearance',
            'type' => 'text',
        ));
        
        // Mensaje de bienvenida
        $wp_customize->add_setting('wland_chat_welcome_message', array(
            'default' => __('¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?', 'wland-chat'),
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('wland_chat_welcome_message', array(
            'label' => __('Mensaje de Bienvenida', 'wland-chat'),
            'section' => 'wland_chat_appearance',
            'type' => 'textarea',
        ));
        
        // Posición
        $wp_customize->add_setting('wland_chat_position', array(
            'default' => 'bottom-right',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => array($this, 'sanitize_position'),
        ));
        
        $wp_customize->add_control('wland_chat_position', array(
            'label' => __('Posición del Chat', 'wland-chat'),
            'section' => 'wland_chat_appearance',
            'type' => 'select',
            'choices' => array(
                'bottom-right' => __('Abajo Derecha', 'wland-chat'),
                'bottom-left' => __('Abajo Izquierda', 'wland-chat'),
                'center' => __('Centro', 'wland-chat'),
            ),
        ));
        
        // Modo de visualización
        $wp_customize->add_setting('wland_chat_display_mode', array(
            'default' => 'modal',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => array($this, 'sanitize_display_mode'),
        ));
        
        $wp_customize->add_control('wland_chat_display_mode', array(
            'label' => __('Modo de Visualización', 'wland-chat'),
            'section' => 'wland_chat_appearance',
            'type' => 'select',
            'choices' => array(
                'modal' => __('Modal (Ventana emergente)', 'wland-chat'),
                'fullscreen' => __('Pantalla completa', 'wland-chat'),
            ),
        ));
        
        // Sección de Comportamiento
        $wp_customize->add_section('wland_chat_behavior', array(
            'title' => __('Comportamiento', 'wland-chat'),
            'panel' => 'wland_chat_panel',
            'priority' => 20,
        ));
        
        // Webhook URL
        $wp_customize->add_setting('wland_chat_webhook_url', array(
            'default' => 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('wland_chat_webhook_url', array(
            'label' => __('URL del Webhook', 'wland-chat'),
            'description' => __('URL del webhook de N8N para procesar mensajes.', 'wland-chat'),
            'section' => 'wland_chat_behavior',
            'type' => 'url',
        ));
        
        // Habilitar horarios
        $wp_customize->add_setting('wland_chat_availability_enabled', array(
            'default' => false,
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
        ));
        
        $wp_customize->add_control('wland_chat_availability_enabled', array(
            'label' => __('Habilitar Horarios de Disponibilidad', 'wland-chat'),
            'section' => 'wland_chat_behavior',
            'type' => 'checkbox',
        ));
        
        // Hora de inicio
        $wp_customize->add_setting('wland_chat_availability_start', array(
            'default' => '09:00',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => array($this, 'sanitize_time'),
        ));
        
        $wp_customize->add_control('wland_chat_availability_start', array(
            'label' => __('Hora de Inicio', 'wland-chat'),
            'section' => 'wland_chat_behavior',
            'type' => 'text',
            'input_attrs' => array(
                'placeholder' => '09:00',
            ),
        ));
        
        // Hora de fin
        $wp_customize->add_setting('wland_chat_availability_end', array(
            'default' => '18:00',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => array($this, 'sanitize_time'),
        ));
        
        $wp_customize->add_control('wland_chat_availability_end', array(
            'label' => __('Hora de Fin', 'wland-chat'),
            'section' => 'wland_chat_behavior',
            'type' => 'text',
            'input_attrs' => array(
                'placeholder' => '18:00',
            ),
        ));
        
        // Mensaje fuera de horario
        $wp_customize->add_setting('wland_chat_availability_message', array(
            'default' => __('Nuestro horario de atención es de 9:00 a 18:00. Déjanos tu mensaje y te responderemos lo antes posible.', 'wland-chat'),
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        
        $wp_customize->add_control('wland_chat_availability_message', array(
            'label' => __('Mensaje Fuera de Horario', 'wland-chat'),
            'section' => 'wland_chat_behavior',
            'type' => 'textarea',
        ));
        
        // Registrar preview
        if ($wp_customize->is_preview()) {
            add_action('wp_footer', array($this, 'customizer_preview_script'));
        }
    }
    
    /**
     * Script para vista previa en vivo en el Customizer
     *
     * Actualiza elementos del chat en tiempo real mientras se edita
     *
     * @since 1.0.0
     * @return void
     */
    public function customizer_preview_script() {
        ?>
        <script type="text/javascript">
        (function($) {
            wp.customize('wland_chat_header_title', function(value) {
                value.bind(function(newval) {
                    $('#chat-header h3').text(newval);
                });
            });
            
            wp.customize('wland_chat_header_subtitle', function(value) {
                value.bind(function(newval) {
                    $('#chat-header p').text(newval);
                });
            });
            
            wp.customize('wland_chat_welcome_message', function(value) {
                value.bind(function(newval) {
                    $('#chat-messages .message.bot .message-bubble').first().text(newval);
                });
            });
        })(jQuery);
        </script>
        <?php
    }
    
    /**
     * Sanitizar campo de posición
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
     * Sanitizar checkbox
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
     * @return string Hora sanitizada
     */
    public function sanitize_time($value) {
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return $value;
        }
        return '09:00';
    }
}