/**
 * Wland Chat iA - Fullscreen Mode
 * Version: 1.0.0
 * MODIFICADO: Implementada autenticación N8N con header X-N8N-Auth
 * REFACTORIZADO: snake_case y JSDoc
 */

class WlandChatScreen {
    /**
     * Constructor de la clase WlandChatScreen
     * Inicializa las propiedades y llama a init()
     */
    constructor() {
        this.is_open = false;
        this.lottie_animation = null;
        this.conversation_history = [];
        this.session_id = this.generate_session_id();

        // Obtener configuración desde PHP
        this.animation_path = window.wlandChatData?.animationPath || window.WlandChatConfig?.animationPath || '';
        this.webhook_url = window.wlandChatConfig?.webhookUrl || window.WlandChatConfig?.webhook_url || '';
        this.auth_token = window.WlandChatConfig?.auth_token || ''; // Token de autenticación

        this.init();
    }

    /**
     * Inicializa el chat en modo fullscreen
     * Configura elementos del DOM, animaciones y event listeners
     * @returns {void}
     */
    init() {
        console.log('🚀 Inicializando Wland Chat Fullscreen...');
        console.log('📡 Webhook URL:', this.webhook_url);
        console.log('🔐 Auth Token:', this.auth_token ? '✓ Configurado' : '✗ No configurado');

        // Elementos del DOM
        this.chat_container = document.getElementById('braveslab-chat-container');
        this.chat_toggle = document.getElementById('chat-toggle');
        this.chat_window = document.getElementById('chat-window');
        this.close_button = document.getElementById('close-chat');
        this.chat_messages = document.getElementById('chat-messages');
        this.chat_input = document.getElementById('chat-input');
        this.send_button = document.getElementById('send-button');
        this.typing_indicator = document.getElementById('typing-indicator');

        if (!this.chat_container) {
            console.error('❌ No se encontró el contenedor del chat');
            return;
        }

        // Inicializar Lottie animation
        this.init_lottie_animation();

        // Mostrar hora del mensaje de bienvenida
        this.display_welcome_time();

        // Event Listeners
        this.setup_event_listeners();

        console.log('✅ Chat Fullscreen inicializado correctamente');
    }

    /**
     * Configura todos los event listeners del chat
     * @returns {void}
     */
    setup_event_listeners() {
        this.chat_toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle_chat();
        });

        this.close_button.addEventListener('click', (e) => {
            e.stopPropagation();
            this.close_window();
        });

        this.chat_input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.send_message();
            }
        });

        this.chat_input.addEventListener('input', () => {
            this.toggle_send_button();
        });

        this.send_button.addEventListener('click', () => {
            this.send_message();
        });
    }

    /**
     * Inicializa la animación Lottie en el botón del chat
     * @returns {void}
     */
    init_lottie_animation() {
        const lottie_container = document.getElementById('chat-lottie');

        if (!lottie_container || !this.animation_path) {
            console.warn('⚠️ No se pudo cargar la animación Lottie');
            return;
        }

        try {
            this.lottie_animation = lottie.loadAnimation({
                container: lottie_container,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: this.animation_path
            });

            console.log('✅ Animación Lottie cargada');
        } catch (error) {
            console.error('❌ Error al cargar animación Lottie:', error);
        }
    }

    /**
     * Genera un ID único para la sesión del chat
     * @returns {string} ID de sesión único
     */
    generate_session_id() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11);
    }

    /**
     * Muestra la hora actual en el mensaje de bienvenida
     * @returns {void}
     */
    display_welcome_time() {
        const welcome_time = document.getElementById('welcome-time');
        if (welcome_time) {
            const now = new Date();
            welcome_time.textContent = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    /**
     * Alterna entre abrir y cerrar el chat
     * @returns {void}
     */
    toggle_chat() {
        if (this.is_open) {
            this.close_window();
        } else {
            this.open_window();
        }
    }

    /**
     * Abre la ventana del chat en modo fullscreen
     * Bloquea el scroll del body
     * @returns {void}
     */
    open_window() {
        this.chat_container.classList.remove('chat-closed');
        this.chat_container.classList.add('chat-open');
        this.is_open = true;

        // Bloquear scroll del body en modo fullscreen
        document.body.style.overflow = 'hidden';

        const close_icon = document.getElementById('close-icon');
        if (close_icon) {
            close_icon.style.display = 'block';
        }

        if (this.lottie_animation) {
            this.lottie_animation.pause();
        }

        // Focus en el input
        setTimeout(() => {
            this.chat_input.focus();
        }, 300);
    }

    /**
     * Cierra la ventana del chat
     * Restaura el scroll del body
     * @returns {void}
     */
    close_window() {
        this.chat_container.classList.remove('chat-open');
        this.chat_container.classList.add('chat-closed');
        this.is_open = false;

        // Restaurar scroll del body
        document.body.style.overflow = '';

        const close_icon = document.getElementById('close-icon');
        if (close_icon) {
            close_icon.style.display = 'none';
        }

        if (this.lottie_animation) {
            this.lottie_animation.goToAndPlay(0);
        }
    }

    /**
     * Habilita o deshabilita el botón de enviar según el contenido del input
     * @returns {void}
     */
    toggle_send_button() {
        const has_text = this.chat_input.value.trim().length > 0;
        this.send_button.disabled = !has_text;
    }

    /**
     * Envía un mensaje al webhook de N8N con autenticación
     * Gestiona el historial de conversación y muestra respuestas
     * @async
     * @returns {Promise<void>}
     */
    async send_message() {
        const message = this.chat_input.value.trim();

        if (!message) {
            console.warn('⚠️ Mensaje vacío, no se envía');
            return;
        }

        console.log('📤 Enviando mensaje:', message);

        // Agregar mensaje del usuario al chat
        this.add_message(message, 'user');
        this.chat_input.value = '';
        this.toggle_send_button();

        // Guardar en historial
        this.conversation_history.push({
            role: 'user',
            content: message
        });

        // Mostrar indicador de escritura
        this.show_typing_indicator();

        try {
            // Preparar headers con autenticación
            const headers = {
                'Content-Type': 'application/json',
            };

            // Solo añadir header de autenticación si existe el token
            if (this.auth_token && this.auth_token.trim() !== '') {
                headers['X-N8N-Auth'] = this.auth_token;
                console.log('🔐 Header de autenticación añadido');
            } else {
                console.log('⚠️ No se añadió header de autenticación (token vacío)');
            }

            // Preparar payload
            const payload = {
                prompt: message,
                sessionId: this.session_id,
                history: this.conversation_history
            };

            console.log('📡 Enviando petición a:', this.webhook_url);

            // Enviar al webhook con autenticación
            const response = await fetch(this.webhook_url, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload),
                mode: 'cors'
            });

            console.log('📥 Respuesta recibida - Status:', response.status);

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();
            console.log('📦 Datos recibidos:', data);

            // Ocultar indicador de escritura
            this.hide_typing_indicator();

            // Adaptarse a la estructura de respuesta de N8N
            const bot_message = data.output || data.response || data.message || data.text ||
                               'Lo siento, hubo un error al procesar tu mensaje.';

            this.add_message(bot_message, 'bot');

            // Guardar en historial
            this.conversation_history.push({
                role: 'assistant',
                content: bot_message
            });

            console.log('✅ Mensaje procesado correctamente');

        } catch (error) {
            console.error('❌ Error al enviar mensaje:', error);
            this.hide_typing_indicator();

            // Mensaje de error amigable
            const error_message = error.message.includes('Failed to fetch')
                ? 'No se pudo conectar con el servidor. Por favor, verifica tu conexión a internet.'
                : 'Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta de nuevo.';

            this.add_message(error_message, 'bot');
        }
    }

    /**
     * Añade un mensaje al área de chat
     * @param {string} text - Texto del mensaje
     * @param {string} type - Tipo de mensaje ('user' o 'bot')
     * @returns {void}
     */
    add_message(text, type) {
        const message_div = document.createElement('div');
        message_div.className = `message ${type}`;

        const bubble_div = document.createElement('div');
        bubble_div.className = 'message-bubble';
        bubble_div.textContent = text;

        const time_div = document.createElement('div');
        time_div.className = 'message-time';
        const now = new Date();
        time_div.textContent = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });

        message_div.appendChild(bubble_div);
        message_div.appendChild(time_div);

        this.chat_messages.appendChild(message_div);

        // Scroll al final
        this.scroll_to_bottom();
    }

    /**
     * Muestra el indicador de escritura
     * @returns {void}
     */
    show_typing_indicator() {
        if (this.typing_indicator) {
            this.typing_indicator.style.display = 'flex';
            this.scroll_to_bottom();
        }
    }

    /**
     * Oculta el indicador de escritura
     * @returns {void}
     */
    hide_typing_indicator() {
        if (this.typing_indicator) {
            this.typing_indicator.style.display = 'none';
        }
    }

    /**
     * Hace scroll hasta el final del área de mensajes
     * @returns {void}
     */
    scroll_to_bottom() {
        setTimeout(() => {
            this.chat_messages.scrollTop = this.chat_messages.scrollHeight;
        }, 100);
    }
}

/**
 * Inicializa el chat fullscreen cuando el DOM está listo
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new WlandChatScreen();
    });
} else {
    new WlandChatScreen();
}
