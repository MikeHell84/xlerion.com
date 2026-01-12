#!/bin/bash
# Script de Deploy para Xlerion.com
# Uso: ./deploy.sh [producción|staging]

set -e  # Salir si hay error

ENVIRONMENT=${1:-produccion}
BUILD_DIR="dist"
GIT_BRANCH="main"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}Xlerion.com - Deploy Script${NC}"
echo -e "${YELLOW}Ambiente: $ENVIRONMENT${NC}"
echo -e "${YELLOW}Timestamp: $TIMESTAMP${NC}"
echo -e "${YELLOW}========================================${NC}\n"

# 1. Verificar estado del repositorio
echo -e "${YELLOW}[1/6]${NC} Verificando estado del repositorio..."
if [[ -n $(git status -s) ]]; then
    echo -e "${RED}ERROR: Hay cambios no comprometidos. Por favor, haz commit primero.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Repositorio limpio${NC}\n"

# 2. Verificar rama correcta
echo -e "${YELLOW}[2/6]${NC} Verificando rama Git..."
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [[ $CURRENT_BRANCH != $GIT_BRANCH ]]; then
    echo -e "${RED}ERROR: Estás en la rama '$CURRENT_BRANCH', debes estar en '$GIT_BRANCH'${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Rama correcta: $GIT_BRANCH${NC}\n"

# 3. Instalar dependencias
echo -e "${YELLOW}[3/6]${NC} Instalando dependencias..."
npm ci --prefer-offline --no-audit
echo -e "${GREEN}✓ Dependencias instaladas${NC}\n"

# 4. Ejecutar linting
echo -e "${YELLOW}[4/6]${NC} Ejecutando linting..."
npm run lint || echo -e "${YELLOW}⚠ Warnings de lint detectados${NC}"
echo -e "${GREEN}✓ Linting completado${NC}\n"

# 5. Crear build
echo -e "${YELLOW}[5/6]${NC} Compilando para producción..."
npm run build
if [[ ! -d $BUILD_DIR ]]; then
    echo -e "${RED}ERROR: Build falló - directorio $BUILD_DIR no creado${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Build completado${NC}"
echo -e "${GREEN}  Tamaño: $(du -sh $BUILD_DIR | cut -f1)${NC}\n"

# 6. Crear backup y copiar a servidor
echo -e "${YELLOW}[6/6]${NC} Preparando para deploy..."

if [[ $ENVIRONMENT == "produccion" ]]; then
    echo -e "${YELLOW}Configuración de Producción:${NC}"
    echo "  - Servidor: xlerion.com (cPanel - host11)"
    echo "  - IP: 51.222.104.17"
    echo "  - Apache: 2.4.66"
    echo "  - PHP-FPM activo"
    echo ""
    read -p "¿Proceder con deploy a producción? (s/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Ss]$ ]]; then
        echo -e "${RED}Deploy cancelado${NC}"
        exit 1
    fi
    
    echo -e "${YELLOW}Próximos pasos:${NC}"
    echo "1. Conectar por SSH/SFTP al servidor"
    echo "2. Crear backup: cp -r public_html public_html_backup_$TIMESTAMP"
    echo "3. Subir contenido de '$BUILD_DIR' a 'public_html/'"
    echo "4. Verificar .htaccess está en public_html/"
    echo "5. Verificar que node_modules NO está en public_html"
    echo "6. Limpiar caché del navegador en xlerion.com"
    
elif [[ $ENVIRONMENT == "staging" ]]; then
    echo -e "${YELLOW}Configuración de Staging:${NC}"
    echo "  - Ambiente local para testing"
    echo "  - Usar: npm run preview"
    
else
    echo -e "${RED}Ambiente desconocido: $ENVIRONMENT${NC}"
    echo "Usa: deploy.sh [produccion|staging]"
    exit 1
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Deploy preparado exitosamente${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}Archivos generados:${NC}"
ls -lh $BUILD_DIR | head -5
echo "  ... y más archivos"
echo ""
echo -e "${YELLOW}Información del Build:${NC}"
echo "  - Directorio: $BUILD_DIR"
echo "  - Entrypoint: $BUILD_DIR/index.html"
echo "  - Assets: $BUILD_DIR/assets/"
echo "  - Configuración: $BUILD_DIR/.htaccess"
