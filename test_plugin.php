<?php
/**
 * Script de diagnóstico para identificar errores en el plugin
 */

// Activar display de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "=== INICIANDO DIAGNÓSTICO DEL PLUGIN ===\n\n";

// Simular constantes de WordPress
define('ABSPATH', '/Applications/XAMPP/xamppfiles/htdocs/wordpress/');
define('WLAND_CHAT_VERSION', '1.1.0');
define('WLAND_CHAT_PLUGIN_DIR', __DIR__ . '/');
define('WLAND_CHAT_PLUGIN_URL', 'http://localhost/wordpress/wp-content/plugins/Wland-Chat-iA/');
define('WLAND_CHAT_PLUGIN_FILE', __FILE__);
define('WLAND_CHAT_TEXT_DOMAIN', 'wland-chat');

// Constantes de WordPress necesarias
if (!defined('YEAR_IN_SECONDS')) {
    define('YEAR_IN_SECONDS', 31536000);
}
if (!defined('COOKIEPATH')) {
    define('COOKIEPATH', '/');
}
if (!defined('COOKIE_DOMAIN')) {
    define('COOKIE_DOMAIN', '');
}

echo "1. Cargando class_helpers.php...\n";
require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class_helpers.php';
echo "   ✓ class_helpers.php cargado correctamente\n\n";

echo "2. Cargando class_cookie_manager.php...\n";
require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class_cookie_manager.php';
echo "   ✓ class_cookie_manager.php cargado correctamente\n\n";

echo "3. Verificando clase WlandChat\\WlandCookieManager...\n";
if (class_exists('WlandChat\\WlandCookieManager')) {
    echo "   ✓ Clase WlandCookieManager existe\n\n";
} else {
    echo "   ✗ ERROR: Clase WlandCookieManager NO existe\n\n";
}

echo "4. Verificando clase WlandChat\\Helpers...\n";
if (class_exists('WlandChat\\Helpers')) {
    echo "   ✓ Clase Helpers existe\n\n";
} else {
    echo "   ✗ ERROR: Clase Helpers NO existe\n\n";
}

echo "5. Cargando class_settings.php...\n";
require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class_settings.php';
echo "   ✓ class_settings.php cargado correctamente\n\n";

echo "6. Cargando class_customizer.php...\n";
require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class_customizer.php';
echo "   ✓ class_customizer.php cargado correctamente\n\n";

echo "7. Cargando class_block.php...\n";
require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class_block.php';
echo "   ✓ class_block.php cargado correctamente\n\n";

echo "8. Cargando class_frontend.php...\n";
require_once WLAND_CHAT_PLUGIN_DIR . 'includes/class_frontend.php';
echo "   ✓ class_frontend.php cargado correctamente\n\n";

echo "=== DIAGNÓSTICO COMPLETADO EXITOSAMENTE ===\n";
echo "No se encontraron errores de sintaxis o carga.\n";
