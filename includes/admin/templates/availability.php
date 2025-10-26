<?php
/**
 * Availability Page Template
 *
 * Página de Horarios con diseño Bentō
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
                    <h1 class="wland-page-title"><?php _e('Horarios', 'wland-chat'); ?></h1>
                    <p class="wland-page-description">
                        <?php _e('Configura los horarios de disponibilidad del chat. Cuando está activado, el chat solo estará disponible durante el horario especificado.', 'wland-chat'); ?>
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

                <!-- Availability Form -->
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wland_chat_settings');
                    // Preservar opciones no mostradas en este formulario
                    \WlandChat\Settings::get_instance()->render_hidden_fields(array(
                        'excluded_pages',
                        'availability_enabled',
                        'availability_start',
                        'availability_end',
                        'availability_timezone',
                        'availability_message'
                    ));
                    ?>

                    <!-- Horarios Section -->
                    <div class="wland-section">
                        <h2 class="wland-section__title">
                            <?php _e('Configuración de Horarios', 'wland-chat'); ?>
                        </h2>

                        <div class="wland-card-grid wland-card-grid--2-cols">

                            <!-- Card: Habilitar Horarios -->
                            <?php
                            $availability_enabled = get_option($option_prefix . 'availability_enabled', false);

                            ob_start();
                            ?>
                            <label class="wland-toggle-wrapper">
                                <input type="checkbox"
                                       id="<?php echo esc_attr($option_prefix . 'availability_enabled'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'availability_enabled'); ?>"
                                       value="1"
                                       <?php checked(1, $availability_enabled); ?>
                                       class="wland-toggle-input">
                                <span class="wland-toggle-slider"></span>
                            </label>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Activar restricción por horarios de atención', 'wland-chat'); ?>
                            </p>
                            <?php
                            $toggle_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Habilitar Horarios', 'wland-chat'),
                                'description' => __('Activa las restricciones de horario para el chat.', 'wland-chat'),
                                'content' => $toggle_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                            <!-- Card: Zona Horaria -->
                            <?php
                            $current_timezone = get_option($option_prefix . 'availability_timezone', 'Europe/Madrid');
                            $timezones = timezone_identifiers_list();

                            ob_start();
                            ?>
                            <select name="<?php echo esc_attr($option_prefix . 'availability_timezone'); ?>"
                                    id="<?php echo esc_attr($option_prefix . 'availability_timezone'); ?>"
                                    class="wland-select"
                                    style="width: 100%;">
                                <?php foreach ($timezones as $timezone): ?>
                                    <option value="<?php echo esc_attr($timezone); ?>"
                                            <?php selected($current_timezone, $timezone); ?>>
                                        <?php echo esc_html(str_replace('_', ' ', $timezone)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Zona horaria de referencia para los horarios configurados.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $timezone_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Zona Horaria', 'wland-chat'),
                                'description' => __('Zona horaria de referencia para el horario.', 'wland-chat'),
                                'content' => $timezone_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                            <!-- Card: Hora de Inicio -->
                            <?php
                            $availability_start = get_option($option_prefix . 'availability_start', '09:00');

                            ob_start();
                            ?>
                            <input type="time"
                                   id="<?php echo esc_attr($option_prefix . 'availability_start'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'availability_start'); ?>"
                                   value="<?php echo esc_attr($availability_start); ?>"
                                   class="wland-input"
                                   style="width: 100%;">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Hora de inicio del horario de atención (formato 24h).', 'wland-chat'); ?>
                            </p>
                            <?php
                            $start_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Hora de Inicio', 'wland-chat'),
                                'description' => __('Hora en que el chat comienza a estar disponible.', 'wland-chat'),
                                'content' => $start_content,
                            ));
                            ?>

                            <!-- Card: Hora de Fin -->
                            <?php
                            $availability_end = get_option($option_prefix . 'availability_end', '18:00');

                            ob_start();
                            ?>
                            <input type="time"
                                   id="<?php echo esc_attr($option_prefix . 'availability_end'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'availability_end'); ?>"
                                   value="<?php echo esc_attr($availability_end); ?>"
                                   class="wland-input"
                                   style="width: 100%;">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Hora de fin del horario de atención (formato 24h).', 'wland-chat'); ?>
                            </p>
                            <?php
                            $end_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Hora de Fin', 'wland-chat'),
                                'description' => __('Hora en que el chat deja de estar disponible.', 'wland-chat'),
                                'content' => $end_content,
                            ));
                            ?>

                            <!-- Card: Mensaje Fuera de Horario -->
                            <?php
                            $availability_message = get_option($option_prefix . 'availability_message', __('Nuestro horario de atención es de 9:00 a 18:00. Déjanos tu mensaje y te responderemos lo antes posible.', 'wland-chat'));

                            ob_start();
                            ?>
                            <textarea id="<?php echo esc_attr($option_prefix . 'availability_message'); ?>"
                                      name="<?php echo esc_attr($option_prefix . 'availability_message'); ?>"
                                      rows="4"
                                      class="wland-textarea"
                                      style="width: 100%;"
                                      placeholder="<?php echo esc_attr(__('Mensaje fuera de horario de atención', 'wland-chat')); ?>"><?php echo esc_textarea($availability_message); ?></textarea>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Mensaje que se mostrará cuando el usuario intente usar el chat fuera del horario de atención.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $message_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Mensaje Fuera de Horario', 'wland-chat'),
                                'description' => __('Texto que verán los usuarios fuera del horario de atención.', 'wland-chat'),
                                'content' => $message_content,
                                'custom_class' => 'wland-card--full-width',
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
