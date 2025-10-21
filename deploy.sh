#!/bin/bash

echo "🚀 Deploy Esmalteria Vida Maria - PWA"
echo "======================================"

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Modo manutenção
echo -e "${YELLOW}1. Ativando modo manutenção...${NC}"
php artisan down

# 2. Git pull (se estiver usando)
# git pull origin main

# 3. Instalar dependências
echo -e "${YELLOW}2. Instalando dependências do Composer...${NC}"
composer install --optimize-autoloader --no-dev

# 4. Compilar assets
echo -e "${YELLOW}3. Compilando assets...${NC}"
npm install
npm run build

# 5. Limpar cache
echo -e "${YELLOW}4. Limpando cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 6. Otimizar para produção
echo -e "${YELLOW}5. Otimizando para produção...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Rodar migrations
echo -e "${YELLOW}6. Rodando migrations...${NC}"
php artisan migrate --force

# 8. Permissões
echo -e "${YELLOW}7. Ajustando permissões...${NC}"
chmod -R 775 storage bootstrap/cache

# 9. Tirar do modo manutenção
echo -e "${YELLOW}8. Desativando modo manutenção...${NC}"
php artisan up

echo -e "${GREEN}✅ Deploy concluído com sucesso!${NC}"
echo ""
echo "🌐 Acesse: https://seudominio.com.br"
echo "💡 Verifique PWA em: DevTools → Application → Manifest"

