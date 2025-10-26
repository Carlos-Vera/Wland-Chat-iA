<?php
/**
 * Gestión del bloque de Gutenberg
 *
 * Maneja el registro y renderizado del bloque de chat para Gutenberg
 *
 * @package WlandChat
 * @since 1.0.0
 * @version 1.0.0
 */

namespace WlandChat;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Block
 *
 * Gestiona el bloque de Gutenberg del chat
 *
 * @since 1.0.0
 */
class Block {

    /**
     * Instancia única (patrón Singleton)
     *
     * @since 1.0.0
     * @var Block|null
     */
    private static $instance = null;

    /**
     * Obtener instancia única
     *
     * @since 1.0.0
     * @return Block Instancia única de la clase
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado (patrón Singleton)
     *
     * @since 1.0.0
     */
    private function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    /**
     * Registrar bloque de Gutenberg
     *
     * Registra el bloque 'wland/chat-widget' con sus atributos y callback
     *
     * @since 1.0.0
     * @return void
     */
    public function register_block() {
        // Asegurar que tenemos los archivos necesarios
        $this->ensure_block_files();
        
        register_block_type('wland/chat-widget', array(
            'editor_script' => 'wland-chat-block-editor',
            'editor_style' => 'wland-chat-block-editor-style',
            'style' => 'wland-chat-block-style',
            'render_callback' => array($this, 'render_block'),
            'attributes' => array(
                'webhookUrl' => array(
                    'type' => 'string',
                    'default' => get_option('wland_chat_webhook_url'),
                ),
                'headerTitle' => array(
                    'type' => 'string',
                    'default' => get_option('wland_chat_header_title'),
                ),
                'headerSubtitle' => array(
                    'type' => 'string',
                    'default' => get_option('wland_chat_header_subtitle'),
                ),
                'welcomeMessage' => array(
                    'type' => 'string',
                    'default' => Helpers::get_welcome_message(),
                ),
                'position' => array(
                    'type' => 'string',
                    'default' => get_option('wland_chat_position', 'bottom-right'),
                ),
                'displayMode' => array(
                    'type' => 'string',
                    'default' => get_option('wland_chat_display_mode', 'modal'),
                ),
            ),
        ));
    }
    
    /**
     * Encolar assets del editor de bloques
     *
     * Carga scripts y estilos necesarios para el editor de Gutenberg
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'wland-chat-block-editor',
            WLAND_CHAT_PLUGIN_URL . 'assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            WLAND_CHAT_VERSION
        );
        
        wp_enqueue_style(
            'wland-chat-block-editor-style',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/block_editor.css',
            array(),
            WLAND_CHAT_VERSION
        );

        wp_enqueue_style(
            'wland-chat-block-style',
            WLAND_CHAT_PLUGIN_URL . 'assets/css/block_style.css',
            array(),
            WLAND_CHAT_VERSION
        );
        
        // Localizar datos para el bloque
        wp_localize_script('wland-chat-block-editor', 'wlandChatBlock', array(
            'defaultWebhookUrl' => get_option('wland_chat_webhook_url'),
            'defaultHeaderTitle' => get_option('wland_chat_header_title'),
            'defaultHeaderSubtitle' => get_option('wland_chat_header_subtitle'),
            'defaultWelcomeMessage' => Helpers::get_welcome_message(),
            'defaultPosition' => get_option('wland_chat_position', 'bottom-right'),
            'defaultDisplayMode' => get_option('wland_chat_display_mode', 'modal'),
        ));
    }
    
    /**
     * Renderizar bloque en el frontend
     *
     * Callback para renderizar el HTML del bloque en la página
     *
     * @since 1.0.0
     * @param array $attributes Atributos del bloque
     * @return string HTML del bloque
     */
    public function render_block($attributes) {
        // Verificar si debe mostrarse el chat
        if (!Helpers::should_display_chat()) {
            return '';
        }
        
        // Sanitizar y mezclar con valores por defecto
        $attributes = Helpers::sanitize_block_attributes($attributes);
        
        // Generar ID único
        $unique_id = Helpers::generate_unique_id();
        
        // Extraer variables
        $webhook_url = $attributes['webhookUrl'];
        $header_title = $attributes['headerTitle'];
        $header_subtitle = $attributes['headerSubtitle'];
        $welcome_message = $attributes['welcomeMessage'];
        $position = $attributes['position'];
        $display_mode = $attributes['displayMode'];
        
        // Buffer de salida
        ob_start();
        
        // Cargar plantilla correspondiente
        if ($display_mode === 'fullscreen') {
            include WLAND_CHAT_PLUGIN_DIR . 'templates/screen.php';
        } else {
            include WLAND_CHAT_PLUGIN_DIR . 'templates/modal.php';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Asegurar que existen los archivos del bloque
     *
     * Crea archivos JS y CSS del bloque si no existen
     *
     * @since 1.0.0
     * @return void
     */
    private function ensure_block_files() {
        $js_dir = WLAND_CHAT_PLUGIN_DIR . 'assets/js/';
        $css_dir = WLAND_CHAT_PLUGIN_DIR . 'assets/css/';
        
        // Crear directorios si no existen
        if (!file_exists($js_dir)) {
            wp_mkdir_p($js_dir);
        }
        
        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
        }
        
        // Crear block.js si no existe
        $block_js_file = $js_dir . 'block.js';
        if (!file_exists($block_js_file)) {
            $this->create_block_js($block_js_file);
        }
        
        // Crear block_editor.css si no existe
        $block_editor_css = $css_dir . 'block_editor.css';
        if (!file_exists($block_editor_css)) {
            $this->create_block_editor_css($block_editor_css);
        }

        // Crear block_style.css si no existe
        $block_style_css = $css_dir . 'block_style.css';
        if (!file_exists($block_style_css)) {
            $this->create_block_style_css($block_style_css);
        }
    }
    
    /**
     * Crear archivo block.js
     */
    private function create_block_js($file) {
        $content = "(function(blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var __ = i18n.__;
    var InspectorControls = blockEditor.InspectorControls || wp.editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var TextareaControl = components.TextareaControl;

    blocks.registerBlockType('wland/chat-widget', {
        title: __('Wland Chat iA', 'wland-chat'),
        description: __('Widget de chat con IA de BravesLab', 'wland-chat'),
        icon: 'format-chat',
        category: 'widgets',
        keywords: [__('chat', 'wland-chat'), __('ia', 'wland-chat'), __('asistente', 'wland-chat')],
        supports: {
            html: false,
            multiple: false
        },
        
        attributes: {
            webhookUrl: {
                type: 'string',
                default: wlandChatBlock.defaultWebhookUrl
            },
            headerTitle: {
                type: 'string',
                default: wlandChatBlock.defaultHeaderTitle
            },
            headerSubtitle: {
                type: 'string',
                default: wlandChatBlock.defaultHeaderSubtitle
            },
            welcomeMessage: {
                type: 'string',
                default: wlandChatBlock.defaultWelcomeMessage
            },
            position: {
                type: 'string',
                default: wlandChatBlock.defaultPosition
            },
            displayMode: {
                type: 'string',
                default: wlandChatBlock.defaultDisplayMode
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Configuración del Chat', 'wland-chat'),
                        initialOpen: true
                    },
                        el(TextControl, {
                            label: __('URL del Webhook', 'wland-chat'),
                            value: attributes.webhookUrl,
                            onChange: function(value) {
                                setAttributes({webhookUrl: value});
                            },
                            help: __('URL del webhook de N8N', 'wland-chat')
                        }),
                        el(TextControl, {
                            label: __('Título del Header', 'wland-chat'),
                            value: attributes.headerTitle,
                            onChange: function(value) {
                                setAttributes({headerTitle: value});
                            }
                        }),
                        el(TextControl, {
                            label: __('Subtítulo del Header', 'wland-chat'),
                            value: attributes.headerSubtitle,
                            onChange: function(value) {
                                setAttributes({headerSubtitle: value});
                            }
                        }),
                        el(TextareaControl, {
                            label: __('Mensaje de Bienvenida', 'wland-chat'),
                            value: attributes.welcomeMessage,
                            onChange: function(value) {
                                setAttributes({welcomeMessage: value});
                            },
                            rows: 4
                        }),
                        el(SelectControl, {
                            label: __('Posición del Chat', 'wland-chat'),
                            value: attributes.position,
                            options: [
                                {label: __('Abajo Derecha', 'wland-chat'), value: 'bottom-right'},
                                {label: __('Abajo Izquierda', 'wland-chat'), value: 'bottom-left'},
                                {label: __('Centro', 'wland-chat'), value: 'center'}
                            ],
                            onChange: function(value) {
                                setAttributes({position: value});
                            }
                        }),
                        el(SelectControl, {
                            label: __('Modo de Visualización', 'wland-chat'),
                            value: attributes.displayMode,
                            options: [
                                {label: __('Modal (Ventana emergente)', 'wland-chat'), value: 'modal'},
                                {label: __('Pantalla completa', 'wland-chat'), value: 'fullscreen'}
                            ],
                            onChange: function(value) {
                                setAttributes({displayMode: value});
                            }
                        })
                    )
                ),
                el('div', {
                    className: 'wland-chat-block-preview',
                    style: {
                        border: '2px dashed #01B7AF',
                        borderRadius: '10px',
                        padding: '30px',
                        textAlign: 'center',
                        backgroundColor: '#CEF2EF',
                        color: '#242424'
                    }
                },
                    el('div', {
                        style: {
                            width: '60px',
                            height: '60px',
                            borderRadius: '50%',
                            background: 'linear-gradient(135deg, #01B7AF 0%, #5DD5C7 100%)',
                            margin: '0 auto 15px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '28px'
                        }
                    }, '💬'),
                    el('h3', {
                        style: {
                            margin: '0 0 10px 0',
                            color: '#01B7AF',
                            fontSize: '18px',
                            fontWeight: '600'
                        }
                    }, attributes.headerTitle),
                    el('p', {
                        style: {
                            margin: '0 0 20px 0',
                            fontSize: '14px',
                            opacity: '0.9'
                        }
                    }, attributes.headerSubtitle),
                    el('div', {
                        style: {
                            backgroundColor: 'white',
                            borderRadius: '15px',
                            padding: '15px',
                            marginTop: '15px',
                            border: '1px solid rgba(1, 183, 175, 0.2)',
                            textAlign: 'left'
                        }
                    },
                        el('p', {
                            style: {
                                margin: 0,
                                fontSize: '13px',
                                lineHeight: '1.5'
                            }
                        }, attributes.welcomeMessage.substring(0, 100) + (attributes.welcomeMessage.length > 100 ? '...' : ''))
                    ),
                    el('p', {
                        style: {
                            marginTop: '20px',
                            fontSize: '12px',
                            fontWeight: 'bold',
                            color: '#01B7AF'
                        }
                    }, '📍 ' + __('Posición:', 'wland-chat') + ' ' + 
                        (attributes.position === 'bottom-right' ? __('Abajo Derecha', 'wland-chat') : 
                         attributes.position === 'bottom-left' ? __('Abajo Izquierda', 'wland-chat') : 
                         __('Centro', 'wland-chat')) + ' | ' +
                        (attributes.displayMode === 'modal' ? __('Modal', 'wland-chat') : __('Pantalla Completa', 'wland-chat'))
                    )
                )
            );
        },

        save: function() {
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor || window.wp.editor,
    window.wp.components,
    window.wp.i18n
);";
        
        file_put_contents($file, $content);
    }
    
    /**
     * Crear archivo block-editor.css
     */
    private function create_block_editor_css($file) {
        $content = ".wland-chat-block-preview {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.wland-chat-block-preview h3 {
    font-weight: 600;
}

.wp-block-wland-chat-widget {
    margin: 20px 0;
}

.components-panel__body .components-base-control {
    margin-bottom: 16px;
}";
        
        file_put_contents($file, $content);
    }
    
    /**
     * Crear archivo block-style.css
     */
    private function create_block_style_css($file) {
        $content = "/* Estilos del bloque en el frontend */
.wp-block-wland-chat-widget {
    position: relative;
}

.braveslab-chat-widget-container {
    position: relative;
    width: 100%;
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}";
        
        file_put_contents($file, $content);
    }
}