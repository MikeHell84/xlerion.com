# Checklist de Deploy para Xlerion.com

## ✅ Pre-Deploy Checklist

- [ ] Todos los cambios están en `git` (sin cambios pendientes)
- [ ] Estás en la rama `main` (verificar con `git branch`)
- [ ] Las dependencias están actualizadas (`npm ci`)
- [ ] Linting pasa sin errores (`npm run lint`)
- [ ] Build se genera sin errores (`npm run build`)
- [ ] Revisar el tamaño del build (< 200MB recomendado)
- [ ] Verificar que no hay archivos sensibles en `dist/` (package.json, .env, etc)

## 📦 Build Output Verificado

**Tamaño por componente:**

- assets/js/: ~0.84 MB (minificado)
- assets/css/: ~0.02 MB (minificado)
- assets/images/: ~6.28 MB (optimizadas)
- total-darkness/: ~36.26 MB (proyecto incluido)
- videos/: ~102.27 MB (videos del sitio)
- **Total: ~147.3 MB**

**Archivos principales generados:**

- ✓ dist/index.html (1.55 kB)
- ✓ dist/js/react-vendor.[hash].js (46 kB)
- ✓ dist/js/three-vendor.[hash].js (491 kB)
- ✓ dist/js/ui-vendor.[hash].js (18 kB)
- ✓ dist/js/index.[hash].js (328 kB)
- ✓ dist/css/index.[hash].css (24.62 kB)
- ✓ dist/.htaccess (Configuración Apache)

## 🚀 Deploy Steps

### Paso 1: Conectar al Servidor

```bash
# SSH
ssh usuario@51.222.104.17

# O SFTP
sftp usuario@51.222.104.17
```

### Paso 2: Crear Backup

```bash
cd public_html
cp -r . ../public_html_backup_$(date +%Y%m%d_%H%M%S)
cd ..
```

### Paso 3: Limpiar public_html (Opcional pero recomendado)

```bash
# Si quieres una limpieza completa:
rm -rf public_html/*
rm -rf public_html/.*
```

### Paso 4: Subir Archivos

```bash
# Opción A: Si usas SFTP local
sftp> cd public_html
sftp> put -r dist/* .

# Opción B: Si tienes acceso SSH
scp -r dist/* usuario@51.222.104.17:~/public_html/
```

### Paso 5: Verificar .htaccess

```bash
# En el servidor
cd ~/public_html
ls -la | grep .htaccess

# Si no existe
cp dist/.htaccess .
```

### Paso 6: Establecer Permisos

```bash
# En el servidor
cd ~/public_html
chmod 755 .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# Permisos especiales para ciertos archivos
chmod 755 public_html
```

### Paso 7: Verificar Estructura

```bash
# En el servidor
cd ~/public_html
ls -la
# Debe mostrar:
# - index.html
# - .htaccess
# - assets/ (carpeta)
# - api/ (carpeta con send-email.php)
# - total-darkness/ (carpeta)
# - videos/ (carpeta)
```

## ✅ Post-Deploy Verification

### En el navegador

- [ ] Abre <https://xlerion.com>
- [ ] Página carga completamente
- [ ] Logo carga correctamente
- [ ] Navegación funciona
- [ ] Haz clic en varias páginas

### Verifica cada sección

- [ ] Inicio
- [ ] Proyectos (Total Darkness, Toolkit, etc)
- [ ] Soluciones (todos los servicios)
- [ ] Documentación
- [ ] Contacto
- [ ] Footer

### Performance

- [ ] Abre DevTools (F12)
- [ ] Tab Network: verifica que assets tienen `.js` y `.css` con hash
- [ ] Verifica gzip compression (Content-Encoding: gzip)
- [ ] Verifica caché headers correctos
- [ ] No hay errores 404
- [ ] No hay errores en Console

### Logs del servidor

```bash
# En el servidor
tail -n 20 ~/logs/error_log
tail -n 20 ~/logs/access_log

# No debe haber errores de 500
# Pueden haber advertencias de deprecated functions de PHP (normal)
```

## 🔄 Rollback Plan

Si algo falla:

```bash
# En el servidor, desde home directory
rm -rf public_html
cp -r public_html_backup_TIMESTAMP public_html

# Reiniciar Apache (si tienes permisos)
sudo service httpd restart

# O contactar al soporte de hosting
```

## 📋 Configuración del Servidor Verificada

- Apache: 2.4.66 ✓
- mod_rewrite: Habilitado ✓
- mod_deflate: Habilitado ✓
- mod_expires: Habilitado ✓
- PHP-FPM: Activo ✓
- MariaDB: 10.11.15 ✓
- OpenSSL/HTTPS: Configurado ✓

## 📞 Soporte

Si encuentras problemas:

1. **Página no carga (404):**
   - Verifica .htaccess está en public_html/
   - Verifica que mod_rewrite está habilitado
   - Revisa logs: `tail ~/logs/error_log`

2. **Assets no cargan:**
   - Verifica que dist/assets/ subió correctamente
   - Hardrefresh: Ctrl+Shift+Delete
   - Hard reload: Ctrl+F5

3. **Videos no reproducen:**
   - Verifica que dist/videos/ subió correctamente
   - Verifica permisos: `chmod 644 ~/public_html/videos/*`
   - Verifica MIME types en Apache

4. **Errores JavaScript:**
   - Abre Console en DevTools (F12)
   - Busca el error exacto
   - Verifica que React y React-DOM cargan correctamente

## Información Importante

- **No incluir node_modules en el servidor** - Ya está minimizado en build
- **No incluir archivos .map** - No generamos source maps en producción
- **No incluir .env** - No usamos variables de entorno
- **Directorios necesarios:** public_html/ y logs/
- **Email:** Usa public/api/send-email.php (PHP puro, no requiere Node.js)

## Notas

- El build es completamente estático (no requiere Node.js en servidor)
- Apache + PHP maneja todo (no necesita Vite en producción)
- Videos se sirven como archivos estáticos (pueden cachear 1 año)
- Gzip comprime automáticamente JS/CSS
- .htaccess redirige todas las rutas a React Router
