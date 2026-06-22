# Resultados Fase 2 — Correcciones y release BravesChat 2.5.0 (WordPress 7.0)

**Fecha:** 13 de junio de 2026
**Versión:** 2.4.6 → **2.5.0**
**Plan de referencia:** `docs/plan-compatibilidad-wp-7.0.md` · **Verificación previa:** `docs/qa-wp70/resultados-fase-1.md`

## Cambios de código aplicados

| # | Cambio | Archivos | Estado |
|---|---|---|---|
| 1 | Bloque migrado a `block.json` con `apiVersion: 3` (ya no des-iframea el editor en WP 7.0) | `block.json` (nuevo), `includes/class_block.php`, `assets/js/block.js` | ✅ verificado en navegador |
| 2 | Dependencia `wp-editor` → `wp-block-editor`; handles registrados en `init` | `includes/class_block.php` | ✅ |
| 3 | Eliminado `ensure_block_files()` y sus helpers (~95 líneas; escribía en el dir del plugin) | `includes/class_block.php` | ✅ |
| 4 | Añadido `example` al bloque → preview en el insertador | `block.json` / `assets/js/block.js` | ✅ |
| 5 | `extract()` reemplazado por asignaciones explícitas | `includes/class_frontend.php:314` | ✅ |
| 6 | Fix Historial en blanco: clase `braves-notice` en los avisos propios | `includes/admin/templates/history.php` | ✅ verificado en navegador |
| 7 | `Requires at least` 5.8 → 5.9 (por `wp_get_global_settings()`) | `braves_chat.php`, `readme.txt` | ✅ |
| 8 | `Tested up to` 6.9 → 7.0 | `readme.txt` | ✅ |
| 9 | Borrados `languages/*.po~`; `*.po~` añadido a `.distignore` | `.distignore`, `languages/` | ✅ |

## Verificación en navegador (WP 7.0, Playwright)

- **Bloque:** `iframed_before = true`, `iframed_after = true` — el editor **ya NO se des-iframea** al insertar el bloque. `.braves-block-card` con estilos dentro del canvas (bg blanco, border-radius 12px). `welcomeMessage` editable. 0 errores de consola. Evidencia: `b4-retest-canvas.png`, `b5-retest-inserter.png`.
- **Historial:** ya no aparece en blanco — se ven los 2 avisos propios (`braves-notice`, `display:block`): "¡Ya casi está! Conecta la URL del Webhook…" y "Webhook no configurado…". Evidencia: `a-retest-history.png`.

## Plugin Check (ítem 2.7) — corrida final

- **0 errores reales de código.** Desaparecieron: `outdated_tested_upto_header` (6.9→7.0), `wp_function_not_compatible_with_requires_wp` (`wp_get_global_settings` vs 5.8→5.9), y los 3 `.po~` (`badly_named_files`).
- Los 8 errores y 6 warnings restantes son **archivos de desarrollo** (`.DS_Store`, `.git*`, `.phpcs.xml`, `.claude`, `.github`, `*.md` en raíz) — todos excluidos del ZIP de producción por `.distignore`. No afectan al paquete que se sube a WordPress.org.

## Archivos del release (bump 2.5.0)

`braves_chat.php` (Version + `BRAVES_CHAT_VERSION`), `readme.txt` (Stable tag + Changelog + Upgrade Notice), `CHANGELOG.md`, `includes/admin/templates/about.php` (ítem timeline v2.5.0), `README.md` (badges versión/WP 5.9+), `CLAUDE.md` (campo Versión), `memory/MEMORY.md` (creado), `errores.md` (2 entradas: des-iframe del bloque + Historial en blanco).

**Nota sobre `about.php`:** las clases `braves-tl-left/right` que menciona el CLAUDE.md ya no existen en el código; el timeline real es un grid fijo de 2 columnas (`axis` col 1, `card-side` col 2) con `grid-row:1` que ignora el orden del HTML. El ítem v2.5.0 se dejó con el patrón axis-first del resto y sin `data-tl-item` (patrón del ítem más reciente). Conviene corregir esa instrucción obsoleta en CLAUDE.md.

## Pendiente (no bloqueante)

- Commit + tag `v2.5.0` (dispara el GitHub Action que genera `braveschat.zip`) y subida a WordPress.org. **Aún no ejecutado** — a la espera de Carlos.
- Fase 3 del plan (Connectors API, AI Client, registro solo-PHP): solo evaluación futura, sin compromiso.
