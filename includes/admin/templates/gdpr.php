<?php
/**
 * GDPR Page Template
 *
 * Página de GDPR con diseño Bentō
 *
 * @package WlandChat
 * @subpackage Admin\Templates
 * @since 1.2.1
 */

use WlandChat\Admin\Admin_Header;
use WlandChat\Admin\Admin_Sidebar;
use WlandChat\Admin\Template_Helpers;

if (!defined('ABSPATH')) {
    exit;
}

// Verificar permisos
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos para acceder a esta página.', 'wland-chat'));
}

// Obtener instancias de componentes
$header = Admin_Header::get_instance();
$sidebar = Admin_Sidebar::get_instance();

// Obtener estado de configuración
$config_status = Template_Helpers::get_config_status();

// Detectar si se guardaron los ajustes
$settings_updated = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';

// Prefijo de opciones
$option_prefix = 'wland_chat_';
?>

<div class="wrap wland-admin-wrap">
    <div class="wland-admin-container">

        <?php
        // Renderizar header
        $header->render(array(
            'show_logo' => true,
            'show_version' => true,
        ));
        ?>

        <div class="wland-admin-body">

            <?php
            // Renderizar sidebar
            $sidebar->render($current_page);
            ?>

            <div class="wland-admin-content">

                <!-- Page Header -->
                <div class="wland-page-header">
                    <h1 class="wland-page-title"><?php _e('GDPR', 'wland-chat'); ?></h1>
                    <p class="wland-page-description">
                        <?php _e('Configure el banner de consentimiento de cookies para cumplir con las regulaciones GDPR. El sistema utiliza cookies persistentes con fingerprinting del navegador.', 'wland-chat'); ?>
                    </p>
                </div>

                <!-- Configuration Status Section -->
                <?php if (!$config_status['is_configured']): ?>
                <div class="wland-section wland-section--warning">
                    <?php
                    Template_Helpers::notice(
                        '<strong>' . __('Acción requerida:', 'wland-chat') . '</strong> ' .
                        __('Para que el chat funcione, necesitas configurar la URL del webhook en la página de ajustes.', 'wland-chat'),
                        'warning'
                    );
                    ?>
                </div>
                <?php endif; ?>

                <!-- Success Notice -->
                <?php if ($settings_updated): ?>
                <div class="wland-section">
                    <?php
                    Template_Helpers::notice(
                        __('Configuración guardada correctamente.', 'wland-chat'),
                        'success'
                    );
                    ?>
                </div>
                <?php endif; ?>

                <!-- GDPR Form -->
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wland_chat_settings');
                    // Preservar opciones no mostradas en este formulario
                    \WlandChat\Settings::get_instance()->render_hidden_fields(array(
                        'gdpr_enabled',
                        'gdpr_message',
                        'gdpr_accept_text'
                    ));
                    ?>

                    <!-- GDPR Section -->
                    <div class="wland-section">
                        <h2 class="wland-section__title">
                            <?php _e('Compliance GDPR / Cookies', 'wland-chat'); ?>
                        </h2>

                        <div class="wland-card-grid wland-card-grid--2-cols">

                            <!-- Card: Habilitar Banner GDPR -->
                            <?php
                            $gdpr_enabled = get_option($option_prefix . 'gdpr_enabled', false);

                            ob_start();
                            ?>
                            <label class="wland-toggle-wrapper">
                                <input type="checkbox"
                                       id="<?php echo esc_attr($option_prefix . 'gdpr_enabled'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'gdpr_enabled'); ?>"
                                       value="1"
                                       <?php checked(1, $gdpr_enabled); ?>
                                       class="wland-toggle-input">
                                <span class="wland-toggle-slider"></span>
                            </label>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Mostrar banner de consentimiento de cookies. El consentimiento se guarda en localStorage.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $toggle_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Habilitar Banner GDPR', 'wland-chat'),
                                'description' => __('Muestra un banner de consentimiento antes de crear cookies.', 'wland-chat'),
                                'content' => $toggle_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                            <!-- Card: Mensaje del Banner -->
                            <?php
                            $gdpr_message = get_option($option_prefix . 'gdpr_message', __('Este sitio utiliza cookies para mejorar tu experiencia y proporcionar un servicio de chat personalizado. Al continuar navegando, aceptas nuestra política de cookies.', 'wland-chat'));

                            ob_start();
                            ?>
                            <textarea id="<?php echo esc_attr($option_prefix . 'gdpr_message'); ?>"
                                      name="<?php echo esc_attr($option_prefix . 'gdpr_message'); ?>"
                                      rows="4"
                                      class="wland-textarea"
                                      style="width: 100%;"
                                      placeholder="<?php echo esc_attr(__('Este sitio utiliza cookies...', 'wland-chat')); ?>"><?php echo esc_textarea($gdpr_message); ?></textarea>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Mensaje que se mostrará en el banner de cookies explicando el uso de cookies para la sesión del chat.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $message_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Mensaje del Banner', 'wland-chat'),
                                'description' => __('Texto informativo sobre el uso de cookies en el chat.', 'wland-chat'),
                                'content' => $message_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                            <!-- Card: Texto del Botón de Aceptar -->
                            <?php
                            $gdpr_accept_text = get_option($option_prefix . 'gdpr_accept_text', __('Aceptar', 'wland-chat'));

                            ob_start();
                            ?>
                            <input type="text"
                                   id="<?php echo esc_attr($option_prefix . 'gdpr_accept_text'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'gdpr_accept_text'); ?>"
                                   value="<?php echo esc_attr($gdpr_accept_text); ?>"
                                   class="wland-input"
                                   style="width: 100%;"
                                   placeholder="<?php echo esc_attr(__('Aceptar', 'wland-chat')); ?>">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Texto del botón para aceptar las cookies (ej: "Aceptar", "Entendido", "Acepto").', 'wland-chat'); ?>
                            </p>
                            <?php
                            $button_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Texto del Botón de Aceptar', 'wland-chat'),
                                'description' => __('Etiqueta del botón de aceptación de cookies.', 'wland-chat'),
                                'content' => $button_content,
                            ));
                            ?>

                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="wland-section wland-section--actions">
                        <div class="wland-button-group">
                            <?php submit_button(
                                __('Guardar', 'wland-chat'),
                                'primary wland-button wland-button--primary',
                                'submit',
                                false
                            ); ?>
                        </div>
                    </div>

                </form>

            </div><!-- .wland-admin-content -->

        </div><!-- .wland-admin-body -->

    </div><!-- .wland-admin-container -->
</div><!-- .wrap -->
