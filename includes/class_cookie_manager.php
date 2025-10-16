<?php
/**
 * Cookie Manager Class
 *
 * Gestiona cookies persistentes con fingerprinting del navegador para identificación única de usuarios.
 * Incluye fallback a localStorage, regeneración de ID por cambios significativos y compliance GDPR.
 *
 * @package WlandChat
 * @since 1.1.0
 */

namespace WlandChat;

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase WlandCookieManager
 *
 * Sistema robusto de gestión de sesiones mediante cookies con fingerprinting del navegador.
 * Implementa patrón Singleton para garantizar una única instancia.
 */
class WlandCookieManager {

    /**
     * Instancia única de la clase (patrón Singleton)
     *
     * @var WlandCookieManager
     */
    private static $instance = null;

    /**
     * Nombre de la cookie
     *
     * @var string
     */
    const COOKIE_NAME = 'wland_chat_session';

    /**
     * Duración de la cookie en segundos (1 año)
     *
     * @var int
     */
    const COOKIE_DURATION = YEAR_IN_SECONDS; // 31536000 segundos = 365 días

    /**
     * Nombre de la opción para configuración GDPR
     *
     * @var string
     */
    const GDPR_OPTION_NAME = 'wland_chat_gdpr_enabled';

    /**
     * Nombre de la opción para mensaje del banner GDPR
     *
     * @var string
     */
    const GDPR_MESSAGE_OPTION = 'wland_chat_gdpr_message';

    /**
     * Nombre de la opción para texto del botón de aceptar GDPR
     *
     * @var string
     */
    const GDPR_ACCEPT_TEXT_OPTION = 'wland_chat_gdpr_accept_text';

    /**
     * Constructor privado (patrón Singleton)
     */
    private function __construct() {
        // Hook para pasar configuración GDPR al JavaScript
        add_action('wp_enqueue_scripts', array($this, 'localize_gdpr_settings'), 100);

        // NOTA: No establecemos cookies automáticamente en PHP para evitar
        // "headers already sent" errors. El JavaScript (wland_fingerprint.js)
        // maneja la creación de cookies en el cliente.
    }

    /**
     * Obtener instancia única de la clase (patrón Singleton)
     *
     * @return WlandCookieManager Instancia única
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Inicializar sesión del usuario
     *
     * NOTA: Este método ya no se usa automáticamente para evitar errores
     * "headers already sent". Las cookies se manejan completamente en JavaScript.
     * Este método se mantiene por si se necesita generar session_id desde PHP
     * en el futuro (por ejemplo, para AJAX o REST API).
     *
     * @return void
     */
    public function initialize_session() {
        // Solo inicializar si no estamos en el admin y headers no enviados
        if (!is_admin() && !headers_sent()) {
            $this->get_or_create_session();
        }
    }

    /**
     * Obtener o crear sesión del usuario
     *
     * Verifica si existe una cookie válida. Si no existe o ha expirado,
     * crea una nueva sesión con ID único basado en fingerprinting.
     *
     * @return string ID de sesión único
     */
    public function get_or_create_session() {
        $session_id = $this->get_session_from_cookie();

        if (empty($session_id)) {
            // No hay sesión válida, crear una nueva
            $session_id = $this->create_new_session();
        }

        return $session_id;
    }

    /**
     * Obtener sesión desde cookie
     *
     * Lee la cookie y valida su formato. Retorna vacío si no existe
     * o el formato es inválido.
     *
     * @return string ID de sesión o cadena vacía si no existe
     */
    private function get_session_from_cookie() {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $session_id = sanitize_text_field($_COOKIE[self::COOKIE_NAME]);

            // Validar formato (debe ser un hash de 64 caracteres hexadecimales)
            if (preg_match('/^[a-f0-9]{64}$/i', $session_id)) {
                return $session_id;
            }
        }

        return '';
    }

    /**
     * Crear nueva sesión
     *
     * Genera un ID único basado en fingerprinting del servidor.
     * Este ID será complementado con fingerprinting del navegador
     * en el lado del cliente mediante JavaScript.
     *
     * @return string ID de sesión único
     */
    private function create_new_session() {
        // Generar ID único basado en datos del servidor
        $session_id = $this->generate_server_fingerprint();

        // Establecer cookie
        $this->set_session_cookie($session_id);

        return $session_id;
    }

    /**
     * Generar fingerprint desde el servidor
     *
     * Crea un hash único usando:
     * - User-Agent del navegador
     * - IP del cliente (con consideración de proxies)
     * - Timestamp actual
     * - Salt aleatorio para mayor unicidad
     *
     * Este fingerprint será combinado con el fingerprint del navegador
     * en JavaScript para crear el ID definitivo.
     *
     * @return string Hash SHA-256 de 64 caracteres hexadecimales
     */
    private function generate_server_fingerprint() {
        $components = array(
            isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            $this->get_client_ip(),
            time(),
            wp_generate_password(20, false) // Salt aleatorio
        );

        // Crear hash único
        $fingerprint_data = implode('|', $components);
        $hash = hash('sha256', $fingerprint_data);

        return $hash;
    }

    /**
     * Obtener IP del cliente
     *
     * Considera headers de proxies y load balancers para obtener
     * la IP real del cliente cuando sea posible.
     *
     * @return string IP del cliente
     */
    private function get_client_ip() {
        $ip_headers = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        );

        foreach ($ip_headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];

                // Si es X-Forwarded-For, puede contener múltiples IPs
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }

                // Validar formato IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Establecer cookie de sesión
     *
     * Crea una cookie persistente con duración de 1 año.
     * Usa configuración segura (HttpOnly, SameSite, Secure si HTTPS).
     *
     * @param string $session_id ID de sesión a almacenar
     * @return bool True si la cookie se estableció correctamente
     */
    private function set_session_cookie($session_id) {
        $expiration = time() + self::COOKIE_DURATION;
        $secure = is_ssl(); // Solo secure si el sitio usa HTTPS
        $httponly = true; // Prevenir acceso desde JavaScript malicioso

        // WordPress 5.3+ soporta el parámetro SameSite
        if (version_compare(get_bloginfo('version'), '5.3', '>=')) {
            return setcookie(
                self::COOKIE_NAME,
                $session_id,
                array(
                    'expires' => $expiration,
                    'path' => COOKIEPATH,
                    'domain' => COOKIE_DOMAIN,
                    'secure' => $secure,
                    'httponly' => $httponly,
                    'samesite' => 'Lax' // Balance entre seguridad y funcionalidad
                )
            );
        } else {
            // Fallback para versiones anteriores de WordPress
            return setcookie(
                self::COOKIE_NAME,
                $session_id,
                $expiration,
                COOKIEPATH,
                COOKIE_DOMAIN,
                $secure,
                $httponly
            );
        }
    }

    /**
     * Regenerar sesión
     *
     * Crea un nuevo ID de sesión y actualiza la cookie.
     * Usado cuando se detectan cambios significativos en el fingerprint.
     *
     * @return string Nuevo ID de sesión
     */
    public function regenerate_session() {
        $new_session_id = $this->generate_server_fingerprint();
        $this->set_session_cookie($new_session_id);
        return $new_session_id;
    }

    /**
     * Eliminar sesión
     *
     * Borra la cookie de sesión estableciendo su expiración en el pasado.
     *
     * @return bool True si la cookie se eliminó correctamente
     */
    public function delete_session() {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            unset($_COOKIE[self::COOKIE_NAME]);
            return setcookie(
                self::COOKIE_NAME,
                '',
                time() - 3600,
                COOKIEPATH,
                COOKIE_DOMAIN
            );
        }
        return false;
    }

    /**
     * Verificar si GDPR está habilitado
     *
     * @return bool True si el banner GDPR debe mostrarse
     */
    public function is_gdpr_enabled() {
        return (bool) get_option(self::GDPR_OPTION_NAME, false);
    }

    /**
     * Obtener mensaje del banner GDPR
     *
     * @return string Mensaje personalizado o mensaje por defecto
     */
    public function get_gdpr_message() {
        $default_message = __('Este sitio utiliza cookies para mejorar tu experiencia y proporcionar un servicio de chat personalizado. Al continuar navegando, aceptas nuestra política de cookies.', 'wland-chat');
        return get_option(self::GDPR_MESSAGE_OPTION, $default_message);
    }

    /**
     * Obtener texto del botón de aceptar GDPR
     *
     * @return string Texto personalizado o texto por defecto
     */
    public function get_gdpr_accept_text() {
        $default_text = __('Aceptar', 'wland-chat');
        return get_option(self::GDPR_ACCEPT_TEXT_OPTION, $default_text);
    }

    /**
     * Pasar configuración GDPR al JavaScript
     *
     * Localiza variables para que el JavaScript pueda acceder a la
     * configuración del banner GDPR.
     *
     * @return void
     */
    public function localize_gdpr_settings() {
        // Solo si el chat está activo
        if (!Helpers::should_display_chat()) {
            return;
        }

        $gdpr_config = array(
            'enabled' => $this->is_gdpr_enabled(),
            'message' => $this->get_gdpr_message(),
            'accept_text' => $this->get_gdpr_accept_text(),
            'cookie_name' => self::COOKIE_NAME,
            'cookie_duration' => self::COOKIE_DURATION
        );

        // Pasar al script de fingerprinting (siempre se carga)
        if (wp_script_is('wland-chat-fingerprint', 'enqueued') || wp_script_is('wland-chat-fingerprint', 'registered')) {
            wp_localize_script('wland-chat-fingerprint', 'wlandGDPRConfig', $gdpr_config);
        }
    }

    /**
     * Prevenir clonación de la instancia
     */
    private function __clone() {}

    /**
     * Prevenir deserialización de la instancia
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
