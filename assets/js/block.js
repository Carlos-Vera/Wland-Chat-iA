/**
 * Bloque de Gutenberg para BravesChat
 *
 * Bloque de pantalla completa: se coloca en la página donde se quiere
 * mostrar el chat embebido. Usa la configuración global del plugin.
 *
 * @package BravesChat
 */

(function (blocks, element, blockEditor, components, i18n) {
    'use strict';

    var el = element.createElement;
    var __ = i18n.__;
    var InspectorControls = blockEditor.InspectorControls || wp.editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextareaControl = components.TextareaControl;

    blocks.registerBlockType('braves/chat-widget', {
        apiVersion: 3,
        title: __('BravesChat — Full Screen', 'braveschat'),
        description: __('Displays the BravesChat widget in full screen on this page. Configure the webhook, colors and title from the plugin panel.', 'braveschat'),
        icon: 'format-chat',
        category: 'widgets',
        keywords: [
            __('chat', 'braveschat'),
            __('ai', 'braveschat'),
            __('assistant', 'braveschat'),
            __('braveslab', 'braveschat'),
            __('fullscreen', 'braveschat')
        ],
        supports: {
            html: false,
            multiple: false,
            reusable: true
        },

        attributes: {
            welcomeMessage: {
                type: 'string',
                default: window.bravesChatBlock ? window.bravesChatBlock.defaultWelcomeMessage : ''
            }
        },

        edit: function (props) {
            var welcomeMessage = props.attributes.welcomeMessage;
            var setAttributes = props.setAttributes;

            var previewText = welcomeMessage
                ? (welcomeMessage.length > 160 ? welcomeMessage.substring(0, 160) + '...' : welcomeMessage)
                : __('(Uses the message configured in the plugin panel)', 'braveschat');

            return el('div', { className: 'wp-block-braves-chat-widget' },

                // Sidebar — solo mensaje de bienvenida
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Welcome message', 'braveschat'),
                        initialOpen: true
                    },
                        el(TextareaControl, {
                            label: __('Welcome message', 'braveschat'),
                            value: welcomeMessage,
                            onChange: function (value) {
                                setAttributes({ welcomeMessage: value });
                            },
                            rows: 5,
                            help: __('First message the user will see on this page. If left empty, the global plugin message will be used.', 'braveschat')
                        })
                    )
                ),

                // Preview card en el editor
                el('div', { className: 'braves-block-card' },

                    // Header
                    el('div', { className: 'braves-block-card__header' },
                        el('div', { className: 'braves-block-card__brand' },
                            el('div', { className: 'braves-block-card__icon' },
                                el('svg', {
                                    width: '16', height: '16',
                                    viewBox: '0 0 24 24', fill: 'none',
                                    stroke: 'currentColor', strokeWidth: '2',
                                    strokeLinecap: 'round', strokeLinejoin: 'round'
                                },
                                    el('path', { d: 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z' })
                                )
                            ),
                            el('span', { className: 'braves-block-card__name' }, 'BravesChat')
                        ),
                        el('span', { className: 'braves-block-card__badge' },
                            __('Full Screen', 'braveschat')
                        )
                    ),

                    // Body — burbuja con mensaje de bienvenida
                    el('div', { className: 'braves-block-card__body' },
                        el('div', { className: 'braves-block-card__message-wrap' },
                            el('div', { className: 'braves-block-card__avatar' },
                                el('svg', {
                                    width: '16', height: '16',
                                    viewBox: '0 0 24 24', fill: 'none',
                                    stroke: '#ffffff', strokeWidth: '2',
                                    strokeLinecap: 'round', strokeLinejoin: 'round'
                                },
                                    el('path', { d: 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z' })
                                )
                            ),
                            el('div', { className: 'braves-block-card__bubble' },
                                el('p', { className: 'braves-block-card__bubble-text' }, previewText)
                            )
                        )
                    ),

                    // Footer — nota informativa
                    el('div', { className: 'braves-block-card__footer' },
                        el('svg', {
                            width: '13', height: '13',
                            viewBox: '0 0 24 24', fill: 'none',
                            stroke: 'currentColor', strokeWidth: '2',
                            strokeLinecap: 'round', strokeLinejoin: 'round',
                            style: { flexShrink: 0 }
                        },
                            el('circle', { cx: '12', cy: '12', r: '10' }),
                            el('line', { x1: '12', y1: '8', x2: '12', y2: '12' }),
                            el('line', { x1: '12', y1: '16', x2: '12.01', y2: '16' })
                        ),
                        el('span', {},
                            __('Webhook, title, colors and position are configured from the BravesChat panel.', 'braveschat')
                        )
                    )
                )
            );
        },

        save: function () {
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
