# Deploy Guide - Xlerion.com

## Información del Servidor Producción

- **URL:** xlerion.com
- **Servidor:** host11 (cPanel H1)
- **IP:** 51.222.104.17
- **Apache:** 2.4.66
- **PHP-FPM:** Activo
- **Database:** MariaDB 10.11.15
- **Arquitectura:** x86_64 / Linux

## Requisitos Antes de Deploy

✅ Asegúrate de que:

- Todos los cambios están committed a `main`
- El repositorio está limpio (`git status`)
- Las dependencias están instaladas (`npm install`)
- El linting pasa (`npm run lint`)
- El build compila sin errores (`npm run build`)

## Scripts de Deploy

### Windows (PowerShell)

```powershell
# Para producción
.\deploy.ps1 -Environment "produccion"

# Para staging (local)
.\deploy.ps1 -Environment "staging"

# Con verbose output
.\deploy.ps1 -Environment "produccion" -Verbose
```

### Linux/Mac (Bash)

```bash
# Para producción
chmod +x deploy.sh
./deploy.sh produccion

# Para staging
./deploy.sh staging
```

## Proceso Manual de Deploy

Si prefieres hacer deploy manualmente sin scripts:

### 1. Crear Build Local

```bash
npm ci --prefer-offline
npm run lint
npm run build
```

Esto crea una carpeta `dist/` con todos los archivos compilados.

### 2. Conectar al Servidor

```bash
# Por SFTP (recomendado)
sftp usuario@51.222.104.17

# O por SSH
ssh usuario@51.222.104.17
```

### 3. Crear Backup

```bash
cd public_html
cp -r . ../public_html_backup_$(date +%Y%m%d_%H%M%S)
```

### 4. Subir Archivos

Sube el contenido de la carpeta `dist/` a `public_html/`:

```bash
# Opción A: Usando SFTP
sftp> put -r dist/* public_html/

# Opción B: Usando SCP (SSH)
scp -r dist/* usuario@51.222.104.17:~/public_html/

# Opción C: Desde el servidor
# Si tienes Git en el servidor:
cd ~/public_html
git pull origin main
npm ci --production
npm run build
```

### 5. Verificar .htaccess

Asegúrate de que el archivo `.htaccess` está en `public_html/`:

```bash
ls -la public_html/.htaccess
```

Si no existe, cópialo desde el build:

```bash
cp dist/.htaccess public_html/
```

### 6. Verificar Permisos

```bash
chmod 755 public_html
chmod 644 public_html/*.html public_html/*.js public_html/*.css
find public_html -type d -exec chmod 755 {} \;
find public_html -type f -exec chmod 644 {} \;
```

### 7. Limpiar Caché

- Limpia el caché del navegador (Ctrl+Shift+Delete)
- O usa: Hard Refresh (Ctrl+F5 o Cmd+Shift+R)

## Estructura del Build

Después del `npm run build`, la carpeta `dist/` contiene:

```
dist/
├── index.html              # Archivo principal
├── .htaccess               # Configuración Apache
├── assets/
│   ├── css/                # Estilos compilados (con hash)
│   ├── js/                 # JavaScript compilado (con hash)
│   └── images/             # Imágenes optimizadas
├── videos/                 # Videos del proyecto
└── total-darkness/         # Proyecto secundario
```

## Optimizaciones Aplicadas

✅ **Performance:**

- Minificación de JS/CSS
- Eliminación de console.log
- Code splitting automático
- Lazy loading de componentes

✅ **Caché:**

- Hash en nombres de archivos (cache busting)
- Cache agresivo para assets estáticos (1 año)
- Cache mínimo para HTML (sin caché)

✅ **Seguridad:**

- Headers de seguridad en .htaccess
- HTTPS forzado
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff

✅ **Compresión:**

- Gzip automático en Apache
- Compresión de imágenes
- Minificación de SVG

## Monitoreo Post-Deploy

Después de desplegar, verifica:

1. **Accesibilidad:**
   - Abre xlerion.com en navegador
   - Verifica que todas las páginas cargan

2. **Performance:**
   - Abre DevTools → Network
   - Verifica que los assets tienen hash en el nombre
   - Comprueba que el size es < 1MB para JS bundles

3. **Console:**
   - No debe haber errores JavaScript
   - No debe haber advertencias de CORS

4. **Logs del servidor:**

   ```bash
   tail -f ~/logs/error_log
   tail -f ~/logs/access_log
   ```

## Rollback (Si algo falla)

```bash
# Desde el servidor
cd ~
rm -rf public_html
cp -r public_html_backup_TIMESTAMP public_html
```

## Variables de Entorno

Actualmente no hay variables de entorno requeridas.

Si en el futuro necesitas variables de entorno, crea un archivo `.env` en la raíz:

```env
VITE_API_URL=https://api.xlerion.com
VITE_ANALYTICS_ID=XXX
```

## Troubleshooting

### Build muy grande (> 5MB)

- Verifica que `node_modules` no está en el build
- Revisa `npm run build` para ver qué está inflando el size
- Considera usar dynamic imports para componentes grandes

### Página muestra 404

- Verifica que `.htaccess` está en `public_html/`
- Asegúrate de que mod_rewrite está habilitado en Apache
- Revisa los logs: `tail ~/logs/error_log`

### Archivos no se actualizan en navegador

- Limpia caché: Ctrl+Shift+Delete
- Hard refresh: Ctrl+F5
- Abre en navegador privado

### Errores de CORS

- Verifica que los archivos de video están sirviendo correctamente
- Revisa headers en `.htaccess` para CORS

## Contacto/Soporte

Para preguntas sobre el deploy, revisa:

- `vite.config.js` - Configuración del build
- `dist/.htaccess` - Configuración del servidor
- `package.json` - Scripts disponibles
