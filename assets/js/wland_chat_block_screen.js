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
            // Validar webhook URL
            if (!this.webhook_url || this.webhook_url.trim() === '') {
                throw new Error('WEBHOOK_NOT_CONFIGURED: La URL del webhook no está configurada en los ajustes del plugin.');
            }

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

            // Preparar payload para N8N Chat (espera chatInput)
            const payload = {
                chatInput: message,
                sessionId: this.session_id
            };

            console.log('📤 Payload enviado:', payload);
            console.log('🌐 Enviando petición a:', this.webhook_url);
            console.log('📋 Headers:', headers);

            // Enviar al webhook con autenticación
            const response = await fetch(this.webhook_url, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload),
                mode: 'cors'
            });

            console.log('📥 Respuesta recibida:');
            console.log('   - Status:', response.status);
            console.log('   - Status Text:', response.statusText);
            console.log('   - Headers:', Object.fromEntries(response.headers.entries()));

            // Capturar el cuerpo de la respuesta antes de verificar
            let response_text = '';
            try {
                response_text = await response.text();
                console.log('   - Body (raw):', response_text);
            } catch (text_error) {
                console.error('❌ Error al leer el cuerpo de la respuesta:', text_error);
            }

            if (!response.ok) {
                // Error del servidor - construir mensaje descriptivo
                let error_details = `ERROR HTTP ${response.status}: ${response.statusText}`;

                if (response.status === 401) {
                    error_details = 'ERROR 401 UNAUTHORIZED: Token de autenticación inválido o expirado.';
                } else if (response.status === 403) {
                    error_details = 'ERROR 403 FORBIDDEN: Acceso denegado. Verifica el token de autenticación.';
                } else if (response.status === 404) {
                    error_details = 'ERROR 404 NOT FOUND: La URL del webhook no existe.';
                } else if (response.status === 500) {
                    error_details = 'ERROR 500 INTERNAL SERVER ERROR: Error en el servidor N8N.';
                } else if (response.status === 502) {
                    error_details = 'ERROR 502 BAD GATEWAY: El servidor N8N no responde.';
                } else if (response.status === 503) {
                    error_details = 'ERROR 503 SERVICE UNAVAILABLE: El servidor N8N está temporalmente no disponible.';
                }

                console.error('❌', error_details);
                console.error('   Respuesta del servidor:', response_text);

                throw new Error(error_details + (response_text ? `\n\nRespuesta: ${response_text.substring(0, 200)}` : ''));
            }

            // Intentar parsear JSON
            let data;
            try {
                data = JSON.parse(response_text);
                console.log('✅ JSON parseado correctamente:', data);
            } catch (json_error) {
                console.error('❌ Error al parsear JSON:', json_error);
                console.error('   Respuesta recibida:', response_text);
                throw new Error(`JSON_PARSE_ERROR: La respuesta del servidor no es JSON válido.\n\nRespuesta: ${response_text.substring(0, 200)}`);
            }

            // Ocultar indicador de escritura
            this.hide_typing_indicator();

            // Adaptarse a diferentes formatos de respuesta de N8N
            // El módulo de chat N8N puede devolver varios formatos
            let bot_message = null;

            if (data.output) {
                bot_message = data.output;
            } else if (data.response) {
                bot_message = data.response;
            } else if (data.message) {
                bot_message = data.message;
            } else if (data.text) {
                bot_message = data.text;
            } else if (typeof data === 'string') {
                // Si la respuesta es directamente un string
                bot_message = data;
            } else if (data.data && typeof data.data === 'string') {
                // Si viene en un campo "data"
                bot_message = data.data;
            }

            if (!bot_message) {
                console.error('❌ No se encontró mensaje en la respuesta');
                console.error('   Estructura recibida:', data);
                console.error('   Tipo de dato:', typeof data);
                throw new Error(`RESPONSE_FORMAT_ERROR: No se encontró el mensaje en la respuesta.\n\nCampos disponibles: ${Object.keys(data).join(', ')}\n\nRespuesta completa: ${JSON.stringify(data).substring(0, 200)}`);
            }

            this.add_message(bot_message, 'bot');

            // Guardar en historial
            this.conversation_history.push({
                role: 'assistant',
                content: bot_message
            });

            console.log('✅ Mensaje procesado correctamente');

        } catch (error) {
            console.error('❌ ERROR COMPLETO:', error);
            console.error('   Stack:', error.stack);
            this.hide_typing_indicator();

            // Construir mensaje de error descriptivo
            let user_message = '❌ Error al procesar tu mensaje:\n\n';
            let technical_details = '';

            if (error.message.includes('Failed to fetch')) {
                user_message += '🔌 **No se pudo conectar con el servidor**\n\n';
                user_message += 'Posibles causas:\n';
                user_message += '• Sin conexión a internet\n';
                user_message += '• El servidor N8N está caído\n';
                user_message += '• Problema de CORS\n';
                user_message += '• URL del webhook incorrecta\n\n';
                technical_details = `URL: ${this.webhook_url}\nError: ${error.message}`;
            } else if (error.message.includes('WEBHOOK_NOT_CONFIGURED')) {
                user_message += '⚙️ **Webhook no configurado**\n\n';
                user_message += 'El administrador debe configurar la URL del webhook en:\n';
                user_message += 'WordPress Admin > Ajustes > Wland Chat iA\n\n';
                technical_details = error.message;
            } else if (error.message.includes('401') || error.message.includes('403')) {
                user_message += '🔐 **Error de autenticación**\n\n';
                user_message += 'El token de autenticación es inválido o ha expirado.\n';
                user_message += 'Contacta al administrador para verificar el token N8N.\n\n';
                technical_details = error.message;
            } else if (error.message.includes('404')) {
                user_message += '🔍 **Webhook no encontrado**\n\n';
                user_message += 'La URL del webhook no existe o es incorrecta.\n';
                user_message += 'Verifica la URL en los ajustes del plugin.\n\n';
                technical_details = `URL: ${this.webhook_url}\n${error.message}`;
            } else if (error.message.includes('JSON_PARSE_ERROR')) {
                user_message += '📦 **Respuesta inválida del servidor**\n\n';
                user_message += 'El servidor N8N no devolvió un JSON válido.\n';
                user_message += 'Verifica la configuración del workflow en N8N.\n\n';
                technical_details = error.message;
            } else if (error.message.includes('RESPONSE_FORMAT_ERROR')) {
                user_message += '📋 **Formato de respuesta incorrecto**\n\n';
                user_message += 'El servidor devolvió una respuesta pero sin el campo esperado.\n';
                user_message += 'El webhook debe devolver: {output: "mensaje"} o {response: "mensaje"}\n\n';
                technical_details = error.message;
            } else if (error.message.includes('500') || error.message.includes('502') || error.message.includes('503')) {
                user_message += '🔧 **Error del servidor**\n\n';
                user_message += 'El servidor N8N tiene un problema interno.\n';
                user_message += 'Contacta al administrador del servidor.\n\n';
                technical_details = error.message;
            } else {
                user_message += '⚠️ **Error desconocido**\n\n';
                user_message += 'Ocurrió un error inesperado. Por favor, intenta de nuevo.\n\n';
                technical_details = `${error.message}\n\nStack: ${error.stack}`;
            }

            user_message += '📋 **Detalles técnicos:**\n';
            user_message += '```\n' + technical_details + '\n```\n\n';
            user_message += `🕐 ${new Date().toLocaleString('es-ES')}`;

            this.add_message(user_message, 'bot');

            // Log adicional para el administrador
            console.log('📊 INFORMACIÓN DE DEBUG:');
            console.log('   - Webhook URL:', this.webhook_url);
            console.log('   - Auth Token configurado:', this.auth_token ? 'Sí' : 'No');
            console.log('   - Session ID:', this.session_id);
            console.log('   - Historial (mensajes):', this.conversation_history.length);
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
