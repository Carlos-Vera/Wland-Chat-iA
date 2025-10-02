/**
 * Wland Chat iA - Fullscreen Mode
 * Version: 1.0.0
 * MODIFICADO: Implementada autenticación N8N con header X-N8N-Auth
 */

class WlandChatScreen {
    constructor() {
        this.isOpen = false;
        this.lottieAnimation = null;
        this.conversationHistory = [];
        this.sessionId = this.generateSessionId();
        
        // Obtener configuración desde PHP
        this.animationPath = window.wlandChatData?.animationPath || window.WlandChatConfig?.animationPath || '';
        this.webhookUrl = window.wlandChatConfig?.webhookUrl || window.WlandChatConfig?.webhook_url || '';
        this.authToken = window.WlandChatConfig?.auth_token || ''; // NUEVO: Token de autenticación
        
        this.init();
    }
    
    init() {
        console.log('🚀 Inicializando Wland Chat Fullscreen...');
        console.log('📡 Webhook URL:', this.webhookUrl);
        console.log('🔐 Auth Token:', this.authToken ? '✓ Configurado' : '✗ No configurado');
        
        // Elementos del DOM
        this.chatContainer = document.getElementById('braveslab-chat-container');
        this.chatToggle = document.getElementById('chat-toggle');
        this.chatWindow = document.getElementById('chat-window');
        this.closeButton = document.getElementById('close-chat');
        this.chatMessages = document.getElementById('chat-messages');
        this.chatInput = document.getElementById('chat-input');
        this.sendButton = document.getElementById('send-button');
        this.typingIndicator = document.getElementById('typing-indicator');
        
        if (!this.chatContainer) {
            console.error('❌ No se encontró el contenedor del chat');
            return;
        }
        
        // Inicializar Lottie animation
        this.initLottieAnimation();
        
        // Mostrar hora del mensaje de bienvenida
        this.displayWelcomeTime();
        
        // Event Listeners
        this.setupEventListeners();
        
        console.log('✅ Chat Fullscreen inicializado correctamente');
    }
    
    setupEventListeners() {
        this.chatToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleChat();
        });
        
        this.closeButton.addEventListener('click', (e) => {
            e.stopPropagation();
            this.closeWindow();
        });
        
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        this.chatInput.addEventListener('input', () => {
            this.toggleSendButton();
        });
        
        this.sendButton.addEventListener('click', () => {
            this.sendMessage();
        });
    }
    
    initLottieAnimation() {
        const lottieContainer = document.getElementById('chat-lottie');
        
        if (!lottieContainer || !this.animationPath) {
            console.warn('⚠️ No se pudo cargar la animación Lottie');
            return;
        }
        
        try {
            this.lottieAnimation = lottie.loadAnimation({
                container: lottieContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: this.animationPath
            });
            
            console.log('✅ Animación Lottie cargada');
        } catch (error) {
            console.error('❌ Error al cargar animación Lottie:', error);
        }
    }
    
    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    displayWelcomeTime() {
        const welcomeTime = document.getElementById('welcome-time');
        if (welcomeTime) {
            const now = new Date();
            welcomeTime.textContent = now.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
    }
    
    toggleChat() {
        if (this.isOpen) {
            this.closeWindow();
        } else {
            this.openWindow();
        }
    }
    
    openWindow() {
        this.chatContainer.classList.remove('chat-closed');
        this.chatContainer.classList.add('chat-open');
        this.isOpen = true;
        
        // Bloquear scroll del body en modo fullscreen
        document.body.style.overflow = 'hidden';
        
        const closeIcon = document.getElementById('close-icon');
        if (closeIcon) {
            closeIcon.style.display = 'block';
        }
        
        if (this.lottieAnimation) {
            this.lottieAnimation.pause();
        }
        
        // Focus en el input
        setTimeout(() => {
            this.chatInput.focus();
        }, 300);
    }
    
    closeWindow() {
        this.chatContainer.classList.remove('chat-open');
        this.chatContainer.classList.add('chat-closed');
        this.isOpen = false;
        
        // Restaurar scroll del body
        document.body.style.overflow = '';
        
        const closeIcon = document.getElementById('close-icon');
        if (closeIcon) {
            closeIcon.style.display = 'none';
        }
        
        if (this.lottieAnimation) {
            this.lottieAnimation.goToAndPlay(0);
        }
    }
    
    toggleSendButton() {
        const hasText = this.chatInput.value.trim().length > 0;
        this.sendButton.disabled = !hasText;
    }
    
    /**
     * ========== TAREA 2C: ENVIAR MENSAJE CON AUTENTICACIÓN ==========
     */
    async sendMessage() {
        const message = this.chatInput.value.trim();
        
        if (!message) {
            console.warn('⚠️ Mensaje vacío, no se envía');
            return;
        }
        
        console.log('📤 Enviando mensaje:', message);
        
        // Agregar mensaje del usuario al chat
        this.addMessage(message, 'user');
        this.chatInput.value = '';
        this.toggleSendButton();
        
        // Guardar en historial
        this.conversationHistory.push({
            role: 'user',
            content: message
        });
        
        // Mostrar indicador de escritura
        this.showTypingIndicator();
        
        try {
            // TAREA 2C: Preparar headers con autenticación
            const headers = {
                'Content-Type': 'application/json',
            };
            
            // Solo añadir header de autenticación si existe el token
            if (this.authToken && this.authToken.trim() !== '') {
                headers['X-N8N-Auth'] = this.authToken;
                console.log('🔐 Header de autenticación añadido');
            } else {
                console.log('⚠️ No se añadió header de autenticación (token vacío)');
            }
            
            // Preparar payload
            const payload = {
                message: message,
                sessionId: this.sessionId,
                conversationHistory: this.conversationHistory
            };
            
            console.log('📡 Enviando petición a:', this.webhookUrl);
            
            // Enviar al webhook con autenticación
            const response = await fetch(this.webhookUrl, {
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
            this.hideTypingIndicator();
            
            // Agregar respuesta del bot
            const botMessage = data.response || data.message || data.output || 
                               'Lo siento, hubo un error al procesar tu mensaje.';
            this.addMessage(botMessage, 'bot');
            
            // Guardar en historial
            this.conversationHistory.push({
                role: 'assistant',
                content: botMessage
            });
            
            console.log('✅ Mensaje procesado correctamente');
            
        } catch (error) {
            console.error('❌ Error al enviar mensaje:', error);
            this.hideTypingIndicator();
            
            // Mensaje de error amigable
            const errorMessage = error.message.includes('Failed to fetch') 
                ? 'No se pudo conectar con el servidor. Por favor, verifica tu conexión a internet.'
                : 'Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta de nuevo.';
            
            this.addMessage(errorMessage, 'bot');
        }
    }
    
    addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'message-bubble';
        bubbleDiv.textContent = text;
        
        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        const now = new Date();
        timeDiv.textContent = now.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageDiv.appendChild(bubbleDiv);
        messageDiv.appendChild(timeDiv);
        
        this.chatMessages.appendChild(messageDiv);
        
        // Scroll al final
        this.scrollToBottom();
    }
    
    showTypingIndicator() {
        if (this.typingIndicator) {
            this.typingIndicator.style.display = 'flex';
            this.scrollToBottom();
        }
    }
    
    hideTypingIndicator() {
        if (this.typingIndicator) {
            this.typingIndicator.style.display = 'none';
        }
    }
    
    scrollToBottom() {
        setTimeout(() => {
            this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        }, 100);
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new WlandChatScreen();
    });
} else {
    new WlandChatScreen();
}