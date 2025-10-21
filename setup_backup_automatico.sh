#!/bin/bash

echo "==============================================="
echo "   CONFIGURAÇÃO DO BACKUP AUTOMÁTICO"
echo "==============================================="
echo

# Criar diretório de backup se não existir
mkdir -p storage/app/backups/financial
mkdir -p storage/app/backups/database

echo "Configurando cron job para backup automático..."

# Adicionar ao crontab (executar todo dia às 02:00)
(crontab -l 2>/dev/null; echo "0 2 * * * cd $(pwd) && php artisan backup:financial >> storage/logs/backup.log 2>&1") | crontab -

echo "Backup automático configurado com sucesso!"
echo
echo "O backup será executado automaticamente todo dia às 02:00"
echo "Para verificar se está funcionando, execute:"
echo "  crontab -l"
echo
echo "Para executar backup manual agora, execute:"
echo "  php artisan backup:financial"
echo
