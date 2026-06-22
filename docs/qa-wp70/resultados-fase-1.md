# Resultados Fase 1 — Verificación BravesChat 2.4.6 en WordPress 7.0

**Fecha:** 13 de junio de 2026
**Entorno:** WordPress 7.0 (tema admin "Modern"), PHP 8.5.1, MAMP local, BravesChat 2.4.6
**Plan de referencia:** `docs/plan-compatibilidad-wp-7.0.md`
**Evidencia:** 15 screenshots en `docs/qa-wp70/`

## Veredicto por ítem del plan

| Ítem | Resultado |
|---|---|
| 1.1 Activación limpia | ✅ Desactivación/reactivación sin warnings ni notices en debug.log |
| 1.2 Panel Bentō vs "Modern" | ✅ con 1 defecto (Historial en blanco, ver D2) |
| 1.3 Bloque en editor iframed | ⚠️ DEFECTO CRÍTICO distinto al previsto (ver D1) |
| 1.4 Frontend | ✅ smoke test: assets encolados y markup del widget renderizado con el chat activado |
| 1.5 Plugin Check | ⚠️ 11 errores / 8 warnings, mayoría dev-only; 3 hallazgos reales (D3, D4, D5) |
| 1.6 Smoke PHP | ✅ 0 deprecations/warnings del plugin bajo PHP 8.5 (más estricto que el 8.3 recomendado) |

## Defectos confirmados (corregir en Fase 2)

### D1 — CRÍTICO: el bloque usa apiVersion 1 y des-iframea el editor entero
`assets/js/block.js:19` registra `braves/chat-widget` con `apiVersion: 1`. Verificado empíricamente en WP 7.0: el canvas arranca iframed y **al insertar el bloque WordPress saca todo el editor del iframe** (es el único bloque del sitio < v3). Esto degrada el editor completo, no solo el bloque.
- **Matiz importante:** el supuesto original del plan (CSS que no llega al canvas) **no se confirmó tal cual** — `block_editor.css` sí estaba presente dentro del iframe y `.braves-block-card` se ve con estilos; pero porque el editor se des-iframea. El fix correcto sigue siendo el mismo del plan (2.1): migrar a `block.json` con `apiVersion: 3`.
- InspectorControls y el atributo `welcomeMessage` funcionan bien. 0 errores de consola.

### D2 — Historial queda en blanco cuando el webhook no está configurado
Los avisos de `includes/admin/templates/history.php:251-263` usan clases `.notice` core y la regla `.braves-chat-admin-page .notice:not(.braves-notice) { display:none }` de `assets/css/admin/dashboard.css:75` los oculta — la página queda vacía sin explicación. Fix: añadir clase `braves-notice` a los avisos propios del plugin (o excluirlos de la regla).

### D3 — Plugin Check ERROR: `wp_get_global_settings()` requiere WP 5.9
`includes/admin/templates/appearance.php:45` usa una función de WP 5.9 pero el plugin declara `Requires at least: 5.8`. Fix: subir el mínimo a 5.9 (recomendado) o guardar la llamada con `function_exists()`.

### D4 — Plugin Check ERROR: archivos `.po~` con nombre inválido
3 backups `languages/braves-chat-es_ES-backup-*.po~` (badly_named_files). No están en git ni los cubre `.distignore` (`*.bak` no matchea `*.po~`). Fix: borrarlos y añadir `*.po~` a `.distignore`.

### D5 — Plugin Check ERROR: `Tested up to: 6.9` < 7.0
`readme.txt:5`. Ya previsto en el plan (ítem 2.5). Con la Fase 1 superada, el plugin **puede declararse compatible** tras corregir D1.

### Menores / no bloqueantes
- Preview del insertador: "Vista previa no disponible" — falta `example` en el registro del bloque.
- Resto de flags de Plugin Check (hidden files, `.claude`, `.github`, `*.md` en raíz, `.DS_Store`) son **dev-only**: `.distignore` ya los excluye del ZIP de producción.
- Plugin Check NO marcó en esta corrida ni `wp-editor` dep ni `extract()` ni la escritura en directorio del plugin (previstos en el análisis estático); mantener los fixes 2.2 y 2.4 del plan igualmente por higiene.

## Lo que salió mejor de lo previsto

- **Bentō vs tema "Modern": encaje correcto.** Las 7 pestañas renderizan sin roturas, dark mode OK, icono del menú y estado activo coherentes, TinyMCE (`wp_editor`) con toolbar funcional en GDPR y Horarios. El riesgo medio previsto (colores hardcodeados) **no se materializó** — el ítem 2.3 del plan baja de prioridad a cosmético-opcional.
- **PHP:** cero deprecations propias incluso en PHP 8.5.

## Estado del entorno tras las pruebas

Todo restaurado: usuario QA temporal eliminado, `WP_DEBUG` devuelto a `false`, `debug.log` eliminado (solo contenía ruido de wp-cli), opción `braves_chat_global_enable` restaurada a su estado original (inexistente). Plugin Check queda instalado para el re-chequeo del ítem 2.7.

**Nota operativa del entorno:** wp-cli en esta máquina necesita `--exec="define('DB_HOST','127.0.0.1:3306');"` para llegar al MySQL de MAMP.

## Prioridad para Fase 2 (ajustada)

1. D1 — migrar bloque a `block.json` + `apiVersion: 3` (incluye 2.2 `wp-block-editor` y arrastra 2.4 `ensure_block_files()`)
2. D3 — `Requires at least: 5.9` (o guard)
3. D2 — fix notices del Historial
4. D4 — limpiar `.po~` + `.distignore`
5. D5 — `Tested up to: 7.0` + bump de versión (flujo `/release`)
6. Opcional: `example` en el bloque; revisar `extract()` de `class_frontend.php:314`
