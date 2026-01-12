# Configuración del Formulario de Contacto

El formulario de contacto en la sección de Contacto ahora envía mensajes al email `contactus@xlerion.com`.

## Arquitectura

### Frontend (React/Vite)

- Componente: `src/components/ContactForm.jsx`
- Valida los datos del formulario
- Envía POST request a `/api/send-email.php`
- Muestra mensajes de estado (éxito/error)

### Backend (PHP)

- Ubicación: `public/api/send-email.php`
- Recibe datos JSON del formulario
- Valida campos requeridos
- Envía email a través del servidor PHP

## Requisitos

1. **Servidor PHP configurado** para enviar emails
2. **Configuración de PHP mail()** o acceso a un SMTP
3. El servidor debe estar en la ruta correcta donde se sirve la aplicación

## Pasos de Configuración

### Opción 1: Usar PHP mail() (Recomendado para desarrollo)

Si tu hosting tiene PHP mail() habilitado:

1. No se requiere configuración adicional
2. Los emails se enviarán directamente desde el servidor

### Opción 2: Usar SMTP externo (Gmail, SendGrid, etc.)

Modifica `public/api/send-email.php` para usar una librería como PHPMailer:

```php
// Ejemplo con PHPMailer
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

### Opción 3: Usar un servicio como EmailJS

Alternativa sin backend PHP:

1. Registrate en [EmailJS](https://www.emailjs.com/)
2. Crea un template de email
3. Reemplaza la implementación en `ContactForm.jsx` para usar la SDK de EmailJS

## Pruebas Locales

Para probar el formulario localmente:

1. Asegúrate de que PHP esté habilitado en tu servidor local
2. El archivo `public/api/send-email.php` debe estar accesible
3. Prueba el endpoint directamente:

```bash
curl -X POST http://localhost:5173/api/send-email.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","message":"Test message"}'
```

## Notas Importantes

- Los emails se envían a `contactus@xlerion.com` (configurable en `send-email.php`)
- El "From" del email es la dirección ingresada por el usuario
- Los mensajes se formatean en HTML
- Se validan todos los campos requeridos
- Se incluye manejo de errores y mensajes de estado

## Troubleshooting

Si los emails no se envían:

1. **Error 404**: Verifica que `public/api/send-email.php` exista
2. **Error 500**: Revisa los logs del servidor PHP
3. **Emails en spam**: Configura SPF/DKIM en el dominio
4. **CORS bloqueado**: Verifica los headers CORS en `send-email.php`

## Futuras Mejoras

- Agregar confirmación de email al usuario
- Implementar rate limiting
- Agregar campos opcionales (asunto, tipo de consulta)
- Integrar con CRM o sistema de tickets
