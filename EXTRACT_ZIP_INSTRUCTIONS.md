# 📦 INSTRUCCIONES - Extraer Build ZIP en Servidor

**Archivo ZIP:** `xlerion-build-20260111_193823.zip` (137.13 MB)  
**Contenido:** Build completo de xlerion.com  
**Destino:** `/home/usuario/public_html/`

---

## 🚀 OPCIÓN 1: Por SFTP (Recomendado para principiantes)

### 1. Descargar el ZIP localmente

```bash
# El archivo está en:
x:\Programacion\XlerionWeb\xlerion-site\xlerion-build-20260111_193823.zip
```

### 2. Conectar por SFTP

```bash
sftp usuario@51.222.104.17
```

### 3. Navegar a public_html

```bash
sftp> cd public_html
```

### 4. Crear backup (importante)

```bash
# Desde el servidor por SSH
ssh usuario@51.222.104.17
cd ~/public_html
cp -r . ../public_html_backup_$(date +%Y%m%d_%H%M%S)
exit
```

### 5. Subir el ZIP

```bash
sftp> put xlerion-build-20260111_193823.zip
# Esperar a que se complete (137 MB)
sftp> quit
```

### 6. Extraer en el servidor

```bash
ssh usuario@51.222.104.17
cd ~/public_html
unzip xlerion-build-20260111_193823.zip
# Esperar a que se complete
ls -la
```

### 7. Reorganizar archivos

```bash
# El ZIP contiene una carpeta "dist" que hay que sacar
mv dist/* .
mv dist/.htaccess .
rmdir dist
rm xlerion-build-20260111_193823.zip
```

### 8. Verificar estructura

```bash
ls -la ~/public_html | head -20
# Debe mostrar: index.html, .htaccess, api/, assets/, images/, etc.
```

### 9. Establecer permisos

```bash
cd ~/public_html
chmod 755 .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
```

---

## 🚀 OPCIÓN 2: Por SSH/SCP (Más rápido para usuarios avanzados)

### 1. Subir el ZIP por SCP

```bash
scp xlerion-build-20260111_193823.zip usuario@51.222.104.17:~/
```

### 2. Conectar y extraer

```bash
ssh usuario@51.222.104.17

# Crear backup
cd ~/public_html
cp -r . ../public_html_backup_$(date +%Y%m%d_%H%M%S)
cd ~

# Extraer ZIP
unzip -q xlerion-build-20260111_193823.zip
# Esperar a que se complete (sin -q para ver progreso)

# Mover archivos
cd ~/public_html
mv ../dist/* .
mv ../dist/.htaccess .
rmdir ~/dist
rm ~/xlerion-build-20260111_193823.zip

# Permisos
chmod 755 .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

exit
```

---

## 🚀 OPCIÓN 3: Por cPanel File Manager

### 1. Subir ZIP desde cPanel

- Conectar a cPanel en `51.222.104.17`
- File Manager → public_html
- Upload → Seleccionar `xlerion-build-20260111_193823.zip`

### 2. Extraer en cPanel

- Click derecho en ZIP
- Extract → Extraer aquí
- Esperar a que se complete

### 3. Reorganizar

- Mover archivos de `dist/` a `public_html/`
- Eliminar carpeta `dist/`
- Eliminar archivo ZIP

---

## ✅ Verificación Post-Extracción

### En el servidor

```bash
# Conectar
ssh usuario@51.222.104.17

# Verificar estructura
cd ~/public_html
ls -la | head -20

# Debe mostrar:
# -rw-r--r--  index.html
# -rw-r--r--  .htaccess
# drwxr-xr-x  api/
# drwxr-xr-x  assets/
# drwxr-xr-x  images/
# drwxr-xr-x  total-darkness/
# drwxr-xr-x  videos/

# Verificar .htaccess existe
cat .htaccess | head -5
# Debe mostrar configuración de Apache

# Verificar tamaño
du -sh .
# Debe mostrar ~147 MB
```

### En el navegador

```
https://xlerion.com
```

- ✅ Página carga
- ✅ Limpia caché (Ctrl+Shift+Delete)
- ✅ Hard refresh (Ctrl+F5)
- ✅ Verifica todas las páginas

---

## 📋 Checklist de Extracción

- [ ] ZIP descargado localmente
- [ ] Backup creado en servidor (`public_html_backup_*`)
- [ ] ZIP subido a servidor
- [ ] ZIP extraído en ~/
- [ ] Archivos movidos a ~/public_html/
- [ ] Carpeta dist/ eliminada
- [ ] Permisos establecidos (755/644)
- [ ] .htaccess presente en public_html/
- [ ] Sitio accesible en <https://xlerion.com>
- [ ] Todas las páginas cargan correctamente
- [ ] Console sin errores JavaScript (F12)

---

## 🔄 Rollback (Si algo falla)

```bash
# En el servidor
cd ~
rm -rf public_html
cp -r public_html_backup_TIMESTAMP public_html
```

---

## 💡 Notas Importantes

✓ **ZIP ya incluye:**

- index.html
- .htaccess (Apache config)
- assets/ (JS/CSS minificados)
- images/ (parallax backgrounds)
- total-darkness/ (proyecto 3D)
- videos/ (videos del sitio)
- api/ (PHP para contacto)

✓ **NO incluye:**

- node_modules (no necesario en servidor)
- .env (no requerido)
- .git (repositorio en GitHub)

✓ **Tamaño:**

- ZIP: 137.13 MB
- Extraído: 147.3 MB
- Espacio mínimo recomendado: 200 MB

✓ **Tiempo de extracción:**

- Estimado: 2-5 minutos (depende de servidor)
- Subida del ZIP: ~5-10 minutos en conexión normal

---

## 📞 Troubleshooting

### ZIP no se extrae

```bash
# Verificar integridad
unzip -t xlerion-build-20260111_193823.zip

# Re-descargar si hay error
```

### Permisos incorrectos

```bash
# Resetear permisos
cd ~/public_html
chmod -R 755 .
find . -type f -exec chmod 644 {} \;
```

### Página muestra 404

```bash
# Verificar .htaccess
cat .htaccess | grep -i rewrite

# Verificar Apache modules
ssh usuario@51.222.104.17
# Revisar cPanel → Apache Modules
```

### Videos no reproducen

```bash
# Verificar permisos de videos
chmod 644 ~/public_html/videos/*
# Verificar que existen
ls -la ~/public_html/videos/
```

---

**ZIP listo para descargar y extraer en producción ✅**
