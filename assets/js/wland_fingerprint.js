/**
 * Wland Chat Fingerprinting System
 *
 * Sistema de fingerprinting del navegador para identificación única de usuarios.
 * Incluye fallback a localStorage, detección de cambios significativos y compliance GDPR.
 *
 * @package WlandChat
 * @since 1.1.0
 */

/**
 * Clase WlandFingerprint
 *
 * Genera fingerprints únicos del navegador combinando múltiples características del dispositivo.
 * Implementa detección de cambios, fallback a localStorage y banner GDPR.
 */
class WlandFingerprint {
    /**
     * Constructor
     *
     * Inicializa el sistema de fingerprinting y verifica configuración GDPR.
     */
    constructor() {
        this.cookie_name = 'wland_chat_session';
        this.cookie_duration = 365; // días
        this.storage_key = 'wland_chat_session_backup';
        this.fingerprint_key = 'wland_chat_fingerprint';
        this.gdpr_consent_key = 'wland_chat_gdpr_consent';

        // Configuración GDPR (viene de PHP via wp_localize_script)
        this.gdpr_config = typeof wlandGDPRConfig !== 'undefined' ? wlandGDPRConfig : {
            enabled: false,
            message: '',
            accept_text: 'Aceptar',
            cookie_name: this.cookie_name,
            cookie_duration: 31536000 // segundos
        };

        // Inicializar
        this.init();
    }

    /**
     * Inicializar sistema
     *
     * Verifica consentimiento GDPR y obtiene o crea sesión.
     *
     * @return {void}
     */
    async init() {
        if (this.gdpr_config.enabled) {
            // GDPR habilitado: verificar consentimiento
            if (!this.has_gdpr_consent()) {
                this.show_gdpr_banner();
                return; // No crear sesión hasta que se acepte
            }
        }

        // GDPR no habilitado o consentimiento otorgado: crear/obtener sesión
        await this.get_or_create_session();
    }

    /**
     * Verificar si hay consentimiento GDPR
     *
     * @return {boolean} True si el usuario ha aceptado las cookies
     */
    has_gdpr_consent() {
        return localStorage.getItem(this.gdpr_consent_key) === 'accepted';
    }

    /**
     * Guardar consentimiento GDPR
     *
     * @return {void}
     */
    save_gdpr_consent() {
        localStorage.setItem(this.gdpr_consent_key, 'accepted');
    }

    /**
     * Mostrar banner GDPR
     *
     * Crea y muestra un banner modal para solicitar consentimiento de cookies.
     *
     * @return {void}
     */
    show_gdpr_banner() {
        // Verificar si ya existe el banner
        if (document.getElementById('wland-gdpr-banner')) {
            return;
        }

        // Crear estructura del banner
        const banner = document.createElement('div');
        banner.id = 'wland-gdpr-banner';
        banner.className = 'wland-gdpr-banner';
        banner.innerHTML = `
            <div class="wland-gdpr-content">
                <p class="wland-gdpr-message">${this.gdpr_config.message}</p>
                <button class="wland-gdpr-accept" id="wland-gdpr-accept-btn">
                    ${this.gdpr_config.accept_text}
                </button>
            </div>
        `;

        // Agregar estilos inline para garantizar visualización
        banner.style.cssText = `
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            color: white;
            padding: 20px;
            z-index: 999999;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease-out;
        `;

        // Agregar al body
        document.body.appendChild(banner);

        // Event listener para botón de aceptar
        const accept_btn = document.getElementById('wland-gdpr-accept-btn');
        if (accept_btn) {
            accept_btn.addEventListener('click', async () => {
                this.save_gdpr_consent();
                banner.remove();
                // Ahora sí crear la sesión
                await this.get_or_create_session();
            });
        }
    }

    /**
     * Obtener o crear sesión
     *
     * Verifica si existe una sesión válida en cookie o localStorage.
     * Si no existe, crea una nueva con fingerprinting completo.
     *
     * @return {Promise<string>} ID de sesión único
     */
    async get_or_create_session() {
        let session_id = this.get_session_from_cookie();

        if (!session_id) {
            // Intentar fallback a localStorage
            session_id = this.get_session_from_storage();
        }

        if (!session_id) {
            // No hay sesión válida, crear una nueva
            session_id = await this.create_new_session();
        } else {
            // Verificar si el fingerprint ha cambiado significativamente
            if (this.should_regenerate_session()) {
                console.log('[Wland Fingerprint] Cambios significativos detectados, regenerando sesión');
                session_id = await this.create_new_session();
            }
        }

        return session_id;
    }

    /**
     * Obtener sesión desde cookie
     *
     * @return {string|null} ID de sesión o null si no existe
     */
    get_session_from_cookie() {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === this.cookie_name) {
                return value;
            }
        }
        return null;
    }

    /**
     * Obtener sesión desde localStorage (fallback)
     *
     * @return {string|null} ID de sesión o null si no existe
     */
    get_session_from_storage() {
        try {
            return localStorage.getItem(this.storage_key);
        } catch (e) {
            console.error('[Wland Fingerprint] Error accediendo a localStorage:', e);
            return null;
        }
    }

    /**
     * Crear nueva sesión
     *
     * Genera un ID único basado en fingerprinting completo del navegador
     * y lo almacena en cookie y localStorage (fallback).
     *
     * @return {Promise<string>} ID de sesión único
     */
    async create_new_session() {
        const fingerprint = this.generate_browser_fingerprint();
        const session_id = await this.hash_fingerprint(fingerprint);

        // Guardar en cookie
        this.set_session_cookie(session_id);

        // Guardar en localStorage como fallback
        this.set_session_storage(session_id);

        // Guardar fingerprint actual para detectar cambios
        this.save_current_fingerprint(fingerprint);

        console.log('[Wland Fingerprint] Nueva sesión creada:', session_id);

        return session_id;
    }

    /**
     * Generar fingerprint completo del navegador
     *
     * Combina múltiples características del navegador y dispositivo:
     * - User-Agent
     * - Resolución de pantalla
     * - Zona horaria
     * - Plugins instalados
     * - Canvas fingerprint
     * - WebGL fingerprint
     * - Idioma
     * - Platform
     * - Color depth
     *
     * @return {object} Objeto con todas las características del dispositivo
     */
    generate_browser_fingerprint() {
        return {
            user_agent: navigator.userAgent,
            screen_resolution: `${screen.width}x${screen.height}x${screen.colorDepth}`,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            timezone_offset: new Date().getTimezoneOffset(),
            language: navigator.language,
            languages: navigator.languages ? navigator.languages.join(',') : '',
            platform: navigator.platform,
            hardware_concurrency: navigator.hardwareConcurrency || 'unknown',
            device_memory: navigator.deviceMemory || 'unknown',
            plugins: this.get_plugins_list(),
            canvas: this.get_canvas_fingerprint(),
            webgl: this.get_webgl_fingerprint(),
            touch_support: this.get_touch_support(),
            cpu_class: navigator.cpuClass || 'unknown',
            do_not_track: navigator.doNotTrack || 'unknown'
        };
    }

    /**
     * Obtener lista de plugins del navegador
     *
     * @return {string} Lista de plugins separados por coma
     */
    get_plugins_list() {
        if (!navigator.plugins || navigator.plugins.length === 0) {
            return 'none';
        }

        const plugins = [];
        for (let i = 0; i < navigator.plugins.length; i++) {
            plugins.push(navigator.plugins[i].name);
        }
        return plugins.join(',');
    }

    /**
     * Generar Canvas fingerprint
     *
     * Utiliza Canvas API para generar un hash único basado en
     * cómo el navegador renderiza gráficos.
     *
     * @return {string} Hash del canvas
     */
    get_canvas_fingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            if (!ctx) {
                return 'unsupported';
            }

            // Dibujar texto con características específicas
            ctx.textBaseline = 'top';
            ctx.font = '14px Arial';
            ctx.textBaseline = 'alphabetic';
            ctx.fillStyle = '#f60';
            ctx.fillRect(125, 1, 62, 20);
            ctx.fillStyle = '#069';
            ctx.fillText('Wland Chat 🤖', 2, 15);
            ctx.fillStyle = 'rgba(102, 204, 0, 0.7)';
            ctx.fillText('Fingerprinting', 4, 17);

            // Obtener data URL y hacer hash
            return this.simple_hash(canvas.toDataURL());
        } catch (e) {
            return 'error';
        }
    }

    /**
     * Generar WebGL fingerprint
     *
     * Utiliza WebGL para obtener información de la GPU.
     *
     * @return {string} Información de WebGL
     */
    get_webgl_fingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');

            if (!gl) {
                return 'unsupported';
            }

            const debug_info = gl.getExtension('WEBGL_debug_renderer_info');
            if (debug_info) {
                const vendor = gl.getParameter(debug_info.UNMASKED_VENDOR_WEBGL);
                const renderer = gl.getParameter(debug_info.UNMASKED_RENDERER_WEBGL);
                return `${vendor}~${renderer}`;
            }

            return 'no-debug-info';
        } catch (e) {
            return 'error';
        }
    }

    /**
     * Detectar soporte táctil
     *
     * @return {string} Capacidades táctiles del dispositivo
     */
    get_touch_support() {
        const max_touch_points = navigator.maxTouchPoints || 0;
        const touch_event = 'ontouchstart' in window;
        const touch_points = navigator.msMaxTouchPoints || 0;

        return `${max_touch_points},${touch_event},${touch_points}`;
    }

    /**
     * Generar hash del fingerprint
     *
     * Convierte el objeto fingerprint en un hash SHA-256.
     *
     * @param {object} fingerprint - Objeto con datos del fingerprint
     * @return {string} Hash hexadecimal de 64 caracteres
     */
    async hash_fingerprint(fingerprint) {
        const fingerprint_string = JSON.stringify(fingerprint);

        // Usar Web Crypto API si está disponible
        if (window.crypto && window.crypto.subtle) {
            try {
                const encoder = new TextEncoder();
                const data = encoder.encode(fingerprint_string);
                const hash_buffer = await crypto.subtle.digest('SHA-256', data);
                const hash_array = Array.from(new Uint8Array(hash_buffer));
                return hash_array.map(b => b.toString(16).padStart(2, '0')).join('');
            } catch (e) {
                console.error('[Wland Fingerprint] Error con Web Crypto API:', e);
            }
        }

        // Fallback a hash simple
        return this.simple_hash(fingerprint_string);
    }

    /**
     * Hash simple (fallback)
     *
     * Implementación de hash simple para navegadores sin Web Crypto API.
     *
     * @param {string} str - String a hashear
     * @return {string} Hash de 64 caracteres
     */
    simple_hash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }

        // Convertir a hex y pad a 64 caracteres
        const hex = Math.abs(hash).toString(16);
        return hex.padStart(64, '0').substring(0, 64);
    }

    /**
     * Establecer cookie de sesión
     *
     * @param {string} session_id - ID de sesión a almacenar
     * @return {void}
     */
    set_session_cookie(session_id) {
        const expiration_date = new Date();
        expiration_date.setTime(expiration_date.getTime() + (this.cookie_duration * 24 * 60 * 60 * 1000));
        const expires = "expires=" + expiration_date.toUTCString();

        // Usar Secure solo si estamos en HTTPS
        const secure = window.location.protocol === 'https:' ? '; Secure' : '';

        document.cookie = `${this.cookie_name}=${session_id}; ${expires}; path=/; SameSite=Lax${secure}`;
    }

    /**
     * Establecer sesión en localStorage (fallback)
     *
     * @param {string} session_id - ID de sesión a almacenar
     * @return {void}
     */
    set_session_storage(session_id) {
        try {
            localStorage.setItem(this.storage_key, session_id);
        } catch (e) {
            console.error('[Wland Fingerprint] Error guardando en localStorage:', e);
        }
    }

    /**
     * Guardar fingerprint actual para comparación futura
     *
     * @param {object} fingerprint - Datos del fingerprint actual
     * @return {void}
     */
    save_current_fingerprint(fingerprint) {
        try {
            localStorage.setItem(this.fingerprint_key, JSON.stringify(fingerprint));
        } catch (e) {
            console.error('[Wland Fingerprint] Error guardando fingerprint:', e);
        }
    }

    /**
     * Verificar si se debe regenerar la sesión
     *
     * Compara el fingerprint actual con el almacenado para detectar
     * cambios significativos que justifiquen regenerar la sesión.
     *
     * @return {boolean} True si se deben detectar cambios significativos
     */
    should_regenerate_session() {
        try {
            const stored_fingerprint_str = localStorage.getItem(this.fingerprint_key);
            if (!stored_fingerprint_str) {
                return false; // No hay fingerprint previo para comparar
            }

            const stored_fingerprint = JSON.parse(stored_fingerprint_str);
            const current_fingerprint = this.generate_browser_fingerprint();

            // Detectar cambios significativos
            const significant_changes = [
                // Cambio de navegador/versión mayor
                stored_fingerprint.user_agent !== current_fingerprint.user_agent,
                // Cambio de resolución (no solo resize, sino cambio de monitor)
                stored_fingerprint.screen_resolution !== current_fingerprint.screen_resolution,
                // Cambio de zona horaria
                stored_fingerprint.timezone !== current_fingerprint.timezone,
                // Cambio en canvas (indica cambio de GPU o configuración)
                stored_fingerprint.canvas !== current_fingerprint.canvas
            ];

            // Si hay al menos 2 cambios significativos, regenerar
            const change_count = significant_changes.filter(c => c).length;
            return change_count >= 2;

        } catch (e) {
            console.error('[Wland Fingerprint] Error verificando cambios:', e);
            return false;
        }
    }

    /**
     * Obtener sesión actual
     *
     * Método público para obtener el session_id actual.
     *
     * @return {string|null} ID de sesión o null si no existe
     */
    get_session_id() {
        return this.get_session_from_cookie() || this.get_session_from_storage();
    }

    /**
     * Limpiar sesión
     *
     * Elimina cookie y datos de localStorage.
     *
     * @return {void}
     */
    clear_session() {
        // Eliminar cookie
        document.cookie = `${this.cookie_name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;

        // Eliminar de localStorage
        try {
            localStorage.removeItem(this.storage_key);
            localStorage.removeItem(this.fingerprint_key);
            localStorage.removeItem(this.gdpr_consent_key);
        } catch (e) {
            console.error('[Wland Fingerprint] Error limpiando localStorage:', e);
        }

        console.log('[Wland Fingerprint] Sesión limpiada');
    }
}

// Inicializar automáticamente cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.wlandFingerprint = new WlandFingerprint();
    });
} else {
    window.wlandFingerprint = new WlandFingerprint();
}
