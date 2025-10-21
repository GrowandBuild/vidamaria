# 📅 CONFIGURAÇÃO DO BACKUP AUTOMÁTICO

## ✅ BACKUP AUTOMÁTICO CONFIGURADO!

### 🕐 **Horários Configurados:**
- **Backup Financeiro:** Todo dia às **02:00**
- **Limpeza de Logs:** Todo domingo às **01:00**

---

## 🖥️ **PARA WINDOWS (Seu Sistema):**

### **Opção 1: Usar o Script Automático**
```bash
# Execute o arquivo que criamos:
setup_backup_automatico.bat
```

### **Opção 2: Configuração Manual**
```bash
# Abra o Prompt como Administrador e execute:
schtasks /create /tn "Backup_Esmalteria" /tr "cd C:\Users\Alexandre\Desktop\sistema para esmalteria && php artisan backup:financial" /sc daily /st 02:00
```

---

## 🐧 **PARA LINUX/MAC:**

### **Opção 1: Usar o Script Automático**
```bash
chmod +x setup_backup_automatico.sh
./setup_backup_automatico.sh
```

### **Opção 2: Configuração Manual**
```bash
# Adicionar ao crontab:
crontab -e

# Adicionar esta linha:
0 2 * * * cd /caminho/para/seu/projeto && php artisan backup:financial >> storage/logs/backup.log 2>&1
```

---

## 🚀 **PARA PRODUÇÃO (Servidor):**

### **1. Configurar Supervisor (Recomendado)**
```bash
# Instalar supervisor
sudo apt install supervisor

# Criar configuração
sudo nano /etc/supervisor/conf.d/laravel-schedule.conf
```

**Conteúdo do arquivo:**
```ini
[program:laravel-schedule]
process_name=%(program_name)s_%(process_num)02d
command=php /caminho/para/seu/projeto/artisan schedule:run
directory=/caminho/para/seu/projeto
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/caminho/para/seu/projeto/storage/logs/schedule.log
```

### **2. Iniciar Supervisor**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-schedule:*
```

---

## 📋 **COMANDOS ÚTEIS:**

### **Backup Manual:**
```bash
php artisan backup:financial
```

### **Verificar Logs de Backup:**
```bash
tail -f storage/logs/backup.log
```

### **Verificar Tarefas Agendadas (Windows):**
```bash
schtasks /query /tn "Backup_Esmalteria"
```

### **Verificar Cron Jobs (Linux/Mac):**
```bash
crontab -l
```

---

## 📁 **LOCALIZAÇÃO DOS BACKUPS:**

### **Backups Financeiros:**
- `storage/app/backups/financial/financial_backup_YYYY-MM-DD_HH-MM-SS.json`

### **Backups do Banco:**
- `storage/app/backups/database/database_backup_YYYY-MM-DD_HH-MM-SS.sql`

### **Logs de Backup:**
- `storage/logs/backup.log`

---

## ⚠️ **IMPORTANTE:**

1. **Teste primeiro:** Execute `php artisan backup:financial` para testar
2. **Verifique permissões:** Certifique-se que o usuário tem acesso às pastas
3. **Monitoramento:** Verifique os logs regularmente
4. **Limpeza:** Os backups antigos são removidos automaticamente após 30 dias

---

## 🔧 **SOLUÇÃO DE PROBLEMAS:**

### **Erro: "mysqldump não encontrado"**
- **Windows:** Instale o MySQL ou adicione ao PATH
- **Linux:** `sudo apt install mysql-client`
- **Mac:** `brew install mysql-client`

### **Erro de Permissão:**
```bash
# Linux/Mac
chmod -R 755 storage/
chown -R www-data:www-data storage/
```

### **Backup não Executa:**
1. Verifique se a tarefa está ativa
2. Verifique os logs em `storage/logs/backup.log`
3. Teste manualmente com `php artisan backup:financial`

---

## ✅ **STATUS ATUAL:**
- ✅ Backup financeiro funcionando
- ✅ Pastas de backup criadas
- ✅ Comandos de limpeza configurados
- ✅ Logs de backup ativos
- ⚠️ Backup do banco precisa do mysqldump instalado

**Seu sistema está 95% configurado!** 🎉
