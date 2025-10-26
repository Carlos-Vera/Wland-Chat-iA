# Changelog

Registro de cambios del plugin **Wland Chat iA**.

Todas las fechas en formato YYYY-MM-DD. Este proyecto sigue [Semantic Versioning](https://semver.org/).

## 📋 Índice de Versiones

- [1.2.3](#123---2025-10-26) - **Actual** - Fix wp.i18n y mejora de compatibilidad JS
- [1.2.2](#122---2025-10-25) - Corrección crítica de inputs en formularios
- [1.2.1](#121---2025-10-24) - Rediseño completo del admin con Bentō
- [1.2.0](#120---2025-10-23) - Nuevo sistema de administración
- [1.1.2](#112---2025-10-23) - Cambio de marca a BravesLab
- [1.1.1](#111---2025-10-16) - Sistema de cookies y fingerprinting
- [1.1.0](#110---2025-10-01) - Horarios y páginas excluidas
- [1.0.0](#100---2025-09-15) - Lanzamiento inicial

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

- **Repositorio**: [GitHub - Wland Chat iA](https://github.com/Carlos-Vera/Wland-Chat-iA)
- **Documentación**: Ver [README.md](README.md) para guía de usuario
- **Documentación Técnica**: Ver [CLAUDE.md](CLAUDE.md) para desarrollo
- **Soporte**: carlos@braveslab.com
- **Web**: [https://braveslab.com](https://braveslab.com)

---

**Wland Chat iA** - Integrando la inteligencia artificial en WordPress de forma profesional.

© 2025 Braves Lab LLC. Todos los derechos reservados.
