# Wland Chat iA - Documentación Técnica para Claude Code

> **Plugin**: Wland Chat iA
> **Versión**: 1.2.3
> **Autor**: Carlos Vera (BravesLab)
> **Diseño**: Bentō moderno
> **Patrón**: Singleton + Componentes modulares

Este archivo proporciona orientación técnica completa a Claude Code al trabajar en este repositorio.

---

## 📍 Entorno de Desarrollo

Este es un plugin de WordPress ubicado dentro de una instalación XAMPP:

- **Ruta del plugin**: `/Applications/XAMPP/xamppfiles/htdocs/wordpress/wp-content/plugins/Wland-Chat-iA`
- **Raíz de WordPress**: `/Applications/XAMPP/xamppfiles/htdocs/wordpress`
- **URL de prueba**: `http://localhost/wordpress/wp-admin`
- **PHP**: `/Applications/XAMPP/xamppfiles/bin/php` (usado para linting)
- **Actualización**: solo se actualizará versión cuando el usuario confirme que los cambios hechos funcionan correctamente, entoces se utilizará la siguiente estructura, se actualizan los archivos en este orden: 
    1. CLAUDE.md: agrega los cambios realizados, actualiza la estructura de archivos, incluye aclaraciones que puedan servir para un mejor desarrollo de parte de Claude en el futuro. 
    2. README.md: Agrega los cambios de funciones, estructura de archivos, mejoras, documentación, etc.
    3. CHANGELOG.md: Actualiza con las funciones, mejoras, implementaciones y demas datos importantes que deban estar aquí.
    4. wland_chat_ia.php: actualiza la versión del plugin.
    5. about.php: agrega la nueva versión en la lista de changelog de la página siguiendo las reglas abajo establecidas para actualizar la sección de about.

### Convenciones de Código

- **Nomenclatura**: `snake_case` para todas las variables, funciones y archivos
- **NO usar camelCase** en ningún lugar
- **JSDoc**: Cada función nueva debe incluir comentarios JSDoc
- **Namespace**: Todas las clases PHP usan `WlandChat\Admin`
- **Patrón Singleton**: Todos los componentes admin usan instancia única

### Comunicación con el Usuario

- **Respuestas concisas**: Al finalizar tareas, solo dar resúmenes breves
- **Sin información excesiva**: Evitar emojis, listas largas y detalles innecesarios a menos que realmente aporten valor
- **Optimización de tokens**: Priorizar respuestas directas y eficientes
- **NO usar TodoWrite**: Evitar usar la herramienta TodoWrite a menos que sea estrictamente necesario
- **Menos herramientas**: Minimizar llamadas a herramientas innecesarias

---

## 📁 Estructura de Archivos

```
wland-chat-ia/
├── wland_chat_ia.php                          # Plugin principal (v1.2.2)
├── includes/
│   ├── admin/                                 # Sistema de administración Bentō
│   │   ├── class_admin_controller.php         # Controlador principal
│   │   ├── class_template_helpers.php         # Helpers estáticos
│   │   ├── components/                        # Componentes reutilizables
│   │   │   ├── class_admin_header.php         # Header compartido
│   │   │   ├── class_admin_sidebar.php        # Sidebar compartido (5 secciones)
│   │   │   └── class_admin_content.php        # Content + Cards Bentō
│   │   └── templates/                         # Plantillas de páginas
│   │       ├── dashboard.php                  # Resumen
│   │       ├── settings.php                   # Ajustes
│   │       ├── appearance.php                 # Apariencia
│   │       ├── availability.php               # Horarios
│   │       └── gdpr.php                       # GDPR
│   ├── class_settings.php                     # WordPress Settings API
│   ├── class_chat_widget.php                  # Widget frontend
│   ├── class_gutenberg_block.php              # Bloque Gutenberg
│   └── ...
├── assets/
│   ├── css/
│   │   └── admin/
│   │       ├── variables.css                  # Variables CSS Bentō
│   │       ├── base.css                       # Estilos base
│   │       ├── components.css                 # Componentes (cards, toggles)
│   │       └── dashboard.css                  # Estilos específicos
│   ├── js/
│   │   ├── admin.js                           # Scripts admin
│   │   └── chat_widget.js                     # Widget frontend
│   └── media/
│       ├── wland-logo.svg                     # Logo del plugin
│       └── menu-icon.svg                      # Icono del menú WP
└── languages/                                 # Traducciones (i18n)
```

---

## 🏗️ Patrón de Arquitectura

### Principios de Diseño

1. **Singleton Pattern**: Todos los componentes admin usan instancia única
2. **Separación de Responsabilidades**: Cada clase tiene un propósito específico
3. **Componentes Reutilizables**: Header, Sidebar y Content compartidos
4. **Template Helpers**: Métodos estáticos para renderizado rápido
5. **Namespace**: `WlandChat\Admin` para organización

### Flujo de Ejecución

```
WordPress Admin Menu
    ↓
Admin_Controller::register_admin_menu()
    ↓
Admin_Controller::render_*_page()
    ↓
Template (settings.php, appearance.php, etc.)
    ↓
┌─────────────────────────────────────┐
│ Admin_Header::render()              │
│ Admin_Sidebar::render($current_page)│
│ Template_Helpers::card()            │
│   └→ Admin_Content::render_card()   │
└─────────────────────────────────────┘
```

---

## 🧩 Componentes del Sistema

### 1. Admin_Controller

**Archivo**: `includes/admin/class_admin_controller.php`

**Responsabilidad**: Controlador principal que coordina todo el sistema admin.

**Métodos clave**:
```php
- register_admin_menu()          // Registra páginas en WordPress
- render_dashboard_page()        // Renderiza Resumen
- render_settings_page()         // Renderiza Ajustes
- render_appearance_page()       // Renderiza Apariencia
- render_availability_page()     // Renderiza Horarios
- render_gdpr_page()             // Renderiza GDPR
- enqueue_admin_assets()         // Carga CSS/JS
- is_wland_admin_page()          // Detecta páginas del plugin
```

**Registro de páginas**:
```php
// WordPress solo muestra "Wland Chat iA" en el menú
add_menu_page('Wland Chat iA', ...);

// Todas las demás páginas están ocultas (parent_slug = null)
add_submenu_page(null, 'Resumen', ...);
add_submenu_page(null, 'Ajustes', ...);
add_submenu_page(null, 'Apariencia', ...);
add_submenu_page(null, 'Horarios', ...);
add_submenu_page(null, 'GDPR', ...);
```

---

### 2. Admin_Header

**Archivo**: `includes/admin/components/class_admin_header.php`

**Responsabilidad**: Renderizar la cabecera con logo y versión.

**Uso**:
```php
$header = Admin_Header::get_instance();
$header->render(array(
    'show_logo' => true,
    'show_version' => true,
));
```

**Salida HTML**:
```html
<header class="wland-admin-header">
    <div class="wland-admin-header__logo">
        <img src="assets/media/wland-logo.svg" alt="Wland Chat iA">
        <span class="wland-admin-header__version">v1.2.2</span>
    </div>
</header>
```

---

### 3. Admin_Sidebar

**Archivo**: `includes/admin/components/class_admin_sidebar.php`

**Responsabilidad**: Navegación lateral compartida entre todas las páginas.

**Características**:
- 5 secciones con iconos SVG
- Estado activo automático
- Hook `wland_chat_admin_menu_items` para extensibilidad

**Uso**:
```php
$sidebar = Admin_Sidebar::get_instance();
$sidebar->render($current_page);
```

**Estructura de menú**:
```php
array(
    array('id' => 'dashboard',   'label' => 'Resumen',    'page_slug' => 'wland-chat-ia'),
    array('id' => 'settings',    'label' => 'Ajustes',    'page_slug' => 'wland-chat-settings'),
    array('id' => 'appearance',  'label' => 'Apariencia', 'page_slug' => 'wland-chat-appearance'),
    array('id' => 'availability','label' => 'Horarios',   'page_slug' => 'wland-chat-availability'),
    array('id' => 'gdpr',        'label' => 'GDPR',       'page_slug' => 'wland-chat-gdpr'),
)
```

---

### 4. Admin_Content

**Archivo**: `includes/admin/components/class_admin_content.php`

**Responsabilidad**: Renderizar cards Bentō y componentes de contenido.

**Métodos**:
```php
- render_card($args)           // Card Bentō (CON SOPORTE PARA 'content')
- render_section($args)        // Sección con header
- render_toggle($args)         // Toggle moderno
- render_quick_action($args)   // Botón de acción rápida
- render_card_grid($cards)     // Grid de cards
```

**Uso de Cards (v1.2.2 - FIXED)**:
```php
Template_Helpers::card(array(
    'title' => 'Título del Card',
    'description' => 'Descripción breve',
    'content' => '<input type="text" name="field" class="wland-input">', // ✅ AHORA FUNCIONA
    'custom_class' => 'wland-card--full-width',
));
```

**Parámetros soportados**:
- `title` - Título del card (h3)
- `subtitle` - Subtítulo opcional
- `description` - Descripción (p)
- **`content`** - HTML personalizado (inputs, selects, textareas) ✅ v1.2.2
- `icon` - Icono SVG
- `action_text` / `action_url` - Botón de acción
- `footer` - Pie del card
- `custom_class` - Clases CSS adicionales

---

### 5. Template_Helpers

**Archivo**: `includes/admin/class_template_helpers.php`

**Responsabilidad**: Helpers estáticos para renderizado rápido.

**Métodos disponibles**:
```php
Template_Helpers::card($args)           // Renderiza card
Template_Helpers::section($args)        // Renderiza sección
Template_Helpers::toggle($args)         // Renderiza toggle
Template_Helpers::quick_action($args)   // Renderiza botón
Template_Helpers::card_grid($cards)     // Renderiza grid
Template_Helpers::notice($msg, $type)   // Renderiza notice
Template_Helpers::get_icon($name)       // Obtiene SVG
Template_Helpers::get_config_status()   // Estado del plugin
```

**Ejemplo de uso en templates**:
```php
<?php
Template_Helpers::notice('Configuración guardada correctamente.', 'success');

Template_Helpers::card(array(
    'title' => 'URL del Webhook',
    'description' => 'Endpoint de N8N',
    'content' => '<input type="url" name="wland_chat_webhook_url" value="..." class="wland-input">',
));
?>
```

---

## 📄 Estructura de Templates

### Anatomía de un Template

Todos los templates siguen la misma estructura:

```php
<?php
// 1. Imports
use WlandChat\Admin\Admin_Header;
use WlandChat\Admin\Admin_Sidebar;
use WlandChat\Admin\Template_Helpers;

// 2. Seguridad
if (!defined('ABSPATH')) exit;
if (!current_user_can('manage_options')) wp_die('...');

// 3. Variables
$header = Admin_Header::get_instance();
$sidebar = Admin_Sidebar::get_instance();
$settings_updated = isset($_GET['settings-updated']);
$option_prefix = 'wland_chat_';
?>

<!-- 4. Layout -->
<div class="wrap wland-admin-wrap">
    <div class="wland-admin-container">

        <!-- Header -->
        <?php $header->render(array('show_logo' => true, 'show_version' => true)); ?>

        <div class="wland-admin-body">

            <!-- Sidebar -->
            <?php $sidebar->render($current_page); ?>

            <!-- Content -->
            <div class="wland-admin-content">

                <!-- Page Header -->
                <div class="wland-page-header">
                    <h1 class="wland-page-title">Título</h1>
                    <p class="wland-page-description">Descripción</p>
                </div>

                <!-- Success Notice -->
                <?php if ($settings_updated): ?>
                    <?php Template_Helpers::notice('Guardado correctamente.', 'success'); ?>
                <?php endif; ?>

                <!-- Form -->
                <form action="options.php" method="post">
                    <?php settings_fields('wland_chat_settings'); ?>

                    <div class="wland-section">
                        <h2 class="wland-section__title">Sección</h2>

                        <div class="wland-card-grid wland-card-grid--2-cols">

                            <?php
                            // Renderizar campos con Template_Helpers::card()
                            ?>

                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="wland-section wland-section--actions">
                        <?php submit_button('Guardar cambios', 'primary wland-button'); ?>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
```

---

## 🎨 Sistema de Estilos CSS

### Cascada de CSS

```
variables.css         → Tokens de diseño (colores, tipografía, espaciado)
    ↓
base.css             → Reset + estilos base
    ↓
components.css       → Cards, toggles, buttons, inputs, notices
    ↓
dashboard.css        → Estilos específicos de páginas
```

### Clases CSS Principales

**Layout**:
```css
.wland-admin-wrap          /* Wrapper principal */
.wland-admin-container     /* Container con padding */
.wland-admin-header        /* Header con logo */
.wland-admin-body          /* Body con sidebar + content */
.wland-admin-sidebar       /* Sidebar de navegación */
.wland-admin-content       /* Área de contenido */
```

**Componentes**:
```css
.wland-card                     /* Card Bentō */
.wland-card__title              /* Título del card */
.wland-card__description        /* Descripción del card */
.wland-card__content            /* ✅ Contenido del card (v1.2.2) */
.wland-card--full-width         /* Card ancho completo */

.wland-card-grid                /* Grid de cards */
.wland-card-grid--2-cols        /* Grid de 2 columnas */
.wland-card-grid--3-cols        /* Grid de 3 columnas */

.wland-toggle-wrapper           /* Wrapper del toggle */
.wland-toggle-input             /* Input checkbox */
.wland-toggle-slider            /* Slider visual */

.wland-input                    /* Input text/url/password */
.wland-textarea                 /* Textarea */
.wland-select                   /* Select */
.wland-button                   /* Botón */
.wland-button--primary          /* Botón primario */

.wland-notice                   /* Notice/alert */
.wland-notice--success          /* Success message */
.wland-notice--error            /* Error message */
```

---

## 🔧 WordPress Settings API

### Registro de Opciones

**Archivo**: `includes/class_settings.php`

Todas las opciones se registran con prefijo `wland_chat_`:

```php
// Ajustes
wland_chat_global_enable         // boolean
wland_chat_webhook_url           // string (URL)
wland_chat_n8n_auth_token        // string
wland_chat_excluded_pages        // array (IDs)

// Apariencia
wland_chat_header_title          // string
wland_chat_header_subtitle       // string
wland_chat_welcome_message       // string (textarea)
wland_chat_position              // string (bottom-right|bottom-left|center)
wland_chat_display_mode          // string (modal|fullscreen)

// Horarios
wland_chat_availability_enabled  // boolean
wland_chat_availability_start    // string (time)
wland_chat_availability_end      // string (time)
wland_chat_availability_timezone // string
wland_chat_availability_message  // string (textarea)

// GDPR
wland_chat_gdpr_enabled          // boolean
wland_chat_gdpr_message          // string (textarea)
wland_chat_gdpr_accept_text      // string
```

### Guardar Datos

Los formularios usan el Settings API nativo de WordPress:

```php
<form action="options.php" method="post">
    <?php settings_fields('wland_chat_settings'); ?>

    <!-- Campos aquí -->

    <?php submit_button(); ?>
</form>
```

---

## 🚀 Extensibilidad

### Hooks Disponibles

```php
// Sidebar: Agregar items de navegación
add_filter('wland_chat_admin_menu_items', function($items) {
    $items[] = array(
        'id' => 'custom',
        'label' => 'Mi Sección',
        'url' => admin_url('admin.php?page=custom'),
        'page_slug' => 'custom',
        'icon' => '<svg>...</svg>',
    );
    return $items;
});

// Sidebar: Agregar contenido extra
add_action('wland_chat_admin_sidebar_items', function($current_page) {
    echo '<div class="custom-sidebar-content">...</div>';
});
```

---

## 📦 Cambios en v1.2.3

### 🎨 Sistema de Personalización de Colores

**Nueva funcionalidad**: Selector de colores para personalizar el aspecto visual del chat desde el panel de Apariencia.

**Características**:
- 4 campos de color personalizables: Burbuja, Primario, Fondo y Texto
- Color picker nativo HTML5 (40x40px) con input de texto hexadecimal
- Paleta de colores del tema de WordPress (colapsable)
- Paleta por defecto de 8 colores cuando el tema no tiene colores personalizados
- CSS inyectado dinámicamente en el frontend con `!important` rules
- Alineación horizontal usando `display: inline-block` con `vertical-align: middle`

**Implementación**:
- Color picker de 40x40px con border-radius 6px
- Input text readonly mostrando código hexadecimal en mayúsculas
- Toggle button para expandir/colapsar paleta de colores del tema
- Botones de color preset de 32x32px con efecto hover
- Helpers PHP para aclarar/oscurecer colores: `lighten_color()` y `darken_color()`

**Archivos nuevos**:
- `assets/js/color_picker.js` - Sincronización color picker con input text

**Archivos modificados**:
- `class_settings.php` - Registro de 4 opciones de color (default: #01B7AF, #FFFFFF, #333333)
- `appearance.php` - Cards Bentō con color pickers y paletas colapsables
- `class_frontend.php` - Método `inject_custom_colors()` con CSS inline
- `components.css` - Estilos para `.wland-color-picker`, `.wland-palette-toggle`, `.wland-color-preset`
- `class_admin_controller.php` - Enqueue color_picker.js

**Opciones registradas**:
```php
wland_chat_bubble_color      // Color del botón flotante (default: #01B7AF)
wland_chat_primary_color     // Color del header y mensajes IA (default: #01B7AF)
wland_chat_background_color  // Color de fondo del chat (default: #FFFFFF)
wland_chat_text_color        // Color del texto de mensajes (default: #333333)
```

**CSS inyectado**:
El método `inject_custom_colors()` en `class_frontend.php` aplica los colores a:
- Botón flotante del chat (`#chat-toggle`)
- Header del modal y fullscreen (`#chat-header`)
- Mensajes del asistente (`.message.assistant`)
- Fondo del área de mensajes (`#chat-messages`)
- Color de texto de los mensajes (`.message-text`)
- Input box de escritura (`#message-input`, `#send-button`)

### ✨ Sistema de Iconos SVG Personalizables

**Nueva funcionalidad**: Selector de iconos para el botón flotante del chat.

**Características**:
- 4 iconos SVG optimizados: Original (robot), Círculo, Happy, Burbuja
- Selector estilo tabs Bentō en página de Apariencia
- Iconos con `width="48" height="48"` desde viewBox 460x460
- Opción `wland_chat_chat_icon` registrada en Settings API
- Icono por defecto: "Original" (robot-chat)

**Implementación**:
- Tabs horizontales con fondo gris claro `#f9fafb`
- Tab seleccionado con borde morado `#5B4CCC`
- Responsive: 2 columnas en móvil (max-width: 782px)
- JavaScript interactivo en `icon_selector.js`

**Archivos nuevos**:
- `assets/media/chat-icons/*.svg` - 4 iconos SVG
- `assets/js/icon_selector.js` - Selector tabs

**Archivos modificados**:
- `class_settings.php` - Registro opción `chat_icon` (default: robot-chat)
- `appearance.php` - Selector tabs Bentō
- `components.css` - Estilos `.wland-icon-tabs`
- `class_admin_controller.php` - Enqueue icon_selector.js
- `modal.php` / `screen.php` - `<img>` SVG en botón flotante
- `class_frontend.php` - Eliminada dependencia Lottie

### 🐛 Eliminación de Lottie Player

**Problema**: Dependencia externa CDN causaba errores de consola.

**Solución**:
1. ✅ Eliminado `lottie-player` de wp_enqueue_script
2. ✅ Removido `animationPath` de configuración JS
3. ✅ Templates usan `<img id="chat-icon">` en lugar de `<div id="chat-lottie">`
4. ✅ JavaScript maneja `this.chat_icon` con show/hide

**Archivos modificados**:
- `wland_chat_block_modal.js` - Eliminado init_lottie_animation()
- `wland_chat_block_screen.js` - Eliminado init_lottie_animation()
- `class_frontend.php` - Eliminado wp_dequeue_script('lottie-player')

### 🔧 Fallback wp.i18n

**Mejora**: Compatibilidad cuando traducciones no están disponibles.

**Implementación**:
```javascript
const { __, _x, _n, sprintf } = window.wp && window.wp.i18n ? window.wp.i18n : {
    __: (text) => text,
    _x: (text) => text,
    _n: (single, plural, number) => number === 1 ? single : plural,
    sprintf: (format, ...args) => format
};
```

---

## 📦 Cambios en v1.2.2

### 🐛 Corrección Crítica

**Problema**: Los inputs no se renderizaban en las tarjetas Bentō.

**Causa**: `Admin_Content::render_card()` no tenía soporte para el parámetro `content`.

**Solución**:
1. ✅ Agregado `'content' => ''` a defaults
2. ✅ Agregado bloque de renderizado con `<div class="wland-card__content">`
3. ✅ Configurado `wp_kses()` con whitelist completa para inputs

**Archivos modificados**:
- `includes/admin/components/class_admin_content.php` (líneas 95-152)
- `includes/admin/templates/settings.php` (reescrito con ob_start)
- `includes/admin/templates/appearance.php` (reescrito con ob_start)
- `includes/admin/templates/availability.php` (reescrito con ob_start)
- `includes/admin/templates/gdpr.php` (reescrito con ob_start)

### 🎨 Corrección de Estilos Inconsistentes (v1.2.2.1)

**Problema**: El Dashboard se veía diferente a las páginas de Ajustes/Apariencia/Horarios/GDPR.
- Background color diferente
- Menú lateral de WordPress con colores inconsistentes
- Variables CSS no aplicadas en todas las páginas

**Causa**: Los selectores CSS en `dashboard.css` solo aplicaban a `.toplevel_page_wland-chat-ia`, pero las subpáginas tienen identificadores diferentes (`.admin_page_wland-chat-settings`, etc.).

**Solución**:
1. ✅ Extendido todos los selectores CSS para incluir las 5 páginas del plugin
2. ✅ Agregado estilos del menú lateral de WordPress para mantener consistencia
3. ✅ Agregado carga de `settings.css` en el controlador
4. ✅ Aplicado background `#f3f6fc` a todas las páginas
5. ✅ Forzado estado activo del menú "Wland Chat iA" en todas las subpáginas

**Archivos modificados**:
- `assets/css/admin/dashboard.css` (líneas 13-64, 362-382)
- `includes/admin/class_admin_controller.php` (líneas 276-281)

**Selectores CSS agregados**:
```css
/* Ahora aplican a TODAS las páginas del plugin */
.toplevel_page_wland-chat-ia,
.admin_page_wland-chat-settings,
.admin_page_wland-chat-appearance,
.admin_page_wland-chat-availability,
.admin_page_wland-chat-gdpr {
    background-color: #f3f6fc;
    --wp-components-color-accent: #3858e9;
    /* ... */
}
```

### 🎨 Mejora de Toggles Estilo Bentō (v1.2.2.2)

**Mejora**: Todos los checkboxes ahora usan toggles estilo Bentō para una apariencia más moderna y consistente.

**Implementación**: Agregados estilos CSS simplificados para toggles que funcionan con la estructura HTML existente.

**Archivos modificados**:
- `assets/css/admin/components.css` (líneas 287-341)

**Uso en templates**:
```php
<label class="wland-toggle-wrapper">
    <input type="checkbox"
           id="option_name"
           name="option_name"
           value="1"
           <?php checked(1, $value); ?>
           class="wland-toggle-input">
    <span class="wland-toggle-slider"></span>
</label>
```

**Características del toggle**:
- ✅ Ancho: 48px, Alto: 24px
- ✅ Color inactivo: gris (`--wland-gray-300`)
- ✅ Color activo: azul primario (`--wland-primary`)
- ✅ Animación suave de transición
- ✅ Focus state accesible
- ✅ Estado disabled con opacidad reducida

### 📄 Nueva Página "Acerca de" (v1.2.2.3)

**Nueva funcionalidad**: Página oculta accesible desde el badge de versión que muestra información del plugin, changelog y créditos del equipo.

**Características**:
- No aparece en el sidebar de navegación
- Accesible haciendo clic en el badge de versión en el header
- Muestra información del plugin, equipo de desarrollo y historial de cambios
- Diseño Bentō consistente con el resto del admin

**Archivos creados**:
- `includes/admin/templates/about.php` - Template de la página

**Archivos modificados**:
- `includes/admin/class_admin_controller.php` - Registro de página oculta y método render_about_page()
- `includes/admin/components/class_admin_header.php` - Badge de versión clickeable
- `assets/css/admin/components.css` - Estilos para badges clickeables, equipo y changelog
- `assets/css/admin/dashboard.css` - Selectores CSS para incluir la nueva página

**Secciones de la página About**:
1. **Información del Plugin**: Versión, autor y empresa
2. **Equipo de Desarrollo**: Carlos Vera, Mikel Marqués, Claude
3. **Historial de Cambios**: Changelog completo con versiones 1.2.2, 1.2.1, 1.1.2, 1.1.1
4. **Enlaces Útiles**: GitHub, BravesLab Website, Soporte

### 🔧 Correcciones Críticas y Mejoras UX (v1.2.2 - Actualización Final)

**Problemas corregidos**:

1. **Pérdida de ajustes al guardar desde diferentes páginas**
   - **Problema**: Al guardar desde Settings, se perdían los ajustes de Appearance. Al guardar desde Appearance, se perdían Settings, etc.
   - **Causa**: WordPress Settings API sobrescribe TODAS las opciones en un grupo cuando se guarda, pero cada formulario solo enviaba sus propios campos visibles
   - **Solución**: Creado método `render_hidden_fields()` en `class_settings.php` que incluye campos ocultos con valores de otras secciones
   - **Archivos modificados**:
     - `includes/class_settings.php` - Nuevo método render_hidden_fields()
     - `includes/admin/templates/settings.php` - Campos ocultos agregados
     - `includes/admin/templates/appearance.php` - Campos ocultos agregados
     - `includes/admin/templates/availability.php` - Campos ocultos agregados
     - `includes/admin/templates/gdpr.php` - Campos ocultos agregados

2. **Icono del menú mostraba color gris en vez de blanco cuando estaba activo**
   - **Problema**: En páginas sin parent_slug (Settings, Appearance, etc.), el icono del menú no se mostraba blanco
   - **Solución**: JavaScript añade dinámicamente las clases `wp-has-current-submenu` y `wp-menu-open` al elemento del menú
   - **Archivos modificados**: `includes/admin/class_admin_controller.php` - Método add_menu_icon_active_styles()

3. **Script admin_settings.js no se cargaba en todas las páginas**
   - **Problema**: Las notificaciones de éxito no desaparecían automáticamente en páginas Appearance, Availability y GDPR
   - **Causa**: Script solo se encolaba en Settings page
   - **Solución**: Movido enqueue de script a `class_admin_controller.php` para todas las páginas del plugin
   - **Archivos modificados**: `includes/admin/class_admin_controller.php` - Método enqueue_admin_assets()

**Mejoras de UX implementadas**:

1. **Auto-ocultación de notificaciones de éxito**
   - **Implementación**: Sistema de auto-hide con animación slide-up después de 3 segundos
   - **Animación**: Transición suave con `translateY(-20px)` y fade-out
   - **Archivos modificados**:
     - `assets/js/admin_settings.js` - Función init_notice_autohide()
     - `assets/css/admin/components.css` - Keyframe wland-notice-slide-out

2. **Actualización de iconos de sidebar a versiones sólidas**
   - Horarios: Cambiado de `access_time` (outline) a `access_time_filled` (solid)
   - GDPR: Cambiado de `lock` (outline) a `lock` (solid/filled)
   - **Archivos modificados**: `includes/admin/components/class_admin_sidebar.php`

3. **Actualización de iconos en página About**
   - Versión: `docs` → `verified` (check badge)
   - Autor: `settings` → `person_pin` (person card)
   - Empresa: `chat` → `business_center` (briefcase)
   - **Archivos modificados**:
     - `includes/admin/class_template_helpers.php` - Nuevos iconos agregados
     - `includes/admin/templates/about.php` - Iconos actualizados

4. **Tarjetas informativas clicables**
   - Tarjetas de información del plugin ahora son clicables con enlaces externos
   - Mejora de UX para acceso rápido a GitHub y BravesLab website
   - **Archivos modificados**: `includes/admin/templates/about.php`

**Archivos técnicos modificados**:
- Total de archivos modificados: 12
- Líneas de código agregadas: ~150
- Nueva funcionalidad JavaScript: init_notice_autohide()
- Nuevas animaciones CSS: wland-notice-slide-out
- Nuevos iconos Material Design: verified, person_pin, business_center

---

## 🛠️ Comandos Útiles

### Linting PHP

```bash
# Verificar sintaxis de un archivo
/Applications/XAMPP/xamppfiles/bin/php -l archivo.php

# Verificar todos los archivos PHP del plugin
find . -name "*.php" -exec /Applications/XAMPP/xamppfiles/bin/php -l {} \;
```

### Testing en Navegador

```bash
# URL del admin de WordPress
http://localhost/wordpress/wp-admin

# URL de la página del plugin
http://localhost/wordpress/wp-admin/admin.php?page=wland-chat-ia
```

### Git Workflow

```bash
# Verificar estado antes de commit
git status
git diff

# Crear commit con mensaje descriptivo
git add .
git commit -m "v1.2.2 - Fix: Cards content rendering + Form inputs functional"

# Push a repositorio
git push origin main
```

---

## 🐛 Debugging

### Console Logs

En desarrollo, buscar estos logs en la consola del navegador:

```javascript
[Wland Fingerprint] Nueva sesión creada: ...
[Wland Chat Modal] Usando session_id con fingerprinting: ...
[Wland Chat] Error en webhook: ...
```

### DevTools

**Cookies** (Application > Cookies):
```
wland_chat_session: [hash SHA-256]
```

**Local Storage** (Application > Local Storage):
```
wland_chat_session_backup: [hash]
wland_chat_fingerprint: {...}
wland_chat_gdpr_consent: accepted
```

### Errores Comunes

1. **Inputs no aparecen**: Verificar que `Admin_Content::render_card()` tiene soporte para `content`
2. **Formularios no guardan**: Verificar nonces y permisos (`manage_options`)
3. **CSS no carga**: Purgar caché del navegador y plugin de caché
4. **PHP Fatal Error**: Verificar sintaxis con `php -l` antes de recargar

---

## 📋 Checklist de Desarrollo

Antes de hacer commit de cambios importantes:

- [ ] ✅ Todos los archivos PHP pasan `php -l` (linting)
- [ ] ✅ Funcionalidad testeada en navegador
- [ ] ✅ Sin errores en consola del navegador
- [ ] ✅ Formularios guardan datos correctamente
- [ ] ✅ Diseño Bentō consistente en todas las páginas
- [ ] ✅ Compatibilidad con WordPress 5.8+
- [ ] ✅ README.md actualizado si hay nuevas features
- [ ] ✅ CHANGELOG.md actualizado con cambios
- [ ] ✅ Versión actualizada en `wland_chat_ia.php`
- [ ] ✅ Página About actualizada con changelog respetando emojis patterns

---

## 📝 Guía de Changelog (IMPORTANTE)

Cuando actualices el changelog en la página About (`includes/admin/templates/about.php`), **SIEMPRE debes respetar** los siguientes emojis y nombres de sección para mantener coherencia:

### Emojis Estándar por Tipo de Cambio

```php
🔧 Correcciones          // Para bugs fixes (FIXED)
🎁 Mejoras               // Para improvements (ADDED, IMPROVED)
⚙️ Características       // Para nuevas features principales (ADDED)
🔁 Cambios               // Para cambios no-breaking (CHANGED)
🛠️ Funcionalidades Principales  // Para features core del plugin
🧬 Arquitectura          // Para cambios estructurales/técnicos
🔒 Seguridad             // Para mejoras de seguridad
🇻🇪 i18n                 // Para traducciones e internacionalización
```

### Ejemplo de Uso en About Page

```php
<div class="wland-changelog__section">
    <h4><?php _e('🔧 Correcciones', 'wland-chat'); ?></h4>
    <ul>
        <li><?php _e('FIXED: Descripción del bug corregido', 'wland-chat'); ?></li>
    </ul>
</div>

<div class="wland-changelog__section">
    <h4><?php _e('🎁 Mejoras', 'wland-chat'); ?></h4>
    <ul>
        <li><?php _e('ADDED: Nueva funcionalidad agregada', 'wland-chat'); ?></li>
        <li><?php _e('IMPROVED: Mejora a funcionalidad existente', 'wland-chat'); ?></li>
    </ul>
</div>
```

### Prefijos de Mensaje

- **FIXED**: Para correcciones de bugs
- **ADDED**: Para nuevas funcionalidades
- **IMPROVED**: Para mejoras a funcionalidades existentes
- **CHANGED**: Para cambios que no son bugs ni mejoras
- **REMOVED**: Para funcionalidades eliminadas
- **DEPRECATED**: Para funcionalidades marcadas como obsoletas

### Reglas Importantes

1. ✅ **SIEMPRE usar los emojis exactos** de la lista anterior
2. ✅ **NUNCA inventar nuevos emojis** para secciones
3. ✅ **Reutilizar nombres de título** de versiones anteriores (v1.2.1, v1.1.1, etc.)
4. ✅ **Agrupar cambios por tipo** usando las secciones correspondientes
5. ✅ **Usar formato de fecha**: "DD de Mes, YYYY" (ejemplo: "26 de Octubre, 2024")
6. ✅ **Incluir función `_e()` para i18n** en todos los textos

### Dónde Aplicar

- **Archivo**: `includes/admin/templates/about.php`
- **Sección**: Dentro de `.wland-changelog` > `.wland-changelog__version`
- **Contexto**: Al crear una nueva versión del plugin

### Proceso de Actualización del Changelog

**IMPORTANTE**: Cuando actualices el changelog en `about.php`, sigue este proceso:

1. **Lee primero `CHANGELOG.md`** para obtener la fecha exacta y los cambios completos
2. **Compara las fechas** entre `CHANGELOG.md` y `about.php` para asegurar coherencia
3. **Copia la estructura** de cambios desde `CHANGELOG.md` adaptándola al formato con emojis
4. **Verifica la fecha** - debe ser idéntica en ambos archivos
5. **Traduce los prefijos** al formato correspondiente (FIXED → sección 🔧 Correcciones, ADDED/IMPROVED → sección 🎁 Mejoras)

**Ejemplo**:

Si en `CHANGELOG.md` dice:
```markdown
## [1.2.2] - 2024-10-26
### Fixed
- Los ajustes se perdían al guardar desde diferentes páginas
```

En `about.php` debe quedar:
```php
<p class="wland-changelog__date"><?php _e('26 de Octubre, 2024', 'wland-chat'); ?></p>

<div class="wland-changelog__section">
    <h4><?php _e('🔧 Correcciones', 'wland-chat'); ?></h4>
    <ul>
        <li><?php _e('FIXED: Los ajustes se perdían al guardar desde diferentes páginas', 'wland-chat'); ?></li>
    </ul>
</div>
```

⚠️ **Nunca inventes fechas** - siempre consulta `CHANGELOG.md` como fuente de verdad.

---

## 📚 Recursos

### WordPress
- **Settings API**: https://developer.wordpress.org/plugins/settings/
- **Hooks Reference**: https://developer.wordpress.org/reference/hooks/
- **Coding Standards**: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/

### Diseño
- **Bentō Design**: https://bento.me

### Herramientas
- **XAMPP**: https://www.apachefriends.org/
- **PHP**: https://www.php.net/
- **N8N**: https://n8n.io/

---

## 📞 Soporte y Contacto

- **Email**: carlos@braveslab.com
- **GitHub Issues**: https://github.com/Carlos-Vera/Wland-Chat-iA/issues
- **Documentación Usuario**: Ver [README.md](README.md)
- **Historial de Cambios**: Ver [CHANGELOG.md](CHANGELOG.md)

---

## 👥 Autores

- **Carlos Vera** - Desarrollo principal - carlos@braveslab.com
- **Mikel Marqués** - Contribuciones - hola@mikimonokia.com
- **Claude (Anthropic)** - Asistencia en desarrollo v1.2.x

---

**Wland Chat iA** - Documentación técnica v1.2.2

© 2025 Braves Lab LLC. Todos los derechos reservados.
