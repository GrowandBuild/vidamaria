@echo off
echo ===============================================
echo   CONFIGURACAO DO BACKUP AUTOMATICO
echo ===============================================
echo.

echo Criando tarefa agendada no Windows...

REM Criar tarefa para executar o backup diariamente às 02:00
schtasks /create /tn "Backup_Esmalteria" /tr "php artisan backup:financial" /sc daily /st 02:00 /f

echo.
echo Tarefa criada com sucesso!
echo.
echo A tarefa executara automaticamente todo dia as 02:00
echo Para verificar se esta funcionando, execute:
echo   schtasks /query /tn "Backup_Esmalteria"
echo.
echo Para executar backup manual agora, execute:
echo   php artisan backup:financial
echo.
pause
