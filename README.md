# Wland Chat iA

Plugin WordPress profesional para integrar chat con inteligencia artificial mediante bloque Gutenberg, desarrollado por Carlos Vera (BravesLab) para Weblandia.es

## 🚀 Características

### Funcionalidades Principales
- **Bloque Gutenberg** personalizable para cualquier página o entrada
- **Integración con N8N** mediante webhooks configurables
- **Horarios de disponibilidad** con zonas horarias
- **Páginas excluidas** mediante selector múltiple
- **Dos modos de visualización**: Modal y Pantalla completa
- **Animación Lottie** en el botón de chat
- **Responsive** y optimizado para móviles
- **Accesibilidad** siguiendo estándares WCAG

### Panel de Administración
- **Settings API** de WordPress con validación completa
- **Customizer API** para personalización en tiempo real
- **Sanitización y validación** de todos los inputs
- **Seguridad con nonces** en formularios
- **Interfaz intuitiva** con vista previa

### Configuración Avanzada
- URL del webhook personalizable
- Textos completamente editables
- Selector de páginas excluidas
- Horarios de inicio y fin
- Zonas horarias internacionales
- Mensajes personalizados fuera de horario

## 📋 Requisitos

- WordPress 5.8 o superior
- PHP 7.4 o superior
- Gutenberg (incluido en WordPress 5.0+)

## 💾 Instalación

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
git clone https://github.com/Carlos-Vera/Wland-Chat-Block-iA.git wland-chat-ia
```

## ⚙️ Configuración

### Configuración Básica

1. Ve a **Ajustes > Wland Chat iA**
2. Configura los ajustes principales:
   - **URL del Webhook**: Introduce la URL de tu webhook de N8N
   - **Título del Header**: Personaliza el título del chat
   - **Subtítulo del Header**: Añade un subtítulo descriptivo
   - **Mensaje de Bienvenida**: Mensaje inicial del asistente
   - **Posición**: Selecciona dónde aparecerá el chat
   - **Modo de Visualización**: Modal o Pantalla completa

### Páginas Excluidas

1. En la sección **Páginas Excluidas**
2. Selecciona las páginas donde NO quieres mostrar el chat
3. Mantén presionado Ctrl (Cmd en Mac) para múltiples selecciones

### Horarios de Disponibilidad

1. En la sección **Horarios de Disponibilidad**
2. Marca **Habilitar Horarios**
3. Configura:
   - **Hora de Inicio**: Formato HH:MM (ejemplo: 09:00)
   - **Hora de Fin**: Formato HH:MM (ejemplo: 18:00)
   - **Zona Horaria**: Selecciona tu zona horaria
   - **Mensaje Fuera de Horario**: Mensaje cuando no estés disponible

### Personalización con Customizer

1. Ve a **Apariencia > Personalizar**
2. Busca el panel **Wland Chat iA**
3. Realiza cambios y visualiza en tiempo real
4. Haz clic en **Publicar** para guardar

## 🎨 Uso del Bloque

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

```
wland-chat-ia/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   ├── block-editor.css
│   │   ├── block-style.css
│   │   ├── wland-chat-block-modal.css
│   │   └── wland-chat-block-screen.css
│   ├── js/
│   │   ├── admin.js
│   │   ├── block.js
│   │   ├── wland-chat-block-modal.js
│   │   └── wland-chat-block-screen.js
│   └── media/
│       └── chat.json (Lottie animation)
├── includes/
│   ├── class-settings.php
│   ├── class-customizer.php
│   ├── class-block.php
│   ├── class-frontend.php
│   └── class-helpers.php
├── languages/
│   └── wland-chat.pot
├── templates/
│   ├── modal.php
│   └── screen.php
├── LICENSE
├── README.md
├── uninstall.php
└── wland-chat-ia.php
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

// Modificar atributos del bloque
add_filter('wland_chat_block_attributes', function($attributes) {
    $attributes['position'] = 'center';
    return $attributes;
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

## 🌐 Internacionalización (i18n)

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

- ✅ Sanitización de todos los inputs
- ✅ Validación de datos en servidor
- ✅ Uso de nonces en formularios
- ✅ Escapado de salidas (esc_html, esc_attr, etc.)
- ✅ Verificación de capacidades de usuario
- ✅ Protección contra CSRF
- ✅ Prevención de inyección SQL
- ✅ Validación de URLs

### Limpieza al Desinstalar

El plugin incluye `uninstall.php` que:
- Elimina todas las opciones de la base de datos
- Borra archivos y directorios creados
- Limpia metadatos de posts y usuarios
- Limpia caché de WordPress

## 📝 Changelog

### 1.0.0 (2025-01-XX)
- ✨ Versión inicial
- ✨ Bloque Gutenberg con personalización completa
- ✨ Settings API con página de ajustes
- ✨ Integración con Customizer API
- ✨ Horarios de disponibilidad
- ✨ Páginas excluidas
- ✨ Dos modos de visualización
- ✨ Animación Lottie
- ✨ Estructura OOP con namespaces
- ✨ Preparado para i18n
- ✨ Seguridad completa con nonces
- ✨ Limpieza automática al desinstalar

## 👥 Autores

- **Carlos Vera** - [GitHub](https://github.com/Carlos-Vera) - carlos@braveslab.com
- **Mikel Marqués (Ymikimonokia)** - hola@mikimonokia.com

Desarrollado para **Weblandia.es** - [https://weblandia.es](https://weblandia.es)

## 📄 Licencia

Este plugin es software comercial. Todos los derechos reservados.

Copyright (c) 2025 Carlos Vera & Mikel Marqués

Para más información sobre la licencia, consulta el archivo [LICENSE](LICENSE).

## 🤝 Soporte

Para soporte, consultas o reportar problemas:

- **Email**: carlos@braveslab.com, hola@mikimonokia.com
- **Web**: [https://weblandia.es](https://weblandia.es)
- **GitHub Issues**: [Reportar un problema](https://github.com/Carlos-Vera/Wland-Chat-Block-iA/issues)

## 🙏 Agradecimientos

- BravesLab por el desarrollo y diseño
- Weblandia.es por la confianza en el proyecto
- La comunidad de WordPress por los estándares y mejores prácticas

---

**Wland Chat iA** - Integrando inteligencia artificial en WordPress de forma profesional.