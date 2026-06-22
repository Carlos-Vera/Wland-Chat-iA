# WordPress 7.0 — Aprendizajes del mail oficial del Release Squad

**Fuente:** Email "The WordPress 7.0 Release Squad" recibido por Carlos Vera (BravesLab), junio 2026.
**Contexto:** Aviso de compatibilidad para el plugin BravesChat (wordpress.org/plugins/braveschat/).

## Datos clave

- **Fecha de lanzamiento de WordPress 7.0:** 20 de mayo de 2026 (ya liberado a la fecha de este análisis).
- **Estado actual de BravesChat en WordPress.org:** `Tested up to: 6.9.4`.
- **Acción requerida mínima:** probar el plugin con WP 7.0 y actualizar `Tested up to: 7.0` en `readme.txt` (en el directorio del stable tag).
- **Riesgo de no actuar:** los plugins no marcados como compatibles con una de las últimas tres versiones mayores muestran una advertencia en su página de WordPress.org.

## Cambios técnicos en WordPress 7.0

1. **Nuevo tema de administración "Modern" por defecto.** Toda pantalla del admin recibió cambios visuales, incluyendo las que usan `@wordpress/components`. Los plugins con paneles de administración propios deben verificar que todo se vea bien.

2. **Editor dentro de iframe (iframed editor).** Continúa el trabajo para que el editor de bloques se sirva siempre dentro de un iframe. Los plugins que inyectan scripts/estilos al editor o asumen acceso al documento principal pueden romperse.
   Ref: https://make.wordpress.org/core/2026/02/24/iframed-editor-changes-in-wordpress-7-0/

3. **Eliminado el soporte para PHP 7.2 y 7.3.** El mínimo soportado pasa a PHP 7.4; el mínimo recomendado sigue siendo PHP 8.3.
   Ref: https://make.wordpress.org/core/2026/01/09/dropping-support-for-php-7-2-and-7-3/

4. **Nuevo AI Client.** API PHP agnóstica de proveedor para enviar prompts a modelos de IA desde plugins con una interfaz consistente.
   Ref: https://make.wordpress.org/core/2026/03/24/introducing-the-ai-client-in-wordpress-7-0/

5. **Nueva Connections Screen (Connectors API).** Gestión centralizada de API keys usadas por los plugins.
   Ref: https://make.wordpress.org/core/2026/03/18/introducing-the-connectors-api-in-wordpress-7-0/

6. **Abilities API extendida a JavaScript.** La Abilities API introducida en WP 6.9 ahora tiene contraparte JS para habilidades del lado del cliente.
   Ref: https://make.wordpress.org/core/2026/03/24/client-side-abilities-api-in-wordpress-7-0/

7. **Registro de bloques solo con PHP.** Los bloques simples ahora pueden crearse usando únicamente PHP.
   Ref: https://make.wordpress.org/core/2026/03/03/php-only-block-registration/

## Recursos

- **Field Guide completo:** https://make.wordpress.org/core/2026/05/14/wordpress-7-0-field-guide/
- **Plugin Check:** https://wordpress.org/plugins/plugin-check/ — próximamente escaneará automáticamente las actualizaciones subidas a WordPress.org y enviará email si encuentra problemas. Conviene pasarlo antes de cada subida.
- **WordPress Beta Tester:** https://wordpress.org/plugins/wordpress-beta-tester/ — para probar con Release Candidates.

## Relevancia para BravesChat

- El plugin ya exige PHP 7.4+ y WP 5.8+, así que el cambio de PHP mínimo no lo afecta directamente, pero hay que verificar el header `Requires PHP`.
- BravesChat tiene panel de administración propio (diseño "Bentō") con CSS personalizado: hay que auditarlo contra el nuevo tema admin "Modern".
- El modo `mixed` usa un bloque Gutenberg: revisar compatibilidad con el editor iframed.
- Oportunidades futuras (no bloqueantes): Connectors API para las API keys de N8N/estadísticas, AI Client como alternativa de integración, registro de bloque solo-PHP.
