<?php
/**
 * About Page Template
 *
 * Página informativa con changelog y créditos del plugin
 *
 * @package WlandChat
 * @subpackage Admin\Templates
 * @since 1.2.2
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
                    <h1 class="wland-page-title"><?php _e('Acerca de Wland Chat iA', 'wland-chat'); ?></h1>
                    <p class="wland-page-description">
                        <?php _e('Información del plugin, historial de cambios y créditos del equipo de desarrollo.', 'wland-chat'); ?>
                    </p>
                </div>

                <!-- Plugin Info Section -->
                <div class="wland-section">
                    <h2 class="wland-section__title">
                        <?php _e('Información del Plugin', 'wland-chat'); ?>
                    </h2>

                    <div class="wland-card-grid wland-card-grid--3-cols">

                        <!-- Card: Versión -->
                        <a href="https://github.com/Carlos-Vera/Wland-Chat-iA" target="_blank" style="text-decoration: none; color: inherit; display: block;">
                            <?php
                            Template_Helpers::card(array(
                                'icon' => Template_Helpers::get_icon('verified', '#023e8a'),
                                'title' => __('Versión', 'wland-chat'),
                                'description' => 'v' . WLAND_CHAT_VERSION,
                                'footer' => 'GitHub Repository',
                            ));
                            ?>
                        </a>

                        <!-- Card: Autor -->
                        <a href="https://braveslab.com" target="_blank" style="text-decoration: none; color: inherit; display: block;">
                            <?php
                            Template_Helpers::card(array(
                                'icon' => Template_Helpers::get_icon('logo_dev', '#023e8a'),
                                'title' => __('Autor Principal', 'wland-chat'),
                                'description' => 'Carlos Vera',
                                'footer' => 'braveslab.com',
                            ));
                            ?>
                        </a>

                        <!-- Card: Empresa -->
                        <a href="https://braveslab.com" target="_blank" style="text-decoration: none; color: inherit; display: block;">
                            <?php
                            Template_Helpers::card(array(
                                'icon' => Template_Helpers::get_icon('business_center', '#023e8a'),
                                'title' => __('Empresa', 'wland-chat'),
                                'description' => 'BRAVES LAB LLC',
                                'footer' => 'braveslab.com',
                            ));
                            ?>
                        </a>

                    </div>
                </div>

                <!-- Team Section -->
                <div class="wland-section">
                    <h2 class="wland-section__title">
                        <?php _e('Equipo de Desarrollo', 'wland-chat'); ?>
                    </h2>

                    <div class="wland-about-team">
                        <a href="mailto:carlos@braveslab.com" style="text-decoration: none; color: inherit; display: block;">
                            <div class="wland-about-team__member">
                                <h3 class="wland-about-team__name">Carlos Vera</h3>
                                <p class="wland-about-team__role"><?php _e('Desarrollo Principal', 'wland-chat'); ?></p>
                                <p class="wland-about-team__email">carlos@braveslab.com</p>
                            </div>
                        </a>

                        <a href="mailto:hola@mikimonokia.com" style="text-decoration: none; color: inherit; display: block;">
                            <div class="wland-about-team__member">
                                <h3 class="wland-about-team__name">Mikel Marqués</h3>
                                <p class="wland-about-team__role"><?php _e('Contribuciones', 'wland-chat'); ?></p>
                                <p class="wland-about-team__email">hola@mikimonokia.com</p>
                            </div>
                        </a>

                        <a href="https://claude.ai" target="_blank" style="text-decoration: none; color: inherit; display: block;">
                            <div class="wland-about-team__member">
                                <h3 class="wland-about-team__name">Claude (Anthropic)</h3>
                                <p class="wland-about-team__role"><?php _e('Asistencia en Desarrollo v1.2.x', 'wland-chat'); ?></p>
                                <p class="wland-about-team__email">claude.ai</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Changelog Section -->
                <div class="wland-section">
                    <h2 class="wland-section__title">
                        <?php _e('Historial de Cambios', 'wland-chat'); ?>
                    </h2>

                    <!-- Version 1.2.3 -->
                    <div class="wland-changelog">
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--primary">v1.2.3</span>
                                <?php _e('Personalización de Colores e Iconos SVG', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('26 de Octubre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🎁 Mejoras', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Sistema completo de personalización de colores desde panel de Apariencia', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: 4 campos de color personalizables: Color de la Burbuja, Color Primario, Color de Fondo y Color de Texto', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Color picker nativo HTML5 con sincronización a input de texto hexadecimal', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Paleta de colores del tema de WordPress extraída desde theme.json (colapsable)', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Paleta por defecto de 8 colores cuando el tema no define colores personalizados', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Sistema de selección de iconos SVG personalizables para botón flotante', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: 4 iconos SVG optimizados (Original/Robot, Círculo, Happy, Burbuja)', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Selector estilo tabs Bentō en página de Apariencia', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Toggle buttons para expandir/colapsar paletas de colores con animación suave', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Color pickers con estilo Material Design list (inline-block, vertical-align: middle)', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Diseño tabs horizontal con fondo gris claro y selección con borde morado', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Iconos optimizados 32x32px desde viewBox 460x460', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Diseño responsive (2 columnas en móvil)', 'wland-chat'); ?></li>
                                </ul>
                            </div>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🔧 Correcciones', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('FIXED: Eliminada dependencia de Lottie Player (CDN externo)', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Errores de consola por animaciones Lottie no cargadas', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Error JavaScript cuando wp.i18n no está disponible', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Alineación del color picker y input text usando display: inline-block con vertical-align: middle', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Configuración JavaScript duplicada entre templates y class_frontend.php', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Templates modal.php y screen.php creaban variable conflictiva wlandChatConfig', 'wland-chat'); ?></li>
                                    <li><?php _e('REMOVED: Gradiente del botón flotante - ahora usa color sólido', 'wland-chat'); ?></li>
                                    <li><?php _e('REMOVED: Borde izquierdo de las burbujas de mensajes', 'wland-chat'); ?></li>
                                    <li><?php _e('CHANGED: Templates usan img SVG en lugar de animación Lottie', 'wland-chat'); ?></li>
                                    <li><?php _e('CHANGED: Icono por defecto cambiado a "Original" (robot-chat)', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version 1.2.2 -->
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--success">v1.2.2</span>
                                <?php _e('Correcciones y Mejoras UX', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('25 de Octubre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🔧 Correcciones', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('FIXED: Los inputs de formulario no se renderizaban en las páginas de configuración', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: El método Admin_Content::render_card() no soportaba el parámetro content', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: wp_kses_post() eliminaba los elementos de formulario HTML', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Los ajustes se perdían al guardar desde diferentes páginas (Settings, Appearance, Availability, GDPR)', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: El icono del menú mostraba color gris en lugar de blanco cuando estaba activo', 'wland-chat'); ?></li>
                                    <li><?php _e('FIXED: Script admin_settings.js no se cargaba en páginas Appearance, Availability y GDPR', 'wland-chat'); ?></li>
                                </ul>
                            </div>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🎁 Mejoras', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Sistema de auto-ocultación para notificaciones de éxito con animación slide-up', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Campos ocultos en formularios para preservar ajustes de otras secciones', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: JavaScript para mantener clase wp-has-current-submenu en páginas del plugin', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Iconos de sidebar actualizados a versiones sólidas (Horarios, GDPR)', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Iconos de página About actualizados (Version: verified, Autor: person_pin, Empresa: business_center)', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Tarjetas informativas ahora son clicables con enlaces externos', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Todos los formularios ahora funcionales con diseño Bentō consistente', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Estilos CSS unificados en todas las páginas del admin', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Toggles estilo Bentō implementados en todos los checkboxes', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version 1.2.1 -->
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--success">v1.2.1</span>
                                <?php _e('Rediseño Completo del Admin', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('24 de Octubre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('⚙️ Características', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('Implementación completa del diseño Bentō', 'wland-chat'); ?></li>
                                    <li><?php _e('Nueva arquitectura modular de componentes', 'wland-chat'); ?></li>
                                    <li><?php _e('5 páginas de administración: Resumen, Ajustes, Apariencia, Horarios, GDPR', 'wland-chat'); ?></li>
                                    <li><?php _e('Sidebar único compartido entre todas las páginas', 'wland-chat'); ?></li>
                                    <li><?php _e('Sistema de Template Helpers para renderizado consistente', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version 1.1.2 -->
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--success">v1.1.2</span>
                                <?php _e('Cambio de Marca', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('23 de Octubre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🔁 Cambios', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('CHANGED: Weblandia → BravesLab', 'wland-chat'); ?></li>
                                    <li><?php _e('CHANGED: URLs actualizadas a braveslab.com', 'wland-chat'); ?></li>
                                    <li><?php _e('CHANGED: Copyright actualizado a BRAVES LAB LLC', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version 1.1.1 -->
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--primary">v1.1.1</span>
                                <?php _e('Sistema de Cookies y GDPR', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('16 de Octubre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🎁 Mejoras', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Sistema de cookies con fingerprinting del navegador', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Banner de consentimiento GDPR configurable', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Hash SHA-256 para identificación única', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Fallback a localStorage si cookies bloqueadas', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version 1.1.0 -->
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--success">v1.1.0</span>
                                <?php _e('Horarios y Páginas Excluidas', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('1 de Octubre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('⚙️ Características', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Sistema de horarios de disponibilidad con zonas horarias', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Páginas excluidas configurables (selector múltiple)', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Token de autenticación N8N (header X-N8N-Auth)', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Mensaje personalizado fuera de horario', 'wland-chat'); ?></li>
                                </ul>
                            </div>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🎁 Mejoras', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('IMPROVED: Configuración del webhook más flexible', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Validación de URLs de webhook', 'wland-chat'); ?></li>
                                    <li><?php _e('IMPROVED: Sanitización de inputs en Settings API', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Version 1.0.0 -->
                        <div class="wland-changelog__version">
                            <h3 class="wland-changelog__title">
                                <span class="wland-badge wland-badge--success">v1.0.0</span>
                                <?php _e('Lanzamiento Inicial', 'wland-chat'); ?>
                            </h3>
                            <p class="wland-changelog__date"><?php _e('15 de Septiembre, 2025', 'wland-chat'); ?></p>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🛠️ Funcionalidades Principales', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Integración de chat con IA mediante bloque Gutenberg', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Configuración de webhook N8N', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Sistema de mensajes personalizables', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Dos modos de visualización: Modal y Pantalla completa', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Posicionamiento configurable (derecha, izquierda, centro)', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Animación Lottie en botón de chat', 'wland-chat'); ?></li>
                                </ul>
                            </div>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🧬 Arquitectura', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Estructura OOP con namespaces PHP (WlandChat)', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: WordPress Settings API para configuración', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: WordPress Customizer API para personalización en tiempo real', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Bloque Gutenberg con opciones personalizables', 'wland-chat'); ?></li>
                                </ul>
                            </div>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🔒 Seguridad', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Sanitización completa de inputs', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Nonces en todos los formularios', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Verificación de capacidades de usuario', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Escapado de salidas (esc_html, esc_attr, esc_url)', 'wland-chat'); ?></li>
                                </ul>
                            </div>

                            <div class="wland-changelog__section">
                                <h4><?php _e('🇻🇪 i18n', 'wland-chat'); ?></h4>
                                <ul>
                                    <li><?php _e('ADDED: Preparado para internacionalización', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Text domain: wland-chat', 'wland-chat'); ?></li>
                                    <li><?php _e('ADDED: Archivo .pot para traducciones', 'wland-chat'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- .wland-admin-content -->

        </div><!-- .wland-admin-body -->

    </div><!-- .wland-admin-container -->
</div><!-- .wrap -->
