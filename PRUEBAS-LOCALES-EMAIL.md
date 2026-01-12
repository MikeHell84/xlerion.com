# 🧪 Guía para Probar el Formulario de Contacto Localmente

Esta guía te permitirá probar el formulario de contacto en tu máquina local usando **nodemailer** antes de subir los cambios al servidor.

## 📋 Requisitos Previos

- Node.js instalado (v16 o superior)
- Una cuenta de Gmail (o cualquier servicio SMTP)
- Puerto 3001 disponible para el servidor de emails

## 🚀 Pasos para la Prueba Local

### 1. Instalar Dependencias del Servidor

```powershell
cd server
npm install
```

Esto instalará:

- `express` - Framework del servidor
- `nodemailer` - Envío de emails
- `cors` - Permitir peticiones del frontend
- `body-parser` - Parsear JSON
- `dotenv` - Variables de entorno

### 2. Configurar Credenciales de Email

#### A) Obtener Contraseña de Aplicación de Gmail

1. Ve a tu cuenta de Google: <https://myaccount.google.com/>
2. Selecciona **Seguridad**
3. Habilita **Verificación en 2 pasos** (si no está activada)
4. Ve a **Contraseñas de aplicaciones**: <https://myaccount.google.com/apppasswords>
5. Selecciona **Correo** y **Windows Computer**
6. Copia la contraseña de 16 caracteres generada

#### B) Crear archivo .env

```powershell
# En la carpeta server/
cp .env.example .env
```

Edita el archivo `.env` con tus credenciales:

```env
EMAIL_USER=tu-email@gmail.com
EMAIL_PASSWORD=abcd efgh ijkl mnop
EMAIL_TO=contactus@xlerion.com
PORT=3001
```

**⚠️ IMPORTANTE:**

- Usa la contraseña de aplicación, NO tu contraseña normal de Gmail
- NO compartas este archivo (ya está en .gitignore)
- Para testing, puedes usar tu propio email como `EMAIL_TO`

### 3. Iniciar el Servidor de Emails

```powershell
# En la carpeta server/
npm start
```

Deberías ver:

```
✅ Servidor de email listo para enviar mensajes

🚀 Servidor de emails XLERION iniciado
📧 Escuchando en http://localhost:3001
📬 Emails se enviarán a: contactus@xlerion.com

💡 Para probar el formulario:
   1. Ejecuta el frontend: npm run dev
   2. Abre: http://localhost:5173
   3. Ve a la sección de contacto y envía un mensaje
```

### 4. Iniciar el Frontend

**En otra terminal:**

```powershell
# En la carpeta raíz del proyecto
npm run dev
```

El sitio se abrirá en: <http://localhost:5173>

### 5. Probar el Formulario

1. Abre <http://localhost:5173>
2. Ve a la sección **Contacto** (al final de la página)
3. Llena el formulario:
   - **Nombre:** Tu Nombre
   - **Email:** <tu-email@example.com>
   - **Mensaje:** Este es un mensaje de prueba
4. Haz clic en **Enviar**

### 6. Verificar Resultados

#### En la Terminal del Servidor

Deberías ver:

```
📨 Nueva solicitud de envío de email:
   Nombre: Tu Nombre
   Email: tu-email@example.com
   Mensaje: Este es un mensaje de prueba...
✅ Email enviado correctamente
```

#### En la Terminal del Frontend

Deberías ver en la consola del navegador (F12):

```
Enviando a: http://localhost:3001/api/send-email
```

#### En tu Bandeja de Entrada

Revisa el email configurado en `EMAIL_TO` (<contactus@xlerion.com> o el que hayas puesto). Deberías recibir un email con:

- Asunto: "Nuevo mensaje de contacto de Tu Nombre"
- Diseño HTML con colores de marca XLERION
- El mensaje que enviaste

## 🔧 Solución de Problemas

### Error: "Cannot find module 'nodemailer'"

```powershell
cd server
npm install
```

### Error: "Invalid login"

- Verifica que estés usando la **contraseña de aplicación** de Gmail, no tu contraseña normal
- Asegúrate de haber habilitado la verificación en 2 pasos
- Revisa que el email en `EMAIL_USER` sea correcto

### Error: "Connection timeout"

- Verifica tu conexión a internet
- Algunos firewalls bloquean el puerto 587/465 de SMTP
- Intenta desactivar temporalmente el antivirus

### Error: "CORS blocked"

Esto no debería pasar porque el servidor tiene CORS habilitado. Si ocurre:

- Verifica que el servidor esté corriendo en el puerto 3001
- Reinicia ambos servidores

### El formulario no se envía

1. Abre la consola del navegador (F12)
2. Ve a la pestaña **Network**
3. Intenta enviar el formulario
4. Busca la petición a `send-email`
5. Revisa la respuesta

## 📊 Flujo de la Aplicación

```
Frontend (localhost:5173)
    ↓
    Detecta: ¿Es localhost?
    ↓
    SÍ → http://localhost:3001/api/send-email (Node.js + nodemailer)
    NO → /api/send-email.php (PHP en producción)
    ↓
    Servidor procesa y envía email
    ↓
    Respuesta al frontend
    ↓
    Mensaje de éxito/error al usuario
```

## 🎯 Ventajas del Testing Local

✅ **Instantáneo**: No necesitas subir archivos al servidor  
✅ **Debugging fácil**: Ves logs en tiempo real  
✅ **Sin riesgos**: No afecta el sitio en producción  
✅ **Desarrollo rápido**: Cambias código y pruebas inmediatamente  
✅ **Control total**: Usas tus propias credenciales SMTP  

## 🌐 Diferencias Local vs Producción

| Aspecto | Local (Development) | Producción |
|---------|-------------------|------------|
| Servidor | Node.js (puerto 3001) | PHP (Apache) |
| Librería | nodemailer | mail() nativo de PHP |
| Endpoint | <http://localhost:3001/api/send-email> | /api/send-email.php |
| Configuración | .env (Git ignorado) | Variables del servidor |
| Logs | Terminal visible | archivo .log |

## 🔒 Seguridad

**NUNCA hagas:**

- ❌ Subir el archivo `.env` al repositorio
- ❌ Compartir tus contraseñas de aplicación
- ❌ Hardcodear credenciales en el código

**SIEMPRE haz:**

- ✅ Usar `.env` para credenciales locales
- ✅ Mantener `.env` en `.gitignore`
- ✅ Usar variables de entorno en producción
- ✅ Regenerar contraseñas si se exponen

## 📝 Notas Adicionales

- El formulario detecta automáticamente si estás en local o producción
- En local usa Node.js, en producción usa PHP
- Los emails se envían con el diseño HTML de marca XLERION
- El campo `Reply-To` se configura con el email del usuario

## ✅ Checklist de Prueba

- [ ] Dependencias instaladas (`npm install` en `/server`)
- [ ] Archivo `.env` creado y configurado
- [ ] Contraseña de aplicación de Gmail obtenida
- [ ] Servidor de emails iniciado (puerto 3001)
- [ ] Frontend iniciado (puerto 5173)
- [ ] Formulario enviado con éxito
- [ ] Email recibido en la bandeja de entrada
- [ ] Logs visibles en ambas terminales
- [ ] Mensaje de éxito mostrado en el sitio

---

**Desarrollado para:** XLERION  
**Última actualización:** 11 de enero de 2026  
**Soporte:** GitHub Copilot
