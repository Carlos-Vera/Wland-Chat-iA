# Changelog

Registro de cambios del plugin **BravesChat iA**.

Todas las fechas en formato YYYY-MM-DD. Este proyecto sigue [Semantic Versioning](https://semver.org/).

## 📋 Índice de Versiones

- [2.5.0](#250---2026-06-13) - **Actual** - Compatibilidad WordPress 7.0, bloque con block.json apiVersion 3, preview en insertador y fix de notices en Historial
- [2.4.6](#246---2026-04-16) - Seguridad XSS, traducciones del historial, versículos en inglés y timeline automático
- [2.4.5](#245---2026-04-15) - Versículos locales, UI traducida al inglés y correcciones de seguridad
- [2.4.4](#244---2026-04-12) - Internacionalización completa: 481 strings traducibles, soporte JS i18n y archivos de idioma para es_ES
- [2.4.3](#243---2026-04-12) - Modo Mixto de visualización y correcciones CSS para iOS Safari dark mode
- [2.4.2](#242---2026-03-26) - Dark mode en el panel admin y limpieza de estilos hardcodeados
- [2.4.0](#240---2026-03-25) - Mobile fullscreen mode, compatibilidad WooCommerce y mejoras Plugin Check
- [2.3.8](#238---2026-03-18) - Versículo diario NVI en el header del panel admin
- [2.3.7](#237---2026-03-18) - Nombre del agente, notices en header y navegación renombrada
- [2.3.5](#235---2026-03-16) - Fix botón de subir imagen en Apariencia
- [2.3.4](#234---2026-03-16) - Burbuja compacta en móviles
- [2.3.3](#233---2026-03-11) - Fix text domain alineado al slug braveschat
- [2.3.2](#232---2026-03-11) - Fix text domain para WordPress.org
- [2.3.1](#231---2026-03-11) - Input activo mientras el agente responde
- [2.3.0](#230---2026-03-10) - Token de N8N server-side, seguridad reforzada y licencia GPL
- [2.2.3](#223---2026-03-05) - Modal de plugin y editor de texto enriquecido
- [2.2.2](#222---2026-03-04) - Protección contra exportación no autorizada del plugin
- [2.2.1](#221---2026-02-26) - Correcciones de compatibilidad y mejoras de UI
- [2.2.0](#220---2026-02-26) - Visor completo de conversaciones y estadísticas combinados
- [2.1.5](#215---2026-02-25) - Página "Historial" con visor completo
- [2.1.4](#214---2026-02-24) - Pestaña "Estadísticas" con historial
- [2.1.3](#213---2026-02-23) - Fix release automatizado con GitHub Actions
- [2.1.2](#212---2026-02-20) - Aislamiento CSS y mejoras de compatibilidad
- [2.1.1](#211---2026-02-16) - Fix Markdown y foco del input
- [2.1.0](#210---2026-02-16) - Fix y mejoras del sistema
- [2.0.0](#200---2026-02-14) - Sistema completo reestructurado, nuevas funcionalidades, rebranding GDPR y mejoras críticas
- [1.2.4](#124---2025-01-17) - Modificaciones UX/UI y mejoras críticas
- [1.2.3](#123---2025-10-26) - Fix wp.i18n y mejora de compatibilidad JS
- [1.2.2](#122---2025-10-25) - Corrección crítica de inputs en formularios
- [1.2.1](#121---2025-10-24) - Rediseño completo del admin con Bentō
- [1.2.0](#120---2025-10-23) - Nuevo sistema de administración
- [1.1.2](#112---2025-10-23) - Cambio de marca a BravesLab
- [1.1.1](#111---2025-10-16) - Sistema de cookies y fingerprinting
- [1.1.0](#110---2025-10-01) - Horarios y páginas excluidas
- [1.0.0](#100---2025-09-15) - Lanzamiento inicial

---

## [2.5.0] - 2026-06-13

### 🔧 Mejoras Técnicas
- **IMPROVED**: Bloque Gutenberg `braves/chat-widget` migrado a `block.json` con `apiVersion: 3`. En WordPress 7.0, el bloque ya no des-iframea el editor de bloques al insertarse.
- **IMPROVED**: Dependencia del script del editor `wp-editor` (obsoleto en WP 7.0) reemplazada por `wp-block-editor`. Los handles del bloque se registran ahora en el hook `init`.
- **IMPROVED**: Eliminada la función `ensure_block_files()` que escribía archivos en el directorio del plugin en tiempo de ejecución (marcada por Plugin Check).
- **IMPROVED**: Añadida la clave `example` al registro del bloque — el insertador de bloques muestra ahora una vista previa en lugar de "Vista previa no disponible".
- **IMPROVED**: `extract()` en `includes/class_frontend.php` reemplazado por asignaciones explícitas de variables (buena práctica, marcado por Plugin Check).

### 📋 Requisitos
- **IMPROVED**: `Requires at least` bumpeado de 5.8 a 5.9. El panel Apariencia usa `wp_get_global_settings()`, disponible desde WP 5.9.
- **IMPROVED**: `Tested up to` actualizado a 7.0.

### 🐛 Correcciones
- **FIXED**: La pestaña Historial aparecía en blanco cuando el webhook no estaba configurado. La regla CSS `.braves-chat-admin-page .notice:not(.braves-notice){display:none}` ocultaba los avisos propios del plugin. Solución: clase `braves-notice` añadida a los notices de `history.php`.

### 🧹 Limpieza
- **CHORE**: Backups `languages/*.po~` eliminados del repositorio.
- **CHORE**: Patrón `*.po~` añadido a `.distignore` para excluirlos del ZIP de release.

---

## [2.4.6] - 2026-04-16

### 🔒 Seguridad
- **FIXED**: El parser de Markdown en el chat (modal y pantalla completa) ya no renderiza enlaces con protocolos no HTTP/HTTPS — `javascript:`, `data:` y similares son ignorados. Previene XSS en respuestas de agentes N8N comprometidos.
- **FIXED**: El nombre del header de autenticación de N8N se sanitiza con `preg_replace` antes de usarse como clave HTTP — previene inyección de headers por caracteres CRLF.

### 🌍 Traducciones
- **FIXED**: El modal de historial ya no muestra "Conversación Anónima", "Session:" ni "Usuario" hardcodeados en español. Todos los strings del visor de conversaciones pasan ahora por `__()` y son traducibles.
- **FIXED**: El nombre del agente por defecto en el modal ahora se traduce correctamente ("Agent" en inglés, "Agente" en español).
- **ADDED**: Versículos bíblicos en inglés (NIV) — cuando WordPress no está en español, el header del panel muestra versículos en inglés desde un archivo local de 365 entradas.

### 🎨 UX/UI
- **IMPROVED**: El timeline del changelog en la página "Acerca de" ya no usa valores de espaciado hardcodeados por ítem (`style="--braves-tl-nudge: ..."`). El layout es ahora automático — agregar una nueva versión solo requiere `braves-tl-right` o `braves-tl-left`.
- **IMPROVED**: Eliminadas las clases de utilidad `braves-tl-mt-*` del CSS del panel — ya no son necesarias.

---

## [2.4.5] - 2026-04-15

### ✨ Nuevas Funcionalidades
- **ADDED**: Archivo local con 365 versículos NVI — eliminada la dependencia de scripture.api.bible. Sin peticiones externas.
- **ADDED**: Traducción completa de la interfaz de administración al inglés con soporte i18n para es_ES — archivos `.po`, `.mo` y JSON actualizados.
- **ADDED**: Página "Acerca de" traducida al inglés; traducciones al español en el archivo `.po`.

### 🔒 Seguridad
- **FIXED**: El token de autenticación de N8N ya no se expone en `console.log` del navegador.
- **FIXED**: Validación del esquema HTTP/HTTPS añadida antes de cada llamada a `wp_remote_get()`.

### 🐛 Correcciones WP.org
- **FIXED**: `$_GET['page']` ahora se sanitiza correctamente antes de usarse.
- **FIXED**: Valores booleanos escapados con `esc_attr()` en inputs ocultos de formularios.
- **FIXED**: Patrón i18n en `admin.js` actualizado a `const { __ } = wp.i18n` — compatible con `wp i18n make-json`.

---

## [2.4.4] - 2026-04-12

### ✨ Nuevas Funcionalidades
- **ADDED**: Archivos de idioma completos para español (es_ES): `.pot` regenerado con 481 strings (desde 83), `.po` con todas las traducciones completadas, `.mo` compilado y archivos JSON para traducciones JS.

### 🔧 Mejoras Técnicas
- **IMPROVED**: Todas las strings visibles al usuario en `admin.js` ahora usan `wp.i18n.__()` — compatibles con el sistema de traducciones de WordPress.
- **IMPROVED**: El script de administración se registra con `wp_set_script_translations()` — carga automáticamente los archivos JSON de idioma según el locale activo.
- **IMPROVED**: Los valores por defecto de `bubble_text`, el pie del modo pantalla completa y los placeholders de configuración ahora pasan por las funciones de traducción de WordPress.

### 🐛 Correcciones
- **FIXED**: Strings hardcodeadas en PHP (`'Chat de voz'`, pie de pantalla completa) que no eran traducibles en instalaciones no en español.

---

## [2.4.3] - 2026-04-12

### ✨ Nuevas Funcionalidades
- **ADDED**: Modo Mixto (`mixed`) en ajustes de visualización — muestra la burbuja flotante en toda la web y el chat en pantalla completa en las páginas que tienen el bloque de Gutenberg.

### 🔧 Mejoras Técnicas
- **IMPROVED**: `color-scheme: light` forzado en el container del widget para prevenir que iOS Safari aplique el dark mode del sistema y invierta los colores del chat.
- **IMPROVED**: El banner GDPR fuerza colores con especificidad `!important` para aislar el texto del dark mode de temas de terceros.

### 🐛 Correcciones
- **FIXED**: En modo pantalla completa (bloque Gutenberg), se añade un overlay de fondo `#braves-fullscreen-overlay` que cubre el fondo blanco del tema mientras carga el chat.
- **FIXED**: El color del placeholder del input de chat ya no hereda del tema activo — se fija en `#9ca3af`.
- **FIXED**: El CSS de pantalla completa se fuerza siempre en páginas con el bloque Gutenberg, independientemente del modo de visualización global configurado.

---

## [2.4.2] - 2026-03-26

### ✨ Nuevas Funcionalidades
- **ADDED**: Dark mode para el panel de administración — botón para alternar entre tema claro y oscuro. La preferencia se guarda en `user_meta` (`braveschat_admin_theme`) por usuario y se restaura sin flash (FOUC) mediante un script en `<head>` antes de que cargue el CSS.
- **ADDED**: Soporte de dark mode en TinyMCE — el editor de bienvenida adapta su fondo y texto al tema activo.

### 🔧 Mejoras Técnicas
- **IMPROVED**: Variables CSS semánticas añadidas a `variables.css` (`--braves-bg-page`, `--braves-surface`, `--braves-surface-alt`, `--braves-border-color`, `--braves-text-primary`, `--braves-text-secondary`, `--braves-text-muted`). Todas las variantes dark mode se declaran bajo `[data-braves-theme="dark"]`.
- **IMPROVED**: `components.css` — cards, títulos, subtítulos y footers usan las nuevas variables semánticas en lugar de valores hardcodeados. Border-radius reducido de 24px a 18px, lift en hover de -4px a -2px.
- **IMPROVED**: Botones "Seleccionar todas" / "Deseleccionar todas" de páginas excluidas usan clases `braves-button braves-button--secondary` en lugar de clases WordPress genéricas, y su lógica JS se movió a `admin.js`.
- **IMPROVED**: Los `style` inline con `font-size: 13px; color: #666` eliminados de los párrafos `.braves-field-help` en `settings.php` — el color ahora viene de la hoja de estilos, compatible con dark mode.
- **IMPROVED**: Las etiquetas del slider de velocidad de tipeo usan clase `braves-range-labels` en lugar de posicionamiento absoluto con `style` inline.
- **IMPROVED**: El div de previsualización del mensaje de bienvenida ya no inyecta estilos inline desde JS — usa clases CSS.
- **IMPROVED**: `bravesAdminConfig` ahora incluye `ajaxUrl` y `themeNonce` para que el toggle de tema pueda guardar la preferencia vía AJAX sin exponer credenciales.

---

## [2.4.0] - 2026-03-25

### ✨ Nuevas Funcionalidades
- **ADDED**: Mobile fullscreen mode — en dispositivos ≤480px el chat se abre como overlay de pantalla completa con header propio, botones de volver/cerrar y soporte de `safe-area-inset` para iPhones con notch. El scroll del body se bloquea y restaura correctamente al cerrar.
- **ADDED**: `#braves-mobile-header` en `modal.php` — header exclusivo para el modo fullscreen móvil, incluye botón volver, título configurable y botón cerrar.

### 🐛 Correcciones
- **FIXED**: `date()` reemplazado por `gmdate()` en `get_daily_verse()` para que la rotación del versículo sea correcta independientemente del timezone del servidor.

### 🔧 Mejoras Técnicas
- **IMPROVED**: z-index del contenedor de chat bajado de 999 a 990 — evita conflictos visuales con el carrito y checkout de WooCommerce.
- **IMPROVED**: Logo del header renderizado como `<img>` en lugar de SVG inline — compatible con políticas CSP estrictas y elimina warnings de Plugin Check.
- **IMPROVED**: Scripts del panel de admin (historial CSV + modal, ajustes) movidos de templates PHP a `wp_add_inline_script()` en `Admin_Controller::enqueue_admin_assets()`.
- **IMPROVED**: Estilos del menú movidos de `admin_head` a `wp_add_inline_style()` — más correcto según WordPress coding standards.
- **IMPROVED**: SVG del icono del menú sanitizado antes de codificarse como data URI: se elimina la declaración XML y se sustituyen dimensiones `100%` por valores fijos.
- **IMPROVED**: `wp_register_style( 'braves-menu-icon' )` ahora incluye `BRAVES_CHAT_VERSION` para cache busting correcto.
- **IMPROVED**: Comentario de disclosure del servicio externo API.Bible añadido en `get_daily_verse()` para cumplimiento con WordPress.org.
- **IMPROVED**: `.gitignore` y `release.yml` actualizados para excluir reportes de Plugin Check (`braveschat-*.md`) y `.phpcs.xml` del ZIP de distribución.

---

## [2.3.8] - 2026-03-18

### ✨ Nuevas Funcionalidades
- **ADDED**: Versículo diario NVI en el header del panel admin. Se obtiene de scripture.api.bible con caché de 24h en transient de WordPress. Si la API no responde, se muestra un versículo del listado de respaldo integrado. La selección varía según el día del año.

### 🎨 CSS
- **ADDED**: `.braves-admin-header__verse` — estilo del versículo: alineado a la derecha, itálica, color gris, tamaño 11px.
- **IMPROVED**: `.braves-admin-header__version` ahora usa `flex-direction: column` para apilar badge y versículo. El badge queda en `.braves-admin-header__version-badge`.

---

## [2.3.7] - 2026-03-18

### ✨ Nuevas Funcionalidades
- **ADDED**: `braves_chat_agent_name` — campo en Apariencia para nombrar al agente (ej. "Charlie"). Aparece en el historial para identificar conversaciones cuando tienes más de un flujo.

### 🎨 Mejoras de UX/UI
- **IMPROVED**: Los avisos de webhook y guardado se mueven al header. El cuerpo de las páginas admin queda libre — afecta a `settings.php`, `appearance.php`, `availability.php` y `gdpr.php`. Nuevo slot `notices` en `Admin_Header::render()`.
- **IMPROVED**: Sidebar renombrado — "Horarios" → "Disponibilidad", "GDPR" → "Privacidad", "Historial" → "Conversaciones".
- **IMPROVED**: Badge de versión en el header con estado activo (`.braves-badge--active`) al estar en la página About.
- **IMPROVED**: Etiquetas en Apariencia: "Modal (ventana flotante)" → "Burbuja Flotante", "Pantalla completa" → "Pantalla Completa", "Básica" → "Skin Básico", "Braves" → "Skin Braves".
- **IMPROVED**: Changelog en About migrado a `.braves-timeline` — grid `45% | 10% | 45%` con línea central y badges de versión.

### 🎨 CSS
- **ADDED**: `.braves-admin-header__notices` — notices en el header, sin fondo, texto bold.
- **ADDED**: Sistema CSS para `.braves-timeline` — grid, línea vertical continua, conectores horizontales, responsive ≤768px.
- **IMPROVED**: `.braves-badge--active` comparte el mismo estilo que el hover del badge primario.

---

## [2.3.5] - 2026-03-16

### 🐛 Correcciones
- **FIXED**: `class_settings.php` — condición `strpos($hook, 'braveschat')` extendida con `|| strpos($hook, 'braves-chat')`. Las páginas de submenú con `null` como parent generan hooks del tipo `admin_page_braves-chat-appearance`, no `toplevel_page_braveschat`. Por eso `wp_enqueue_media()` y `admin_media_uploader.js` nunca se cargaban en Apariencia, inutilizando el botón de subir imagen.

---

## [2.3.4] - 2026-03-16

### 🎨 Mejoras de Experiencia
- **IMPROVED**: Burbuja del chat reducida en móviles (< 480px). Skin default: círculo de 48×48px en lugar de 60×60px. Skin Braves: vista compacta forzada (avatar + botón redondo) ocultando el texto y el CTA completo. Sin cambios en escritorio.

---

## [2.3.3] - 2026-03-11

### 🐛 Correcciones
- **FIXED**: Text domain actualizado a `braveschat` en los 21 archivos PHP del plugin, alineándolo con el slug asignado por WordPress.org. Elimina todos los errores `TextDomainMismatch` reportados por Plugin Check (PCP). La carpeta del ZIP de distribución también se renombra a `braveschat/` y el archivo principal a `braveschat.php`.

---

## [2.3.2] - 2026-03-11

### 🐛 Correcciones
- **FIXED**: El ZIP de distribución ahora renombra el archivo principal a `braves-chat.php` dentro de la carpeta `braves-chat/`, alineando el slug del plugin con el text domain declarado (`braves-chat`) y eliminando el error de validación en Plugin Check (PCP) de WordPress.org.

---

## [2.3.1] - 2026-03-11

### 🎨 Mejoras de Experiencia
- **IMPROVED**: El campo de texto permanece activo mientras el agente está respondiendo. El usuario puede escribir y enviar un nuevo mensaje en cualquier momento — si el agente está respondiendo, la respuesta se cancela y el nuevo mensaje se envía de inmediato.

---

## [2.3.0] - 2026-03-10

### ✨ Nuevas Funcionalidades
- **ADDED**: El token de autenticación de N8N ya no se expone en el navegador. Ahora viaja exclusivamente en el servidor: el frontend envía el mensaje a WordPress, y WordPress lo reenvía al webhook con el token añadido de forma segura.
- **ADDED**: Soporte para tres métodos de autenticación hacia N8N: cabecera personalizada, Basic Auth o sin autenticación — configurable desde el panel de ajustes.

### 🎨 Mejoras de Experiencia
- **IMPROVED**: El modo de visualización activo (modal o pantalla completa) ahora aparece visible en la cabecera del panel de administración para referencia rápida.
- **IMPROVED**: El bloque de Gutenberg muestra un preview rediseñado con el estilo "Bentō" del panel: cabecera, cuerpo y pie de página con branding del chat.

### 🔧 Mejoras
- **IMPROVED**: El JavaScript del frontend se simplificó drásticamente al eliminar la lógica de streaming y NDJSON: toda la complejidad de conexión con N8N se gestiona ahora en el servidor.
- **IMPROVED**: Las imágenes del plugin (banners, capturas, iconos) se convirtieron a PNG para máxima compatibilidad con navegadores y WordPress.org.
- **IMPROVED**: La licencia del plugin actualizada a GPL-2.0-or-later, alineada con los requisitos de WordPress.org.
- **IMPROVED**: La carga de traducciones ahora delega en el sistema automático de WordPress (6.7+), eliminando código manual innecesario.

### 🐛 Correcciones
- **FIXED**: Eliminada la clase de detección de plugins de exportación ZIP, que generaba falsos positivos y no era necesaria con la distribución vía WordPress.org.

---

## [2.2.3] - 2026-03-05

### 🎨 Mejoras de Experiencia
- **ADDED**: Modal "Ver detalles" en la lista de plugins de WordPress con información completa, FAQ, instalación y compatibilidad de BravesChat.

### 🔧 Mejoras
- **IMPROVED**: Editor de texto enriquecido (TinyMCE) para mensajes GDPR y mensajes fuera de horario, permitiendo usuarios formatear texto con negritas, cursivas, listas y enlaces sin necesidad de HTML.
- **IMPROVED**: Sanitización de mensajes usando `wp_kses_post()` para soportar correctamente HTML formateado desde el editor.

---

## [2.2.2] - 2026-03-04

### 🔧 Mejoras
- **ADDED**: Nueva clase `Protection` que detecta plugins de exportación ZIP instalados y muestra un aviso de advertencia en el panel de administración de WordPress.

---

## [2.2.1] - 2026-02-26

### 🐛 Correcciones
- **FIXED**: Avisos de otros plugins no se muestran en las páginas de administración de BravesChat, mejorando la claridad de la interfaz.

---

## [2.2.0] - 2026-02-26

### 🚀 Lanzamiento Menor - BravesChat iA 2.2
- **MAJOR**: Unificación de la documentación interna, consolidando cambios de versiones 2.1.4 y 2.1.5 dentro del plugin.
- **ADDED**: Visor completo de conversaciones y estadísticas.
- Se mantienen los registros individuales en la documentación general (CHANGELOG.md).

---

## [2.1.5] - 2026-02-25

### ✨ Nuevas Funcionalidades
- **ADDED**: Página **"Historial"** reemplaza "Estadísticas" con visor completo de conversaciones por sesión.
- **ADDED**: Modal de conversación con burbujas de chat, etiquetas de remitente ("Agente" / nombre del usuario) y renderizado de Markdown (negrita, cursiva, enlaces).
- **ADDED**: Agrupación de mensajes por `session_id`: cada fila de la tabla representa una conversación completa.
- **ADDED**: Exportación CSV limpia con columnas: `Session ID`, `Client Name`, `Updated At`, `Chat History JSON`.

### 🔧 Mejoras UX/UI
- **IMPROVED**: Orden de la tabla: conversaciones de más reciente a más antigua.
- **IMPROVED**: Orden del modal: mensajes en orden cronológico natural (más antiguo arriba).
- **IMPROVED**: Columna "Session ID" eliminada de la tabla (sigue visible en el modal).
- **IMPROVED**: Extracto de la tabla muestra el último mensaje del usuario, limpio de prefijos N8N.

### 🛡️ Filtrado de contenido interno del agente
- **FIXED**: Se ocultan tool calls (`Calling {tool} with input:`), respuestas JSON de herramientas (`[{"response":"..."}]`, `{"response":"..."}`), razonamiento interno (`Plan:`, `Thought:`, `Notes:`, `There was an error`, `Name:`), y mensajes con `tool_calls` no vacíos.
- **FIXED**: Se extraen y muestran correctamente las respuestas de herramientas de búsqueda (`{"response":"..."}`).
- **FIXED**: Prefijos N8N eliminados de mensajes del usuario: "Mensaje del usuario:" y "Su id de sesión: hash" (corte en primer `\n`).
- **FIXED**: Timestamp de la sesión extraído de resultados del CRM tool antes de ocultarlos.

### 📋 Archivos Modificados
- `includes/admin/templates/history.php` _(nuevo — reemplaza statistics.php)_
- `includes/admin/components/class_admin_sidebar.php`
- `includes/admin/class_admin_controller.php`

---

## [2.1.4] - 2026-02-24

### ✨ Nuevas Funcionalidades
- **ADDED**: Nueva pestaña **"Estadísticas"** en el sidebar del panel de administración (icono de gráfico de barras).
- **ADDED**: Tabla de historial de conversaciones obtenida en tiempo real desde un webhook N8N que consulta Postgres.
- **ADDED**: Sección de configuración propia con campo URL del webhook y API Key (header `x-api-key`).
- **ADDED**: Exportación del historial a **CSV** con todos los campos: `session_id`, `client_mail`, `last_message`, `updated_at`, `chat_history`, `metadata`, `user_height`.
- **ADDED**: Opciones `braves_chat_stats_webhook_url` y `braves_chat_stats_api_key` registradas en `braves_chat_settings`.
- **ADDED**: Título de pestaña del navegador: "BravesChat | Estadísticas | [Sitio]".

### 🔧 Técnico
- **CHANGED**: `class_admin_sidebar.php` — ítem "Estadísticas" + SVG de barras.
- **CHANGED**: `class_admin_controller.php` — submenu `braves-chat-stats`, método `render_stats_page()`, listas de páginas Braves actualizadas.
- **CHANGED**: `class_settings.php` — registro de dos nuevas opciones + preservación en `render_hidden_fields()`.
- **ADDED**: Template `includes/admin/templates/statistics.php` con formulario de configuración, tabla y script de exportación CSV.

### 📋 Archivos Modificados
- `includes/admin/components/class_admin_sidebar.php`
- `includes/admin/class_admin_controller.php`
- `includes/class_settings.php`
- `includes/admin/templates/statistics.php` _(nuevo)_

---

## [2.1.3] - 2026-02-23

### 🔧 Backend / CI
- **FIXED**: El workflow de GitHub Actions (`release.yml`) no se ejecutaba porque `.gitignore` excluía el directorio `.github/workflows/`.
- **FIXED**: Eliminada la regla `**/.github/workflows/*` del `.gitignore` para que el workflow quede correctamente trackeado en git.
- **ADDED**: Workflow `release.yml` ahora commiteado y disponible en GitHub, generando automáticamente `braveschat.zip` al hacer push de un tag `v*`.

---

## [2.1.2] - 2026-02-20

### 🎨 Mejoras de Interfaz
- **IMPROVED**: Sistema completo de aislamiento CSS (Isolation Layer) para prevenir conflictos con temas de WordPress.
- **IMPROVED**: Reset exhaustivo de estilos de botones, inputs y elementos de texto en todos los modos.
- **IMPROVED**: Protección contra bleeding de CSS de temas externos en Modal, Fullscreen y GDPR Banner.
- **IMPROVED**: Forzado de tipografía Montserrat en todos los elementos del chat.

### 🐛 Correcciones
- **FIXED**: Posicionamiento del chat en modo centrado (position-center) ahora correctamente alineado.
- **FIXED**: Ventana del chat centrada cuando la burbuja está en el centro.
- **FIXED**: Posicionamiento bottom-left corregido para modo modal.
- **FIXED**: Eliminación de cursores de escritura múltiples en streaming (solo el último mensaje muestra el cursor palpitante).

### 🔧 Backend
- **ADDED**: Filtro `admin_title` para títulos dinámicos por sección en el panel de administración.
- **ADDED**: Títulos de pestañas del navegador personalizados: "BravesChat | Sección | Nombre del Sitio".
- **CHANGED**: Script de desinstalación ahora preserva configuraciones al desinstalar (funciones comentadas).

---

## [2.1.1] - 2026-02-16

### 🎨 Mejoras UX
- **IMPROVED**: Renderizado incremental de Markdown en tiempo real para una experiencia de escritura más fluida.
- **FIXED**: El foco del input se mantiene activo tras enviar un mensaje para facilitar la escritura continua.

---

## [2.1.0] - 2026-02-16

### ✨ Nuevas Funcionalidades
- **ADDED**: Slider de control de velocidad de escritura con tooltip dinámico en tiempo real.
- **ADDED**: Soporte para HTML y Markdown en el mensaje del banner GDPR.
- **ADDED**: Tipografía Montserrat implementada localmente para cumplimiento GDPR y mejora de performance.

### 🎨 Mejoras Visuales y UX
- **IMPROVED**: Refinamiento general de la UI del administrador (diseño Bentō).
- **IMPROVED**: Unificación de estilos con variables CSS global `--braves-*`.
- **IMPROVED**: Scroll automático del chat al enviar mensajes para asegurar visibilidad del último contenido.

### 🐛 Correcciones
- **FIXED**: Comportamiento del scroll cuando el usuario envía múltiples mensajes consecutivos.
- **FIXED**: Estilos del contenedor del banner GDPR para evitar cortes en móviles.

---

## [2.0.0] - 2026-02-14

### 🚀 Lanzamiento Mayor - BravesChat iA 2.0
- **MAJOR**: Sistema reestructurado completo adoptando el nombre de "**BravesChat iA**"
- **MAJOR**: Refactorización profunda de namespaces a `BravesChat\` y `BravesChat\Admin`
- **MAJOR**: Actualización de estructura de directorios y nombres de archivos principales

### ✨ Nuevas Funcionalidades UI/UX
- **ADDED**: Funcionalidad de **expansión del chat** (botón de maximizar)
- **ADDED**: **Auto-crecimiento** del área de texto (textarea) al escribir múltiples líneas
- **ADDED**: Enlace directo a la sección "About" desde la tarjeta de versión en el Dashboard
- **ADDED**: **Estado Minimizado** de la burbuja tras interacción (pill shape con solo imagen e icono)

### 🎨 Mejoras Visuales
- **IMPROVED**: Unificación de identidad visual con colores corporativos "**Braves Primary**"
- **IMPROVED**: Corrección de estilos en burbujas de chat (texto cortado, borders)
- **IMPROVED**: Actualización de tooltips predeterminados ("Habla con nuestro asistente IA")
- **IMPROVED**: Icono de enviar mensaje actualizado a diseño personalizado (blanco)
- **IMPROVED**: Títulos de menú admin actualizados a "**BravesChat iA**"

### 🐛 Correcciones y Estabilidad
- **FIXED**: Lógica del botón de enviar (estado habilitado/deshabilitado)
- **FIXED**: Guardado de configuración en sección "Páginas Excluidas"
- **FIXED**: Depuración de salida JSON en integración con n8n
- **FIXED**: Renderizado de campos ocultos en formularios de configuración (`render_hidden_fields`)

### 📋 Archivos Clave Modificados
- `braves_chat.php` - Definición de constantes y versión
- `includes/admin/class_admin_controller.php` - Títulos de menús y encolado de assets
- `assets/css/skins/braves.css` - Estilos visuales del chat
- `assets/js/braves_chat_block_modal.js` - Lógica de expansión y textarea

---

## [1.2.4] - 2025-01-17

### 🎨 Nueva Funcionalidad - Tooltip Personalizable
- **ADDED**: Campo de texto para personalizar el tooltip del botón flotante del chat
- **ADDED**: Opción `bubble_tooltip` registrada en WordPress Settings API
- **ADDED**: Card "Tooltip del Botón" en página de Apariencia (antes del selector de iconos)
- **ADDED**: Input de texto con `width: 100%` para consistencia visual
- **ADDED**: Atributo `title` dinámico en botón flotante usando valor personalizado

### 🎨 Mejoras de Apariencia
- **IMPROVED**: Color por defecto del icono SVG cambiado de `#5B4CCC` a `#f2f2f2` (gris claro)
- **IMPROVED**: Mejor organización de opciones en panel de Apariencia
- **IMPROVED**: Tooltip ubicado estratégicamente antes del selector de iconos

### 📋 Archivos Modificados
- `class_settings.php` - Registro opción `bubble_tooltip` + actualización default `icon_color` a #f2f2f2
- `appearance.php` - Card "Tooltip del Botón" agregada + fallback actualizado para `icon_color`
- `modal.php` - Variable `$bubble_tooltip` obtenida y usada en atributo `title`
- `screen.php` - Variable `$bubble_tooltip` obtenida y usada en atributo `title`

### 🔧 Opciones Nuevas
```php
wland_chat_bubble_tooltip  // Tooltip del botón flotante (default: "Habla con nuestro asistente IA")
```

### 🔄 Nueva Funcionalidad - Detección y Reemplazo Automático de Versiones
- **ADDED**: Sistema automático de detección de versiones anteriores del plugin al activar
- **ADDED**: Desactivación automática de plugins antiguos si están activos
- **ADDED**: Eliminación automática de directorios de versiones anteriores
- **ADDED**: Preservación de configuraciones del usuario durante la migración
- **ADDED**: Prevención de errores fatales por funciones redeclaradas
- **ADDED**: Método `detect_and_replace_old_versions()` en hook de activación

### 🐛 Correcciones Críticas
- **FIXED**: Hotfix para error fatal causado por múltiples versiones instaladas simultáneamente
- **FIXED**: Implementación de `function_exists()` check para prevenir redeclaraciones
- **FIXED**: Fallback del color del icono corregido en appearance.php

### 📋 Archivos Modificados
- `wland_chat_ia.php` - Método `detect_and_replace_old_versions()` agregado al hook de activación + hotfix function_exists()

### 🔧 Opciones Actualizadas
```php
wland_chat_icon_color      // Color del icono SVG (default: #f2f2f2 - antes: #5B4CCC)
```

---

## [1.2.3] - 2025-10-26

### 🎨 Nueva Funcionalidad - Sistema de Personalización de Colores
- **ADDED**: Sistema completo de personalización de colores desde panel de Apariencia
- **ADDED**: 4 campos de color personalizables: Color de la Burbuja, Color Primario, Color de Fondo y Color de Texto
- **ADDED**: Color picker nativo HTML5 (40x40px) con sincronización a input de texto hexadecimal
- **ADDED**: Paleta de colores del tema de WordPress extraída desde `theme.json` (colapsable)
- **ADDED**: Paleta por defecto de 8 colores cuando el tema no define colores personalizados
- **ADDED**: Helpers PHP `lighten_color()` y `darken_color()` para manipulación de colores
- **ADDED**: Método `inject_custom_colors()` en `class_frontend.php` para inyectar CSS dinámico
- **ADDED**: Opciones registradas: `bubble_color`, `primary_color`, `background_color`, `text_color`

### ✨ Nueva Funcionalidad - Iconos SVG Personalizables
- **ADDED**: Sistema de selección de iconos SVG personalizables para botón flotante
- **ADDED**: 4 iconos SVG optimizados (Original/Robot, Círculo, Happy, Burbuja)
- **ADDED**: Selector estilo tabs Bentō en página de Apariencia
- **ADDED**: Opción `chat_icon` registrada en WordPress Settings API

### 🎨 Mejoras de Diseño
- **IMPROVED**: Selector de iconos con diseño tabs horizontal estilo Bentō
- **IMPROVED**: Tabs con fondo gris claro y selección con borde morado
- **IMPROVED**: Iconos 32x32px optimizados desde viewBox 460x460
- **IMPROVED**: Responsive design (2 columnas en móvil)
- **IMPROVED**: Toggle buttons para expandir/colapsar paletas de colores con animación suave
- **IMPROVED**: Color pickers con estilo Material Design list (inline-block, vertical-align: middle)

### 🐛 Correcciones
- **FIXED**: Eliminada dependencia de Lottie Player (CDN externo)
- **FIXED**: Errores de consola por animaciones Lottie no cargadas
- **FIXED**: Error JavaScript cuando `wp.i18n` no está disponible
- **FIXED**: Alineación del color picker y input text usando `display: inline-block` con `vertical-align: middle`
- **FIXED**: Configuración JavaScript duplicada entre templates y class_frontend.php
- **FIXED**: Templates modal.php y screen.php creaban variable conflictiva `wlandChatConfig`

### 🔧 Cambios Técnicos
- **CHANGED**: Templates usan `<img>` SVG en lugar de animación Lottie
- **CHANGED**: Eliminado `lottie-player` de enqueue scripts
- **CHANGED**: Removido `animationPath` de configuración JavaScript
- **CHANGED**: Icono por defecto cambiado a "Original" (robot-chat)
- **CHANGED**: Añadido fallback wp.i18n en archivos JS
- **REMOVED**: Gradiente del botón flotante - ahora usa color sólido
- **REMOVED**: Borde izquierdo de las burbujas de mensajes

### 📋 Archivos Nuevos
- `assets/media/chat-icons/` - Directorio de iconos SVG
- `assets/js/icon_selector.js` - JavaScript para tabs interactivos
- `assets/js/color_picker.js` - Sincronización color picker con input text

### 📋 Archivos Modificados
- `class_settings.php` - Registro opciones `chat_icon` y colores (4 campos)
- `appearance.php` - Selector tabs Bentō + cards de colores personalizados
- `class_frontend.php` - Eliminada dependencia Lottie + método `inject_custom_colors()`
- `modal.php` / `screen.php` - SVG en lugar de Lottie
- `wland_chat_block_modal.js` / `screen.js` - Lógica SVG + unificación de configuración
- `components.css` - Estilos tabs + estilos color picker, paletas y presets
- `class_admin_controller.php` - Enqueue icon_selector.js y color_picker.js

---

## [1.2.2] - 2025-10-25

### 🐛 Correcciones Críticas
- **FIXED**: Los inputs de formulario no se renderizaban en las páginas de configuración (Ajustes, Apariencia, Horarios, GDPR)
- **FIXED**: El método `Admin_Content::render_card()` no soportaba el parámetro `content`
- **FIXED**: `wp_kses_post()` eliminaba los elementos de formulario HTML necesarios
- **FIXED**: Configuraciones se perdían al guardar en páginas diferentes (Apariencia, Horarios, GDPR)
- **FIXED**: Icono del menú se mostraba en gris en lugar de blanco en páginas del plugin
- **FIXED**: Script de auto-hide de notificaciones no se cargaba en todas las páginas

### ✨ Mejoras de UI/UX
- Agregado auto-hide de notificaciones con animación slide-up suave (3 segundos)
- Mejorado botón "Guardar" con estilo Bentō completamente redondeado
- Tarjetas de información ahora completamente clickeables sin enlaces azules visibles
- Eliminado outline azul al hacer clic en tarjetas
- Notificaciones con background sólido de color y animación fade-in
- Campos ocultos preservan configuraciones al guardar formularios parciales

### 🎨 Iconos Actualizados
- Icono de menú lateral "Horarios" cambiado a `access_time_filled` (sólido)
- Icono de menú lateral "GDPR" cambiado a versión sólida
- Página About: "Versión" → `verified` (icono de verificación)
- Página About: "Autor" → `person_pin` (tarjeta de persona)
- Página About: "Empresa" → `business_center` (maletín)

### 🔧 Mejoras Técnicas
- Script `admin_settings.js` ahora se carga en todas las páginas del plugin
- JavaScript agrega clases `wp-has-current-submenu` para páginas sin parent_slug
- Función `render_hidden_fields()` para preservar opciones entre formularios
- Changelog completo (v1.1.0 y v1.0.0) agregado a página About

### 🗑️ Limpieza
- Eliminado archivo huérfano `class_admin_settings_sidebar.php` (no utilizado)
- Eliminada sección "Enlaces Útiles" de página About

### 📋 Archivos Modificados
- `includes/admin/components/class_admin_content.php` - Soporte para content en cards
- `includes/admin/components/class_admin_sidebar.php` - Iconos sólidos para Horarios y GDPR
- `includes/admin/class_admin_controller.php` - Script settings.js en todas las páginas
- `includes/admin/class_template_helpers.php` - Nuevos iconos (verified, person_pin, business_center)
- `includes/class_settings.php` - Función render_hidden_fields(), estilos de icono
- `assets/css/admin/components.css` - Animación slide-out, estilos de notificaciones
- `assets/js/admin_settings.js` - Auto-hide de notificaciones (3s)
- `includes/admin/templates/*.php` - Campos ocultos, tarjetas clickeables
- `includes/admin/templates/about.php` - Iconos actualizados, changelog completo

### ✅ Verificación
- ✅ Todos los archivos PHP pasan linting
- ✅ Inputs renderizados correctamente en todas las páginas
- ✅ Guardado de configuración funcional sin pérdida de datos
- ✅ Diseño Bentō consistente en todas las páginas
- ✅ Iconos blancos en menú cuando página activa
- ✅ Notificaciones desaparecen automáticamente
- ✅ Animación slide-up suave funcionando

---

## [1.2.1] - 2025-10-24

### 🎨 Rediseño Completo del Admin
- Implementación completa del diseño Bentō en el panel de administración
- Nueva arquitectura modular de componentes
- Sistema unificado de navegación con sidebar compartido

### ✨ Nuevas Características
- 5 páginas de administración: Resumen, Ajustes, Apariencia, Horarios, GDPR
- Sidebar único compartido entre todas las páginas
- Componentes reutilizables: Header, Sidebar, Content
- Sistema de Template Helpers para renderizado consistente
- Cards estilo Bentō con diseño moderno

### 🏗️ Arquitectura
- Patrón Singleton en todos los componentes
- Separación clara de responsabilidades
- Sistema modular de CSS (variables, base, components, dashboard)
- Namespace `WlandChat\Admin` para todos los componentes

### 📦 Componentes Creados
- `Admin_Controller` - Controlador principal del admin
- `Admin_Header` - Componente de cabecera
- `Admin_Sidebar` - Navegación lateral compartida
- `Admin_Content` - Renderizado de contenido con cards Bentō
- `Template_Helpers` - Métodos estáticos para helpers

### 🎯 Páginas Implementadas
- **Resumen**: Dashboard con métricas y quick actions
- **Ajustes**: Configuración general, webhook, token, páginas excluidas
- **Apariencia**: Títulos, mensajes, posición, modo de visualización
- **Horarios**: Configuración de disponibilidad por horario
- **GDPR**: Banner de consentimiento de cookies

---

## [1.2.0] - 2025-10-23

### 🎨 Nuevo Sistema de Administración
- Rediseño inicial del backend con diseño moderno
- Implementación de diseño Bentō para cards
- Nueva página de Dashboard

### 🏗️ Refactorización
- Migración de configuración a nueva arquitectura
- Implementación de componentes modulares iniciales

---

## [1.1.2] - 2025-10-23

### 🔄 Cambio de Marca
- **CHANGED**: Weblandia → BravesLab
- **CHANGED**: URLs actualizadas: weblandia.es → braveslab.com
- **CHANGED**: Autor principal: Carlos Vera (Mikel Marqués como colaborador)
- **CHANGED**: Copyright actualizado a Braves Lab LLC

### 📄 Documentación
- **ADDED**: Archivo LICENSE con términos comerciales
- **UPDATED**: Todos los headers de archivos con nueva información de autoría
- **UPDATED**: Branding en assets y textos

---

## [1.1.1] - 2025-10-16

### ✨ Sistema de Cookies y Fingerprinting
- **ADDED**: Sistema de cookies con fingerprinting del navegador
- **ADDED**: Hash SHA-256 para identificación única de usuarios
- **ADDED**: Fallback automático a localStorage si cookies bloqueadas
- **ADDED**: Detección inteligente de cambios de dispositivo (regenera session_id)
- **ADDED**: Integración de `sessionId` en payload enviado a N8N
- **ADDED**: Clase `WlandFingerprint` en JavaScript

### ✨ Compliance GDPR
- **ADDED**: Banner de consentimiento de cookies configurable
- **ADDED**: Configuración GDPR en panel de administración
- **ADDED**: Opciones: habilitar banner, mensaje personalizado, texto del botón
- **ADDED**: Estilos responsive para banner GDPR

### 🐛 Correcciones
- **FIXED**: Error 500 al cargar frontend
- **FIXED**: Localización de configuración GDPR
- **FIXED**: Implementado flujo async/await correcto en fingerprinting

### 📝 Documentación
- **ADDED**: Documentación completa del sistema de cookies
- **ADDED**: Guía de verificación en DevTools
- **ADDED**: Flujo de funcionamiento técnico

### 📋 Archivos Nuevos
- `includes/class_cookie_manager.php` - Gestión de cookies y GDPR
- `assets/js/wland_fingerprint.js` - Sistema de fingerprinting
- `assets/css/wland_gdpr_banner.css` - Estilos del banner GDPR

---

## [1.1.0] - 2025-10-01

### ✨ Nuevas Características
- **ADDED**: Sistema de horarios de disponibilidad con zonas horarias
- **ADDED**: Páginas excluidas configurables (selector múltiple)
- **ADDED**: Token de autenticación N8N (header X-N8N-Auth)
- **ADDED**: Mensaje personalizado fuera de horario

### 🎨 Mejoras
- **IMPROVED**: Configuración del webhook más flexible
- **IMPROVED**: Validación de URLs de webhook
- **IMPROVED**: Sanitización de inputs en Settings API

---

## [1.0.0] - 2025-09-15

### 🎉 Lanzamiento Inicial

#### Funcionalidades Principales
- **ADDED**: Integración de chat con IA mediante bloque Gutenberg
- **ADDED**: Configuración de webhook N8N
- **ADDED**: Sistema de mensajes personalizables
- **ADDED**: Dos modos de visualización: Modal y Pantalla completa
- **ADDED**: Posicionamiento configurable (derecha, izquierda, centro)
- **ADDED**: Animación Lottie en botón de chat

#### Arquitectura
- **ADDED**: Estructura OOP con namespaces PHP (`WlandChat`)
- **ADDED**: WordPress Settings API para configuración
- **ADDED**: WordPress Customizer API para personalización en tiempo real
- **ADDED**: Bloque Gutenberg con opciones personalizables

#### Seguridad
- **ADDED**: Sanitización completa de inputs
- **ADDED**: Nonces en todos los formularios
- **ADDED**: Verificación de capacidades de usuario
- **ADDED**: Escapado de salidas (esc_html, esc_attr, esc_url)

#### i18n
- **ADDED**: Preparado para internacionalización
- **ADDED**: Text domain: `wland-chat`
- **ADDED**: Archivo .pot para traducciones

#### Desinstalación
- **ADDED**: Script `uninstall.php` para limpieza completa
- **ADDED**: Eliminación de opciones, metadatos y caché

---

## Leyenda de Etiquetas

- 🎉 **Lanzamiento**: Nueva versión mayor
- ✨ **ADDED**: Nueva funcionalidad agregada
- 🎨 **IMPROVED**: Mejora de funcionalidad existente
- 🐛 **FIXED**: Corrección de bug
- 🔒 **SECURITY**: Corrección de vulnerabilidad de seguridad
- 🔄 **CHANGED**: Cambio en funcionalidad existente
- 🗑️ **REMOVED**: Funcionalidad eliminada
- 📝 **DOCS**: Cambios en documentación
- 🏗️ **REFACTOR**: Refactorización de código
- 📦 **DEPS**: Actualización de dependencias
- ⚡ **PERF**: Mejora de rendimiento

---

## Información de Versiones

### Versiones Mayores (x.0.0)
Cambios incompatibles con versiones anteriores, nueva arquitectura o refactorización completa.

### Versiones Menores (1.x.0)
Nuevas funcionalidades compatibles con versiones anteriores.

### Parches (1.1.x)
Correcciones de bugs y mejoras menores.

---

## Enlaces

- **Repositorio**: [GitHub - BravesChat iA](https://github.com/Carlos-Vera/braveschat)
- **Documentación**: Ver [README.md](README.md) para guía de usuario
- **Documentación Técnica**: Ver [CLAUDE.md](CLAUDE.md) para desarrollo
- **Soporte**: carlos@braveslab.com
- **Web**: [https://braveslab.com](https://braveslab.com)

---

**BravesChat iA** - Integrando la inteligencia artificial en WordPress de forma profesional.

© 2025 Braves Lab LLC. Todos los derechos reservados.
