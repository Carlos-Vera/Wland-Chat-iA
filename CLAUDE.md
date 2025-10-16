# CLAUDE.md

Este archivo proporciona orientación a Claude Code (claude.ai/code) cuando se trabaja con código en este repositorio.

## Descripción general del proyecto

**Wland Chat iA** es un plugin profesional de WordPress que integra el modulo de chat embedido con tecnología de IA a través de N8N. Creado con opciones de visualización global e inserción por página mediante un bloque Gutenberg. Desarrollado por Carlos Vera (BravesLab) para ser usado en instalaciones de wordpress.

## Entorno de desarrollo

Se trata de un plugin de WordPress ubicado dentro de una instalación XAMPP:
- **Ruta del plugin**: `/Applications/XAMPP/xamppfiles/htdocs/wordpress/wp-content/plugins/Wland-Chat-iA`
- **Raíz de WordPress**: `/Applications/XAMPP/xamppfiles/htdocs/wordpress`
- Prueba en el navegador en el sitio de WordPress que se ejecuta en XAMPP
- Cada función nueva debe incluir un bloque de comentario JSDoc encima
- Todas las variables y funciones nuevas deben escribirse en snake_case
- No uses camelCase.

## Estructura de Carpetas del Plugin

```
Wland-Chat-iA/
│
├── wland_chat_ia.php          # Archivo principal del plugin (entry point)
├── uninstall.php              # Script de desinstalación
├── CLAUDE.md                  # Documentación para desarrollo con Claude Code
├── README.md                  # Documentación general del plugin
│
├── includes/                  # Clases PHP principales
│   ├── class_settings.php     # Gestión del panel de configuración (Settings API)
│   ├── class_frontend.php     # Renderizado y carga de assets en frontend
│   ├── class_block.php        # Registro del bloque Gutenberg
│   ├── class_customizer.php   # Integración con WordPress Customizer
│   ├── class_helpers.php      # Funciones auxiliares y utilidades
│   └── class_cookie_manager.php  # Sistema de cookies con fingerprinting y GDPR
│
├── assets/                    # Recursos estáticos
│   ├── css/                   # Estilos
│   │   ├── admin.css          # Estilos del panel de administración
│   │   ├── block_editor.css   # Estilos del editor de bloques
│   │   ├── block_style.css    # Estilos base del bloque
│   │   ├── wland_chat_block_modal.css      # Estilos modo modal
│   │   ├── wland_chat_block_screen.css     # Estilos modo pantalla completa
│   │   └── wland_gdpr_banner.css           # Estilos del banner GDPR
│   │
│   ├── js/                    # Scripts JavaScript
│   │   ├── admin.js           # JavaScript del panel de administración
│   │   ├── block.js           # Registro del bloque Gutenberg
│   │   ├── wland_chat_block_modal.js       # Lógica modo modal (clase WlandChatModal)
│   │   ├── wland_chat_block_screen.js      # Lógica modo pantalla completa (clase WlandChatScreen)
│   │   └── wland_fingerprint.js            # Sistema de fingerprinting del navegador (clase WlandFingerprint)
│   │
│   └── media/                 # Recursos multimedia
│       └── chat.json          # Animación Lottie del botón de chat
│
├── templates/                 # Plantillas PHP para renderizado
│   ├── modal.php              # Template HTML para modo modal
│   └── screen.php             # Template HTML para modo pantalla completa
│
└── languages/                 # Archivos de traducción (i18n)
    ├── wland-chat.pot         # Plantilla de traducción (Portable Object Template)
    └── wland-chat-es_ES.po    # Traducción al español
```

**IMPORTANTE**: Cuando agregues nuevos archivos o componentes, actualiza esta sección inmediatamente.

## Descripción general de la arquitectura

### Estructura OOP con espacios de nombres PHP

Todas las clases PHP utilizan el espacio de nombres `WlandChat` y siguen el patrón Singleton:

**Archivo principal del complemento**: `wland_chat_ia.php`
- Punto de entrada que inicializa el complemento
- Define constantes (`WLAND_CHAT_VERSION`, `WLAND_CHAT_PLUGIN_DIR`, etc.)
- Carga todas las dependencias de clase
- Gestiona los ganchos de activación/desactivación

**Clases principales** (en `includes/`):
- **`class_settings.php`**: Implementación de la API de configuración para la configuración del panel de administración.
  - Registra todas las opciones del complemento (URL del webhook, token de autenticación N8N, títulos, mensajes, etc.).
  - Se encarga de la desinfección y la validación.
  - Representa la página de configuración del administrador en **Configuración > Wland Chat iA**.

- **`class_customizer.php`**: Integración con la API Customizer de WordPress para vista previa en vivo

- **`class_block.php`**: Registro del bloque Gutenberg
  - Registra el tipo de bloque `wland/chat-widget`
  - Genera dinámicamente block.js, block_editor.css y block_style.css si faltan
  - Renderizado del lado del servidor mediante el método `render_block()`

- **`class_frontend.php`**: Gestión de activos del frontend y renderizado
  - **Carga condicional de activos** (optimización WPO) - solo carga CSS/JS cuando el chat debe mostrarse
  - Maneja el renderizado global del chat (cuando está habilitado en todo el sitio)
  - Pasa el token de autenticación N8N a JavaScript mediante `wp_localize_script()`

- **`class_helpers.php`**: Funciones de utilidad
  - `should_display_chat()`: Verifica exclusiones y disponibilidad
  - `is_within_availability_hours()`: Programación con zona horaria
  - `get_chat_config()`: Devuelve la configuración completa incluyendo el token N8N
  - `sanitize_block_attributes()`: Desinfecta los parámetros del bloque

- **`class_cookie_manager.php`**: Sistema de cookies con fingerprinting y compliance GDPR
  - **Patrón Singleton** para garantizar única instancia
  - `get_or_create_session()`: Obtiene o crea sesión del usuario con ID único
  - `generate_server_fingerprint()`: Genera hash SHA-256 basado en User-Agent, IP, timestamp
  - `set_session_cookie()`: Crea cookie persistente de 1 año con flags de seguridad (Secure, SameSite)
  - `regenerate_session()`: Regenera ID de sesión cuando se detectan cambios significativos
  - `is_gdpr_enabled()`: Verifica si el banner GDPR está activo
  - `localize_gdpr_settings()`: Pasa configuración GDPR a JavaScript
  - Cookie name: `wland_chat_session`
  - Duración: 1 año (YEAR_IN_SECONDS)
  - Formato del ID: Hash SHA-256 de 64 caracteres hexadecimales

### Arquitectura JavaScript del Frontend

**Modo Modal** (`assets/js/wland_chat_block_modal.js`):
- La clase `WlandChatModal` maneja el ciclo de vida de la ventana de chat
- Obtiene datos del webhook N8N con autenticación mediante encabezado `X-N8N-Auth`
- Mantiene el historial de conversación en el array `conversation_history`
- Formato de payload enviado: `{prompt, sessionId, history}`
- Todos los métodos usan nomenclatura snake_case
- Documentación JSDoc completa en cada función

**Modo Pantalla Completa** (`assets/js/wland_chat_block_screen.js`):
- Arquitectura similar para el modo de visualización a pantalla completa
- Todos los métodos usan nomenclatura snake_case
- Documentación JSDoc completa en cada función

**Sistema de Fingerprinting** (`assets/js/wland_fingerprint.js`):
- Clase `WlandFingerprint` para identificación única de usuarios mediante fingerprinting del navegador
- **Características del fingerprint**:
  - User-Agent del navegador
  - Resolución de pantalla (width, height, colorDepth)
  - Zona horaria (timezone y offset)
  - Idioma y preferencias de idiomas
  - Platform y hardware (CPU cores, memoria)
  - Lista de plugins del navegador
  - Canvas fingerprint (renderizado único por navegador/GPU)
  - WebGL fingerprint (información de GPU)
  - Soporte táctil (maxTouchPoints)
- **Métodos principales**:
  - `get_or_create_session()`: Obtiene sesión existente o crea nueva con fingerprinting
  - `generate_browser_fingerprint()`: Genera objeto con todas las características del dispositivo
  - `hash_fingerprint()`: Convierte fingerprint a hash SHA-256 usando Web Crypto API
  - `should_regenerate_session()`: Detecta cambios significativos (≥2 cambios críticos)
  - `show_gdpr_banner()`: Muestra banner de consentimiento si GDPR está habilitado
  - `get_session_id()`: Método público para obtener el session_id actual
- **Fallback a localStorage**: Si cookies están bloqueadas, usa `wland_chat_session_backup`
- **Detección de cambios**: Regenera ID si detecta cambios en User-Agent, resolución, timezone o canvas
- **GDPR compliance**: Guarda consentimiento en `wland_chat_gdpr_consent` antes de crear cookies

**Bloque Gutenberg** (`assets/js/block.js`):
- Registra el bloque con el editor de bloques de WordPress
- Proporciona InspectorControls para personalización por bloque
- Muestra vista previa en el editor con actualizaciones de atributos en vivo

### Plantillas

**`templates/modal.php`**: Estructura HTML para el widget de chat modal
**`templates/screen.php`**: Estructura HTML para el widget de chat a pantalla completa

Ambas plantillas:
- Extraen variables del array `$attributes`
- Incluyen contenedor de animación Lottie
- Renderizan con valores desinfectados desde PHP

## Características Clave

### Integración con N8N Chat Embebido
- URL del webhook configurable en ajustes (módulo Chat de N8N, no Webhook)
- **Autenticación**: Token N8N almacenado en la opción `wland_chat_n8n_auth_token`
- Token pasado al JS del frontend y enviado como encabezado `X-N8N-Auth`
- **Payload enviado**: `{chatInput: string, sessionId: string}`
  - El campo `chatInput` es requerido por el módulo Chat de N8N (`{{ $json.chatInput }}`)
  - El campo `sessionId` identifica la sesión del usuario
- **Respuesta aceptada**: El plugin detecta automáticamente el mensaje en estos campos:
  - `data.output` (preferido)
  - `data.response`
  - `data.message`
  - `data.text`
  - `data.data`
  - `data` (si es string directamente)
- **Manejo de errores**: Sistema completo de detección y mensajes descriptivos

### Sistema de Cookies con Fingerprinting (v1.1.0)
Sistema robusto de identificación de usuarios mediante cookies persistentes con fingerprinting del navegador.

**Características principales**:
- **Cookie persistente**: `wland_chat_session` con duración de 1 año
- **Fingerprinting del navegador**: Combina múltiples características del dispositivo para crear ID único
  - User-Agent, resolución de pantalla, zona horaria
  - Plugins del navegador, Canvas fingerprint, WebGL
  - Hardware: CPU cores, memoria del dispositivo
- **Fallback automático**: Si cookies bloqueadas, usa localStorage (`wland_chat_session_backup`)
- **Regeneración inteligente**: Detecta cambios significativos en el dispositivo y regenera ID
- **Compliance GDPR**: Banner configurable desde admin para solicitar consentimiento
- **Session tracking**: Cada mensaje al webhook incluye `sessionId` único y persistente

**Flujo de funcionamiento**:
1. Usuario visita el sitio por primera vez
2. Si GDPR habilitado: muestra banner y espera consentimiento
3. Sistema genera fingerprint del navegador (JavaScript)
4. Servidor genera componente adicional (IP, User-Agent, timestamp)
5. Se combina todo en hash SHA-256 de 64 caracteres
6. Se almacena en cookie `wland_chat_session` (1 año) y localStorage (backup)
7. Cada mensaje al chat incluye este `sessionId` en el payload
8. N8N puede usar el `sessionId` para mantener contexto de conversación

**Detección de cambios**:
El sistema regenera el session_id cuando detecta ≥2 cambios significativos:
- Cambio de navegador o versión mayor (User-Agent)
- Cambio de monitor o resolución
- Cambio de zona horaria
- Cambio en canvas fingerprint (GPU diferente)

### Modos de Visualización
- **Modal**: Widget de chat flotante (inferior-derecha, inferior-izquierda o centro)
- **Pantalla Completa**: Interfaz de chat de página completa

### Lógica de Visualización Condicional
1. **Exclusiones de página**: Selección múltiple de páginas de WordPress en ajustes
2. **Horarios de disponibilidad**: Hora de inicio/fin con soporte de zona horaria
3. **Global vs Bloque**: Se puede habilitar en todo el sitio o usar bloque Gutenberg por página
4. **Optimización WPO**: Los activos solo se cargan cuando el chat debe mostrarse

### Estructura de Configuración
Todas las opciones con prefijo `wland_chat_`:
- `webhook_url`: Endpoint N8N
- `n8n_auth_token`: Token de autenticación para N8N
- `global_enable`: Booleano para visualización en todo el sitio
- `header_title`, `header_subtitle`, `welcome_message`: Texto de la interfaz
- `position`: 'bottom-right' | 'bottom-left' | 'center'
- `display_mode`: 'modal' | 'fullscreen'
- `excluded_pages`: Array de IDs de páginas
- `availability_enabled`: Booleano para restricciones de horario
- `availability_start`, `availability_end`: Formato 'HH:MM'
- `availability_timezone`: Identificador de zona horaria PHP
- `availability_message`: Mensaje mostrado fuera del horario
- `gdpr_enabled`: Booleano para habilitar banner GDPR (v1.1.0)
- `gdpr_message`: Texto del banner de cookies (v1.1.0)
- `gdpr_accept_text`: Texto del botón de aceptar cookies (v1.1.0)

## Flujo de Trabajo de Desarrollo

### Pruebas de Cambios
1. Los cambios en archivos PHP requieren limpiar caché de WordPress o actualizar página
2. Los cambios en JavaScript pueden requerir limpiar caché del navegador (Cmd+Shift+R)
3. Probar panel de administración en: `/wp-admin/options-general.php?page=wland_chat_settings`
4. Probar editor de bloques: Crear/editar cualquier entrada/página, añadir bloque "Wland Chat iA"

### Flujo de Trabajo Git
- Rama principal: `main`
- Rama actual: `claude-edits`
- El repositorio incluye historial git

### Generación de Archivos
El plugin auto-genera archivos de activos faltantes en la inicialización:
- `assets/js/block.js`
- `assets/css/block_editor.css`
- `assets/css/block_style.css`

No crear estos manualmente - dejar que los métodos de `class_block.php` lo manejen.

## Detalles Importantes de Implementación

### Seguridad
- Todas las entradas desinfectadas (callbacks de Settings API)
- Salidas escapadas (esc_html, esc_attr, esc_url)
- Verificación de nonce para AJAX
- Patrón Singleton previene instancias múltiples

### Hooks y Filtros
El plugin proporciona filtros (revisar README.md para lista completa):
- `wland_chat_config`: Modificar configuración
- `wland_chat_welcome_message`: Personalizar texto de bienvenida
- `wland_chat_block_attributes`: Alterar valores predeterminados del bloque

### Internacionalización
- Dominio de texto: `wland-chat` (convención de WordPress mantiene guiones en dominios de texto)
- Archivo POT: `languages/wland_chat.pot`
- Todas las cadenas usan funciones `__()`, `_e()`, `_x()`

## Modificaciones Comunes

### Añadir Nuevo Campo de Configuración
1. Registrar configuración en `class_settings.php::register_settings()`
2. Añadir método callback para renderizado del campo
3. Añadir callback de desinfección si es necesario
4. Actualizar `class_helpers.php::get_chat_config()` si se pasa al frontend

### Modificar Petición N8N
Editar `assets/js/wland_chat_block_modal.js::send_message()` y `wland_chat_block_screen.js::send_message()`:
- **Cambiar encabezados**: Modificar objeto `headers` (línea ~255)
- **Modificar payload**: Cambiar estructura enviada (línea ~267-270)
  - Campo `chatInput` es requerido para módulo Chat de N8N
  - Campo `sessionId` mantiene contexto de conversación
- **Ajustar detección de respuesta**: Modificar lógica de extracción del mensaje (línea ~340-355)
  - Agregar nuevos campos a verificar en orden de prioridad
- **Personalizar mensajes de error**: Editar bloques catch (línea ~357-422)

### Cambiar Lógica de Visualización
Modificar `class_helpers.php::should_display_chat()`:
- Añadir condiciones
- Verificar opciones adicionales
- Actualizar reglas de exclusión

## Manejo de Errores y Debugging

### Sistema de Mensajes de Error Descriptivos
El plugin incluye un sistema completo de detección y reporte de errores en ambos modos (modal y pantalla completa).

**Tipos de errores detectados:**
- **Failed to fetch**: Sin conexión a internet, servidor caído, problema de CORS, URL incorrecta
- **WEBHOOK_NOT_CONFIGURED**: URL del webhook vacía en configuración
- **401 UNAUTHORIZED**: Token de autenticación inválido o expirado
- **403 FORBIDDEN**: Acceso denegado, verificar token de autenticación
- **404 NOT FOUND**: URL del webhook no existe
- **JSON_PARSE_ERROR**: Respuesta del servidor no es JSON válido
- **RESPONSE_FORMAT_ERROR**: JSON válido pero sin campo de mensaje esperado
- **500 INTERNAL SERVER ERROR**: Error interno en el servidor N8N
- **502 BAD GATEWAY**: El servidor N8N no responde
- **503 SERVICE UNAVAILABLE**: Servidor N8N temporalmente no disponible

**Información de logging en consola del navegador:**
- URL del webhook utilizada
- Headers enviados (incluyendo autenticación)
- Payload completo con estructura exacta
- Status HTTP de respuesta
- Headers de respuesta del servidor
- Body raw (texto sin procesar antes de parsear)
- JSON parseado y validado
- Stack trace completo de excepciones
- Session ID único de la conversación
- Información de debug (número de mensajes en historial, configuración)

**Mensajes al usuario:**
Cada error muestra en el chat:
- Descripción clara del problema
- Posibles causas
- Pasos sugeridos para solución
- Detalles técnicos en bloque de código
- Timestamp del error

### Debugging en Navegador
1. Abrir Consola de Desarrollador (F12 o Cmd+Option+I en Mac)
2. Enviar mensaje en el chat
3. Revisar logs detallados con prefijos claros para fácil identificación
4. Verificar petición en pestaña Network para ver headers y payload
5. Los errores muestran información completa para diagnóstico rápido

### Debugging en Producción
- Todos los errores se registran en `console.error()` para herramientas de monitoreo
- Los mensajes técnicos incluyen URLs, tokens (parcialmente ocultos), y respuestas del servidor
- El Session ID permite rastrear conversaciones específicas

## Dependencias

### PHP
- WordPress 5.8+
- PHP 7.4+
- WordPress Settings API, Customizer API, Block API

### JavaScript
- Paquetes del editor de bloques de WordPress (wp-blocks, wp-element, wp-components)
- Lottie Web 5.12.2 (vía CDN)
- API Fetch nativa para peticiones webhook

### Activos
- Animación Lottie: `assets/media/chat.json`
- Archivos CSS se cargan condicionalmente según el modo de visualización

## Estilo de Código

**IMPORTANTE**: Esta base de código sigue convenciones de nomenclatura estrictas:

### Convención de Nomenclatura
- **snake_case para todo**: Todas las funciones y variables DEBEN usar snake_case
- **NO camelCase**: Nunca usar camelCase para funciones o variables
- Ejemplos:
  - ✅ `generate_session_id()`, `webhook_url`, `auth_token`
  - ❌ `generateSessionId()`, `webhookUrl`, `authToken`

### Documentación
- **JSDoc requerido**: Cada función JavaScript debe tener bloque de comentario JSDoc encima
- **PHP DocBlocks**: Usar formato DocBlock estándar de WordPress para funciones PHP

### Convención de Nomenclatura de Archivos
- **Todos los archivos usan snake_case**: `wland_chat_ia.php`, `class_settings.php`, `wland_chat_block_modal.js`
- **NO guiones en nombres de archivos**: Cambiado de `class-settings.php` a `class_settings.php`
- **NO camelCase en nombres de archivos**: Cambiado de `wlandChatModal.js` a `wland_chat_modal.js`

### Estándares
- PHP: Estándares de Codificación de WordPress + nomenclatura snake_case
- JavaScript: ES6+ con sintaxis de clases + nomenclatura snake_case + JSDoc
- CSS: Nomenclatura tipo BEM para componentes de chat
- Todas las clases PHP bajo el namespace `WlandChat`
- Todos los nombres de archivos usan snake_case (guiones bajos, no guiones)

---

## Protocolo de Actualización de CLAUDE.md

**REGLA CRÍTICA**: Este archivo CLAUDE.md debe mantenerse sincronizado con el código en todo momento.

### Cuándo actualizar CLAUDE.md

Debes actualizar este archivo INMEDIATAMENTE cuando:

1. **Crees un nuevo archivo** (PHP, JS, CSS)
   - Agregar a la sección "Estructura de Carpetas del Plugin"
   - Documentar su propósito y responsabilidades
   - Especificar qué clases/funciones principales contiene

2. **Agregues una nueva clase PHP**
   - Agregar en la sección "Descripción general de la arquitectura"
   - Documentar métodos públicos principales
   - Especificar patrones de diseño utilizados (Singleton, etc.)

3. **Implementes una nueva función JavaScript**
   - Agregar en la sección "Arquitectura JavaScript del Frontend"
   - Documentar parámetros y valores de retorno
   - Especificar en qué archivo se encuentra

4. **Cambies la estructura de datos**
   - Actualizar sección "Estructura de Configuración"
   - Documentar nuevos campos en payload o respuestas
   - Especificar formato esperado

5. **Agregues una nueva característica**
   - Crear nueva subsección en "Características Clave"
   - Documentar cómo funciona
   - Especificar archivos involucrados

6. **Modifiques el manejo de errores**
   - Actualizar "Manejo de Errores y Debugging"
   - Agregar nuevos tipos de error detectados
   - Documentar nuevos mensajes de error

7. **Agregues nuevas dependencias**
   - Actualizar sección "Dependencias"
   - Especificar versión y propósito

8. **Cambies convenciones de código**
   - Actualizar sección "Estilo de Código"
   - Documentar la razón del cambio

### Estructura de nuevas secciones

Cuando documentes una nueva función o componente, usa este formato:

```markdown
### Nombre del Componente

**Archivo**: `ruta/al/archivo.ext`

**Propósito**: Breve descripción de qué hace y por qué existe

**Métodos/Funciones principales**:
- `nombre_funcion(parametros)`: Descripción breve
- `otra_funcion(parametros)`: Descripción breve

**Dependencias**:
- Lista de archivos o clases que requiere

**Usado por**:
- Lista de archivos o clases que lo utilizan

**Ejemplo de uso**:
```php o javascript
// Código de ejemplo
```

**Notas importantes**:
- Cualquier consideración especial
- Limitaciones conocidas
- Patrones de diseño aplicados
```

### Checklist antes de completar una tarea

Antes de marcar una tarea como completada, verifica:

- [ ] El código está implementado y probado
- [ ] CLAUDE.md ha sido actualizado
- [ ] La estructura de carpetas refleja los cambios
- [ ] Los comentarios JSDoc/DocBlocks están presentes
- [ ] Se siguen las convenciones de nomenclatura (snake_case)
- [ ] Se han agregado ejemplos de uso si es relevante

### Historial de Cambios Mayores

**IMPORTANTE**: Mantener registro de cambios significativos

#### 2025-01-09: Corrección de integración con N8N Chat
- Cambiado payload de `{prompt, sessionId, history}` a `{chatInput, sessionId}`
- Implementado sistema completo de detección de errores con mensajes descriptivos
- Agregado logging detallado en consola para debugging
- Mejorada detección de respuestas para soportar múltiples formatos

#### 2025-01-09: Corrección de panel de configuración
- Corregida inconsistencia de nombres entre secciones y campos en `class_settings.php`
- Cambiado de `'wland-chat-settings'` a `'wland_chat_settings'` en `add_settings_section()`

#### 2025-01-09: Traducción de CLAUDE.md
- Traducido completamente al español
- Eliminados todos los emojis para estilo profesional

#### 2025-10-14: Implementación completa de i18n (CAR-17)
- Añadido soporte completo para wp.i18n en JavaScript (modal.js y screen.js)
- Implementadas 21 nuevas cadenas traducibles en mensajes de error
- Configurado wp_set_script_translations() en class_frontend.php
- Actualizado wland-chat.pot con 83 cadenas totales
- Actualizado wland-chat-es_ES.po con todas las traducciones al español
- Generado wland-chat-es_ES.mo compilado (7.5KB, 84 mensajes)
- Todos los mensajes de error ahora son traducibles sin emojis
- Compatible con WPML, Polylang y Loco Translate

#### 2025-10-16: Sistema de Cookies con Fingerprinting (WPC-002)
- **Implementado sistema robusto de identificación de usuarios**
- Creada clase `WlandCookieManager` (includes/class_cookie_manager.php):
  - Cookie persistente `wland_chat_session` con 1 año de duración
  - Generación de ID único usando fingerprinting servidor (IP, User-Agent, timestamp)
  - Flags de seguridad: Secure (HTTPS), SameSite=Lax
  - Métodos: get_or_create_session(), regenerate_session(), delete_session()
  - NOTA: Cookies NO se establecen desde PHP para evitar "headers already sent"
- Creada clase `WlandFingerprint` (assets/js/wland_fingerprint.js):
  - Fingerprinting completo del navegador (User-Agent, resolución, timezone, plugins, canvas, WebGL)
  - Hash SHA-256 usando Web Crypto API con fallback a hash simple
  - Fallback automático a localStorage si cookies bloqueadas
  - Detección inteligente de cambios significativos (≥2 cambios críticos)
  - Regeneración automática de session_id cuando se detectan cambios
  - Métodos async/await para correcta generación de hash
- **Sistema de Compliance GDPR**:
  - Banner configurable desde panel admin (Ajustes > Wland Chat iA)
  - Opciones: gdpr_enabled, gdpr_message, gdpr_accept_text
  - CSS responsive con animaciones (assets/css/wland_gdpr_banner.css)
  - Consentimiento guardado en localStorage antes de crear cookies
  - Banner se muestra automáticamente si GDPR habilitado y sin consentimiento previo
- **Integración con N8N**:
  - Campo `sessionId` incluido en cada payload al webhook
  - Modificados wland_chat_block_modal.js y wland_chat_block_screen.js
  - Función generate_session_id() async que espera resolución del fingerprint
  - Session ID se inicializa de forma asíncrona en constructor
- **Carga optimizada de assets**:
  - Script wland_fingerprint.js cargado en head (sin dependencias)
  - CSS del banner GDPR cargado condicionalmente
  - Configuración GDPR pasada a JavaScript via wp_localize_script() en class_frontend.php
- **Correcciones realizadas**:
  - Corregido error 500: Helpers usa métodos estáticos, no Singleton
  - Corregida localización de configuración GDPR (movida a class_frontend.php)
  - Implementado flujo async/await correcto en toda la cadena de fingerprinting
  - Versión actualizada a 1.1.1 para forzar recarga de scripts
- **Documentación completa**:
  - Creado TESTING_COOKIES.md con guía exhaustiva de pruebas (10 secciones)
  - Actualizado CLAUDE.md con arquitectura completa del sistema
  - Documentación de troubleshooting y resolución de problemas
- **Versión actualizada**: 1.0.2 → 1.1.1
- **Estado**: ✅ PROBADO Y FUNCIONANDO - Todos los acceptance criteria cumplidos

---

**Última actualización**: 2025-10-16
**Versión del plugin**: 1.1.1
**Mantenedor**: Claude Code con supervisión de Carlos Vera (BravesLab)
