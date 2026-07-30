# RapidTask

Plataforma web de **gerenciamento de projetos e tarefas** para equipes que precisam organizar trabalho, clientes e colaboradores em um só lugar.

## Sobre o projeto

O RapidTask é um painel administrativo com autenticação obrigatória, organizado em torno de **times**. Usuários participam de equipes, gerenciam clientes e projetos, acompanham tarefas com prazos e prioridades, colaboram via comentários e recebem notificações por e-mail.

Em resumo: um sistema de gestão de projetos estilo mini-Jira/Trello, com tarefas, clientes, projetos, colaboração e auditoria de atividades.

## Stack tecnológica

| Camada | Tecnologias |
|--------|-------------|
| Backend | Laravel 5.7, PHP 7.1+ |
| Frontend | Blade, Vue 2, Bootstrap 4, Sass |
| Build | Laravel Mix, Webpack |
| Banco de dados | MySQL |
| Autenticação | Sistema padrão do Laravel |

## Funcionalidades

| Módulo | Descrição |
|--------|-----------|
| **Dashboard** | Visão geral de tarefas, projetos, clientes e últimas atividades |
| **Tarefas** | CRUD, arquivar, recuperar, relatórios, comentários e histórico de alterações |
| **Projetos** | CRUD, anotações, upload de arquivos, datas de início e fim |
| **Clientes** | Cadastro e gestão de clientes vinculados a projetos |
| **Times** | Criação de equipes, convite de membros (aceitar/recusar por e-mail), níveis de permissão |
| **Perfil** | Atualização de dados do usuário (inclui avatar) |
| **Logs** | Registro de ações do sistema e visualizador de logs do Laravel |

## Fluxo principal

1. Usuário se cadastra ou faz login.
2. Participa de um ou mais **times**.
3. Dentro do time, gerencia **clientes** → **projetos** → **tarefas** (prioridade, situação, tipo, prazos e tempo estimado).
4. Colabora via **comentários** nas tarefas e acompanha o **histórico** de mudanças.
5. Recebe **e-mails** (boas-vindas, convites para time, etc.).

## Rotas principais

| Rota | Descrição |
|------|-----------|
| `/` | Página inicial |
| `/login` | Autenticação |
| `/admin/dashboard` | Painel principal (requer login) |
| `/admin/tarefas` | Gestão de tarefas |
| `/admin/projetos` | Gestão de projetos |
| `/admin/clientes` | Gestão de clientes |
| `/admin/times` | Gestão de times |
| `/logs` | Visualizador de logs do Laravel |

## Requisitos

### Instalação local

- PHP >= 7.1.3
- Composer
- Node.js e npm
- MySQL

### Docker

- Docker
- Docker Compose

## Instalação com Docker (recomendado)

```bash
# Clonar o repositório e entrar na pasta
cd rapidtask

# Subir os containers (app, nginx e mysql)
docker compose up -d --build

# Popular tabelas auxiliares (situações, tipos, prioridades, etc.)
docker compose exec app php artisan db:seed
```

Acesse `http://localhost:8080` e faça login para entrar no painel em `/admin/dashboard`.

### Serviços Docker

| Serviço | Descrição | Porta |
|---------|-----------|-------|
| `nginx` | Servidor web | `8080` |
| `app` | PHP-FPM (Laravel) | — |
| `mysql` | Banco de dados | `3307` |

### Comandos úteis

```bash
# Ver logs
docker compose logs -f

# Parar containers
docker compose down

# Parar e remover volumes (apaga o banco)
docker compose down -v

# Executar comandos Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Recompilar assets frontend
docker compose --profile assets run --rm node sh -c "npm install && npm run dev"
```

As variáveis de ambiente do banco são definidas no `docker-compose.yml` e sobrescrevem o `.env` dentro dos containers.

## Instalação local

```bash
# Clonar o repositório e entrar na pasta
cd rapidtask

# Instalar dependências PHP
composer install

# Instalar dependências frontend
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados no .env e rodar migrations
php artisan migrate

# Compilar assets
npm run dev

# Subir o servidor
php artisan serve
```

Acesse `http://localhost:8000` e faça login para entrar no painel em `/admin/dashboard`.

## CSS API — Botões

### Classes

| Classe | Descrição | Variáveis |
|--------|-----------|-----------|
| `button` | Estilo padrão do botão | — |
| `button-{cor}` | Cor do botão | `green`, `blue`, `yellow`, `red`, `orange` |
| `button-{tamanho}` | Tamanho do botão | `small`, `large`, `large-ext`, `block`, `disabled` |

### Exemplos

```html
<button class="button">Texto</button>
<button class="button button-orange">Texto</button>
<button class="button button-small">Texto</button>
<button class="button button-orange button-small">Texto</button>
```

## Licença

MIT
