# Vida Maria Esmalteria

Sistema de gestão para esmalteria desenvolvido em Laravel.

## 🚀 Funcionalidades

- **Gestão de Profissionais**: Cadastro e controle de profissionais
- **Gestão de Clientes**: Cadastro e histórico de clientes
- **Agendamentos**: Sistema completo de agendamentos
- **Serviços**: Catálogo de serviços oferecidos
- **Financeiro**: Controle de pagamentos e comissões
- **Backup**: Sistema de backup e reset do banco de dados
- **Relatórios**: Dashboard com estatísticas e relatórios

## 🛠️ Tecnologias

- **Backend**: Laravel 10
- **Frontend**: Blade Templates + Tailwind CSS
- **Banco de Dados**: MySQL
- **Autenticação**: Laravel Breeze

## 📋 Pré-requisitos

- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js e NPM

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/GrowandBuild/vidamaria.git
cd vidamaria
```

2. Instale as dependências:
```bash
composer install
npm install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vidamaria
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. Execute as migrações e seeders:
```bash
php artisan migrate
php artisan db:seed
```

6. Compile os assets:
```bash
npm run build
```

7. Inicie o servidor:
```bash
php artisan serve
```

## 👥 Contas Padrão

- **Proprietária**: val@vidamaria.com.br / admin123
- **Desenvolvedor**: alexandre@dev.com / dev123

## 📱 Acesso

Acesse: `http://127.0.0.1:8000`

## 🔒 Segurança

- Sistema de permissões por tipo de usuário
- Middleware de segurança para operações críticas
- Validação de dados em todas as entradas
- Backup automático antes de operações de reset

## 📄 Licença

Este projeto é privado e pertence à Vida Maria Esmalteria.

## 👨‍💻 Desenvolvedor

Desenvolvido por Alexandre - Grow and Build