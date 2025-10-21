# 🚀 Guia de Deploy na Hostinger - Esmalteria Vida Maria

## 📋 Pré-requisitos

- ✅ Conta na Hostinger
- ✅ Plano com suporte a PHP 8.0+
- ✅ Acesso SSH (recomendado)
- ✅ MySQL Database

---

## 🔧 Passo a Passo

### **1. Preparar o Projeto Localmente**

```bash
# Otimizar autoload
composer install --optimize-autoloader --no-dev

# Compilar assets para produção
npm run build

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

### **2. Configurar .env para Produção**

Crie um arquivo `.env` na Hostinger com:

```env
APP_NAME="Esmalteria Vida Maria"
APP_ENV=production
APP_KEY=  # Gerar com: php artisan key:generate
APP_DEBUG=false
APP_URL=https://seudominio.com.br

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seu_banco_vida_maria
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@seudominio.com
MAIL_PASSWORD=sua_senha_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu_email@seudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

### **3. Upload dos Arquivos**

#### **Via FTP/SFTP:**

1. **Conecte via FTP** (FileZilla, WinSCP)
2. **Navegue até:** `public_html/`
3. **Crie pasta:** `vida-maria/`
4. **Upload TUDO exceto:**
   - ❌ `node_modules/`
   - ❌ `.git/`
   - ❌ `storage/` (criar vazio no servidor)
   - ❌ `.env` (criar manualmente no servidor)

#### **Via SSH (Recomendado):**

```bash
# Conectar SSH
ssh usuario@servidor.hostinger.com

# Ir para public_html
cd public_html

# Clonar ou fazer upload
# ... fazer upload via git ou scp
```

---

### **4. Configurar Permissões**

```bash
# Dar permissão de escrita
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Garantir ownership correto
chown -R usuario:usuario storage
chown -R usuario:usuario bootstrap/cache
```

---

### **5. Configurar Banco de Dados**

No **cPanel da Hostinger:**

1. **MySQL Databases** → Criar novo banco
2. **Criar usuário** MySQL
3. **Dar todas permissões** ao usuário no banco
4. **Anotar credenciais** para o `.env`

Depois via **SSH ou phpMyAdmin:**

```bash
# Via SSH
php artisan migrate --force
php artisan db:seed --force

# Criar usuário proprietária
php artisan tinker
```

No tinker:
```php
\App\Models\User::create([
    'name' => 'Proprietária', 
    'email' => 'admin@vidamaria.com', 
    'password' => bcrypt('SuaSenhaMuitoForte123!'), 
    'tipo' => 'proprietaria'
]);
exit
```

---

### **6. Configurar Document Root**

No **Hostinger cPanel:**

1. Vá em **Website** → **Configurações**
2. **Document Root** deve apontar para: `public_html/vida-maria/public`
3. **Salvar**

Ou criar arquivo `.htaccess` na raiz:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

### **7. Otimizações para Produção**

```bash
# Cache de configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoload
composer dump-autoload --optimize
```

---

### **8. Configurar HTTPS**

Na **Hostinger:**

1. **SSL/TLS** → **Ativar SSL gratuito**
2. Esperar 1-5 minutos
3. **Forçar HTTPS** nas configurações

Adicionar no `.htaccess` (dentro de `public/`):

```apache
# Forçar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

### **9. Configurar PWA na Hostinger**

**IMPORTANTE:** Certifique-se que estes arquivos estão acessíveis:

- ✅ `public/manifest.json`
- ✅ `public/service-worker.js`
- ✅ `public/logo.svg`

**Teste PWA:**
1. Acesse via HTTPS
2. Chrome DevTools → Application → Manifest
3. Verificar se manifest carrega
4. Service Worker deve estar registrado

---

### **10. Criar Ícones PNG (Recomendado)**

Para melhor compatibilidade, crie ícones PNG:

#### **Opção A: Converter SVG → PNG Online**
- Acesse: https://cloudconvert.com/svg-to-png
- Upload `logo.svg`
- Criar: 192x192, 512x512, 1024x1024

#### **Opção B: Usar SVG direto**
- A Hostinger aceita SVG, mas PNG é mais compatível

Salvar como:
- `public/icon-192.png`
- `public/icon-512.png`
- `public/icon-maskable.png`

---

### **11. Testar PWA**

**No Chrome Desktop:**
1. Abra o site em HTTPS
2. DevTools (F12) → **Application**
3. **Manifest** → Verificar carregamento
4. **Service Workers** → Deve estar ativo
5. **Install** → Testar instalação

**No Mobile:**
1. Abra no Chrome/Safari
2. Menu → **Adicionar à tela inicial**
3. App deve abrir em tela cheia
4. Testar offline (modo avião)

---

### **12. Otimizações Finais na Hostinger**

#### **Cache de Opcodes (PHP)**
No `php.ini` ou `.htaccess`:
```
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

#### **Compressão Gzip**
Adicionar em `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

#### **Cache de Navegador**
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

### **13. Segurança**

Criar arquivo `public/.htaccess`:

```apache
Options -Indexes

<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Forçar HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Redirecionar para index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Proteção de arquivos sensíveis
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

### **14. Backup Automático**

Configure no cPanel da Hostinger:
1. **Backups** → Ativar backup automático
2. Frequência: Diária
3. Incluir: Arquivos + Banco de dados

---

### **15. Monitoramento**

**Logs de Erro:**
- Laravel: `storage/logs/laravel.log`
- PHP: Verificar no cPanel → Logs

**Monitorar:**
- Espaço em disco
- Uso de CPU/RAM
- Erros 500

---

## 🎯 Checklist Final

- [ ] Arquivos uploaded
- [ ] `.env` configurado
- [ ] Permissões corretas (775)
- [ ] Banco de dados criado e migrado
- [ ] Usuário proprietária criado
- [ ] SSL/HTTPS ativado
- [ ] Document root configurado
- [ ] Cache otimizado
- [ ] PWA testado (manifest + service worker)
- [ ] Ícones PNG criados (192, 512, 1024)
- [ ] Teste em mobile real
- [ ] Backup configurado

---

## 📱 Recursos PWA Implementados

✅ **Instalável** - Adicionar à tela inicial
✅ **Offline** - Funciona sem internet (cache)
✅ **Rápido** - Cache de assets
✅ **Responsivo** - Mobile-first
✅ **Seguro** - HTTPS obrigatório
✅ **Engajamento** - Notificações push (preparado)
✅ **Atalhos** - Agenda, Novo Agendamento, Financeiro

---

## 🌐 URLs Importantes

**Depois do Deploy:**
- Site: https://seudominio.com.br
- Admin: https://seudominio.com.br/login
- API (futura): https://seudominio.com.br/api

---

## 🆘 Troubleshooting

### Erro 500
- Verificar permissões storage/
- Checar .env
- Verificar logs: `storage/logs/laravel.log`

### Manifest não carrega
- Verificar HTTPS
- Checar caminho: `/manifest.json`
- Content-Type deve ser `application/json`

### Service Worker não registra
- HTTPS é obrigatório
- Verificar console do navegador
- Limpar cache do navegador

### Assets não carregam
- Rodar: `npm run build`
- Verificar `public/build/` existe
- Checar permissões

---

## 📞 Suporte Hostinger

- Chat: 24/7
- Tutoriais: https://www.hostinger.com.br/tutoriais
- Fórum: Comunidade Hostinger

---

**Desenvolvido com ❤️ para Esmalteria Vida Maria**
Sistema PWA Premium - Padrão Ouro ✨

