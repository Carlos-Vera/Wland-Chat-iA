# Guía de Pruebas - Sistema de Cookies y Fingerprinting

## WPC-002: Sistema de Cookies con Fingerprinting

### Pruebas Implementadas

Este documento describe las pruebas necesarias para verificar el correcto funcionamiento del sistema de cookies con fingerprinting.

---

## 1. Preparación del Entorno

### 1.1. Activar el Plugin
1. Acceder al panel de administración de WordPress
2. Ir a **Plugins > Plugins Instalados**
3. Activar **Wland Chat iA** si no está activo
4. Verificar que la versión sea **1.1.0**

### 1.2. Configurar GDPR (Opcional)
1. Ir a **Ajustes > Wland Chat iA**
2. Desplazarse hasta la sección **Compliance GDPR / Cookies**
3. Opciones disponibles:
   - **Habilitar Banner GDPR**: Activar para mostrar banner de consentimiento
   - **Mensaje del Banner**: Personalizar el texto del aviso
   - **Texto del Botón de Aceptar**: Personalizar el botón (ej: "Aceptar", "Entendido")

---

## 2. Pruebas Funcionales

### 2.1. Cookie Creada Automáticamente en Primera Visita ✓

**Objetivo**: Verificar que la cookie se crea automáticamente al visitar el sitio.

**Pasos**:
1. Abrir navegador en modo incógnito
2. Visitar cualquier página del sitio donde esté activo el chat
3. Abrir DevTools (F12) > Pestaña **Application** > **Cookies**
4. Buscar la cookie `wland_chat_session`

**Resultado Esperado**:
- Cookie `wland_chat_session` existe
- Tiene un valor hexadecimal de 64 caracteres (hash SHA-256)
- Fecha de expiración: 1 año desde la creación
- Path: `/`
- SameSite: `Lax`
- Secure: `true` (solo si el sitio usa HTTPS)

---

### 2.2. Cookie Persiste tras Cerrar Navegador ✓

**Objetivo**: Verificar que la cookie no se elimina al cerrar el navegador.

**Pasos**:
1. Con la cookie ya creada, copiar el valor de `wland_chat_session`
2. Cerrar completamente el navegador
3. Abrir nuevamente el navegador
4. Visitar el sitio
5. Verificar el valor de la cookie en DevTools

**Resultado Esperado**:
- La cookie `wland_chat_session` mantiene el mismo valor
- No se generó un nuevo session_id

---

### 2.3. ID Único Incluye Fingerprint del Dispositivo ✓

**Objetivo**: Verificar que el fingerprint del navegador se genera correctamente.

**Pasos**:
1. Abrir DevTools > Pestaña **Console**
2. Recargar la página
3. Buscar en consola: `[Wland Fingerprint] Nueva sesión creada:`
4. Verificar que aparece el session_id

**Resultado Esperado**:
- Se muestra el mensaje de sesión creada con hash de 64 caracteres
- El hash es único para cada dispositivo/navegador
- En consola también aparece información del fingerprint:
  - User-Agent
  - Resolución de pantalla
  - Zona horaria
  - Plugins del navegador
  - Canvas fingerprint
  - WebGL fingerprint

---

### 2.4. Session_id se Envía en Payload al Webhook ✓

**Objetivo**: Verificar que el session_id se incluye en cada petición al webhook N8N.

**Pasos**:
1. Abrir el chat (botón flotante o bloque)
2. Abrir DevTools > Pestaña **Network**
3. Enviar un mensaje en el chat
4. Buscar la petición POST al webhook
5. Ver el **Payload** enviado

**Resultado Esperado**:
- El payload contiene:
  ```json
  {
    "chatInput": "mensaje del usuario",
    "sessionId": "hash_de_64_caracteres"
  }
  ```
- El `sessionId` coincide con el valor de la cookie `wland_chat_session`

---

### 2.5. Funciona con Cookies Bloqueadas (localStorage) ✓

**Objetivo**: Verificar el fallback a localStorage cuando las cookies están bloqueadas.

**Pasos**:
1. En Chrome: Configuración > Privacidad y seguridad > Cookies de terceros > **Bloquear cookies de terceros**
2. O bien: DevTools > Application > Cookies > **Block cookies**
3. Borrar todas las cookies del sitio
4. Recargar la página
5. Abrir DevTools > Application > **Local Storage**

**Resultado Esperado**:
- No existe cookie `wland_chat_session` (bloqueada)
- Existe entrada en localStorage: `wland_chat_session_backup` con el session_id
- El chat funciona normalmente
- Los mensajes se envían con el session_id desde localStorage
- En consola aparece warning: `[Wland Fingerprint] Error guardando en cookie...` (esperado)

---

### 2.6. Banner GDPR Configurable desde Admin ✓

**Objetivo**: Verificar que el banner GDPR funciona correctamente.

**Pasos**:
1. Ir a **Ajustes > Wland Chat iA**
2. Activar **Habilitar Banner GDPR**
3. Personalizar mensaje y texto del botón
4. Guardar cambios
5. Abrir sitio en modo incógnito
6. Verificar que aparece el banner

**Resultado Esperado**:
- Banner aparece en la parte inferior de la pantalla con animación
- Muestra el mensaje personalizado
- Botón muestra el texto personalizado
- Al hacer clic en "Aceptar":
  - Banner desaparece
  - Se guarda consentimiento en localStorage: `wland_chat_gdpr_consent = 'accepted'`
  - Se crea la cookie `wland_chat_session`
  - El chat funciona normalmente

---

### 2.7. Regenerar ID si Cambios Significativos Detectados ✓

**Objetivo**: Verificar que el sistema detecta cambios significativos y regenera el session_id.

**Pasos**:
1. Abrir DevTools > Console
2. Obtener el session_id actual: `window.wlandFingerprint.get_session_id()`
3. Ejecutar en consola:
   ```javascript
   // Simular cambio significativo modificando el fingerprint almacenado
   let stored = JSON.parse(localStorage.getItem('wland_chat_fingerprint'));
   stored.user_agent = 'Mozilla/5.0 (Different Browser)';
   stored.screen_resolution = '1920x1080x24';
   localStorage.setItem('wland_chat_fingerprint', JSON.stringify(stored));
   ```
4. Recargar la página
5. Verificar el nuevo session_id

**Resultado Esperado**:
- En consola aparece: `[Wland Fingerprint] Cambios significativos detectados, regenerando sesión`
- Se genera un nuevo session_id diferente
- La cookie se actualiza con el nuevo valor

---

## 3. Pruebas de Integración

### 3.1. Verificar session_id en Conversación N8N

**Objetivo**: Confirmar que N8N recibe correctamente el session_id.

**Pasos**:
1. En N8N, agregar un nodo **Set** o **Code** después del nodo Chat
2. Configurar para mostrar: `{{ $json.sessionId }}`
3. Enviar mensajes desde el chat
4. Verificar en N8N que llega el session_id

**Resultado Esperado**:
- N8N recibe el campo `sessionId` en cada mensaje
- El valor es consistente durante toda la conversación
- El mismo usuario siempre tiene el mismo sessionId (hasta que cambie de dispositivo/navegador)

---

### 3.2. Persistencia entre Páginas

**Objetivo**: Verificar que el session_id se mantiene al navegar por el sitio.

**Pasos**:
1. Iniciar conversación en la página de inicio
2. Enviar un mensaje
3. Navegar a otra página del sitio
4. Abrir el chat nuevamente
5. Enviar otro mensaje

**Resultado Esperado**:
- El session_id es el mismo en ambas páginas
- El historial de conversación persiste (dependiente de implementación)

---

## 4. Pruebas de Compatibilidad

### 4.1. Navegadores Soportados

Probar en:
- ✓ Chrome/Chromium (v90+)
- ✓ Firefox (v88+)
- ✓ Safari (v14+)
- ✓ Edge (v90+)
- ✓ Opera (v76+)

**Resultado Esperado**: Sistema funciona en todos los navegadores

---

### 4.2. Dispositivos

Probar en:
- ✓ Desktop (Windows, macOS, Linux)
- ✓ Tablet (iPad, Android)
- ✓ Móvil (iOS, Android)

**Resultado Esperado**: Fingerprint único por dispositivo

---

## 5. Pruebas de Seguridad

### 5.1. Cookie Flags

**Verificar**:
- `HttpOnly`: ❌ No (necesario para acceso desde JavaScript)
- `Secure`: ✓ Sí (solo en HTTPS)
- `SameSite`: ✓ Lax (protección contra CSRF)
- Duración: ✓ 1 año (YEAR_IN_SECONDS)

---

### 5.2. Validación de Formato

**Objetivo**: Verificar que se rechaza session_id con formato inválido.

**Pasos**:
1. Abrir DevTools > Application > Cookies
2. Editar manualmente el valor de `wland_chat_session` a: `invalid_format_123`
3. Recargar página

**Resultado Esperado**:
- El sistema detecta formato inválido
- Se genera un nuevo session_id válido
- Cookie se actualiza con valor correcto

---

## 6. Pruebas de Rendimiento

### 6.1. Tiempo de Generación

**Objetivo**: Verificar que la generación de fingerprint no afecta performance.

**Pasos**:
1. Abrir DevTools > Performance
2. Iniciar grabación
3. Recargar página
4. Detener grabación
5. Analizar tiempo de ejecución de `wland_fingerprint.js`

**Resultado Esperado**:
- Tiempo de ejecución < 100ms
- No bloquea el rendering de la página
- Script carga de forma asíncrona

---

## 7. Debugging y Logs

### 7.1. Mensajes de Consola

El sistema genera logs descriptivos en consola del navegador:

```
✓ [Wland Fingerprint] Nueva sesión creada: abc123...
✓ [Wland Chat Modal] Usando session_id con fingerprinting: abc123...
✓ [Wland Fingerprint] Cambios significativos detectados, regenerando sesión
⚠ [Wland Fingerprint] Error accediendo a localStorage: ...
⚠ [Wland Chat Modal] Sistema de fingerprinting no disponible, usando session_id temporal
```

---

## 8. Resolución de Problemas

### Problema: Cookie no se crea

**Soluciones**:
1. Verificar que el plugin está activo (versión 1.1.0)
2. Verificar que el chat está configurado para mostrarse en la página
3. Verificar configuración de privacidad del navegador
4. Comprobar que JavaScript está habilitado

### Problema: Session_id cambia constantemente

**Soluciones**:
1. Verificar que las cookies no están bloqueadas
2. Verificar que localStorage está disponible
3. Comprobar que el fingerprint no tiene errores en consola

### Problema: Banner GDPR no aparece

**Soluciones**:
1. Verificar que está habilitado en **Ajustes > Wland Chat iA**
2. Borrar localStorage: `wland_chat_gdpr_consent`
3. Verificar que el CSS del banner se carga: `wland_gdpr_banner.css`

---

## 9. Checklist Final

Antes de dar por completada la implementación, verificar:

- [ ] Cookie se crea automáticamente en primera visita
- [ ] Cookie persiste tras cerrar navegador (1 año)
- [ ] ID único incluye fingerprint del dispositivo
- [ ] Session_id se envía en payload al webhook
- [ ] Fallback a localStorage funciona cuando cookies bloqueadas
- [ ] Banner GDPR configurable desde admin
- [ ] Regeneración de ID por cambios significativos funciona
- [ ] Sistema probado en múltiples navegadores
- [ ] Logs en consola son descriptivos
- [ ] Documentación actualizada (CLAUDE.md)

---

## 10. Documentación de Componentes

### Archivos Creados/Modificados

1. **PHP**:
   - `includes/class_cookie_manager.php` (NUEVO)
   - `includes/class_frontend.php` (MODIFICADO)
   - `includes/class_settings.php` (MODIFICADO)
   - `wland_chat_ia.php` (MODIFICADO - versión 1.1.0)

2. **JavaScript**:
   - `assets/js/wland_fingerprint.js` (NUEVO)
   - `assets/js/wland_chat_block_modal.js` (MODIFICADO)
   - `assets/js/wland_chat_block_screen.js` (MODIFICADO)

3. **CSS**:
   - `assets/css/wland_gdpr_banner.css` (NUEVO)

4. **Documentación**:
   - `TESTING_COOKIES.md` (NUEVO)
   - `CLAUDE.md` (PENDIENTE ACTUALIZAR)

---

## Conclusión

Este sistema de cookies con fingerprinting proporciona:
- Identificación única y persistente de usuarios
- Compliance con regulaciones GDPR
- Fallback robusto cuando cookies no están disponibles
- Detección inteligente de cambios de dispositivo
- Integración transparente con el sistema de chat existente

**Estado**: ✓ IMPLEMENTADO - Pendiente pruebas en producción
