<?php
/**
 * panatella para modo Pantalla compleat
 * 
 * @package WlandChat
 */

if (!defined('ABSPATH')) {
    exit;
}

// Obtener el icono seleccionado
$chat_icon = get_option('wland_chat_chat_icon', 'robot-chat');
$icon_path = WLAND_CHAT_PLUGIN_URL . 'assets/media/chat-icons/' . $chat_icon . '.svg';
?>

<div id="<?php echo esc_attr($unique_id); ?>" class="braveslab-chat-widget-container position-<?php echo esc_attr($position); ?>">
    <div id="braveslab-chat-container" class="chat-closed">
        <button id="chat-toggle" title="<?php esc_attr_e('Habla con nuestro asistente IA', 'wland-chat'); ?>">
            <img id="chat-icon" src="<?php echo esc_url($icon_path); ?>" alt="<?php esc_attr_e('Chat', 'wland-chat'); ?>">
            <span id="close-icon" style="display: none;">✕</span>
        </button>
        
        <div id="chat-window">
            <div id="chat-header">
                <div>
                    <h3><?php echo esc_html($header_title); ?></h3>
                    <p><?php echo esc_html($header_subtitle); ?></p>
                </div>
                <button id="close-chat" aria-label="<?php esc_attr_e('Cerrar chat', 'wland-chat'); ?>">×</button>
            </div>
            
            <div id="chat-messages">
                <div class="message bot">
                    <div class="message-bubble">
                        <?php echo wp_kses_post(nl2br($welcome_message)); ?>
                    </div>
                    <div class="message-time" id="welcome-time"></div>
                </div>
            </div>
            
            <div class="typing-indicator" id="typing-indicator">
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
            
            <div id="chat-input-container">
                <input 
                    type="text" 
                    id="chat-input" 
                    placeholder="<?php esc_attr_e('Escribe tu mensaje...', 'wland-chat'); ?>"
                    aria-label="<?php esc_attr_e('Escribe tu mensaje', 'wland-chat'); ?>"
                />
                <button id="send-button" disabled aria-label="<?php esc_attr_e('Enviar mensaje', 'wland-chat'); ?>">
                    <span>➤</span>
                </button>
            </div>
        </div>
    </div>
</div>