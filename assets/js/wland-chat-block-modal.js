/**
 * Wland Chat iA - Modal Mode
 * Version: 1.0.0
 */

class WlandChatModal {
    constructor() {
        this.isOpen = false;
        this.lottieAnimation = null;
        this.conversationHistory = [];
        this.animationPath = window.wlandChatData?.animationPath || '';
        this.webhookUrl = window.wlandChatConfig?.webhookUrl || '';
        
        this.init();
    }
    
    init() {
        console.log('Inicializando Wland Chat Modal...');
        
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
        
        // Mostrar hora del mensaje de bienvenida
        this.displayWelcomeTime();
        
        // Event Listeners
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
        
        this.chatInput.addEventListener('input', () => this.toggleSendButton());
        
        this.sendButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.sendMessage();
        });
        
        this.chatInput.addEventListener('focus', () => {
            this.scrollToBottom();
        });
        
        console.log('✓ Wland Chat inicializado correctamente');
        this.initLottie();
    }
    
    initLottie() {
        if (!window.lottie) {
            console.error('❌ Lottie no está cargado');
            return;
        }
        
        if (!this.animationPath) {
            console.error('❌ No se especificó ruta de animación');
            return;
        }
        
        console.log('Cargando animación Lottie desde:', this.animationPath);
        
        try {
            this.lottieAnimation = lottie.loadAnimation({
                container: document.getElementById('chat-lottie'),
                renderer: 'svg',
                loop: false,
                autoplay: false,
                path: this.animationPath
            });
            
            this.lottieAnimation.addEventListener('DOMLoaded', () => {
                console.log('✓ Animación Lottie cargada correctamente');
                
                // Cambiar colores personalizados
                const svgElements = document.querySelectorAll('#chat-lottie svg path[stroke="#545454"]');
                svgElements.forEach(el => {
                    el.setAttribute('stroke', '#01B7AF');
                });
                
                // Reproducir una vez al cargar
                this.lottieAnimation.play();
            });
            
            this.lottieAnimation.addEventListener('error', (error) => {
                console.error('❌ Error al cargar animación Lottie:', error);
            });
            
            this.setupHoverAnimation();
            
        } catch (error) {
            console.error('❌ Error al inicializar Lottie:', error);
        }
    }
    
    setupHoverAnimation() {
        if (!this.lottieAnimation) return;
        
        this.chatToggle.addEventListener('mouseenter', () => {
            if (!this.isOpen && this.lottieAnimation) {
                this.lottieAnimation.goToAndPlay(0);
            }
        });
        
        this.chatToggle.addEventListener('mouseleave', () => {
            if (!this.isOpen && this.lottieAnimation) {
                this.lottieAnimation.goToAndStop(this.lottieAnimation.totalFrames - 1);
            }
        });
    }
    
    displayWelcomeTime() {
        const welcomeTimeEl = document.getElementById('welcome-time');
        if (welcomeTimeEl) {
            welcomeTimeEl.textContent = this.getCurrentTime();
        }
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            this.openWindow();
        } else {
            this.closeWindow();
        }
    }
    
    openWindow() {
        console.log('Abriendo ventana de chat');
        this.chatWindow.style.display = 'flex';
        this.chatContainer.classList.remove('chat-closed');
        this.chatContainer.classList.add('chat-open');
        this.chatInput.focus();
        this.scrollToBottom();
        this.isOpen = true;
        
        const closeIcon = document.getElementById('close-icon');
        if (closeIcon) {
            closeIcon.style.display = 'block';
        }
        
        if (this.lottieAnimation) {
            this.lottieAnimation.pause();
        }
    }
    
    closeWindow() {
        console.log('Cerrando ventana de chat');
        this.chatWindow.style.display = 'none';
        this.chatContainer.classList.remove('chat-open');
        this.chatContainer.classList.add('chat-closed');
        this.isOpen = false;
        
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
    
    async sendMessage() {
        const message = this.chatInput.value.trim();
        
        if (!message) return;
        
        // Agregar mensaje del usuario
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
            // Enviar al webhook
            const response = await fetch(this.webhookUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    conversationHistory: this.conversationHistory
                })
            });
            
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            
            const data = await response.json();
            
            // Ocultar indicador
            this.hideTypingIndicator();
            
            // Agregar respuesta del bot
            const botMessage = data.response || data.message || 'Lo siento, hubo un error al procesar tu mensaje.';
            this.addMessage(botMessage, 'bot');
            
            // Guardar en historial
            this.conversationHistory.push({
                role: 'assistant',
                content: botMessage
            });
            
        } catch (error) {
            console.error('Error al enviar mensaje:', error);
            this.hideTypingIndicator();
            this.addMessage('Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta de nuevo.', 'bot');
        }
    }
    
    addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'message-bubble';
        bubbleDiv.textContent = text;
        
        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        timeDiv.textContent = this.getCurrentTime();
        
        messageDiv.appendChild(bubbleDiv);
        messageDiv.appendChild(timeDiv);
        
        this.chatMessages.appendChild(messageDiv);
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
    
    getCurrentTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('braveslab-chat-container')) {
        new WlandChatModal();
    }
});