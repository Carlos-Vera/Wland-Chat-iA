# Plan de compatibilidad — BravesChat × WordPress 7.0

**Fecha del análisis:** 13 de junio de 2026
**Versión analizada:** BravesChat 2.4.6
**Referencia:** `docs/wp-7.0-aprendizajes-mail.md` (mail oficial del Release Squad, WP 7.0 lanzado el 20/05/2026)

---

## Resumen ejecutivo

- **Estado actual:** el plugin declara `Tested up to: 6.9` en `readme.txt` y `Requires PHP: 7.4`. El frontend (widget modal/fullscreen, vanilla JS en el documento principal) **no se ve afectado** por ningún cambio de WP 7.0. El requisito de PHP ya cumple el nuevo mínimo.
- **Riesgo real: MEDIO.** Dos focos concretos:
  1. **Bloque Gutenberg `braves/chat-widget`** registrado sin `block.json` y con assets de editor encolados vía `enqueue_block_editor_assets`: con el editor iframed de WP 7.0, el CSS de la tarjeta de preview no llega al canvas (iframe) y el bloque se verá sin estilos en el editor. Además, el script del editor declara dependencia del handle obsoleto `wp-editor`.
  2. **Panel admin Bentō** que sobreescribe estructuras de wp-admin core (`#wpcontent`, `#adminmenu`, `.notice`, `#wpfooter`) y hardcodea colores del esquema clásico: el tema admin "Modern" puede romper el encaje visual.
- **Esfuerzo estimado:** 1–2 días de trabajo efectivo. Fase 1 (pruebas): medio día. Fase 2 (correcciones + release): medio día a un día. Fase 3 es opcional y no bloquea.

---

## Hallazgos del análisis

### a) readme.txt (raíz del plugin)

| Campo | Valor actual | Línea |
|---|---|---|
| `Requires at least` | 5.8 | `readme.txt:4` |
| `Tested up to` | **6.9** ← debe pasar a 7.0 | `readme.txt:5` |
| `Requires PHP` | 7.4 | `readme.txt:6` |
| `Stable tag` | 2.4.6 | `readme.txt:7` |

### b) braves_chat.php (headers)

- `Version: 2.4.6` y `BRAVES_CHAT_VERSION = '2.4.6'` (`braves_chat.php:6` y `:25`).
- `Requires at least: 5.8`, `Requires PHP: 7.4` (`braves_chat.php:11-12`). Cumplen el nuevo mínimo de WP 7.0 (PHP 7.4). No existe header `Update URI` (el plugin vive en WordPress.org, correcto).

### c) Bloque Gutenberg — riesgo con el editor iframed (CRÍTICO)

El bloque **existe y es real**: `braves/chat-widget`, registrado en PHP en `includes/class_block.php` con JS de editor en `assets/js/block.js`.

1. **Sin `block.json`** — `register_block_type()` con array PHP (`includes/class_block.php:70-84`) referenciando los handles `braves-chat-block-editor`, `braves-chat-block-editor-style` y `braves-chat-block-style` (líneas 71-73). Esos handles **nunca se registran en `init`**: solo se registran+encolan dentro de `enqueue_block_editor_assets()` (`class_block.php:95-122`). Con el editor iframed, WordPress recopila los assets de bloques registrados para inyectarlos en el iframe; si los handles no están registrados en el momento de la recolección, **los estilos no entran al canvas**.
2. **CSS de preview fuera del iframe** — la tarjeta de preview (`.braves-block-card`, `assets/js/block.js:73-131`) se renderiza dentro del canvas (iframe), pero su hoja `assets/css/block_editor.css` se encola con `enqueue_block_editor_assets`, que en el editor iframed inyecta **solo en el documento padre**. Resultado en WP 7.0: el bloque aparece sin estilos en el editor.
3. **Dependencia obsoleta `wp-editor`** — `includes/class_block.php:99` declara `wp-editor` como dependencia del script del editor. Es un handle deprecado para bloques (carga el paquete completo del editor clásico de posts), históricamente fuerza al editor a salir del iframe y **Plugin Check lo marca**. Debe ser `wp-block-editor`.
4. **El JS del editor en sí está bien** — `block.js` corre en el documento padre (donde corre React), no usa `document.querySelector` globales ni manipula el DOM del canvas directamente; `window.bravesChatBlock` (localize) también vive en el padre. Sin cambios necesarios en la lógica JS.
5. **Hallazgo colateral (Plugin Check)** — `ensure_block_files()` (`includes/class_block.php:183-256`) escribe archivos dentro del directorio del plugin con `copy()`/`file_put_contents()`. Plugin Check lo marcará (escritura en directorio de plugin) y es un fallback innecesario: los archivos reales ya están versionados.

### d) Panel admin Bentō — riesgo con el tema admin "Modern" (MEDIO)

El Bentō es **mayormente autocontenido** (cadena propia `variables.css` → `base.css` → `components.css` → `dashboard.css` → `settings.css`, encolada en `includes/admin/class_admin_controller.php:382-433`), pero tiene puntos de acoplamiento con wp-admin core:

1. **Overrides de estructura core** — `assets/css/admin/dashboard.css:50-90`: fuerza estado activo en `ul#adminmenu li.toplevel_page_braveschat` con color hardcodeado `#2271b1` (azul del esquema clásico), `#wpcontent { padding-left: 0 !important }`, oculta `#wpfooter` y `.notice:not(.braves-notice)`, y pinta `#wpbody`/`#wpbody-content` en dark mode. Si "Modern" cambia el layout o la paleta de esas estructuras, el encaje se rompe o el menú activo queda con un azul ajeno al nuevo tema.
2. **Variables de wp-components** — `assets/css/admin/dashboard.css:350` y `:431` usan `--wp-admin-border-width-focus` y `--wp-components-color-accent`. Probablemente sigan definidas en "Modern", pero sus valores cambiaron: verificar focos y acentos.
3. **`wp_enqueue_style('wp-components')`** — `includes/admin/class_admin_controller.php:388` y `includes/class_settings.php:1154`. La hoja de wp-components fue reestilizada por "Modern": verificar que ningún componente del Bentō dependa del aspecto anterior.
4. **CSS inline del icono del menú** — `includes/class_settings.php:~270-311` (`add_menu_icon_styles`) hardcodea colores del esquema admin clásico: `#a7aaad`, `#00a0d2`, `#ffffff !important`. Con la paleta de "Modern" pueden desentonar. Lo mismo aplica a `add_menu_icon_active_styles()` (`includes/admin/class_admin_controller.php:92+`).
5. **Clases core en templates** — todas las páginas usan `<div class="wrap braves-admin-wrap">` y `history.php:244-271` usa `.notice notice-warning|error|info inline`. Siguen soportadas en 7.0 pero "Modern" las reestiliza: verificación visual necesaria.
6. **`wp_editor()` (TinyMCE clásico)** — `includes/admin/templates/gdpr.php:147` y `includes/admin/templates/availability.php:231`. Sigue soportado en WP 7.0, pero verificar el render de la toolbar bajo "Modern".

### e) PHP — señales

- **Sin funciones WP deprecadas detectadas** (no hay `get_page_by_title`, `create_function`, `get_currentuserinfo`, `screen_icon`, etc.).
- **`extract($attributes)`** en `includes/class_frontend.php:314` — no rompe con WP 7.0, pero es patrón desaconsejado que **Plugin Check marca** (WordPress.PHP.DontExtract).
- Compatibilidad PHP 8.3 (mínimo recomendado por core): el código es vanilla PHP 7.4+ con propiedades declaradas en los Singletons; no se ven señales de propiedades dinámicas. Confirmar con PHPCompatibility/Plugin Check en Fase 1.

### f) Otros puntos de fricción

- **Frontend del widget:** sin riesgo. Los assets van por `wp_enqueue_scripts` al documento principal del sitio; el iframed editor no afecta al frontend.
- **Nota de mantenimiento:** `CLAUDE.md` menciona un método `plugin_api_info()` en `braves_chat.php` que ya no existe en el código — referencia obsoleta a corregir cuando se toque `CLAUDE.md` en Fase 2.

---

## Plan de acción

### Fase 1 — Verificación y pruebas (con WP 7.0 instalado)

| # | Ítem | Archivos afectados | Criterio de verificación |
|---|---|---|---|
| 1.1 | Instalar WP 7.0 limpio (o actualizar el entorno "WordPress Limpio") con PHP 8.3 y activar BravesChat 2.4.6 | — | Activación sin warnings/notices en `debug.log` |
| 1.2 | Auditar el panel Bentō contra el tema admin "Modern": las 7 pestañas (Dashboard, Ajustes, Apariencia, Horarios, GDPR, Historial, About), claro y oscuro, icono del menú, estado activo del menú, notices de `history.php`, `wp_editor()` en GDPR/Horarios | `assets/css/admin/*.css`, `includes/admin/templates/*.php` | Captura de cada pestaña sin roturas de layout ni colores discordantes; lista de defectos concretos para Fase 2 |
| 1.3 | Probar el bloque `braves/chat-widget` en el editor iframed: insertar el bloque, comprobar estilos de la tarjeta de preview dentro del canvas, panel lateral (InspectorControls), guardado del atributo `welcomeMessage` | `includes/class_block.php`, `assets/js/block.js`, `assets/css/block_editor.css` | La tarjeta de preview se ve con estilos dentro del iframe; el sidebar funciona; sin errores en consola |
| 1.4 | Probar el frontend: modo modal, fullscreen y `mixed`, skins Default y Braves, banner GDPR, envío al webhook N8N | `templates/`, `assets/js/`, `assets/css/` | El widget renderiza y envía mensajes igual que en WP 6.9 |
| 1.5 | Pasar **Plugin Check** (plugin oficial) sobre el plugin completo | todo el plugin | Cero errores; warnings documentados y triados (se esperan: `wp-editor` dep, `extract()`, escritura en directorio de plugin) |
| 1.6 | Smoke test PHP 8.3: navegar todas las páginas admin y frontend con `WP_DEBUG` activo | — | `debug.log` sin deprecations del plugin |

### Fase 2 — Cambios obligatorios

| # | Ítem | Archivos afectados | Criterio de verificación |
|---|---|---|---|
| 2.1 | **Arreglar assets del bloque para el editor iframed.** Opción recomendada: migrar a `block.json` (`editorScript`, `editorStyle`, `style`, `render`) — WP inyecta automáticamente los estilos del bloque en el iframe. Opción mínima: registrar los handles con `wp_register_script/style` en `init` antes de `register_block_type` y mover el CSS del canvas a `enqueue_block_assets` | `includes/class_block.php`, nuevo `block.json` (o ajuste de hooks), `assets/css/block_editor.css` | Repetir 1.3: preview con estilos dentro del iframe |
| 2.2 | **Sustituir la dependencia `wp-editor` por `wp-block-editor`** en el script del editor | `includes/class_block.php:99` | El bloque carga sin el paquete del editor clásico; Plugin Check ya no lo marca; sidebar funciona |
| 2.3 | **Corregir defectos visuales del Bentō detectados en 1.2**: como mínimo, sustituir colores hardcodeados del esquema clásico (`#2271b1` en `dashboard.css:51`, `#a7aaad`/`#00a0d2` del CSS inline del icono) por variables de wp-admin o valores propios del Bentō; revisar los overrides de `#wpcontent`/`#wpfooter`/`.notice` | `assets/css/admin/dashboard.css`, `includes/class_settings.php` (add_menu_icon_styles), `includes/admin/class_admin_controller.php` | Repetir 1.2 sin defectos |
| 2.4 | **Eliminar `ensure_block_files()`** (escritura en directorio del plugin, flag de Plugin Check, fallback innecesario) y **reemplazar `extract()`** en `class_frontend.php:314` por asignaciones explícitas | `includes/class_block.php:66-68,183-256`, `includes/class_frontend.php:314` | Plugin Check sin esos flags; bloque y shortcode/render frontend intactos |
| 2.5 | **`readme.txt` → `Tested up to: 7.0`** | `readme.txt:5` | Header actualizado; WordPress.org deja de mostrar advertencia de compatibilidad |
| 2.6 | **Bump de versión (sugerido 2.4.7 o 2.5.0 según alcance de 2.1-2.4) siguiendo el flujo del proyecto** (usar `/release`): `braves_chat.php` (header `Version` + `BRAVES_CHAT_VERSION`), `readme.txt` (`Stable tag`, `== Changelog ==`, `== Upgrade Notice ==`), `README.md`, `CHANGELOG.md`, `includes/admin/templates/about.php` (ítem timeline), `CLAUDE.md` (campo Versión; aprovechar para quitar la referencia obsoleta a `plugin_api_info()`), `memory/MEMORY.md`, `errores.md` si aplica | los listados | Todos los archivos consistentes con la nueva versión; tag `vX.Y.Z` genera el ZIP por GitHub Action |
| 2.7 | **Re-pasar Plugin Check** antes de subir a WordPress.org | todo el plugin | Cero errores, warnings justificados |

### Fase 3 — Mejoras opcionales / futuras (solo evaluar, no implementar ahora)

| # | Ítem | Archivos afectados (potenciales) | Criterio de evaluación |
|---|---|---|---|
| 3.1 | **Connectors API (Connections Screen)** para las credenciales N8N: `braves_chat_n8n_auth_token`, `braves_chat_stats_api_key`. Evaluar registrar conectores para que las keys se gestionen desde la pantalla central de WP 7.0, manteniendo las opciones actuales como fallback (WP < 7.0 sigue soportado: `Requires at least: 5.8`) | `includes/class_settings.php`, `includes/admin/templates/settings.php`, `history.php` | Spike: ¿la API permite keys por-plugin con fallback limpio a `get_option()`? Decidir en versión 2.6+ |
| 3.2 | **Registro de bloque solo-PHP** (novedad 7.0): el bloque es simple (1 atributo, `save: null`, render server-side), candidato natural. Pero el preview personalizado y el `InspectorControls` requieren JS, así que probablemente convenga quedarse en `block.json` + JS. Evaluar tras 2.1 | `includes/class_block.php`, `assets/js/block.js` | Si el preview puede expresarse como template PHP sin perder el textarea del sidebar, simplificar; si no, descartar |
| 3.3 | **AI Client de WP 7.0**: evaluar como alternativa/complemento al webhook N8N (p.ej. respuestas offline o resumen de historial). Solo análisis de viabilidad, sin compromiso | — | Documento corto de pros/contras; el webhook N8N sigue siendo el core del producto |
| 3.4 | **Abilities API JS**: sin caso de uso claro para BravesChat hoy. Revisitar cuando haya demanda | — | N/A |

---

## Riesgos y notas

- **Riesgo principal (alto impacto, certeza alta):** el CSS del editor del bloque no llegará al canvas iframed en WP 7.0 → bloque visualmente roto en el editor. No afecta al frontend ni pierde datos; es cosmético-funcional en el editor. Mitigación: 2.1.
- **Riesgo secundario (impacto medio, certeza media):** desencaje visual del Bentō bajo "Modern" por los overrides de `#wpcontent`/`#adminmenu` y colores hardcodeados. El Bentō oculta los notices core (`dashboard.css:74-76`), lo que también suprime avisos importantes de WordPress en sus pantallas — comportamiento intencional pero a revisar si "Modern" cambia el markup de notices.
- **No hay riesgo de PHP:** el mínimo del plugin (7.4) coincide con el nuevo mínimo de core. Aun así, probar bajo PHP 8.3 (recomendado por core) en Fase 1.
- **WordPress.org:** mientras `Tested up to` siga en 6.9, la página del plugin mostrará advertencia de "no probado con las últimas 3 versiones mayores" conforme avancen los releases. El cambio 2.5 es de bajo costo y debería ir en el primer release.
- **Plugin Check escaneará automáticamente** las subidas a WordPress.org próximamente: los flags conocidos (`wp-editor`, `extract()`, escritura en directorio del plugin) conviene limpiarlos ya (2.2, 2.4) para no recibir emails de incidencias.
- **Compatibilidad hacia atrás:** el plugin declara `Requires at least: 5.8`. Cualquier corrección (especialmente 2.1) debe seguir funcionando en WP 5.8–6.9 sin editor iframed. `block.json` es compatible desde WP 5.8, así que la migración es segura.
- **Nota de documentación:** `CLAUDE.md` referencia un método `plugin_api_info()` inexistente en `braves_chat.php`; corregir al actualizar docs en 2.6.
