<?php
/**
 * Appearance Page Template
 *
 * Página de Apariencia moderno con diseño Bentō
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

// Obtener colores del tema de WordPress
$theme_colors = array();

// Intentar obtener colores desde el editor de bloques (theme.json)
if (function_exists('wp_get_global_settings')) {
    $global_settings = wp_get_global_settings();
    if (isset($global_settings['color']['palette']['theme'])) {
        foreach ($global_settings['color']['palette']['theme'] as $color) {
            $theme_colors[] = array(
                'name' => $color['name'],
                'color' => $color['color']
            );
        }
    }
}

// Si no hay colores del tema, agregar paleta por defecto
if (empty($theme_colors)) {
    $theme_colors = array(
        array('name' => 'Turquesa', 'color' => '#01B7AF'),
        array('name' => 'Azul', 'color' => '#3B82F6'),
        array('name' => 'Violeta', 'color' => '#8B5CF6'),
        array('name' => 'Rosa', 'color' => '#EC4899'),
        array('name' => 'Naranja', 'color' => '#F59E0B'),
        array('name' => 'Verde', 'color' => '#10B981'),
        array('name' => 'Rojo', 'color' => '#EF4444'),
        array('name' => 'Gris', 'color' => '#6B7280'),
    );
}
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
                    <h1 class="wland-page-title"><?php _e('Apariencia', 'wland-chat'); ?></h1>
                    <p class="wland-page-description">
                        <?php _e('Personaliza el aspecto visual del chat: títulos, mensajes, posición y modo de visualización.', 'wland-chat'); ?>
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

                <!-- Appearance Form -->
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wland_chat_settings');
                    // Preservar opciones no mostradas en este formulario
                    \WlandChat\Settings::get_instance()->render_hidden_fields(array(
                        'header_title',
                        'header_subtitle',
                        'welcome_message',
                        'position',
                        'display_mode',
                        'chat_icon',
                        'bubble_color',
                        'primary_color',
                        'background_color',
                        'text_color'
                    ));
                    ?>

                    <!-- Apariencia del Chat Section -->
                    <div class="wland-section">
                        <h2 class="wland-section__title">
                            <?php _e('Apariencia del Chat', 'wland-chat'); ?>
                        </h2>

                        <div class="wland-card-grid wland-card-grid--2-cols">

                            <!-- Card: Título de la Cabecera -->
                            <?php
                            $header_title = get_option($option_prefix . 'header_title', __('BravesLab AI Assistant', 'wland-chat'));

                            ob_start();
                            ?>
                            <input type="text"
                                   id="<?php echo esc_attr($option_prefix . 'header_title'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'header_title'); ?>"
                                   value="<?php echo esc_attr($header_title); ?>"
                                   class="wland-input"
                                   style="width: 100%;"
                                   placeholder="<?php echo esc_attr(__('BravesLab AI Assistant', 'wland-chat')); ?>">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Título principal que aparece en la cabecera del chat.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $header_title_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Título de la Cabecera', 'wland-chat'),
                                'description' => __('El título principal que verán los usuarios en el chat.', 'wland-chat'),
                                'content' => $header_title_content,
                            ));
                            ?>

                            <!-- Card: Subtítulo de la Cabecera -->
                            <?php
                            $header_subtitle = get_option($option_prefix . 'header_subtitle', __('Artificial Intelligence Marketing Agency', 'wland-chat'));

                            ob_start();
                            ?>
                            <input type="text"
                                   id="<?php echo esc_attr($option_prefix . 'header_subtitle'); ?>"
                                   name="<?php echo esc_attr($option_prefix . 'header_subtitle'); ?>"
                                   value="<?php echo esc_attr($header_subtitle); ?>"
                                   class="wland-input"
                                   style="width: 100%;"
                                   placeholder="<?php echo esc_attr(__('Artificial Intelligence Marketing Agency', 'wland-chat')); ?>">
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Subtítulo que aparece debajo del título principal.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $header_subtitle_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Subtítulo de la Cabecera', 'wland-chat'),
                                'description' => __('Texto descriptivo que complementa el título.', 'wland-chat'),
                                'content' => $header_subtitle_content,
                            ));
                            ?>

                            <!-- Card: Mensaje de Bienvenida -->
                            <?php
                            $welcome_message = get_option($option_prefix . 'welcome_message', __('¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?', 'wland-chat'));

                            ob_start();
                            ?>
                            <textarea id="<?php echo esc_attr($option_prefix . 'welcome_message'); ?>"
                                      name="<?php echo esc_attr($option_prefix . 'welcome_message'); ?>"
                                      rows="4"
                                      class="wland-textarea"
                                      style="width: 100%;"
                                      placeholder="<?php echo esc_attr(__('¡Hola! ¿Cómo podemos ayudarte?', 'wland-chat')); ?>"><?php echo esc_textarea($welcome_message); ?></textarea>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Mensaje inicial que verá el usuario al abrir el chat.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $welcome_message_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Mensaje de Bienvenida', 'wland-chat'),
                                'description' => __('El primer mensaje que verá el usuario en el chat.', 'wland-chat'),
                                'content' => $welcome_message_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                            <!-- Card: Posición del Chat -->
                            <?php
                            $current_position = get_option($option_prefix . 'position', 'bottom-right');
                            $positions = array(
                                'bottom-right' => __('Abajo a la derecha', 'wland-chat'),
                                'bottom-left' => __('Abajo a la izquierda', 'wland-chat'),
                                'center' => __('Centro', 'wland-chat'),
                            );

                            ob_start();
                            ?>
                            <select name="<?php echo esc_attr($option_prefix . 'position'); ?>"
                                    id="<?php echo esc_attr($option_prefix . 'position'); ?>"
                                    class="wland-select"
                                    style="width: 100%;">
                                <?php foreach ($positions as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>"
                                            <?php selected($current_position, $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Posición del widget de chat en la pantalla.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $position_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Posición del Chat', 'wland-chat'),
                                'description' => __('Dónde aparecerá el botón del chat en la pantalla.', 'wland-chat'),
                                'content' => $position_content,
                            ));
                            ?>

                            <!-- Card: Modo de Visualización -->
                            <?php
                            $current_mode = get_option($option_prefix . 'display_mode', 'modal');
                            $modes = array(
                                'modal' => __('Modal (ventana flotante)', 'wland-chat'),
                                'fullscreen' => __('Pantalla completa', 'wland-chat'),
                            );

                            ob_start();
                            ?>
                            <select name="<?php echo esc_attr($option_prefix . 'display_mode'); ?>"
                                    id="<?php echo esc_attr($option_prefix . 'display_mode'); ?>"
                                    class="wland-select"
                                    style="width: 100%;">
                                <?php foreach ($modes as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>"
                                            <?php selected($current_mode, $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="wland-field-help" style="margin-top: 8px; font-size: 13px; color: #666;">
                                <?php _e('Modo de presentación del chat al abrirse.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $mode_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Modo de Visualización', 'wland-chat'),
                                'description' => __('Cómo se mostrará el chat al abrirse.', 'wland-chat'),
                                'content' => $mode_content,
                            ));
                            ?>

                            <!-- Card: Icono del Botón de Chat -->
                            <?php
                            $current_icon = get_option($option_prefix . 'chat_icon', 'robot-chat');
                            $available_icons = array(
                                'robot-chat' => __('Original', 'wland-chat'),
                                'chat-circle' => __('Círculo', 'wland-chat'),
                                'chat-happy' => __('Happy', 'wland-chat'),
                                'chat-burbble' => __('Burbuja', 'wland-chat'),
                            );

                            ob_start();
                            ?>
<div class="wland-icon-tabs">
                                <?php foreach ($available_icons as $icon_key => $icon_label): ?>
                                    <label class="wland-icon-tab">
                                        <input type="radio"
                                               name="<?php echo esc_attr($option_prefix . 'chat_icon'); ?>"
                                               value="<?php echo esc_attr($icon_key); ?>"
                                               <?php checked($current_icon, $icon_key); ?>>
                                        <div class="wland-icon-tab__content">
                                            <img src="<?php echo esc_url(WLAND_CHAT_PLUGIN_URL . 'assets/media/chat-icons/' . $icon_key . '.svg'); ?>"
                                                 alt="<?php echo esc_attr($icon_label); ?>"
                                                 width="32"
                                                 height="32">
                                            <span><?php echo esc_html($icon_label); ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            $icon_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Icono del Botón', 'wland-chat'),
                                'description' => __('Icono que aparecerá en el botón flotante del chat.', 'wland-chat'),
                                'content' => $icon_content,
                                'custom_class' => 'wland-card--full-width',
                            ));
                            ?>

                        </div>
                    </div>

                    <!-- Personalización de Colores Section -->
                    <div class="wland-section">
                        <h2 class="wland-section__title">
                            <?php _e('Colores Personalizados', 'wland-chat'); ?>
                        </h2>

                        <div class="wland-card-grid wland-card-grid--2-cols">

                            <!-- Card: Color de la Burbuja -->
                            <?php
                            $bubble_color = get_option($option_prefix . 'bubble_color', '#01B7AF');

                            ob_start();
                            ?>
                            <!-- Color Picker Principal - Material Design List Style -->
                            <div style="margin-bottom: 16px;">
                                <input type="color"
                                       id="<?php echo esc_attr($option_prefix . 'bubble_color'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'bubble_color'); ?>"
                                       value="<?php echo esc_attr($bubble_color); ?>"
                                       class="wland-color-picker"
                                       title="<?php esc_attr_e('Seleccionar color personalizado', 'wland-chat'); ?>"
                                       style="display: inline-block; vertical-align: middle; margin: 0;">
                                <input type="text"
                                       value="<?php echo esc_attr($bubble_color); ?>"
                                       class="wland-color-text"
                                       data-color-input="<?php echo esc_attr($option_prefix . 'bubble_color'); ?>"
                                       readonly
                                       style="display: inline-block; vertical-align: middle; width: 200px; height: 40px; font-family: monospace; text-transform: uppercase; font-size: 13px; color: #6B7280; padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: #F9FAFB; margin: 0 0 0 12px; box-sizing: border-box;">
                            </div>

                            <!-- Toggle para mostrar paleta -->
                            <div style="margin-bottom: 12px;">
                                <button type="button" class="wland-palette-toggle" data-palette-target="bubble-color-palette">
                                    <span class="wland-palette-toggle-icon">▶</span>
                                    <span><?php _e('Colores del tema', 'wland-chat'); ?></span>
                                </button>
                            </div>

                            <!-- Paleta de colores predefinidos (oculta por defecto) -->
                            <div class="wland-color-palette wland-color-palette--collapsed" id="bubble-color-palette">
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <?php foreach ($theme_colors as $preset): ?>
                                        <button type="button"
                                                class="wland-color-preset"
                                                data-color="<?php echo esc_attr($preset['color']); ?>"
                                                data-target="<?php echo esc_attr($option_prefix . 'bubble_color'); ?>"
                                                title="<?php echo esc_attr($preset['name']); ?>"
                                                style="background: <?php echo esc_attr($preset['color']); ?>;">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <p class="wland-field-help" style="margin-top: 12px; font-size: 13px; color: #666;">
                                <?php _e('Color del botón flotante (burbuja) del chat.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $bubble_color_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Color de la Burbuja', 'wland-chat'),
                                'description' => __('Color del botón flotante que abre el chat.', 'wland-chat'),
                                'content' => $bubble_color_content,
                            ));
                            ?>

                            <!-- Card: Color Primario -->
                            <?php
                            $primary_color = get_option($option_prefix . 'primary_color', '#01B7AF');

                            ob_start();
                            ?>
                            <!-- Color Picker Principal - Material Design List Style -->
                            <div style="margin-bottom: 16px;">
                                <input type="color"
                                       id="<?php echo esc_attr($option_prefix . 'primary_color'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'primary_color'); ?>"
                                       value="<?php echo esc_attr($primary_color); ?>"
                                       class="wland-color-picker"
                                       title="<?php esc_attr_e('Seleccionar color personalizado', 'wland-chat'); ?>"
                                       style="display: inline-block; vertical-align: middle; margin: 0;">
                                <input type="text"
                                       value="<?php echo esc_attr($primary_color); ?>"
                                       class="wland-color-text"
                                       data-color-input="<?php echo esc_attr($option_prefix . 'primary_color'); ?>"
                                       readonly
                                       style="display: inline-block; vertical-align: middle; width: 200px; height: 40px; font-family: monospace; text-transform: uppercase; font-size: 13px; color: #6B7280; padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: #F9FAFB; margin: 0 0 0 12px; box-sizing: border-box;">
                            </div>

                            <!-- Toggle para mostrar paleta -->
                            <div style="margin-bottom: 12px;">
                                <button type="button" class="wland-palette-toggle" data-palette-target="primary-color-palette">
                                    <span class="wland-palette-toggle-icon">▶</span>
                                    <span><?php _e('Colores del tema', 'wland-chat'); ?></span>
                                </button>
                            </div>

                            <!-- Paleta de colores predefinidos (oculta por defecto) -->
                            <div class="wland-color-palette wland-color-palette--collapsed" id="primary-color-palette">
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <?php foreach ($theme_colors as $preset): ?>
                                        <button type="button"
                                                class="wland-color-preset"
                                                data-color="<?php echo esc_attr($preset['color']); ?>"
                                                data-target="<?php echo esc_attr($option_prefix . 'primary_color'); ?>"
                                                title="<?php echo esc_attr($preset['name']); ?>"
                                                style="background: <?php echo esc_attr($preset['color']); ?>;">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <p class="wland-field-help" style="margin-top: 12px; font-size: 13px; color: #666;">
                                <?php _e('Color del header y mensajes del asistente.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $primary_color_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Color Primario', 'wland-chat'),
                                'description' => __('Color principal usado en el header del chat.', 'wland-chat'),
                                'content' => $primary_color_content,
                            ));
                            ?>

                            <!-- Card: Color de Fondo -->
                            <?php
                            $background_color = get_option($option_prefix . 'background_color', '#FFFFFF');

                            ob_start();
                            ?>
                            <!-- Color Picker Principal - Material Design List Style -->
                            <div style="margin-bottom: 16px;">
                                <input type="color"
                                       id="<?php echo esc_attr($option_prefix . 'background_color'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'background_color'); ?>"
                                       value="<?php echo esc_attr($background_color); ?>"
                                       class="wland-color-picker"
                                       title="<?php esc_attr_e('Seleccionar color personalizado', 'wland-chat'); ?>"
                                       style="display: inline-block; vertical-align: middle; margin: 0;">
                                <input type="text"
                                       value="<?php echo esc_attr($background_color); ?>"
                                       class="wland-color-text"
                                       data-color-input="<?php echo esc_attr($option_prefix . 'background_color'); ?>"
                                       readonly
                                       style="display: inline-block; vertical-align: middle; width: 200px; height: 40px; font-family: monospace; text-transform: uppercase; font-size: 13px; color: #6B7280; padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: #F9FAFB; margin: 0 0 0 12px; box-sizing: border-box;">
                            </div>

                            <!-- Toggle para mostrar paleta -->
                            <div style="margin-bottom: 12px;">
                                <button type="button" class="wland-palette-toggle" data-palette-target="background-color-palette">
                                    <span class="wland-palette-toggle-icon">▶</span>
                                    <span><?php _e('Colores del tema', 'wland-chat'); ?></span>
                                </button>
                            </div>

                            <!-- Paleta de colores predefinidos (oculta por defecto) -->
                            <div class="wland-color-palette wland-color-palette--collapsed" id="background-color-palette">
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <?php
                                    // Para fondo, agregar blanco y negro
                                    $background_presets = array_merge(
                                        array(
                                            array('name' => 'Blanco', 'color' => '#FFFFFF'),
                                            array('name' => 'Gris claro', 'color' => '#F3F4F6'),
                                            array('name' => 'Negro', 'color' => '#1F2937'),
                                        ),
                                        $theme_colors
                                    );
                                    foreach ($background_presets as $preset): ?>
                                        <button type="button"
                                                class="wland-color-preset"
                                                data-color="<?php echo esc_attr($preset['color']); ?>"
                                                data-target="<?php echo esc_attr($option_prefix . 'background_color'); ?>"
                                                title="<?php echo esc_attr($preset['name']); ?>"
                                                style="background: <?php echo esc_attr($preset['color']); ?>;">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <p class="wland-field-help" style="margin-top: 12px; font-size: 13px; color: #666;">
                                <?php _e('Color de fondo de la ventana del chat.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $background_color_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Color de Fondo', 'wland-chat'),
                                'description' => __('Color de fondo del área de mensajes.', 'wland-chat'),
                                'content' => $background_color_content,
                            ));
                            ?>

                            <!-- Card: Color de Texto -->
                            <?php
                            $text_color = get_option($option_prefix . 'text_color', '#333333');

                            ob_start();
                            ?>
                            <!-- Color Picker Principal - Material Design List Style -->
                            <div style="margin-bottom: 16px;">
                                <input type="color"
                                       id="<?php echo esc_attr($option_prefix . 'text_color'); ?>"
                                       name="<?php echo esc_attr($option_prefix . 'text_color'); ?>"
                                       value="<?php echo esc_attr($text_color); ?>"
                                       class="wland-color-picker"
                                       title="<?php esc_attr_e('Seleccionar color personalizado', 'wland-chat'); ?>"
                                       style="display: inline-block; vertical-align: middle; margin: 0;">
                                <input type="text"
                                       value="<?php echo esc_attr($text_color); ?>"
                                       class="wland-color-text"
                                       data-color-input="<?php echo esc_attr($option_prefix . 'text_color'); ?>"
                                       readonly
                                       style="display: inline-block; vertical-align: middle; width: 200px; height: 40px; font-family: monospace; text-transform: uppercase; font-size: 13px; color: #6B7280; padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: #F9FAFB; margin: 0 0 0 12px; box-sizing: border-box;">
                            </div>

                            <!-- Toggle para mostrar paleta -->
                            <div style="margin-bottom: 12px;">
                                <button type="button" class="wland-palette-toggle" data-palette-target="text-color-palette">
                                    <span class="wland-palette-toggle-icon">▶</span>
                                    <span><?php _e('Colores del tema', 'wland-chat'); ?></span>
                                </button>
                            </div>

                            <!-- Paleta de colores predefinidos (oculta por defecto) -->
                            <div class="wland-color-palette wland-color-palette--collapsed" id="text-color-palette">
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <?php
                                    // Para texto, agregar tonos oscuros
                                    $text_presets = array_merge(
                                        array(
                                            array('name' => 'Negro', 'color' => '#1F2937'),
                                            array('name' => 'Gris oscuro', 'color' => '#4B5563'),
                                            array('name' => 'Gris', 'color' => '#6B7280'),
                                            array('name' => 'Blanco', 'color' => '#FFFFFF'),
                                        ),
                                        $theme_colors
                                    );
                                    foreach ($text_presets as $preset): ?>
                                        <button type="button"
                                                class="wland-color-preset"
                                                data-color="<?php echo esc_attr($preset['color']); ?>"
                                                data-target="<?php echo esc_attr($option_prefix . 'text_color'); ?>"
                                                title="<?php echo esc_attr($preset['name']); ?>"
                                                style="background: <?php echo esc_attr($preset['color']); ?>;">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <p class="wland-field-help" style="margin-top: 12px; font-size: 13px; color: #666;">
                                <?php _e('Color del texto de los mensajes.', 'wland-chat'); ?>
                            </p>
                            <?php
                            $text_color_content = ob_get_clean();

                            Template_Helpers::card(array(
                                'title' => __('Color de Texto', 'wland-chat'),
                                'description' => __('Color del texto principal en los mensajes.', 'wland-chat'),
                                'content' => $text_color_content,
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
