/**
 * JavaScript para el chat en modo Modal
 * 
 * @package WlandChat
 */

(function() {
    'use strict';
    
    // Evitar múltiples inicializaciones
    if (window.WlandChatInitialized) {
        console.log('Wland Chat ya inicializado');
        return;
    }
    window.WlandChatInitialized = true;
    
    class WlandChat {
        constructor() {
            console.log('Inicializando Wland Chat...');
            
            // Usar configuración global si existe
            this.webhookUrl = window.wlandChatConfig?.webhookUrl || '';
            this.animationPath = window.wlandChatConfig?.animationPath || '';
            this.sessionId = this.generateSessionId();
            
            console.log('Webhook URL:', this.webhookUrl);
            console.log('Animation Path:', this.animationPath);
            
            this.initializeElements();
        }
        
        initializeElements() {
            const maxAttempts = 10;
            let attempts = 0;
            
            const tryInitialize = () => {
                attempts++;
                console.log(`Intento de inicialización #${attempts}`);
                
                this.chatContainer = document.getElementById('braveslab-chat-container');
                this.chatToggle = document.getElementById('chat-toggle');
                this.chatWindow = document.getElementById('chat-window');
                this.closeChat = document.getElementById('close-chat');
                this.chatMessages = document.getElementById('chat-messages');
                this.chatInput = document.getElementById('chat-input');
                this.sendButton = document.getElementById('send-button');
                this.typingIndicator = document.getElementById('typing-indicator');
                
                if (this.chatContainer && this.chatToggle && this.chatWindow && 
                    this.closeChat && this.chatMessages && this.chatInput && 
                    this.sendButton && this.typingIndicator) {
                    
                    console.log('✓ Todos los elementos encontrados');
                    this.isOpen = false;
                    this.isTyping = false;
                    this.init();
                    return true;
                }
                
                if (attempts < maxAttempts) {
                    console.log('Reintentando en 500ms...');
                    setTimeout(tryInitialize, 500);
                } else {
                    console.error('❌ No se pudieron encontrar todos los elementos después de', maxAttempts, 'intentos');
                }
                
                return false;
            };
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', tryInitialize);
            } else {
                tryInitialize();
            }
        }
        
        generateSessionId() {
            return 'wland_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
        }
        
        init() {
            console.log('Configurando eventos...');
            
            // Configurar hora de bienvenida
            const welcomeTime = document.getElementById('welcome-time');
            if (welcomeTime) {
                welcomeTime.textContent = this.formatTime(new Date());
            }
            
            // Eventos del botón toggle
            this.chatToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleChat();
            });
            
            this.chatToggle.addEventListener('touchstart', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleChat();
            });
            
            // Evento cerrar
            this.closeChat.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.closeWindow();
            });
            
            // Eventos del input
            this.chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
            
            this.chatInput.addEventListener('input', () => this.toggleSendButton());
            
            // Evento botón enviar
            this.sendButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.sendMessage();
            });
            
            // Scroll al hacer focus
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
            
            console.log('Cargando animación Lottie...');
            
            try {
                this.lottieAnimation = lottie.loadAnimation({
                    container: document.getElementById('chat-lottie'),
                    renderer: 'svg',
                    loop: false,
                    autoplay: false,
                    path: this.animationPath
                });
                
                this.lottieAnimation.addEventListener('DOMLoaded', () => {
                    console.log('✓ Animación Lottie cargada');
                    
                    // Cambiar colores
                    const svgElements = document.querySelectorAll('#chat-lottie svg path[stroke="#545454"]');
                    svgElements.forEach(el => {
                        el.setAttribute('stroke', '#01B7AF');
                    });
                    
                    this.lottieAnimation.play();
                });
                
                this.setupHoverAnimation();
                
            } catch (error) {
                console.error('Error cargando Lottie:', error);
            }
        }
        
        setupHoverAnimation() {
            if (!this.lottieAnimation) return;
            
            this.chatToggle.addEventListener('mouseenter', () => {
                if (!this.isOpen) {
                    this.lottieAnimation.goToAndPlay(0);
                }
            });
            
            this.chatToggle.addEventListener('mouseleave', () => {
                if (!this.isOpen) {
                    this.lottieAnimation.goToAndStop(this.lottieAnimation.totalFrames - 1);
                }
            });
        }
        
        toggleChat() {
            console.log('Toggle chat, estado actual:', this.isOpen);
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
                this.lottieAnimation.goToAndStop(0);
            }
        }
        
        toggleSendButton() {
            const hasText = this.chatInput.value.trim().length > 0;
            this.sendButton.disabled = !hasText || this.isTyping;
        }
        
        async sendMessage() {
            const message = this.chatInput.value.trim();
            if (!message || this.isTyping) return;
            
            console.log('Enviando mensaje:', message);
            
            this.addMessage(message, 'user');
            this.chatInput.value = '';
            this.toggleSendButton();
            this.showTyping();
            
            try {
                const payload = {
                    message: message,
                    sessionId: this.sessionId
                };
                
                console.log('Payload:', payload);
                console.log('Webhook URL:', this.webhookUrl);
                
                const response = await fetch(this.webhookUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    mode: 'cors'
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                console.log('Response:', responseText);
                
                let botMessage = '';
                
                try {
                    const data = JSON.parse(responseText);
                    botMessage = data.output || data.Output || data.response || 
                                data.message || data.text || responseText;
                } catch (e) {
                    botMessage = responseText;
                }
                
                // Procesar formato de enlaces [texto](url)
                botMessage = botMessage.replace(
                    /\[([^\]]+)\]\(([^)]+)\)/g,
                    '<a href="$2" target="_blank" rel="noopener noreferrer" style="color: #01B7AF; text-decoration: underline;">$1</a>'
                );
                
                this.hideTyping();
                this.addMessage(botMessage, 'bot');
                
            } catch (error) {
                console.error('Error:', error);
                this.hideTyping();
                this.addMessage(
                    'Hay un problema de conexión. Por favor, contáctanos directamente en BravesLab.com o intenta de nuevo en unos segundos.',
                    'bot'
                );
            }
        }
        
        addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'message-bubble';
            bubbleDiv.innerHTML = this.processLinks(text);
            
            const timeDiv = document.createElement('div');
            timeDiv.className = 'message-time';
            timeDiv.textContent = this.formatTime(new Date());
            
            messageDiv.appendChild(bubbleDiv);
            messageDiv.appendChild(timeDiv);
            this.chatMessages.appendChild(messageDiv);
            
            this.scrollToBottom();
        }
        
        processLinks(text) {
            const escaped = text.replace(/&/g, '&amp;')
                               .replace(/</g, '&lt;')
                               .replace(/>/g, '&gt;');
            
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return escaped.replace(
                urlRegex,
                '<a href="$1" target="_blank" rel="noopener noreferrer" style="color: #01B7AF; text-decoration: underline;">$1</a>'
            );
        }
        
        showTyping() {
            this.isTyping = true;
            this.typingIndicator.style.display = 'flex';
            this.toggleSendButton();
            this.scrollToBottom();
        }
        
        hideTyping() {
            this.isTyping = false;
            this.typingIndicator.style.display = 'none';
            this.toggleSendButton();
        }
        
        scrollToBottom() {
            setTimeout(() => {
                this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
            }, 100);
        }
        
        formatTime(date) {
            return date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM listo, inicializando Wland Chat...');
            new WlandChat();
        });
    } else {
        console.log('DOM ya listo, inicializando Wland Chat...');
        new WlandChat();
    }
    
})();