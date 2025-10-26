<?php
/**
 * Clase de funciones auxiliares
 *
 * Proporciona métodos estáticos para validación, configuración y utilidades
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
 * Clase Helpers
 *
 * Funciones auxiliares estáticas para el plugin
 *
 * @since 1.0.0
 */
class Helpers {

    /**
     * Verificar si el chat debe mostrarse en la página actual
     *
     * Valida exclusiones de página y horarios de disponibilidad
     *
     * @since 1.0.0
     * @return bool True si el chat debe mostrarse, false en caso contrario
     */
    public static function should_display_chat() {
        // No mostrar en páginas excluidas
        if (self::is_page_excluded()) {
            return false;
        }

        // Verificar horarios si está habilitado
        if (self::is_availability_enabled() && !self::is_within_availability_hours()) {
            // Mostramos con mensaje diferente
            return true;
        }

        return true;
    }

    /**
     * Verificar si la página actual está excluida
     *
     * Comprueba si el ID de la página actual está en la lista de exclusión
     *
     * @since 1.0.0
     * @return bool True si está excluida, false en caso contrario
     */
    public static function is_page_excluded() {
        $excluded_pages = get_option('wland_chat_excluded_pages', array());

        if (empty($excluded_pages) || !is_array($excluded_pages)) {
            return false;
        }

        $current_page_id = get_the_ID();

        return in_array($current_page_id, $excluded_pages);
    }

    /**
     * Verificar si está habilitada la restricción por horarios
     *
     * @since 1.0.0
     * @return bool True si los horarios están habilitados
     */
    public static function is_availability_enabled() {
        return (bool) get_option('wland_chat_availability_enabled', false);
    }

    /**
     * Verificar si estamos dentro del horario de disponibilidad
     *
     * Compara la hora actual con el rango configurado, soportando horarios
     * que cruzan medianoche
     *
     * @since 1.0.0
     * @return bool True si está dentro del horario, false en caso contrario
     */
    public static function is_within_availability_hours() {
        $start_time = get_option('wland_chat_availability_start', '09:00');
        $end_time = get_option('wland_chat_availability_end', '18:00');
        $timezone = get_option('wland_chat_availability_timezone', 'Europe/Madrid');
        
        try {
            $tz = new \DateTimeZone($timezone);
            $now = new \DateTime('now', $tz);
            $current_time = $now->format('H:i');
            
            // Comparar horarios
            if ($start_time <= $end_time) {
                // Horario normal (ej: 09:00 a 18:00)
                return ($current_time >= $start_time && $current_time <= $end_time);
            } else {
                // Horario que cruza medianoche (ej: 22:00 a 06:00)
                return ($current_time >= $start_time || $current_time <= $end_time);
            }
        } catch (\Exception $e) {
            // Si hay error, asumimos que está disponible
            return true;
        }
    }
    
    /**
     * Obtener el mensaje de bienvenida apropiado
     *
     * Retorna el mensaje fuera de horario si aplica, o el mensaje normal
     *
     * @since 1.0.0
     * @return string Mensaje de bienvenida
     */
    public static function get_welcome_message() {
        if (self::is_availability_enabled() && !self::is_within_availability_hours()) {
            return get_option('wland_chat_availability_message',
                __('Nuestro horario de atención es de 9:00 a 18:00. Déjanos tu mensaje y te responderemos lo antes posible.', 'wland-chat')
            );
        }

        return get_option('wland_chat_welcome_message',
            __('¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?', 'wland-chat')
        );
    }

    /**
     * Obtener configuración completa del chat
     *
     * Incluye webhook URL, token N8N, títulos, mensajes y disponibilidad
     *
     * @since 1.0.0
     * @since 1.2.2 Añadido token N8N de autenticación
     * @return array Configuración del chat
     */
    public static function get_chat_config() {
        return array(
            'webhook_url'     => get_option('wland_chat_webhook_url'),
            'n8n_auth_token'  => get_option('wland_chat_n8n_auth_token', ''),
            'header_title'    => get_option('wland_chat_header_title'),
            'header_subtitle' => get_option('wland_chat_header_subtitle'),
            'welcome_message' => self::get_welcome_message(),
            'position'        => get_option('wland_chat_position', 'bottom-right'),
            'display_mode'    => get_option('wland_chat_display_mode', 'modal'),
            'is_available'    => !self::is_availability_enabled() || self::is_within_availability_hours(),
        );
    }

    /**
     * Sanitizar atributos del bloque de Gutenberg
     *
     * Mezcla con valores por defecto y aplica sanitización a cada campo
     *
     * @since 1.0.0
     * @param array $attributes Atributos del bloque sin sanitizar
     * @return array Atributos sanitizados
     */
    public static function sanitize_block_attributes($attributes) {
        $defaults = array(
            'webhookUrl'      => get_option('wland_chat_webhook_url'),
            'headerTitle'     => get_option('wland_chat_header_title'),
            'headerSubtitle'  => get_option('wland_chat_header_subtitle'),
            'welcomeMessage'  => self::get_welcome_message(),
            'position'        => get_option('wland_chat_position', 'bottom-right'),
            'displayMode'     => get_option('wland_chat_display_mode', 'modal'),
        );
        
        $attributes = wp_parse_args($attributes, $defaults);
        
        return array(
            'webhookUrl'      => esc_url($attributes['webhookUrl']),
            'headerTitle'     => sanitize_text_field($attributes['headerTitle']),
            'headerSubtitle'  => sanitize_text_field($attributes['headerSubtitle']),
            'welcomeMessage'  => sanitize_textarea_field($attributes['welcomeMessage']),
            'position'        => in_array($attributes['position'], array('bottom-right', 'bottom-left', 'center')) 
                ? $attributes['position'] : 'bottom-right',
            'displayMode'     => in_array($attributes['displayMode'], array('modal', 'fullscreen'))
                ? $attributes['displayMode'] : 'modal',
        );
    }
    
    /**
     * Generar ID único para el widget
     *
     * Crea un identificador único prefijado con 'wland-chat-'
     *
     * @since 1.0.0
     * @return string ID único del widget
     */
    public static function generate_unique_id() {
        return 'wland-chat-' . wp_generate_password(8, false);
    }

    /**
     * Registrar mensaje en el log de depuración
     *
     * Solo funciona cuando WP_DEBUG está habilitado
     *
     * @since 1.0.0
     * @param string $message Mensaje a registrar
     * @param mixed  $data    Datos adicionales opcionales para registrar
     * @return void
     */
    public static function log($message, $data = null) {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $log_message = '[Wland Chat] ' . $message;

        if ($data !== null) {
            $log_message .= ' | Data: ' . print_r($data, true);
        }

        error_log($log_message);
    }
}