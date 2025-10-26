<?php
/**
 * Settings Page Template
 *
 * Página de Ajustes con diseño Bentō
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
                    <h1 class="wland-page-title"><?php _e('Ajustes', 'wland-chat'); ?></h1>
                    <p class="wland-page-description">
                        <?php _e('Configura los ajustes principales del chat: habilitación global, webhook, token de autenticación y páginas excluidas.', 'wland-chat'); ?>
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

                <!-- Settings Form -->
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wland_chat_settings');
                    // Preservar opciones no mostradas en este formulario
                    \WlandChat\Settings::get_instance()->render_hidden_fields(array(
                        'global_enable',
                        'webhook_url',
                        'n8n_auth_token'
                    ));
                    ?>

                    <!-- Configuración General Section -->
                    <div class="wland-section">
                        <h2 class="wland-section__title">
                            <?php _e('Configuración General', 'wland-chat'); ?>
                        </h2>

                        <div class="wland-card-grid wland-card-grid--2-cols">

                            <!-- Card: Mostrar en toda la web -->
                            <?php
                            $global_enable = get_option($option_prefix . 'global_enable', false);

                            ob_start();
                            ?>
                            <label class="wland-toggle-wrapper">
                                <input type="checkbox"
                                       id="<?php echo esc_attr($option_prefix . 'global_enable'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'global_enable'); ?>"
                                       value="1"
                                       <?php checked(1, $global_enable); ?>
                                       class="wland-toggle-input">
                                <span class="wland-toggle-slider"></span>
                            </label>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Mostrar el chat en todas las páginas del sitio web', 'wland-chat'); ?>
                            </p>
                            <?php
                            $toggle_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Mostrar en toda la web', 'wland-chat'),
                                'description' => __('Habilita el chat globalmente en todas las páginas del sitio web.', 'wland-chat'),
                                'content' => $toggle_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                            <!-- Card: URL del Webhook -->
                            <?php
                            $webhook_url = get_option($option_prefix . 'webhook_url', 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat');

                            ob_start();
                            ?>
                            <input type="url"
                                   id="<?php echo esc_attr($option_prefix . 'webhook_url'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'webhook_url'); ?>"
                                   value="<?php echo esc_attr($webhook_url); ?>"
                                   class="wland-input"
                                   style="width: 100%;"
                                   placeholder="https://flow.braveslab.com/webhook/...">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('URL del webhook de N8N para procesar los mensajes del chat.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $webhook_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('URL del Webhook', 'wland-chat'),
                                'description' => __('URL del endpoint de N8N donde se procesarán los mensajes.', 'wland-chat'),
                                'content' => $webhook_content,
                            ));
                            ?>

                            <!-- Card: Token de Autenticación N8N -->
                            <?php
                            $n8n_token = get_option($option_prefix . 'n8n_auth_token', '');

                            ob_start();
                            ?>
                            <input type="password"
                                   id="<?php echo esc_attr($option_prefix . 'n8n_auth_token'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'n8n_auth_token'); ?>"
                                   value="<?php echo esc_attr($n8n_token); ?>"
                                   class="wland-input"
                                   style="width: 100%;"
                                   autocomplete="new-password"
                                   placeholder="••••••••••••••••">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Token secreto para autenticar las peticiones al webhook (Header X-N8N-Auth). Déjalo vacío si no usas autenticación.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $token_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Token de Autenticación N8N', 'wland-chat'),
                                'description' => __('Token de seguridad para verificar las peticiones al webhook.', 'wland-chat'),
                                'content' => $token_content,
                            ));
                            ?>

                            <!-- Card: Páginas Excluidas -->
                            <?php
                            $excluded_pages = get_option($option_prefix . 'excluded_pages', array());
                            $all_pages = get_pages();

                            ob_start();
                            ?>
                            <select name="<?php echo esc_attr($option_prefix . 'excluded_pages'); ?>[]"
                                    id="<?php echo esc_attr($option_prefix . 'excluded_pages'); ?>"
                                    multiple
                                    size="8"
                                    class="wland-select"
                                    style="width: 100%; height: auto;">
                                <?php foreach ($all_pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>"
                                            <?php echo in_array($page->ID, (array)$excluded_pages) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples páginas.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $pages_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Páginas Excluidas', 'wland-chat'),
                                'description' => __('Selecciona las páginas donde NO quieres que aparezca el chat.', 'wland-chat'),
                                'content' => $pages_content,
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
