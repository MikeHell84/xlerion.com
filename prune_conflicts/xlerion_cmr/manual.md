# Manual: Capturas automatizadas y Cron/Task Scheduler

Este documento describe cómo funcionan las capturas de pantalla automáticas, cómo regenerar el índice de previews, cómo rotar artefactos antiguos y cómo programar la ejecución periódica en Windows (Task Scheduler) y Linux/cPanel (cron).

Rutas relevantes
- Proyecto: `xlerion_cmr/`
- Scripts: `xlerion_cmr/scripts/`
- Docroot de artefactos: `xlerion_cmr/public/artifacts/`

Scripts principales
- `collect_artifacts.js` — Colector headless existente (toma screenshot, guarda HTML, consola y requests en `artifacts/run-<timestamp>/` y copia `screenshot-<slug>.png` a `public/artifacts/`).
- `capture_published_routes.js` — Script que itera sobre páginas publicadas y llama al colector para cada slug.
- `capture_with_retry.js` — Wrapper que ejecuta `collect_artifacts.js` para una URL con reintentos/exponential backoff.
- `generate_artifacts_index.js` — Genera `public/artifacts/index.html` a partir de los archivos `screenshot-*.png` en `public/artifacts/`.
- `run_capture_now.ps1` — Runner para Windows (invoca `capture_with_retry.js`).
- `schedule_capture_task.ps1` — Crea o reemplaza una tarea en Task Scheduler para ejecutar `run_capture_now.ps1` diariamente.
- `run_capture_linux.sh` — Runner para Linux/cPanel (ejecuta `capture_with_retry.js` y regenera índice; fallback con curl si Node no está disponible).
- `install_cron.sh` — Helper para instalar una entrada de crontab que ejecute el runner y luego el rotador.
- `rotate_artifacts.sh` — Elimina artefactos según edad (N días) y si el tamaño total excede un límite, borra los archivos más antiguos hasta ajustarlo.

Flujo recomendado (ejecución manual)
1. Arrancar el servidor PHP integrado en la máquina donde vayas a ejecutar las capturas:

```powershell
# PowerShell (Windows)
php -S 127.0.0.1:8080 -t "x:\Programacion\XlerionWeb\LocalAI\xlerion_cmr\public"
```

o en Linux:

```bash
php -S 127.0.0.1:8080 -t /ruta/al/proyecto/xlerion_cmr/public
```

2. Ejecutar captura en caliente (Windows):

```powershell
cd x:\Programacion\XlerionWeb\LocalAI\xlerion_cmr\scripts
.\n+\run_capture_now.ps1 -Url 'http://127.0.0.1:8080/'
```

En Linux:

```bash
cd /ruta/al/proyecto/xlerion_cmr/scripts
./run_capture_linux.sh 'http://127.0.0.1:8080/'
```

3. Regenerar índice (si no se hace automáticamente):

```bash
node xlerion_cmr/scripts/generate_artifacts_index.js
```

4. Verificar resultados en `public/artifacts/index.html`.

Automatización con Task Scheduler (Windows)
1. Abre PowerShell en modo administrador.
2. Ejecuta:

```powershell
x:\Programacion\XlerionWeb\LocalAI\xlerion_cmr\scripts\schedule_capture_task.ps1 -TriggerTime '03:00' -Url 'http://127.0.0.1:8080/'
```

El script creará o reemplazará la tarea `Xlerion_Capture_Artefacts` que ejecuta `run_capture_now.ps1`. Ajusta `-TriggerTime` según necesites.

Automatización con cron (Linux / cPanel)
1. Si tienes acceso SSH o la UI de Cron en cPanel, puedes añadir un cron que ejecute el runner diariamente.
2. Con acceso SSH, desde la carpeta `xlerion_cmr/scripts` ejecuta:

```bash
./install_cron.sh '0 3 * * *' 'http://127.0.0.1:8080/'
```

Esto añadirá una línea en `crontab` que ejecuta `run_capture_linux.sh` y luego `rotate_artifacts.sh`.

Si sólo tienes acceso a la UI de cPanel Cron Jobs, crea una entrada que ejecute el comando completo (ajusta la ruta absoluta):

```bash
/ruta/a/xlerion_cmr/scripts/run_capture_linux.sh 'http://127.0.0.1:8080/' && /ruta/a/xlerion_cmr/scripts/rotate_artifacts.sh /ruta/a/xlerion_cmr/public/artifacts 30 500
```

Rotación automática de artefactos
- `rotate_artifacts.sh [ARTIFACTS_DIR] [MAX_DAYS] [MAX_SIZE_MB]`
- Por defecto elimina archivos con más de 30 días y asegura que la carpeta no exceda 500 MB.
- Puedes ajustar los parámetros al invocarlo desde cron o manualmente.

Buenas prácticas y notas
- Asegúrate de que Node.js esté instalado en el servidor si quieres capturas reales (screenshots). Si Node no está disponible, el runner hace un GET con curl (sin screenshot).
- En servidores compartidos con cPanel posiblemente no puedas instalar Node; en ese caso generar previews localmente y subirlas por FTP/Copia puede ser una alternativa.
- Revisa los permisos de `public/artifacts/` para que el servidor web pueda servir los archivos y para que los scripts puedan escribirlos.
- Si el servidor o la app usan autenticación, actualiza `collect_artifacts.js` o el runner para autenticar antes de capturar.

Ejemplos rápidos
- Forzar rotación manteniendo 90 días y 1GB máximo:

```bash
./rotate_artifacts.sh /ruta/a/xlerion_cmr/public/artifacts 90 1024
```

- Crear cron que ejecute capturas cada 6 horas:

```bash
./install_cron.sh '0 */6 * * *' 'http://127.0.0.1:8080/'
```

Soporte y ampliaciones
- Puedo añadir: rotación por número de snapshots por slug, compresión adicional de thumbnails, notificaciones por fallo, o integración con almacenamiento remoto (S3/FTP) para conservar históricos.

---
Fecha: 2025-11-17
