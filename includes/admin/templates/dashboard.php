<?php
/**
 * Template del Dashboard Admin
 *
 * Página principal moderno con diseño Bentō
 *
 * @package WlandChat
 * @version 1.2.0
 */

use WlandChat\Admin\Admin_Header;
use WlandChat\Admin\Admin_Sidebar;
use WlandChat\Admin\Template_Helpers;

// Exit if accessed directly
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
                    <h1 class="wland-page-title">
                        <?php _e('Dashboard', 'wland-chat'); ?>
                    </h1>
                    <p class="wland-page-description">
                        <?php _e('Una visión general de las funciones de Wland Chat iA para construir y personalizar tu chat.', 'wland-chat'); ?>
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

                <!-- Status Cards Grid -->
                <div class="wland-card-grid wland-card-grid--3-cols">

                    <!-- Chat Activo Card -->
                    <?php
                    Template_Helpers::card(array(
                        'icon' => Template_Helpers::get_icon('chat', '#023e8a'),
                        'title' => __('Chat Activo', 'wland-chat'),
                        'description' => $config_status['is_configured']
                            ? __('El chat está configurado y funcionando correctamente en tu sitio web.', 'wland-chat')
                            : __('El chat aún no está configurado. Configura el webhook para empezar.', 'wland-chat'),
                        'action_text' => __('Ver sitio web', 'wland-chat'),
                        'action_url' => home_url(),
                        'action_target' => '_blank',
                    ));
                    ?>

                    <!-- Configuración Card -->
                    <?php
                    Template_Helpers::card(array(
                        'icon' => Template_Helpers::get_icon('settings', '#023e8a'),
                        'title' => __('Configuración', 'wland-chat'),
                        'description' => __('Personaliza la apariencia, mensajes y comportamiento del chat según tus necesidades.', 'wland-chat'),
                        'action_text' => __('Ir a Ajustes', 'wland-chat'),
                        'action_url' => admin_url('admin.php?page=wland-chat-settings'),
                    ));
                    ?>

                    <!-- Documentación Card -->
                    <?php
                    Template_Helpers::card(array(
                        'icon' => Template_Helpers::get_icon('docs', '#023e8a'),
                        'title' => __('Documentación', 'wland-chat'),
                        'description' => __('Aprende cómo sacar el máximo provecho del plugin con nuestra documentación completa.', 'wland-chat'),
                        'action_text' => __('Ver documentación', 'wland-chat'),
                        'action_url' => 'https://github.com/Carlos-Vera/Wland-Chat-iA',
                        'action_target' => '_blank',
                    ));
                    ?>

                </div>

                <!-- Quick Actions Section -->
                <div class="wland-section wland-section--actions">
                    <h2 class="wland-section__title">
                        <?php _e('Acciones Rápidas', 'wland-chat'); ?>
                    </h2>

                    <div class="wland-button-group">
                        <?php
                        Template_Helpers::quick_action(array(
                            'text' => __('Personalizar Apariencia', 'wland-chat'),
                            'url' => admin_url('customize.php'),
                            'style' => 'secondary',
                        ));

                        Template_Helpers::quick_action(array(
                            'text' => __('Configurar Webhook', 'wland-chat'),
                            'url' => admin_url('admin.php?page=wland-chat-settings'),
                            'style' => 'secondary',
                        ));
                        ?>
                    </div>
                </div>

                <!-- System Info Cards -->
                <div class="wland-section wland-section--info">
                    <h2 class="wland-section__title">
                        <?php _e('Estado del Sistema', 'wland-chat'); ?>
                    </h2>

                    <div class="wland-card-grid wland-card-grid--2-cols">

                        <!-- Configuración General -->
                        <?php
                        $global_enabled_text = $config_status['global_enabled']
                            ? __('Activo en todo el sitio', 'wland-chat')
                            : __('Usar bloque Gutenberg', 'wland-chat');

                        Template_Helpers::card(array(
                            'title' => __('Modo de visualización', 'wland-chat'),
                            'description' => sprintf(
                                __('Modo: %s', 'wland-chat'),
                                ucfirst($config_status['display_mode'])
                            ),
                            'footer' => $global_enabled_text,
                            'custom_class' => 'wland-card--compact',
                        ));
                        ?>

                        <!-- Versión del Plugin -->
                        <?php
                        Template_Helpers::card(array(
                            'title' => __('Versión del Plugin', 'wland-chat'),
                            'description' => 'Wland Chat iA v' . WLAND_CHAT_VERSION,
                            'footer' => sprintf(
                                __('Última actualización: %s', 'wland-chat'),
                                date_i18n(get_option('date_format'))
                            ),
                            'custom_class' => 'wland-card--compact',
                        ));
                        ?>

                    </div>
                </div>

            </div><!-- .wland-admin-content -->

        </div><!-- .wland-admin-body -->

    </div><!-- .wland-admin-container -->
</div><!-- .wrap -->
