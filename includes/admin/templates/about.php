<?php
/**
 * About Page Template
 *
 * Página informativa con changelog y créditos del plugin
 *
 * @package BravesChat
 * @subpackage Admin\Templates
 * @since 1.2.2
 */

use BravesChat\Admin\Admin_Header;
use BravesChat\Admin\Admin_Sidebar;
use BravesChat\Admin\Template_Helpers;

if (!defined('ABSPATH')) {
    exit;
}

// Verificar permisos
if (!current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have permission to access this page.', 'braveschat'));
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template-scoped variables, not true globals.
// Obtener instancias de componentes
$header = Admin_Header::get_instance();
$sidebar = Admin_Sidebar::get_instance();
?>

<div class="wrap braves-admin-wrap">
    <div class="braves-admin-container">

        <?php
        // Renderizar header
        $header->render(array(
            'show_logo' => true,
            'show_version' => true,
        ));
        ?>

        <div class="braves-admin-body">

            <?php
            // Renderizar sidebar con botón de volver arriba
            $sidebar->render($current_page, array('show_scroll_top' => true));
            ?>

            <div class="braves-admin-content">

                <!-- Page Header -->
                <div class="braves-page-header">
                    <h1 class="braves-page-title"><?php echo wp_kses_post( __('About <strong>BravesChat iA</strong>', 'braveschat') ); ?></h1>
                    <p class="braves-page-description">
                        <?php esc_html_e('Plugin information and changelog.', 'braveschat'); ?>
                    </p>
                </div>

                <!-- Plugin Info Section -->
                <div class="braves-section">
                    <h2 class="braves-section__title">
                        <?php esc_html_e('Plugin Information', 'braveschat'); ?>
                    </h2>

                    <div class="braves-card-grid braves-card-grid--3-cols">

                        <!-- Card: Version -->
                        <?php
                        Template_Helpers::card(array(
                            'icon' => Template_Helpers::get_icon('verified'),
                            'title' => __('Version', 'braveschat'),
                            'description' => 'v' . BRAVES_CHAT_VERSION,
                            'footer' => __('Last Update: <b>Jun 13, 2026</b>', 'braveschat'),
                            'action_text' => 'GitHub Repository',
                            'action_url' => 'https://github.com/Carlos-Vera/braveschat',
                            'action_target' => '_blank',
                            'is_link_card' => true,
                        ));
                        ?>

                        <!-- Card: Author -->
                        <?php
                        Template_Helpers::card(array(
                            'icon' => Template_Helpers::get_icon('logo_dev'),
                            'title' => __('Lead Author', 'braveschat'),
                            'description' => 'Carlos Vera',
                            'action_text' => 'carlos@braveslab.com',
                            'action_url' => 'mailto:carlos@braveslab.com',
                            'action_target' => '_blank',
                            'is_link_card' => true,
                        ));
                        ?>

                        <!-- Card: Company -->
                        <?php
                        Template_Helpers::card(array(
                            'icon' => Template_Helpers::get_icon('business_center'),
                            'title' => __('Company', 'braveschat'),
                            'description' => 'BRAVES LAB LLC',
                            'action_text' => 'braveslab.com',
                            'action_url' => 'https://braveslab.com',
                            'action_target' => '_blank',
                            'is_link_card' => true,
                        ));
                        ?>

                    </div>
                </div>

                <!-- Changelog Section -->
                <div class="braves-timeline">
                    
                    <h2 class="braves-section__title" style="margin-left: 48px; transform: translateX(-50%); width: fit-content; text-align: center; margin-bottom: 2rem;">
                        <?php esc_html_e('Changelog', 'braveschat'); ?>
                    </h2>

                    <div class="braves-timeline__cap">
                            <span class="braves-timeline__cap-label"><?php esc_html_e('Today', 'braveschat'); ?></span>
                        </div>

                    <!-- Version 2.5.0 -->
                        <div class="braves-timeline__item">
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.5.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Jun 13, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Compatibilidad con WordPress 7.0', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Mejoras', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('Tu chat funciona perfecto con la última versión de WordPress — el editor sigue corriendo sin interrupciones al insertar el bloque.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('El bloque del chat ahora muestra una vista previa al insertarlo desde el panel de bloques.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Correcciones', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('El historial ya no se ve vacío cuando el webhook aún no está configurado — las instrucciones de configuración vuelven a aparecer correctamente.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.4.6 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.4.6</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Apr 16, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Safer links, translatable history', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Security', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('Links from your agent only open if they point to http:// or https://. Anything else — javascript:, data: — is ignored.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('The N8N auth header is cleaned before it leaves WordPress. Characters that could alter the request are stripped out.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('WordPress in English? The daily verse in the panel header shows in English too — 365 NIV entries, loaded locally.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('The conversation viewer now speaks your language. Contact name, session ID, and sender labels follow your WordPress locale — no more hardcoded Spanish.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('Adding a new version to this page no longer means adjusting spacing by hand. The layout figures itself out.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.4.5 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.4.5</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Apr 15, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Local verses, English UI, security fixes', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Bible verses now load from a local file — no external API calls, no rate limits, no dependency on scripture.api.bible.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Full English translation of the admin panel and chat widget, with es_ES support via .po/.mo files.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Security', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: The N8N auth token no longer leaks through console.log in the browser.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: HTTP/HTTPS scheme validated before every wp_remote_get() call.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: $_GET[\'page\'] sanitized and boolean values properly escaped in hidden form inputs.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: admin.js i18n pattern updated to const { __ } = wp.i18n — compatible with wp i18n make-json.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.4.4 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.4.4</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Apr 12, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('The plugin in your language', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: If your WordPress is not in Spanish, the panel and chat adapt automatically — buttons, widget text, and labels included.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: Some chat texts always appeared in Spanish regardless of the site language. Not anymore.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.4.3 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.4.3</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Apr 12, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Mixed mode and dark mode compatibility', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: New "Mixed" display mode — the chat appears as a floating bubble across the entire site and as full screen on pages where you add the Gutenberg block.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: In iOS Safari with dark mode active, chat colors no longer invert — the widget always keeps its original design.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The GDPR privacy banner no longer inherits colors from dark mode themes — text stays readable on any theme.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: Opening the chat in full screen no longer shows a white background flash before the interface loads.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.4.2 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.4.2</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 26, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Dark mode for the admin panel', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: The admin panel now has dark mode. (My obsession with protecting my eyes.) Toggle it with one button — your preference is saved and applied instantly, with no flash on load.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: The welcome message editor also switches to dark mode along with the rest of the panel.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The entire panel design switches automatically when dark mode is active — no exceptions, no hardcoded colors.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The excluded pages selection buttons now look consistent with the rest of the panel.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.4.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Native mobile chat', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: On mobile, the chat now takes the full screen. It has its own header with a close button, respects the iPhone notch, and blocks background scroll while open.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The widget no longer interferes with WooCommerce cart or checkout — z-index adjusted to coexist without conflicts.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Panel scripts load via the WordPress system instead of being embedded in templates — better compatibility and security.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.4.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 25, 2026', 'braveschat'); ?></div>
                            </div>
                        </div>

                    <!-- Version 2.3.8 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.3.8</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 18, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Verse of the day', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: A Bible verse (NIV) appears every day in the panel header. It changes at midnight automatically — no configuration needed. Fetched from the American Bible Society API with a 24h WordPress transient cache. Fully safe, no user data collected.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Versions 2.3.2 → 2.3.7 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('UX/UI design improvements', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: On mobile, the bubble takes less space. The default skin shrinks to 48×48px; the Braves skin collapses to avatar + round button. Desktop is unchanged.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Webhook and save notices move to the header. The page stays clean.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Sidebar renamed — Availability, Privacy, Conversations. More direct.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The version badge highlights when you are on this page.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: The custom image upload button in Appearance now correctly opens the WordPress Media Library.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: WordPress.org Plugin Check no longer reports text domain errors. The plugin uses the correct slug (braveschat) in all files and in the distribution ZIP.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.3.7</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 18, 2026', 'braveschat'); ?></div>
                            </div>
                        </div>

                    <!-- Version 2.3.1 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Type while the agent responds', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The text field no longer locks while the agent responds. You can type at any time. If the agent is mid-response, it cancels and your new message is sent immediately.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.3.1</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 11, 2026', 'braveschat'); ?></div>
                            </div>
                        </div>

                    <!-- Version 2.3.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.3.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 10, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('The token lives on the server', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: The N8N token no longer travels to the browser. The frontend sends the message to WordPress, and WordPress forwards it to the webhook with the token added server-side — invisible to any visitor.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Three authentication methods for N8N: custom header, Basic Auth, or none.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The active display mode (modal or full screen) now appears in the panel header.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The Gutenberg block shows a redesigned preview matching the panel style.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: GPL-2.0-or-later license, aligned with WordPress.org requirements.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Banners, screenshots, and icons converted to PNG for maximum compatibility.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Versions 2.2.1 → 2.2.3 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.2.3</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Mar 5, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Plugin details without leaving the admin', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Click "View details" in the WordPress plugins list to see the full plugin card: screenshots, FAQ, instructions, and compatibility.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The GDPR banner message and offline schedule notice now use a visual editor. Add bold text, lists, or links directly from the text field.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The BravesChat panel no longer shows notices from other plugins. Only yours, without noise.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: The panel detects if you have tools installed that could export the plugin code and warns you. An extra layer of protection.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.2.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.2.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Feb 26, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Conversation history', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php echo wp_kses_post( __('ADDED: New <strong>History</strong> section: access all conversations your agent has had with your visitors.', 'braveschat') ); ?></li>
                                            <li><?php esc_html_e('ADDED: Open any conversation with a click and read each message as it happened, in a clear chat interface.', 'braveschat'); ?></li>
                                            <li><?php echo wp_kses_post( __('ADDED: Export the full history to <strong>CSV</strong> with one click to analyze it in Excel, import it to your CRM, or share it with your team.', 'braveschat') ); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: Your customers\' messages are shown exactly as they wrote them, without noise that makes reading harder.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Each row groups all messages from the conversation so you see the full context.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.1.3 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.1.3</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Feb 23, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Automatic ZIP on every release', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Every time we publish a version, the install-ready ZIP is generated automatically on GitHub. No manual steps.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The automatic ZIP generation process was not executing correctly in previous versions.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.1.2 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                    <div class="braves-timeline__badge">v2.1.2</div>
                                    <div class="braves-timeline__label"><?php esc_html_e('Feb 20, 2026', 'braveschat'); ?></div>
                                </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Chat ignores your theme styles', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The chat no longer inherits styles from the active theme. Buttons, inputs, and text look the same regardless of what theme you have installed.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The Montserrat font applies to all chat elements: modal, full screen, and GDPR banner.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: The chat in center position was displayed offset on some themes. It now centers correctly.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The bottom-left position in modal mode was misaligned on certain screen sizes.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: Each panel section has its own title in the browser tab.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: When you uninstall the plugin, your settings are preserved in case you reinstall later.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.1.1 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.1.1</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Feb 16, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Real-time Markdown', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: Agent responses render in real time with Markdown formatting. Bold, lists, and links appear as they arrive.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: After sending a message, the cursor returns automatically to the text field. You can keep typing without clicking.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                    <!-- Version 2.1.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v2.1.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Feb 16, 2026', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Control the typing speed', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Slider to adjust the speed at which the agent "types" its responses. From slow and deliberate to near-instant.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: The GDPR banner now accepts rich text. You can add bold text, lists, or links directly from the text field.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: The chat font loads from your own server, with no dependency on external services.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: The chat scroll moves down automatically when a new message arrives. The GDPR banner displays correctly on all themes.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 2.0.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                    <div class="braves-timeline__badge">v2.0.0</div>
                                    <div class="braves-timeline__label"><?php esc_html_e('Feb 14, 2026', 'braveschat'); ?></div>
                                </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('BravesChat iA 2.0', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: The plugin is renamed BravesChat iA with a fully rebuilt architecture from top to bottom.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Button to maximize the chat. The user can expand it to full screen at any time.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: The text area grows automatically when typing multiple lines, without overflowing the chat.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: After interacting, the chat bubble minimizes into a compact pill so it stays out of the way.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: Updated visual identity with BravesChat corporate colors.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Message bubbles no longer cut text or show odd borders.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The send message icon is now a custom white design.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The floating button tooltip defaults to "Talk to our AI assistant".', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: The send button no longer stays locked in unexpected situations.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: When saving Excluded Pages settings, the selection was no longer lost.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The N8N integration no longer sent extra data that could confuse the agent.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: When saving settings from any section, values configured on other pages were no longer lost.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.2.4 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                    <div class="braves-timeline__badge">v1.2.4</div>
                                    <div class="braves-timeline__label"><?php esc_html_e('Nov 17, 2025', 'braveschat'); ?></div>
                                </div>

                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Customizable tooltip', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Text field to customize the message that appears when hovering over the floating chat button.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: On activation, the plugin automatically detects older installed versions, deactivates them, and cleans up outdated files. No settings are lost.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The default floating button icon color changes to light gray for better contrast on dark backgrounds.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Appearance options are better organized. The tooltip appears right before the icon selector.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: Critical error that prevented WordPress from loading when two plugin versions were installed simultaneously.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The icon color was not applied correctly when editing appearance settings.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.2.3 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.2.3</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Oct 26, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Color and icon customization', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Complete color customization system from the panel: bubble, primary color, background, and chat text.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Native color picker synced with a hex text field. Type the exact code or use the visual picker.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Color palette automatically extracted from the active theme, plus a default palette of 8 colors.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Four icons for the floating button: Robot, Circle, Happy, and Bubble.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: Color palettes can be expanded and collapsed with smooth animation to avoid screen clutter.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The icon selector uses a horizontal tab layout — clearer and faster to use.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: The Appearance panel is responsive. Selectors reorganize into two columns on small screens.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: Lottie Player dependency removed — it was loaded from an external CDN. The chat button no longer fails without an internet connection.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The floating button now uses a solid color instead of the gradient that caused visual inconsistencies.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The button image changed from animation to static SVG. Faster and no console errors.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.2.2 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.2.2</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Oct 25, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Complete and functional panel', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Bug Fixes', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('FIXED: Configuration fields were not showing on some panel pages. They now appear correctly in all sections.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: When saving from any settings page, values configured in other sections were no longer cleared.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('FIXED: The sidebar menu icon showed gray instead of white when selected.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Experience Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Successful save notifications disappear automatically after a few seconds, with smooth animation.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Sidebar icons updated to solid versions for better readability.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: Info cards are now clickable with external links.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('IMPROVED: All panel checkboxes use the toggle style from the Bentō design.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.2.1 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.2.1</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Oct 24, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Full admin panel redesign', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Admin panel redesigned with the Bentō style — clean, modular, and easy to navigate.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Five independent sections: Dashboard, Settings, Appearance, Availability, and Privacy.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Shared sidebar across all panel pages for consistent navigation.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.1.2 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.1.2</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Oct 23, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Brand change', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: The plugin updates its brand from Weblandia to BravesLab — all URLs and references updated to braveslab.com.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.1.1 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.1.1</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Oct 16, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Cookie system and GDPR', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Unique user identification system via browser fingerprinting, without storing personal data.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Configurable GDPR consent banner — blocks the chat until the user accepts.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: If cookies are blocked by the browser, the chat history is saved in local memory as a fallback.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.1.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.1.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Oct 1, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Schedules and excluded pages', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: Define the hours when the chat is available, with timezone support.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Automatic out-of-hours message. The agent responds with your text when unavailable.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Excluded pages selector. Choose which pages on your site should not show the chat.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Authentication token to secure communication with your N8N agent.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('Improvements', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('IMPROVED: Webhook configuration is more flexible and validates the URL before saving.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Version 1.0.0 -->
                        <div class="braves-timeline__item" data-tl-item>
                            <div class="braves-timeline__axis">
                                <div class="braves-timeline__badge">v1.0.0</div>
                                <div class="braves-timeline__label"><?php esc_html_e('Sep 15, 2025', 'braveschat'); ?></div>
                            </div>
                            <div class="braves-timeline__card-side">
                                <div class="braves-changelog__version">
                                    <h3 class="braves-changelog__title">
                                        <?php esc_html_e('Initial release', 'braveschat'); ?>
                                    </h3>
                                    <div class="braves-changelog__section">
                                        <h4><?php esc_html_e('New Features', 'braveschat'); ?></h4>
                                        <ul>
                                            <li><?php esc_html_e('ADDED: AI chat widget integrated in WordPress via Gutenberg block.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Direct connection to N8N workflows via configurable webhook.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Two display modes: floating modal and full screen.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Configurable floating button positioning: right, left, or center.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Welcome messages, chat title and subtitle customizable from the panel.', 'braveschat'); ?></li>
                                            <li><?php esc_html_e('ADDED: Ready for translation into any language.', 'braveschat'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div><!-- .braves-admin-content -->

        </div><!-- .braves-admin-body -->

    </div><!-- .braves-admin-container -->
</div><!-- .wrap -->
