<div align="center">

<img src="resources\js\components\ApplicationLogo.vue" alt="SolidariSys Logo" width="180" />

# SolidariSys

**Sistema de Gerenciamento de Doações para ONGs**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-6C50CA?style=flat-square)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-3.x-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](LICENSE)

</div>

---

## Sobre o Projeto

O **SolidariSys** é uma aplicação web desenvolvida para auxiliar organizações não governamentais (ONGs) no controle e rastreamento de doações recebidas. Por meio de uma interface moderna e intuitiva, o sistema permite que colaboradores cadastrem doadores, registrem itens doados por categoria e acompanhem o histórico completo de doações — tudo em um único painel.

O projeto nasceu da necessidade de substituir planilhas manuais e processos descentralizados por uma solução digital que ofereça rastreabilidade, controle de acesso por perfil de usuário e relatórios exportáveis em PDF.

### Funcionalidades Principais

- **Autenticação e controle de acesso** com três níveis de perfil: Administrador, Moderador e Usuário Comum
- **Cadastro e gestão de doadores** com dados de contato (nome, CPF, e-mail, telefone)
- **Registro de doações** vinculadas a doador, item e responsável pelo lançamento
- **Catálogo de itens por categoria** para padronizar os registros
- **Dashboard com indicadores** de doações recentes e totais
- **Exportação de relatórios** em PDF via jsPDF
- **Interface responsiva** com suporte a tema claro/escuro

---

## Stack Tecnológica

| Camada      | Tecnologia                                      |
|-------------|--------------------------------------------------|
| Backend     | PHP 8.2+, Laravel 12, Laravel Breeze, Sanctum   |
| Frontend    | Vue.js 3, TypeScript, Inertia.js, Vite           |
| Estilização | Tailwind CSS, Reka UI, Radix Vue                 |
| Banco       | SQLite (dev) / MySQL ou PostgreSQL (produção)    |
| Gráficos    | Chart.js, vue-chartjs                            |
| PDF         | jsPDF, jsPDF-AutoTable                           |
| Testes      | PestPHP                                          |

---

## Estrutura de Pastas

```
SolidariSys/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Lógica de requisição (Auth, Doação, Doador, Usuário...)
│   │   ├── Middleware/        # Middlewares (Inertia, autenticação...)
│   │   └── Requests/          # Form Requests com validação
│   ├── Models/                # Eloquent Models (User, Doacao, Doador, Item, Categoria...)
│   └── Providers/
├── backend/                   # (Pasta reservada para APIs ou microserviços futuros)
├── database/
│   ├── migrations/            # Migrações do banco de dados
│   ├── seeders/               # Seeds iniciais
│   └── database.sqlite        # Banco SQLite para desenvolvimento local
├── docs/                      # Documentação do projeto
│   └── Documento_de_Visao.pdf
├── frontend/                  # (Pasta reservada para build/deploy do frontend separado)
├── resources/
│   └── js/
│       ├── components/        # Componentes Vue reutilizáveis (DataGrid, Dialogs, Header...)
│       └── pages/             # Páginas Inertia (Dashboard, Doacao, CadastroDoador...)
├── routes/
│   ├── web.php                # Rotas principais da aplicação
│   ├── auth.php               # Rotas de autenticação
│   └── settings.php           # Rotas de configurações de perfil
├── .env.example               # Variáveis de ambiente necessárias
├── .gitignore
├── composer.json
├── package.json
└── vite.config.ts
```

---

## Pré-requisitos

Antes de iniciar, certifique-se de ter os seguintes itens instalados:

- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/) >= 2.x
- [Node.js](https://nodejs.org/) >= 18.x e npm >= 9.x
- [Git](https://git-scm.com/)
- SQLite (incluso no PHP) **ou** MySQL/PostgreSQL para produção

---

## Instalação e Configuração

### 1. Clonar o repositório

```bash
git clone https://github.com/sua-organizacao/solidarisys.git
cd solidarisys
```

### 2. Instalar dependências PHP

```bash
composer install
```

### 3. Instalar dependências JavaScript

```bash
npm install
```

### 4. Configurar as variáveis de ambiente

Copie o arquivo de exemplo e ajuste as variáveis conforme seu ambiente:

```bash
cp .env.example .env
```

Edite o `.env` com as configurações do banco de dados e e-mail (veja a seção [Variáveis de Ambiente](#variáveis-de-ambiente)).

### 5. Gerar a chave da aplicação

```bash
php artisan key:generate
```

### 6. Executar as migrações

Para SQLite (padrão de desenvolvimento):

```bash
touch database/database.sqlite
php artisan migrate
```

Para MySQL/PostgreSQL, ajuste as variáveis `DB_*` no `.env` antes de migrar.

### 7. Iniciar o servidor de desenvolvimento

```bash
composer run dev
```

Isso inicia simultaneamente o servidor PHP (`php artisan serve`), o worker de filas e o Vite para hot-reload do frontend.

Acesse: [http://localhost:8000](http://localhost:8000)

---

## Variáveis de Ambiente

O arquivo `.env.example` contém todas as variáveis necessárias. As principais são:

```dotenv
# Aplicação
APP_NAME=SolidariSys
APP_ENV=local                  # local | production
APP_KEY=                       # gerado via artisan key:generate
APP_DEBUG=true                 # false em produção
APP_URL=http://localhost

# Banco de Dados
DB_CONNECTION=sqlite           # sqlite | mysql | pgsql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=solidarisys
# DB_USERNAME=root
# DB_PASSWORD=

# E-mail (configurar SMTP em produção)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@solidarisys.org"
MAIL_FROM_NAME="${APP_NAME}"

# Cache e Filas
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

> ⚠️ **Nunca versione o arquivo `.env`** com dados reais. Apenas o `.env.example` deve estar no repositório.

---

## Banco de Dados

O modelo de dados do SolidariSys é composto pelas seguintes tabelas:

```
tipo_usuarios       → Perfis de acesso (Admin, Moderador, Usuário Comum)
users               → Usuários do sistema (vinculados a um tipo_usuario)
categorias          → Categorias de itens doados (ex: Alimentos, Roupas)
itens               → Itens doáveis vinculados a uma categoria
doadores            → Cadastro de doadores (nome, CPF, e-mail, telefone)
doacoes             → Registro de cada doação (item + doador + usuário + quantidade + data)
```

Para visualizar o diagrama ER completo, consulte `/docs/Documento_de_Visao.pdf`.

---

## Perfis de Usuário

| Perfil          | `tipo_usuario_id` | Permissões                                         |
|-----------------|-------------------|----------------------------------------------------|
| Administrador   | 1                 | Acesso total: usuários, doadores, itens, doações   |
| Moderador       | 2                 | Gerencia doadores, itens e doações                 |
| Usuário Comum   | 3                 | Visualiza e registra doações                       |

---

## Scripts Disponíveis

```bash
# Iniciar ambiente de desenvolvimento completo
composer run dev

# Apenas frontend (Vite)
npm run dev

# Build de produção do frontend
npm run build

# Rodar os testes
php artisan test
# ou
./vendor/bin/pest

# Formatar o código PHP
./vendor/bin/pint

# Lint do JavaScript/Vue
npm run lint
```

---

## Documentação

A pasta `/docs` contém:

- `Documento_de_Visao.pdf` — Visão geral do produto, objetivos, escopo, stakeholders e requisitos funcionais

---

## Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas alterações com mensagens descritivas (`git commit -m "feat: adiciona exportação de doações por período"`)
4. Envie para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

Siga o padrão [Conventional Commits](https://www.conventionalcommits.org/pt-br/) nas mensagens de commit.

---

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

<div align="center">
  Desenvolvido com ❤️ para apoiar ONGs e organizações sociais.
</div>