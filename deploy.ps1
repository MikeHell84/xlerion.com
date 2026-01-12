# Deploy Script for Xlerion.com (Windows PowerShell)
# Usage: .\deploy.ps1 -Environment "produccion" -Verbose

param(
    [Parameter(Mandatory = $true)]
    [ValidateSet("produccion", "staging")]
    [string]$Environment,
    [switch]$Verbose
)

# Colors
$RED = "`e[31m"
$GREEN = "`e[32m"
$YELLOW = "`e[33m"
$RESET = "`e[0m"

$BUILD_DIR = "dist"
$GIT_BRANCH = "main"
$TIMESTAMP = Get-Date -Format "yyyyMMdd_HHmmss"

Write-Host "$YELLOW========================================$RESET"
Write-Host "$YELLOW Xlerion.com - Deploy Script (Windows)$RESET"
Write-Host "$YELLOW Ambiente: $Environment$RESET"
Write-Host "$YELLOW Timestamp: $TIMESTAMP$RESET"
Write-Host "$YELLOW========================================$RESET`n"

# 1. Check git status
Write-Host "$YELLOW[1/6]$RESET Verificando estado del repositorio..."
$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "$RED ERROR: Hay cambios no comprometidos.$RESET"
    Write-Host "Por favor, haz commit primero:"
    git status --short
    exit 1
}
Write-Host "$GREEN ✓ Repositorio limpio$RESET`n"

# 2. Check git branch
Write-Host "$YELLOW[2/6]$RESET Verificando rama Git..."
$currentBranch = git rev-parse --abbrev-ref HEAD
if ($currentBranch -ne $GIT_BRANCH) {
    Write-Host "$RED ERROR: Estás en la rama '$currentBranch', debes estar en '$GIT_BRANCH'$RESET"
    exit 1
}
Write-Host "$GREEN ✓ Rama correcta: $GIT_BRANCH$RESET`n"

# 3. Install dependencies
Write-Host "$YELLOW[3/6]$RESET Instalando dependencias..."
npm ci --prefer-offline --no-audit
if ($LASTEXITCODE -ne 0) {
    Write-Host "$RED ERROR: npm ci falló$RESET"
    exit 1
}
Write-Host "$GREEN ✓ Dependencias instaladas$RESET`n"

# 4. Run lint
Write-Host "$YELLOW[4/6]$RESET Ejecutando linting..."
npm run lint
if ($Verbose) {
    Write-Host "$YELLOW ⚠ Resultados de lint mostrados arriba$RESET"
}
Write-Host "$GREEN ✓ Linting completado$RESET`n"

# 5. Build
Write-Host "$YELLOW[5/6]$RESET Compilando para producción..."

# Eliminar build anterior si existe
if (Test-Path $BUILD_DIR) {
    Remove-Item -Path $BUILD_DIR -Recurse -Force
}

npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "$RED ERROR: Build falló$RESET"
    exit 1
}

if (-not (Test-Path $BUILD_DIR)) {
    Write-Host "$RED ERROR: Directorio $BUILD_DIR no creado$RESET"
    exit 1
}

# Calcular tamaño
$buildSize = (Get-ChildItem $BUILD_DIR -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB
Write-Host "$GREEN ✓ Build completado$RESET"
Write-Host "$GREEN   Tamaño: $([Math]::Round($buildSize, 2)) MB$RESET`n"

# 6. Prepare for deploy
Write-Host "$YELLOW[6/6]$RESET Preparando para deploy..."

if ($Environment -eq "produccion") {
    Write-Host ""
    Write-Host "$YELLOW Configuración de Producción:$RESET"
    Write-Host "  - Servidor: xlerion.com (cPanel - host11)"
    Write-Host "  - IP: 51.222.104.17"
    Write-Host "  - Apache: 2.4.66"
    Write-Host "  - PHP-FPM activo"
    Write-Host "  - MariaDB: 10.11.15"
    Write-Host ""
    
    $response = Read-Host "¿Proceder con deploy a producción? (s/n)"
    if ($response -ne "s" -and $response -ne "S") {
        Write-Host "$RED Deploy cancelado$RESET"
        exit 1
    }
    
    Write-Host ""
    Write-Host "$YELLOW Próximos pasos:$RESET"
    Write-Host "1. Conectar por SSH/SFTP al servidor (51.222.104.17)"
    Write-Host "2. Crear backup:"
    Write-Host "   cp -r public_html public_html_backup_$TIMESTAMP"
    Write-Host "3. Subir contenido de '$BUILD_DIR' a 'public_html/'"
    Write-Host "4. Verificar .htaccess está en public_html/"
    Write-Host "5. Verificar que node_modules NO está en public_html"
    Write-Host "6. Verificar permisos: chmod 755 public_html"
    Write-Host "7. Limpiar caché en navegador: xlerion.com"
    Write-Host ""
    Write-Host "$YELLOW Archivos a subir:$RESET"
    Get-ChildItem $BUILD_DIR -Recurse | Select-Object -First 10 | ForEach-Object {
        Write-Host "  $_"
    }
    
}
elseif ($Environment -eq "staging") {
    Write-Host "$YELLOW Configuración de Staging:$RESET"
    Write-Host "  - Ambiente local para testing"
    Write-Host "  - Usar: npm run preview"
    Write-Host ""
    Write-Host "$GREEN Para previsualizar el build, ejecuta:$RESET"
    Write-Host "  npm run preview"
}

Write-Host ""
Write-Host "$GREEN========================================$RESET"
Write-Host "$GREEN Deploy preparado exitosamente$RESET"
Write-Host "$GREEN========================================$RESET"
Write-Host ""
Write-Host "$YELLOW Información del Build:$RESET"
Write-Host "  - Directorio: $BUILD_DIR"
Write-Host "  - Entrypoint: $BUILD_DIR\index.html"
Write-Host "  - Assets: $BUILD_DIR\assets\"
Write-Host "  - Configuración: $BUILD_DIR\.htaccess"
Write-Host "  - Tamaño total: $([Math]::Round($buildSize, 2)) MB"
Write-Host ""

# Mostrar contenido del directorio
Write-Host "$YELLOW Contenido del build:$RESET"
Get-ChildItem -Path $BUILD_DIR | Format-Table -Property Name, @{Name = "Tamaño"; Expression = { if ($_.PSIsContainer) { "(carpeta)" }else { [Math]::Round($_.Length / 1KB, 2).ToString() + " KB" } } }
