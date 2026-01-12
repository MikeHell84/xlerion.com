# Solución: Error en Formulario de Contacto

## Problema Identificado

Al enviar el formulario de contacto en xlerion.com, aparecía el error:
> "Error al enviar el mensaje. Intenta nuevamente."

## Causas del Problema

1. **Redirección incorrecta en .htaccess**: El archivo `.htaccess` estaba redirigiendo TODAS las peticiones (incluyendo las de PHP) a `index.html`, impidiendo que el archivo `/api/send-email.php` se ejecutara correctamente.

2. **Configuración PHP insuficiente**: El archivo PHP no tenía logging de errores ni validación robusta, haciendo difícil identificar problemas.

3. **Headers de email problemáticos**: Los headers del email no eran compatibles con todos los servidores de correo.

## Soluciones Implementadas

### 1. Actualización del .htaccess

**Cambio realizado:**

```apache
# No reescribir archivos PHP en /api/
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^ - [L]
```

Esta regla permite que los archivos PHP en la carpeta `/api/` se ejecuten correctamente sin ser redirigidos a `index.html`.

**Archivos actualizados:**

- `public/.htaccess` (para futuros builds)
- `dist/.htaccess` (build actual)

### 2. Mejoras en send-email.php

**Mejoras implementadas:**

1. **Logging de errores:**

   ```php
   error_reporting(E_ALL);
   ini_set('log_errors', 1);
   ini_set('error_log', __DIR__ . '/email-errors.log');
   ```

2. **Validación mejorada:**
   - Validación de formato de email con `filter_var()`
   - Sanitización de datos con `htmlspecialchars()` y `strip_tags()`
   - Mensajes de error más descriptivos

3. **Headers de email compatibles:**

   ```php
   $headers[] = "From: Formulario Web <noreply@xlerion.com>";
   $headers[] = "Reply-To: $name <$from>";
   ```

   Ahora usa `noreply@xlerion.com` como remitente y el email del usuario como Reply-To, lo cual es más compatible con servidores SMTP.

4. **Email HTML mejorado:**
   - Diseño más profesional
   - Mejor formato
   - Colores de marca XLERION

## Verificación del Servidor

Para verificar que el formulario funciona correctamente en el servidor, sigue estos pasos:

### 1. Verificar que PHP está habilitado

```bash
# Crear archivo test.php en el servidor
echo "<?php phpinfo(); ?>" > public_html/test.php

# Visitar: https://xlerion.com/test.php
# Deberías ver la información de PHP

# ELIMINAR el archivo después de verificar
rm public_html/test.php
```

### 2. Verificar configuración de mail()

La función `mail()` de PHP requiere que el servidor tenga configurado un servidor SMTP. Verifica con tu proveedor de hosting:

```php
// Crear api/test-mail.php para probar
<?php
$to = 'tu-email@example.com';
$subject = 'Test desde xlerion.com';
$message = 'Este es un email de prueba';
$headers = 'From: noreply@xlerion.com';

if (mail($to, $subject, $message, $headers)) {
    echo "Email enviado correctamente";
} else {
    echo "Error al enviar email";
}
?>
```

### 3. Verificar logs de errores

Si el formulario sigue sin funcionar, revisa el archivo de logs:

```bash
# En el servidor
cat public_html/api/email-errors.log
```

## Si el problema persiste

Si después de aplicar estos cambios el formulario sigue sin funcionar, puede ser debido a:

### Opción 1: Configuración SMTP del servidor

Contacta a tu proveedor de hosting (ej: cPanel, HostGator, etc.) y pregunta:

- ¿Está habilitada la función `mail()` de PHP?
- ¿Necesito configurar SMTP?
- ¿Hay límites de envío de emails?

### Opción 2: Usar PHPMailer (Recomendado)

PHPMailer es más confiable que `mail()`. Para implementarlo:

1. Instalar PHPMailer:

   ```bash
   cd public_html/api
   composer require phpmailer/phpmailer
   ```

2. Crear nuevo `send-email-smtp.php`:

   ```php
   <?php
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\Exception;

   require 'vendor/autoload.php';

   $mail = new PHPMailer(true);
   
   try {
       // Configuración SMTP
       $mail->isSMTP();
       $mail->Host = 'smtp.gmail.com'; // O tu servidor SMTP
       $mail->SMTPAuth = true;
       $mail->Username = 'tu-email@gmail.com';
       $mail->Password = 'tu-contraseña';
       $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
       $mail->Port = 587;

       // Destinatarios
       $mail->setFrom('noreply@xlerion.com', 'Formulario XLERION');
       $mail->addAddress('contactus@xlerion.com');
       $mail->addReplyTo($input['email'], $input['name']);

       // Contenido
       $mail->isHTML(true);
       $mail->Subject = 'Nuevo mensaje de contacto';
       $mail->Body = $body;

       $mail->send();
       echo json_encode(['success' => true]);
   } catch (Exception $e) {
       echo json_encode(['error' => $mail->ErrorInfo]);
   }
   ?>
   ```

### Opción 3: Usar servicios externos

Servicios como **SendGrid**, **Mailgun**, o **Amazon SES** son más confiables:

1. Registrarte en el servicio
2. Obtener API key
3. Actualizar `send-email.php` para usar la API del servicio

## Próximos Pasos

1. **Subir archivos actualizados al servidor:**
   - `public/.htaccess` → `public_html/.htaccess`
   - `public/api/send-email.php` → `public_html/api/send-email.php`

2. **Hacer un rebuild y subir el ZIP:**

   ```bash
   npm run build
   ```

3. **Verificar permisos en el servidor:**

   ```bash
   chmod 755 public_html/api
   chmod 644 public_html/api/send-email.php
   ```

4. **Probar el formulario** en <https://xlerion.com>

## Verificación de funcionamiento

Después de subir los archivos:

1. Abre <https://xlerion.com>
2. Ve a la sección de contacto
3. Llena el formulario:
   - Nombre: Test Usuario
   - Email: <tu-email@example.com>
   - Mensaje: Este es un mensaje de prueba
4. Haz clic en "Enviar"
5. Revisa:
   - El mensaje en pantalla (debe decir "Mensaje enviado correctamente")
   - Tu bandeja de entrada en <contactus@xlerion.com>
   - El archivo `api/email-errors.log` si hay errores

## Debugging adicional

Si necesitas más información sobre el error:

1. **Ver errores del servidor:**

   ```bash
   tail -f /var/log/apache2/error.log
   ```

2. **Ver errores de PHP:**

   ```bash
   tail -f public_html/api/email-errors.log
   ```

3. **Probar manualmente el endpoint:**

   ```bash
   curl -X POST https://xlerion.com/api/send-email.php \
     -H "Content-Type: application/json" \
     -d '{"name":"Test","email":"test@example.com","message":"Hola"}'
   ```

---

**Fecha:** 11 de enero de 2026  
**Desarrollado por:** GitHub Copilot  
**Para:** XLERION - Miguel Eduardo Rodríguez Martínez
