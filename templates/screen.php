<?php
/**
 * panatella para modo Pantalla compleat
 * 
 * @package WlandChat
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="<?php echo esc_attr($unique_id); ?>" class="braveslab-chat-widget-container position-<?php echo esc_attr($position); ?>">
    <div id="braveslab-chat-container" class="chat-closed">
        <button id="chat-toggle" title="<?php esc_attr_e('Habla con nuestro asistente IA', 'wland-chat'); ?>">
            <div id="chat-lottie"></div>
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

<script type="text/javascript">
/* Pasar datos PHP a JavaScript */
var wlandChatConfig = {
    webhookUrl: <?php echo json_encode($webhook_url); ?>,
    animationPath: <?php echo json_encode(WLAND_CHAT_PLUGIN_URL . 'assets/media/chat.json'); ?>,
    uniqueId: <?php echo json_encode($unique_id); ?>
};
</script>