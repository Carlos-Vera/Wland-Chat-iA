/**
 * Bloque de Gutenberg para Wland Chat iA
 * 
 * @package WlandChat
 */

(function(blocks, element, blockEditor, components, i18n) {
    'use strict';
    
    var el = element.createElement;
    var __ = i18n.__;
    var InspectorControls = blockEditor.InspectorControls || wp.editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var TextareaControl = components.TextareaControl;

    /**
     * Registrar el bloque
     */
    blocks.registerBlockType('wland/chat-widget', {
        title: __('Wland Chat iA', 'wland-chat'),
        description: __('Widget de chat con IA de BravesLab para integración con N8N', 'wland-chat'),
        icon: 'format-chat',
        category: 'widgets',
        keywords: [
            __('chat', 'wland-chat'),
            __('ia', 'wland-chat'),
            __('asistente', 'wland-chat'),
            __('braveslab', 'wland-chat'),
            __('ai', 'wland-chat')
        ],
        supports: {
            html: false,
            multiple: false,
            reusable: true
        },
        
        attributes: {
            webhookUrl: {
                type: 'string',
                default: window.wlandChatBlock?.defaultWebhookUrl || 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat'
            },
            headerTitle: {
                type: 'string',
                default: window.wlandChatBlock?.defaultHeaderTitle || 'BravesLab AI Assistant'
            },
            headerSubtitle: {
                type: 'string',
                default: window.wlandChatBlock?.defaultHeaderSubtitle || 'Artificial Intelligence Marketing Agency'
            },
            welcomeMessage: {
                type: 'string',
                default: window.wlandChatBlock?.defaultWelcomeMessage || '¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?'
            },
            position: {
                type: 'string',
                default: window.wlandChatBlock?.defaultPosition || 'bottom-right'
            },
            displayMode: {
                type: 'string',
                default: window.wlandChatBlock?.defaultDisplayMode || 'modal'
            }
        },

        /**
         * Función de edición (en el editor de Gutenberg)
         */
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Función para obtener el nombre legible de la posición
            function getPositionLabel(position) {
                switch(position) {
                    case 'bottom-right':
                        return __('Abajo Derecha', 'wland-chat');
                    case 'bottom-left':
                        return __('Abajo Izquierda', 'wland-chat');
                    case 'center':
                        return __('Centro', 'wland-chat');
                    default:
                        return position;
                }
            }

            // Función para obtener el nombre legible del modo
            function getDisplayModeLabel(mode) {
                switch(mode) {
                    case 'modal':
                        return __('Modal', 'wland-chat');
                    case 'fullscreen':
                        return __('Pantalla Completa', 'wland-chat');
                    default:
                        return mode;
                }
            }

            return el('div', { className: 'wp-block-wland-chat-widget' },
                // Panel de Inspector (sidebar derecho)
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Configuración del Chat', 'wland-chat'),
                        initialOpen: true
                    },
                        // Webhook URL
                        el(TextControl, {
                            label: __('URL del Webhook', 'wland-chat'),
                            value: attributes.webhookUrl,
                            onChange: function(value) {
                                setAttributes({ webhookUrl: value });
                            },
                            help: __('URL del webhook de N8N para procesar los mensajes', 'wland-chat'),
                            type: 'url'
                        }),
                        
                        // Título del Header
                        el(TextControl, {
                            label: __('Título del Header', 'wland-chat'),
                            value: attributes.headerTitle,
                            onChange: function(value) {
                                setAttributes({ headerTitle: value });
                            },
                            help: __('Título que aparecerá en la cabecera del chat', 'wland-chat')
                        }),
                        
                        // Subtítulo del Header
                        el(TextControl, {
                            label: __('Subtítulo del Header', 'wland-chat'),
                            value: attributes.headerSubtitle,
                            onChange: function(value) {
                                setAttributes({ headerSubtitle: value });
                            },
                            help: __('Subtítulo descriptivo del chat', 'wland-chat')
                        }),
                        
                        // Mensaje de Bienvenida
                        el(TextareaControl, {
                            label: __('Mensaje de Bienvenida', 'wland-chat'),
                            value: attributes.welcomeMessage,
                            onChange: function(value) {
                                setAttributes({ welcomeMessage: value });
                            },
                            rows: 5,
                            help: __('Primer mensaje que verá el usuario al abrir el chat', 'wland-chat')
                        }),
                        
                        // Posición del Chat
                        el(SelectControl, {
                            label: __('Posición del Chat', 'wland-chat'),
                            value: attributes.position,
                            options: [
                                { label: __('Abajo Derecha', 'wland-chat'), value: 'bottom-right' },
                                { label: __('Abajo Izquierda', 'wland-chat'), value: 'bottom-left' },
                                { label: __('Centro', 'wland-chat'), value: 'center' }
                            ],
                            onChange: function(value) {
                                setAttributes({ position: value });
                            },
                            help: __('Ubicación del botón de chat en la página', 'wland-chat')
                        }),
                        
                        // Modo de Visualización
                        el(SelectControl, {
                            label: __('Modo de Visualización', 'wland-chat'),
                            value: attributes.displayMode,
                            options: [
                                { label: __('Modal (Ventana emergente)', 'wland-chat'), value: 'modal' },
                                { label: __('Pantalla completa', 'wland-chat'), value: 'fullscreen' }
                            ],
                            onChange: function(value) {
                                setAttributes({ displayMode: value });
                            },
                            help: __('Cómo se mostrará la ventana de chat', 'wland-chat')
                        })
                    )
                ),
                
                // Vista previa en el editor
                el('div', {
                    className: 'wland-chat-block-preview',
                    style: {
                        border: '2px dashed #01B7AF',
                        borderRadius: '10px',
                        padding: '30px',
                        textAlign: 'center',
                        backgroundColor: '#CEF2EF',
                        color: '#242424',
                        minHeight: '300px',
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }
                },
                    // Icono del chat
                    el('div', {
                        style: {
                            width: '70px',
                            height: '70px',
                            borderRadius: '50%',
                            background: 'linear-gradient(135deg, #01B7AF 0%, #5DD5C7 100%)',
                            margin: '0 auto 20px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '32px',
                            boxShadow: '0 4px 15px rgba(1, 183, 175, 0.3)'
                        }
                    }, '💬'),
                    
                    // Título
                    el('h3', {
                        style: {
                            margin: '0 0 10px 0',
                            color: '#01B7AF',
                            fontSize: '22px',
                            fontWeight: '600'
                        }
                    }, attributes.headerTitle),
                    
                    // Subtítulo
                    el('p', {
                        style: {
                            margin: '0 0 25px 0',
                            fontSize: '15px',
                            opacity: '0.9',
                            fontWeight: '500'
                        }
                    }, attributes.headerSubtitle),
                    
                    // Mensaje de bienvenida (preview)
                    el('div', {
                        style: {
                            backgroundColor: 'white',
                            borderRadius: '15px',
                            padding: '20px',
                            marginTop: '15px',
                            marginBottom: '20px',
                            border: '1px solid rgba(1, 183, 175, 0.2)',
                            textAlign: 'left',
                            maxWidth: '500px',
                            width: '100%',
                            boxShadow: '0 2px 8px rgba(1, 183, 175, 0.1)'
                        }
                    },
                        el('p', {
                            style: {
                                margin: 0,
                                fontSize: '14px',
                                lineHeight: '1.6',
                                color: '#242424'
                            }
                        }, attributes.welcomeMessage.substring(0, 150) + 
                           (attributes.welcomeMessage.length > 150 ? '...' : ''))
                    ),
                    
                    // Información de configuración
                    el('div', {
                        style: {
                            marginTop: '20px',
                            padding: '15px',
                            backgroundColor: 'rgba(1, 183, 175, 0.1)',
                            borderRadius: '8px',
                            fontSize: '13px',
                            fontWeight: '600',
                            color: '#01B7AF',
                            width: '100%',
                            maxWidth: '500px'
                        }
                    },
                        el('div', { 
                            style: { 
                                marginBottom: '8px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            } 
                        }, 
                            '📍 ', __('Posición:', 'wland-chat'), ' ',
                            el('strong', { 
                                style: { marginLeft: '5px' } 
                            }, getPositionLabel(attributes.position))
                        ),
                        el('div', { 
                            style: { 
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            } 
                        }, 
                            '🖥️ ', __('Modo:', 'wland-chat'), ' ',
                            el('strong', { 
                                style: { marginLeft: '5px' } 
                            }, getDisplayModeLabel(attributes.displayMode))
                        )
                    ),
                    
                    // Nota informativa
                    el('p', {
                        style: {
                            marginTop: '20px',
                            fontSize: '12px',
                            color: '#666',
                            fontStyle: 'italic'
                        }
                    }, __('Esta es una vista previa. El chat aparecerá en el frontend con la configuración indicada.', 'wland-chat'))
                )
            );
        },

        /**
         * Función de guardado (no guardamos nada en el contenido)
         * El renderizado se hace en PHP
         */
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
);