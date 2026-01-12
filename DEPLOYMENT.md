# 🚀 Despliegue del Sitio XLERION

## 📦 Build Generado

El sitio ha sido compilado exitosamente y está listo para producción en la carpeta `dist/`.

### Archivos Incluidos

- ✅ `index.html` - Página principal optimizada con SEO
- ✅ `robots.txt` - Configuración para buscadores
- ✅ `sitemap.xml` - Mapa del sitio con todas las páginas
- ✅ `.htaccess` - Configuración Apache con:
  - React Router (HTML5 History API)
  - Compresión GZIP
  - Cache del navegador
  - Headers de seguridad
  - Protección de archivos sensibles

---

## 🌐 Instrucciones de Despliegue

### Opción 1: Servidor Apache (Recomendado)

1. **Subir archivos al servidor:**

   ```bash
   # Copiar todo el contenido de la carpeta dist/ al directorio público del servidor
   scp -r dist/* usuario@servidor:/var/www/html/
   ```

2. **Verificar configuración Apache:**
   - Asegúrate de que `mod_rewrite` esté habilitado
   - Asegúrate de que `mod_deflate` esté habilitado (para compresión)
   - Asegúrate de que `mod_expires` esté habilitado (para caché)
   - Asegúrate de que `mod_headers` esté habilitado (para seguridad)

   ```bash
   # Habilitar módulos en Ubuntu/Debian:
   sudo a2enmod rewrite
   sudo a2enmod deflate
   sudo a2enmod expires
   sudo a2enmod headers
   sudo systemctl restart apache2
   ```

3. **Configurar AllowOverride:**
   En tu configuración de Apache (generalmente `/etc/apache2/sites-available/000-default.conf`), asegúrate de tener:

   ```apache
   <Directory /var/www/html>
       Options -Indexes +FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

4. **Reiniciar Apache:**

   ```bash
   sudo systemctl restart apache2
   ```

---

### Opción 2: Servidor Nginx

Si usas Nginx, necesitas configurar el routing en el archivo de configuración del sitio:

```nginx
server {
    listen 80;
    server_name xlerion.com www.xlerion.com;
    root /var/www/html;
    index index.html;

    # Redirigir a HTTPS (cuando esté configurado)
    # return 301 https://$server_name$request_uri;

    # Compresión GZIP
    gzip on;
    gzip_vary on;
    gzip_min_length 10240;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;
    gzip_disable "MSIE [1-6]\.";

    # Cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # React Router - Redirigir todo a index.html
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Proteger archivos sensibles
    location ~ /\. {
        deny all;
    }

    # Headers de seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}

# HTTPS (cuando tengas certificado SSL)
# server {
#     listen 443 ssl http2;
#     server_name xlerion.com www.xlerion.com;
#     
#     ssl_certificate /path/to/certificate.crt;
#     ssl_certificate_key /path/to/private.key;
#     
#     root /var/www/html;
#     index index.html;
#     
#     # ... resto de la configuración igual que arriba
# }
```

---

### Opción 3: Hosting Compartido (cPanel)

1. Accede a tu cPanel
2. Ve a "Administrador de archivos"
3. Navega a `public_html` o `www`
4. Elimina el contenido existente
5. Sube todos los archivos de la carpeta `dist/`
6. El archivo `.htaccess` se aplicará automáticamente

---

## 🔧 Configuración Post-Despliegue

### 1. Configurar HTTPS/SSL

Una vez que el sitio esté en línea, configura un certificado SSL:

- **Let's Encrypt (Gratis):** Usa Certbot

  ```bash
  sudo certbot --apache -d xlerion.com -d www.xlerion.com
  ```

- **SSL comercial:** Instala el certificado proporcionado por tu proveedor

### 2. Actualizar URLs en archivos

Si tu dominio es diferente a `xlerion.com`, actualiza:

- `sitemap.xml` - Cambia todas las URLs
- `robots.txt` - Actualiza la URL del sitemap
- `index.html` - Actualiza meta tags Open Graph y Twitter

### 3. Verificar en Google Search Console

1. Ve a [Google Search Console](https://search.google.com/search-console)
2. Agrega tu sitio
3. Verifica la propiedad
4. Envía el sitemap: `https://xlerion.com/sitemap.xml`

### 4. Configurar Google Analytics (Opcional)

1. Crea una cuenta en [Google Analytics](https://analytics.google.com)
2. Obtén tu ID de seguimiento
3. Agrega el código de tracking a `index.html`

---

## 🧪 Verificación Post-Despliegue

Después de subir los archivos, verifica:

1. **Routing funciona:**
   - `https://xlerion.com/` ✓
   - `https://xlerion.com/mision` ✓
   - `https://xlerion.com/toolkit/validacion` ✓
   - Recarga cualquier página directamente ✓

2. **SEO:**
   - Robots: `https://xlerion.com/robots.txt`
   - Sitemap: `https://xlerion.com/sitemap.xml`

3. **Compresión:**

   ```bash
   curl -H "Accept-Encoding: gzip" -I https://xlerion.com
   # Debe mostrar: Content-Encoding: gzip
   ```

4. **Seguridad:**
   Verifica headers en [SecurityHeaders.com](https://securityheaders.com/)

---

## 📊 Rendimiento

El build está optimizado con:

- ✅ Code splitting (chunks separados por vendor)
- ✅ Minificación de JS y CSS
- ✅ Compresión GZIP
- ✅ Cache del navegador (1 año para assets)
- ✅ Lazy loading de imágenes y componentes

**Tamaños del build:**

- CSS: 25.29 KB (4.86 KB gzipped)
- JavaScript total: ~976 KB (~248 KB gzipped)
- Three.js vendor: 491 KB (optimizado para 3D)

---

## 🔄 Actualizaciones Futuras

Para actualizar el sitio:

1. Hacer cambios en el código
2. Generar nuevo build:

   ```bash
   npm run build
   ```

3. Subir solo los archivos modificados en `dist/`
4. Limpiar caché del navegador si es necesario

---

## 📞 Soporte

Para problemas o dudas sobre el despliegue:

- Email: <contacto@xlerion.tech>
- GitHub: [xlerion.com](https://github.com/MikeHell84/xlerion.com)

---

**Versión del Build:** 11/01/2026  
**Tiempo de compilación:** 49.41s  
**Estado:** ✅ Listo para producción
