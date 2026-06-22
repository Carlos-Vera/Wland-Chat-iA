# BravesChat Plugin - Documentación Técnica

## Bucle de mejora automatica: Cada vez que corrijas un error en el código actualiza el archivo errores.md, en el especifica que error haz correjido y como.

## 📋 Información General
- **Nombre:** BravesChat
- **Versión:** 2.5.0
- **Descripción:** Plugin profesional de chat para WordPress con integración a N8N, soporte de horarios, cumplimiento GDPR, personalización avanzada y estadísticas de conversaciones.
- **Autor:** Carlos Vera (BravesLab)
- **Repositorio:** Carlos-Vera/BravesChat

## 🛠️ Stack Tecnológico
- **PHP:** 7.4+
- **WordPress:** 5.8+
- **JavaScript:** Vanilla JS (ES6+)
- **CSS:** Vanilla CSS + CSS Variables (Custom Properties)
- **Integración:** N8N Webhooks (HTTP POST)

## 🏗️ Arquitectura
El plugin sigue el patrón **Singleton** para sus clases principales, asegurando una única instancia de cada componente.

### Estructura de Clases
- `BravesChat` (Main): Inicialización, carga de dependencias, hooks globales.
- `Settings`: Gestión de opciones (Settings API), registro de campos.
- `Frontend`: Encolado de scripts/estilos, renderizado del widget (Modal/Fullscreen).
- `Admin_Controller`: Controlador del panel de administración (Diseño "Bentō").
- `BravesCookieManager`: Gestión de cookies y fingerprinting.

### Directorios Clave
- `/includes`: Lógica de negocio (PHP).
- `/includes/admin`: Lógica y templates del panel de administración.
- `/assets/css`: Estilos frontend y backend.
- `/assets/js`: Lógica frontend (chat, fingerprint, redirect) y backend.
- `/templates`: Archivos de vista del widget (`modal.php`, `screen.php`).

## ✨ Funcionalidades Principales

### 1. Integración con N8N
- **Webhook:** Envío de mensajes a flujos de trabajo en N8N.
- **Autenticación:** Soporte para Token de Seguridad (Header `X-N8N-Auth`).
- **Payload:** Incluye mensaje, historial, fingerprint del usuario, y metadatos de la página.

### 2. Modos de Visualización
- **Modal:** Widget flotante en esquina (configurable).
- **Fullscreen:** Interfaz de chat a pantalla completa.
- **Mixto (`mixed`):** Burbuja flotante global + pantalla completa en páginas con bloque Gutenberg.
- **Skins:** "Default" y "Braves" (diseño personalizado con header transparente y avatares).

### 3. Personalización (Apariencia)
- **Colores:** Primario, Fondo, Texto, Burbuja, Icono.
- **Textos:** Título, Subtítulo, Mensaje de Bienvenida, Estado ("Escribiendo...").
- **Posición:** Inferior Derecha, Inferior Izquierda.
- **Icono:** Selección de iconos (SVG/Dashicons) o imagen personalizada.

### 4. Disponibilidad y Horarios
- **Horario:** Configuración de hora de inicio y fin.
- **Zona Horaria:** Selección de timezone.
- **Mensaje Offline:** Respuesta automática fuera de horario.

### 5. Privacidad y GDPR
- **Banner de Consentimiento:** Bloqueo del chat hasta aceptación.
- **Fingerprinting:** Identificación única de usuario sin datos personales (`braves_fingerprint.js`).
- **Cookies:** `braves_chat_session` (Duración: 1 año).

### 6. Estadísticas (v2.1.4)
- **Webhook:** Consulta a N8N que extrae el historial de conversaciones desde Postgres.
- **Configuración:** URL del webhook + API Key propios (opciones `braves_chat_stats_webhook_url` / `braves_chat_stats_api_key`).
- **Tabla:** Columnas Session ID, Email, Último Mensaje, Fecha/Hora.
- **CSV:** Exportación con todos los campos: `session_id`, `client_mail`, `last_message`, `updated_at`, `chat_history`, `metadata`, `user_height`.

### 7. Experiencia de Usuario (UX)
- **Markdown:** Soporte para renderizado de Markdown en mensajes del bot (enlaces, negritas, listas).
- **Typing Indicator:** Animación de puntos suspensivos y velocidad de escritura configurable.
- **Persistencia:** Historial de chat guardado en `localStorage` (o transitorio si no hay consentimiento).
- **Tipografía:** Montserrat (cargada localmente).

## 🎨 Convenciones de Diseño (UX/UI)
Consultar el archivo `UX-UI-Convenciones.md` para detalles sobre:
- Paleta de Colores
- Tipografía
- Componentes UI
- Animaciones
- Estructura CSS/JS

## 💻 Comandos de Desarrollo
No se requiere compilación (Vanilla JS/CSS).

- **Logs:** Revisar `debug.log` de WordPress para errores PHP.
- **Consola:** Revisar consola del navegador para errores JS (prefijo `BravesChat:`).

## 🚀 Flujo de Trabajo
1.  **Ramas:** `main` (producción), `develop` (desarrollo).
2.  **Versiones:** SemVer (MAJOR.MINOR.PATCH).
3.  **Commits:** Conventional Commits (e.g., `feat: nueva opción de color`, `fix: error en safari`).
4.  **Archivos a actualizar en cada versión** (usar `/release` para automatizar esto):
    - `braves_chat.php` — header `Version:` y constante `BRAVES_CHAT_VERSION`
    - `readme.txt` — `Stable tag:`, sección `== Changelog ==` y `== Upgrade Notice ==` ⚠️ WordPress lo usa para detectar actualizaciones. También actualizar `== Description ==` si se añade una feature notable, y `== Screenshots ==` si cambian las capturas (el slug correcto de la carpeta es `braveschat`, no `braves-chat`)
    - `README.md` — actualizar número de versión donde aparezca mencionado
    - `CHANGELOG.md` — índice de versiones + nueva entrada al tope
    - `includes/admin/templates/about.php` — nuevo `<div class="braves-timeline__item">` al tope del timeline (justo bajo el cap "Today"), **sin** `data-tl-item`, y añadir `data-tl-item` al ítem que antes era el más reciente. Estructura interna: `.braves-timeline__axis` (con `.braves-timeline__badge` = `vX.Y.Z` y `.braves-timeline__label` = fecha) + `.braves-timeline__card-side` (con `.braves-changelog__version`). El layout es un grid fijo de 2 columnas (`axis` → col 1, `card-side` → col 2) con `grid-row:1` que **ignora el orden del HTML**, así que NO hay clases de lado (`braves-tl-left/right` ya no existen) ni `--braves-tl-nudge` ni márgenes manuales. La posición se resuelve sola por CSS.
    - `CLAUDE.md` — campo `Versión:` en Información General
    - `memory/MEMORY.md` — versión actual y cambios clave
    - `braves_chat.php` método `plugin_api_info()` — actualizar `$plugin->active_installs`
    - `errores.md` — si se corrigieron bugs en esta versión
5.  **Release Automatizado:**
    - Al crear un tag que empiece por `v` (e.g., `v2.1.3`), un GitHub Action genera automáticamente `braveschat.zip`.
    - Este ZIP contiene la carpeta `braveschat/` en la raíz, asegurando actualizaciones limpias en WordPress.
    - El ZIP se adjunta automáticamente al Release en GitHub.

## 🗄️ Opciones WP Registradas (prefijo `braves_chat_`)

| Opción | Tipo | Página |
|---|---|---|
| `global_enable` | bool | Ajustes |
| `webhook_url` | url | Ajustes |
| `n8n_auth_token` | string | Ajustes |
| `typing_speed` | int | Ajustes |
| `header_title/subtitle/status_text` | string | Ajustes |
| `welcome_message` | string | Ajustes |
| `excluded_pages` | array | Ajustes |
| `position`, `display_mode`, `chat_icon` | string | Apariencia |
| `icon_color`, `bubble_color`, `primary_color`, `background_color`, `text_color` | hex | Apariencia |
| `bubble_tooltip`, `bubble_image`, `bubble_text` | string | Apariencia |
| `agent_name` | string | Apariencia |
| `chat_skin` | string | Apariencia |
| `availability_enabled` | bool | Horarios |
| `availability_start/end` | time | Horarios |
| `availability_timezone`, `availability_message` | string | Horarios |
| `gdpr_enabled` | bool | GDPR |
| `gdpr_message`, `gdpr_accept_text` | string | GDPR |
| `stats_webhook_url` | url | Estadísticas |
| `stats_api_key` | string | Estadísticas |

## 📊 Funcionalidad Historial / Estadísticas
- **Template:** `includes/admin/templates/history.php` (reemplazó a `statistics.php` en v2.1.5)
- **Fetch:** `wp_remote_get($url, ['headers' => ['x-api-key' => $key]])` → JSON decode
- **JSON esperado de N8N:** `[{ session_id, client_mail, last_message, chat_history, metadata, user_height, updated_at }]`
- **CSV:** exportado por JS inline con BOM UTF-8, columnas extra en `data-*` de cada `<tr>`
- **Nombre archivo CSV:** `braveschat_estadisticas_YYYYMMDD.csv`

### Patrón para agregar una nueva pestaña admin
1. `class_admin_sidebar.php` → ítem en `get_menu_items()` + SVG en `get_icon_svg()`
2. `class_admin_controller.php` → `add_submenu_page(null, ...)`, método `render_*_page()`, añadir slug a `is_braves_admin_page()`, `add_menu_icon_active_styles()` y `filter_admin_title()`
3. `class_settings.php` → `register_setting()` para nuevas opciones + añadir a `$all_fields` en `render_hidden_fields()`
4. Crear `includes/admin/templates/[page].php` siguiendo el patrón: `Admin_Header`, `Admin_Sidebar`, `Template_Helpers`, form con `settings_fields('braves_chat_settings')` + `render_hidden_fields()`

### `render_hidden_fields()` — CRÍTICO
Preserva todas las opciones al guardar formularios parciales. Siempre que se registre una nueva opción, añadirla a `$all_fields` en este método.

## ⚠️ Notas Importantes
- **Cache:** Al actualizar Assets, incrementar `BRAVES_CHAT_VERSION` en `braves_chat.php` para romper la caché del navegador.
- **Base de Datos:** Las opciones se guardan en `wp_options` con prefijo `braves_chat_`.
- **Versículos diarios:** Servidos desde archivos locales sin peticiones externas. `get_daily_verse()` en `class_admin_header.php` carga `includes/admin/data/bible-verses-es.php` (NVI) o `bible-verses-en.php` (NIV) según `get_locale()`.

# CLAUDE.md

Behavioral guidelines to reduce common LLM coding mistakes. Merge with project-specific instructions as needed.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.
