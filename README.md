# Wland Chat iA

Plugin WordPress profesional para integrar chat con inteligencia artificial mediante bloque Gutenberg.

**Versión actual**: 1.2.3

## 📋 Tabla de Contenidos

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#️-configuración)
  - [Ajustes](#1-ajustes-configuración-general)
  - [Apariencia](#2-apariencia)
  - [Horarios](#3-horarios)
  - [GDPR](#4-gdpr-compliance-de-cookies)
- [Uso del Bloque Gutenberg](#uso-del-bloque)
- [Desarrollo](#-desarrollo)
- [Sistema de Cookies](#sistema-de-cookies-con-fingerprinting)
- [Seguridad](#-seguridad)
- [Internacionalización](#internacionalización-i18n)
- [FAQ](#-preguntas-frecuentes-faq)
- [Solución de Problemas](#-solución-de-problemas)
- [Changelog](#changelog)
- [Compatibilidad](#-compatibilidad)
- [Licencia](#licencia)
- [Soporte](#soporte)

---

## Características

### Funcionalidades Principales
- **Bloque Gutenberg** para personalizar en cualquier página o entrada
- **Integración con N8N** mediante webhooks configurables
- **Sistema de Cookies con Fingerprinting** para identificación única de usuarios
- **Compliance GDPR** con banner de consentimiento configurable
- **Horarios de disponibilidad** con zonas horarias
- **Páginas excluidas** mediante selector múltiple
- **Dos modos de visualización**: Modal y Pantalla completa
- **Animación Lottie** en el botón de chat
- **Responsive** y optimizado para móviles
- **Accesibilidad** siguiendo estándares WCAG

### Panel de Administración (v1.2.x)

Desde la versión 1.2.0, el panel de administración ha sido completamente rediseñado con una arquitectura modular y diseño Bentō moderno:

- **5 páginas de configuración**: Resumen, Ajustes, Apariencia, Horarios, GDPR
- **Página "Acerca de" oculta** (v1.2.2.3): Accesible desde el badge de versión en el header
- **Navegación mediante sidebar compartido** entre todas las páginas
- **Diseño consistente de tarjetas Bento** en todas las interfaces
- **Formularios funcionales** integrados con WordPress Settings API (v1.2.2)
- **Toggles estilo Bento** en todos los checkboxes (v1.2.2.2)
- **Arquitectura de componentes** con patrón Singleton
- **Sistema modular de CSS** (variables, base, components, dashboard)
- **Template Helpers** para renderizado consistente

Para documentación técnica completa, consulta [CLAUDE.md](CLAUDE.md)

#### Página "Acerca de" (Nueva en v1.2.2.3)

Accede a información detallada del plugin haciendo clic en el **badge de versión** (esquina superior derecha del header):

- **Información del Plugin**: Versión actual, autor principal, empresa
- **Equipo de Desarrollo**: Créditos completos de Carlos Vera, Mikel Marqués y Claude
- **Historial de Cambios**: Changelog detallado con todas las versiones
- **Enlaces Útiles**: GitHub repository, BravesLab website, soporte técnico

### Configuración Avanzada
- URL del webhook personalizable
- Textos completamente editables
- Selector de páginas excluidas
- Horarios de inicio y fin
- Zonas horarias internacionales
- Mensajes personalizados fuera de horario

## Requisitos

- WordPress 5.8 o superior
- PHP 7.4 o superior
- Gutenberg (incluido en WordPress 5.0+)

## Instalación

### Método 1: Desde el archivo ZIP
1. Descarga el plugin como archivo ZIP
2. Ve a **Plugins > Añadir nuevo** en tu WordPress
3. Haz clic en **Subir plugin**
4. Selecciona el archivo ZIP descargado
5. Haz clic en **Instalar ahora**
6. Activa el plugin

### Método 2: Instalación manual
1. Descomprime el archivo ZIP
2. Sube la carpeta `wland-chat-ia` a `/wp-content/plugins/`
3. Activa el plugin desde el menú **Plugins** de WordPress

### Método 3: Desde GitHub
```bash
cd wp-content/plugins/
git clone https://github.com/Carlos-Vera/Wland-Chat-iA.git wland-chat-ia
```

## ⚙️ Configuración

### Acceso al Panel de Administración

Después de activar el plugin, encontrarás el menú **"Wland Chat iA"** en el panel lateral de WordPress.

### 1. Ajustes (Configuración General)

**Ubicación**: Wland Chat iA > Ajustes

Configura los parámetros fundamentales del chat:

- **Mostrar en toda la web**: Toggle para habilitar/deshabilitar el chat globalmente
- **URL del Webhook**: Introduce la URL de tu webhook de N8N (ejemplo: `https://flow.braveslab.com/webhook/...`)
- **Token de Autenticación N8N**: Token secreto opcional para autenticar las peticiones (header `X-N8N-Auth`)
- **Páginas Excluidas**: Selecciona páginas donde NO mostrar el chat (mantén Ctrl/Cmd para selección múltiple)

### 2. Apariencia

**Ubicación**: Wland Chat iA > Apariencia

Personaliza el aspecto visual del chat:

- **Título de la Cabecera**: Título principal del chat (default: "BravesLab AI Assistant")
- **Subtítulo de la Cabecera**: Subtítulo descriptivo (default: "Artificial Intelligence Marketing Agency")
- **Mensaje de Bienvenida**: Mensaje inicial que verá el usuario
- **Posición del Chat**:
  - Abajo a la derecha (default)
  - Abajo a la izquierda
  - Centro
- **Modo de Visualización**:
  - Modal (ventana flotante) - default
  - Pantalla completa

### 3. Horarios

**Ubicación**: Wland Chat iA > Horarios

Configura la disponibilidad del chat por horario:

- **Habilitar Horarios**: Toggle para activar restricciones de horario
- **Hora de Inicio**: Formato 24h (ejemplo: 09:00)
- **Hora de Fin**: Formato 24h (ejemplo: 18:00)
- **Zona Horaria**: Selecciona tu zona horaria (Europe/Madrid, America/New_York, etc.)
- **Mensaje Fuera de Horario**: Mensaje personalizado cuando el chat no está disponible

### 4. GDPR (Compliance de Cookies)

**Ubicación**: Wland Chat iA > GDPR

Configura el banner de consentimiento de cookies:

- **Habilitar Banner GDPR**: Toggle para mostrar/ocultar banner de consentimiento
- **Mensaje del Banner**: Texto informativo sobre el uso de cookies
- **Texto del Botón de Aceptar**: Etiqueta del botón (ejemplo: "Aceptar", "Entendido", "Acepto")

### Guardar Cambios

Todas las páginas incluyen un botón **"Guardar cambios"** al final del formulario. Los cambios se guardan en la base de datos de WordPress usando la Settings API.

## Uso del Bloque

### Agregar el Bloque en Gutenberg

1. Edita una página o entrada
2. Haz clic en el botón **+** para agregar un bloque
3. Busca **"Wland Chat iA"**
4. Haz clic para insertarlo

### Personalizar el Bloque

En el panel lateral derecho encontrarás:
- **URL del Webhook**: Específica para esta instancia
- **Título del Header**: Personalizado por bloque
- **Subtítulo del Header**: Personalizado por bloque
- **Mensaje de Bienvenida**: Personalizado por bloque
- **Posición del Chat**: Abajo derecha, izquierda o centro
- **Modo de Visualización**: Modal o pantalla completa

### Opciones Disponibles

```php
// Valores por defecto del bloque
webhookUrl: 'URL configurada en ajustes'
headerTitle: 'BravesLab AI Assistant'
headerSubtitle: 'Artificial Intelligence Marketing Agency'
welcomeMessage: '¡Hola! Soy el asistente de BravesLab...'
position: 'bottom-right' // 'bottom-left', 'center'
displayMode: 'modal' // 'fullscreen'
```

## 🔧 Desarrollo

### Estructura del Plugin

Para documentación técnica completa sobre arquitectura, componentes, clases PHP, sistema de CSS y guías de desarrollo, consulta [CLAUDE.md](CLAUDE.md).

```
wland-chat-ia/
├── wland_chat_ia.php                          # Archivo principal del plugin (entry point)
├── uninstall.php                              # Script de desinstalación limpia
├── includes/
│   ├── admin/                                 # Sistema de administración Bentō (v1.2.x)
│   │   ├── class_admin_controller.php         # Controlador principal, registra páginas y assets
│   │   ├── class_template_helpers.php         # Helpers estáticos para renderizado rápido
│   │   ├── components/                        # Componentes reutilizables (Singleton)
│   │   │   ├── class_admin_header.php         # Header con logo y versión
│   │   │   ├── class_admin_sidebar.php        # Sidebar de navegación (5 secciones)
│   │   │   └── class_admin_content.php        # Renderizado de cards Bentō, toggles, etc.
│   │   └── templates/                         # Plantillas de páginas admin
│   │       ├── dashboard.php                  # Página Resumen con métricas
│   │       ├── settings.php                   # Página Ajustes (webhook, token, exclusiones)
│   │       ├── appearance.php                 # Página Apariencia (títulos, posición, modo)
│   │       ├── availability.php               # Página Horarios (disponibilidad)
│   │       ├── gdpr.php                       # Página GDPR (consentimiento cookies)
│   │       └── about.php                      # Página Acerca de (oculta, accesible desde versión)
│   ├── class_settings.php                     # Registro de opciones con WordPress Settings API
│   ├── class_frontend.php                     # Renderizado del chat en frontend
│   ├── class_block.php                        # Registro del bloque Gutenberg
│   ├── class_customizer.php                   # Integración con WordPress Customizer
│   ├── class_cookie_manager.php               # Sistema de cookies con fingerprinting
│   └── class_helpers.php                      # Funciones auxiliares y utilidades
├── assets/
│   ├── css/
│   │   ├── admin/                             # CSS del panel admin (v1.2.x)
│   │   │   ├── variables.css                  # Variables CSS (colores, espaciado, fuentes)
│   │   │   ├── base.css                       # Estilos base y reset
│   │   │   ├── components.css                 # Componentes Bentō (cards, buttons, toggles)
│   │   │   └── dashboard.css                  # Estilos específicos de páginas
│   │   ├── wland_chat_block_modal.css         # Estilos del chat en modo modal
│   │   ├── wland_chat_block_screen.css        # Estilos del chat en modo pantalla completa
│   │   ├── wland_gdpr_banner.css              # Estilos del banner GDPR
│   │   ├── block_editor.css                   # Estilos del editor de bloques
│   │   └── block_style.css                    # Estilos base del bloque
│   ├── js/
│   │   ├── admin.js                           # Scripts del panel de administración
│   │   ├── block.js                           # Registro del bloque Gutenberg
│   │   ├── wland_chat_block_modal.js          # Lógica del chat modo modal
│   │   ├── wland_chat_block_screen.js         # Lógica del chat modo pantalla completa
│   │   └── wland_fingerprint.js               # Sistema de fingerprinting del navegador
│   └── media/
│       ├── chat.json                          # Animación Lottie del botón de chat
│       ├── wland-logo.svg                     # Logo del plugin
│       └── menu-icon.svg                      # Icono del menú WordPress
├── templates/                                 # Templates PHP frontend
│   ├── modal.php                              # Template HTML para modo modal
│   └── screen.php                             # Template HTML para modo pantalla completa
├── languages/                                 # Archivos de traducción (i18n)
│   ├── wland-chat.pot                         # Plantilla de traducción
│   └── wland-chat-es_ES.po                    # Traducción al español
├── CHANGELOG.md                               # Historial completo de cambios
├── CLAUDE.md                                  # Documentación técnica para desarrollo
├── README.md                                  # Este archivo (documentación general)
└── LICENSE                                    # Licencia comercial
```

### Hooks y Filtros Disponibles

#### Filtros

```php
// Modificar configuración del chat
add_filter('wland_chat_config', function($config) {
    $config['webhook_url'] = 'https://mi-webhook.com';
    return $config;
});

// Modificar mensaje de bienvenida
add_filter('wland_chat_welcome_message', function($message) {
    return 'Mensaje personalizado';
});

// Admin: Agregar items al sidebar
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
```

#### Acciones

```php
// Antes de renderizar el chat
add_action('wland_chat_before_render', function($attributes) {
    // Tu código aquí
});

// Después de renderizar el chat
add_action('wland_chat_after_render', function($attributes) {
    // Tu código aquí
});

// Admin: Agregar contenido al sidebar
add_action('wland_chat_admin_sidebar_items', function($current_page) {
    echo '<div class="custom-sidebar-content">...</div>';
});
```

### Funciones Auxiliares

```php
// Verificar si el chat debe mostrarse
\WlandChat\Helpers::should_display_chat();

// Verificar horarios de disponibilidad
\WlandChat\Helpers::is_within_availability_hours();

// Obtener configuración del chat
\WlandChat\Helpers::get_chat_config();

// Obtener mensaje de bienvenida apropiado
\WlandChat\Helpers::get_welcome_message();
```

## Internacionalización (i18n)

### Traducir el Plugin

1. Copia el archivo `languages/wland-chat.pot`
2. Usa [Poedit](https://poedit.net/) para crear traducciones
3. Guarda los archivos como:
   - `wland-chat-es_ES.po` y `wland-chat-es_ES.mo` (Español)
   - `wland-chat-fr_FR.po` y `wland-chat-fr_FR.mo` (Francés)
   - etc.
4. Coloca los archivos en `/wp-content/languages/plugins/`

### Cargar Traducciones Personalizadas

```php
add_filter('load_textdomain_mofile', function($mofile, $domain) {
    if ('wland-chat' === $domain) {
        $mofile = WP_CONTENT_DIR . '/languages/plugins/wland-chat-' . get_locale() . '.mo';
    }
    return $mofile;
}, 10, 2);
```

## 🔒 Seguridad

### Mejores Prácticas Implementadas

El plugin sigue todas las mejores prácticas de seguridad de WordPress:

- ✅ **Sanitización de inputs**: Todos los datos de usuario se sanitizan antes de guardarse
- ✅ **Validación en servidor**: Validación de tipos de datos, URLs y formatos
- ✅ **Nonces en formularios**: Protección contra ataques CSRF en todos los formularios
- ✅ **Escapado de salidas**: Uso de `esc_html()`, `esc_attr()`, `esc_url()`, etc.
- ✅ **Verificación de capacidades**: Solo usuarios con permisos `manage_options` pueden configurar
- ✅ **wp_kses() personalizado**: Whitelist específica para elementos de formulario en admin (v1.2.2)
- ✅ **Prepared statements**: Uso de `$wpdb->prepare()` para prevenir SQL injection
- ✅ **Validación de URLs**: Comprobación de URLs de webhooks
- ✅ **Hash seguro**: SHA-256 para fingerprinting de cookies
- ✅ **Flags de cookies**: Secure (HTTPS), SameSite=Lax para cookies persistentes

### Permisos y Acceso

- Solo usuarios con rol **Administrator** pueden acceder al panel de configuración
- Verificación de `current_user_can('manage_options')` en todas las páginas admin
- Verificación de `ABSPATH` en todos los archivos PHP

### Limpieza al Desinstalar

El plugin incluye `uninstall.php` que realiza limpieza completa:

- Elimina **todas las opciones** de la base de datos con prefijo `wland_chat_`
- Borra archivos y directorios creados por el plugin
- Limpia metadatos de posts y usuarios relacionados
- Limpia transients y caché de WordPress
- **No deja rastros** en la base de datos después de desinstalar

## Sistema de Cookies con Fingerprinting

**Versión 1.1.0+** incluye un sistema robusto de identificación de usuarios mediante cookies persistentes con fingerprinting del navegador.

### Características

#### Identificación Única de Usuarios
- **Cookie persistente** `wland_chat_session` con duración de 1 año
- **Hash SHA-256** de 64 caracteres hexadecimales
- **Fingerprinting multi-característica** del navegador y dispositivo
- **Fallback automático** a localStorage si las cookies están bloqueadas

#### Características del Fingerprint

El sistema genera un ID único basado en:
- User-Agent del navegador
- Resolución de pantalla (width × height × colorDepth)
- Zona horaria y offset
- Idioma y preferencias de idiomas
- Platform y arquitectura del sistema
- Hardware (CPU cores, memoria del dispositivo)
- Lista de plugins del navegador
- **Canvas fingerprint** (renderizado único por GPU)
- **WebGL fingerprint** (información de la tarjeta gráfica)
- Soporte táctil (maxTouchPoints)

#### Compliance GDPR

Banner de consentimiento configurable desde el panel de administración:

**Configuración**: WordPress Admin > Ajustes > Wland Chat iA > "Compliance GDPR / Cookies"

**Opciones disponibles**:
- **Habilitar Banner GDPR**: Activa/desactiva el banner de consentimiento
- **Mensaje del Banner**: Texto personalizable del aviso de cookies
- **Texto del Botón**: Personaliza el botón de aceptación (ej: "Aceptar", "Entendido")

El consentimiento se guarda en localStorage antes de crear cualquier cookie.

#### Detección Inteligente de Cambios

El sistema regenera automáticamente el session_id cuando detecta **2 o más cambios significativos**:
- Cambio de navegador o versión mayor
- Cambio de resolución de pantalla o monitor
- Cambio de zona horaria
- Cambio en canvas fingerprint (GPU diferente)

### Integración con N8N

Cada mensaje enviado al webhook de N8N incluye el campo `sessionId`:

```json
{
  "chatInput": "Mensaje del usuario",
  "sessionId": "9f12e684d6abd5ef281b2f33cff298d72f337083ceeb843d61ce84efe599486a"
}
```

Esto permite:
- **Mantener contexto de conversación** entre sesiones
- **Identificar usuarios únicos** sin datos personales
- **Analítica de uso** por dispositivo/navegador
- **Prevención de abusos** mediante rate limiting

### Verificación

#### Cookies (DevTools > Application > Cookies)
```
Name:      wland_chat_session
Value:     [hash de 64 caracteres]
Domain:    tu-dominio.com
Path:      /
Expires:   [1 año desde creación]
SameSite:  Lax
Secure:    ✓ (solo en HTTPS)
```

#### Local Storage (DevTools > Application > Local Storage)
```
wland_chat_session_backup:  [hash de 64 caracteres] (fallback)
wland_chat_fingerprint:     [objeto JSON con datos del fingerprint]
wland_chat_gdpr_consent:    accepted
```

#### Consola del Navegador
```javascript
[Wland Fingerprint] Nueva sesión creada: 9f12e684d6abd5ef281b2f...
[Wland Chat Modal] Usando session_id con fingerprinting: 9f12e684d6abd5ef281b2f...
```

### Implementación Técnica

#### Archivos del Sistema

**PHP**:
- `includes/class_cookie_manager.php` - Gestión de cookies y configuración GDPR

**JavaScript**:
- `assets/js/wland_fingerprint.js` - Clase WlandFingerprint con generación de hash

**CSS**:
- `assets/css/wland_gdpr_banner.css` - Estilos del banner GDPR responsive

#### Flujo de Funcionamiento

1. Usuario visita el sitio por primera vez
2. Si GDPR habilitado → Muestra banner y espera consentimiento
3. Genera fingerprint del navegador (múltiples características)
4. Calcula hash SHA-256 usando Web Crypto API
5. Crea cookie `wland_chat_session` (1 año de duración)
6. Guarda backup en localStorage por si cookies bloqueadas
7. Cada mensaje incluye `sessionId` en el payload a N8N
8. En visitas futuras, verifica cambios y regenera si es necesario

### Privacidad y Seguridad

- **No se almacenan datos personales** (nombre, email, IP, etc.)
- **Hash irreversible** - imposible obtener datos originales del fingerprint
- **Compliance GDPR** con consentimiento explícito opcional
- **Flags de seguridad**: Secure (HTTPS), SameSite=Lax
- **Fallback respetuoso** si usuario bloquea cookies

## Changelog

Ver historial completo de cambios y detalles técnicos en [CHANGELOG.md](CHANGELOG.md).

### Versión Actual: 1.2.3 (26 de Octubre, 2025)

**🎨 Nuevas Funcionalidades**:
- Added: Sistema de personalización de colores desde el panel de Apariencia
- Added: 4 campos de color personalizables: Color de la Burbuja, Color Primario, Color de Fondo y Color de Texto
- Added: Color picker nativo HTML5 con input de texto hexadecimal sincronizado
- Added: Paleta de colores del tema de WordPress (colapsable)
- Added: Paleta por defecto de 8 colores cuando el tema no define colores personalizados
- Added: Helpers PHP para manipular colores: `lighten_color()` y `darken_color()`
- Added: CSS dinámico inyectado en el frontend para aplicar colores personalizados

**🐛 Correcciones Críticas**:
- Fixed: Configuración JavaScript duplicada entre templates y class_frontend.php causaba inconsistencias
- Fixed: Los templates modal.php y screen.php creaban variable `wlandChatConfig` conflictiva con `WlandChatConfig`
- Fixed: JavaScript intentaba leer configuración de múltiples objetos con propiedades diferentes (camelCase vs snake_case)
- Fixed: Eliminada variable duplicada en templates - ahora usa únicamente `WlandChatConfig` desde PHP
- Fixed: Alineación del color picker y input text usando `display: inline-block` con `vertical-align: middle`

**✨ Mejoras**:
- Improved: Unificación de configuración JavaScript - solo se usa `WlandChatConfig` pasado desde `class_frontend.php`
- Improved: Logs de debug mejorados para troubleshooting (muestra objeto completo y valores de auth_token)
- Improved: Eliminados emojis de todos los logs de consola en archivos JavaScript
- Improved: Lectura simplificada de configuración en wland_chat_block_modal.js y wland_chat_block_screen.js
- Improved: Toggle buttons para expandir/colapsar paletas de colores con animación suave
- Added: Propiedad `is_available` ahora se lee correctamente desde configuración PHP

**🗑️ Limpieza**:
- Removed: Bloque `<script>` duplicado en modal.php que creaba `wlandChatConfig`
- Removed: Bloque `<script>` duplicado en screen.php que creaba `wlandChatConfig`
- Removed: Emojis de todos los console.log en archivos JavaScript del frontend
- Removed: Gradiente del botón flotante - ahora usa color sólido
- Removed: Borde izquierdo de las burbujas de mensajes

### Versiones Anteriores

- **1.2.2**: Correcciones de formularios admin y sistema de auto-ocultación de notificaciones
- **1.2.1**: Rediseño de Settings con diseño Bentō
- **1.2.0**: Refactorización completa del admin con diseño Bentō
- **1.1.2**: Cambio de marca a BravesLab
- **1.1.1**: Sistema de cookies con fingerprinting y GDPR
- **1.0.0**: Versión inicial

## Autores

- **Carlos Vera** (Autor Principal) - [GitHub](https://github.com/Carlos-Vera) - carlos@braveslab.com
- **Mikel Marqués** (Colaborador) - hola@mikimonokia.com

Desarrollado para **BravesLab.com** - [https://braveslab.com](https://braveslab.com)

### Asistencia en Desarrollo

- **Claude (Anthropic)** - Asistencia en desarrollo v1.2.x

## Licencia

Este plugin es software comercial. Todos los derechos reservados.

Copyright (c) 2025 Braves Lab LLC

Para más información sobre la licencia, consulta el archivo [LICENSE](LICENSE).

## ❓ Preguntas Frecuentes (FAQ)

### ¿El plugin requiere configuración técnica complicada?

No. Solo necesitas la URL del webhook de N8N. El resto de configuraciones son opcionales y tienen valores por defecto sensatos.

### ¿Funciona con cualquier tema de WordPress?

Sí. El plugin es independiente del tema y funciona con cualquier tema compatible con WordPress 5.8+.

### ¿Puedo personalizar completamente el diseño del chat?

Sí. Puedes personalizar títulos, mensajes, colores (mediante CSS custom), posición y modo de visualización.

### ¿El chat se muestra en todas las páginas?

Por defecto sí, pero puedes:
- Deshabilitarlo globalmente con el toggle "Mostrar en toda la web"
- Excluir páginas específicas desde Ajustes > Páginas Excluidas
- Usar el bloque Gutenberg para control por página

### ¿Cómo funcionan las cookies y el fingerprinting?

El plugin genera un ID único de sesión basado en características del navegador (hash SHA-256). Este ID se envía con cada mensaje para mantener el contexto de la conversación. Ver sección "Sistema de Cookies con Fingerprinting" para más detalles.

### ¿Es compatible con GDPR?

Sí. Incluye un banner de consentimiento configurable que se muestra antes de crear cualquier cookie. El consentimiento se guarda en localStorage.

### ¿Puedo tener diferentes configuraciones en diferentes páginas?

Sí. Usa el bloque Gutenberg que permite configurar webhook, títulos y mensajes específicos por página.

### ¿Qué pasa si mi webhook de N8N está caído?

El chat mostrará un mensaje de error al usuario. Es recomendable tener monitoreo en tu webhook de N8N.

### ¿El plugin afecta el rendimiento del sitio?

No. Los assets (CSS/JS) solo se cargan cuando es necesario y están optimizados. El impacto en rendimiento es mínimo.

## 🔧 Solución de Problemas

### El chat no aparece en el frontend

**Posibles causas**:
1. El plugin no está activado
2. Toggle "Mostrar en toda la web" está desactivado en Ajustes
3. La página actual está en la lista de páginas excluidas
4. Conflicto con caché (purga la caché del sitio y del navegador)
5. JavaScript bloqueado por otro plugin

**Solución**:
- Verifica que el plugin esté activado
- Ve a Ajustes y asegúrate que "Mostrar en toda la web" esté activado
- Revisa la lista de páginas excluidas
- Purga toda la caché (plugin de caché + navegador)
- Desactiva temporalmente otros plugins para identificar conflictos

### Los formularios del admin no guardan los datos

**Posibles causas**:
1. Problemas de permisos de usuario
2. Conflicto con otro plugin de seguridad
3. Nonces expirados

**Solución**:
- Asegúrate de estar logueado como Administrator
- Verifica que no haya plugins de seguridad bloqueando las peticiones
- Recarga la página antes de hacer cambios (refresca los nonces)

### El banner GDPR no aparece

**Causas comunes**:
- Toggle "Habilitar Banner GDPR" está desactivado
- El usuario ya aceptó cookies anteriormente (revisar localStorage)

**Solución**:
- Ve a GDPR y activa "Habilitar Banner GDPR"
- Para testear, borra `wland_chat_gdpr_consent` del localStorage del navegador

### Error "Las cookies están bloqueadas"

El sistema tiene fallback automático a localStorage. Si ves este mensaje:
- Las cookies de terceros están bloqueadas en el navegador
- El sistema funcionará igualmente usando localStorage como backup

### El webhook no recibe el sessionId

**Solución**:
1. Abre DevTools > Console
2. Busca mensajes `[Wland Fingerprint]`
3. Verifica que el sessionId se genere correctamente
4. Revisa la petición en DevTools > Network para confirmar que incluye el campo

## Soporte

Para soporte, consultas o reportar problemas:

- **Email**: carlos@braveslab.com
- **Web**: [https://braveslab.com](https://braveslab.com)
- **GitHub Issues**: [Reportar un problema](https://github.com/Carlos-Vera/Wland-Chat-iA/issues)
- **Documentación Técnica**: Ver [CLAUDE.md](CLAUDE.md) para desarrollo

## 🙏 Agradecimientos

- **BravesLab** por el desarrollo, diseño y financiación del proyecto
- **Carlos Vera** por la visión y dirección técnica
- **Mikel Marqués** por las contribuciones al código
- **Claude (Anthropic)** por la asistencia en desarrollo de la arquitectura v1.2.x
- **La comunidad de WordPress** por los estándares y mejores prácticas
- **N8N** por la plataforma de automatización que permite la integración con IA
- **Lottie (Airbnb)** por las animaciones del botón de chat

## 🌐 Compatibilidad

### Navegadores Soportados

El chat funciona en todos los navegadores modernos:

- ✅ Chrome / Edge (Chromium) 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Opera 76+
- ✅ Navegadores móviles (iOS Safari, Chrome Mobile, Samsung Internet)

### WordPress

- ✅ WordPress 5.8 - 6.4+
- ✅ Multisite compatible
- ✅ Compatible con temas populares (Astra, GeneratePress, Kadence, Twenty Twenty-Four)
- ✅ Compatible con page builders (Elementor, Beaver Builder, Divi)

### PHP

- ✅ PHP 7.4, 8.0, 8.1, 8.2, 8.3
- ⚠️ PHP 7.3 o inferior no soportado

### Servidor

- Funciona en cualquier servidor compatible con WordPress (Apache, Nginx, LiteSpeed)
- Requiere HTTPS para cookies con flag Secure
- Compatible con CDN (Cloudflare, CloudFront, etc.)

---

**Wland Chat iA** - Integrando la inteligencia artificial en WordPress de forma profesional.

---

**Versión**: 1.2.3 | **Autor**: Carlos Vera @ BravesLab | **Licencia**: Comercial