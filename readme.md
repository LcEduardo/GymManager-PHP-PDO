# Gym Management Project

Aplicação PHP para gerenciamento de academia com cadastro de alunos, controle de planos, autenticação administrativa, dashboard financeiro, exportação em PDF e automações com `n8n`.

O projeto utiliza `PDO` com PostgreSQL e hoje já segue o padrão MVC no fluxo principal da aplicação. Os arquivos antigos foram mantidos em `legacy/` apenas para estudo e comparação de evolução.

## Funcionalidades atuais

- login administrativo com sessão
- cadastro de alunos
- listagem de alunos no dashboard
- edição de dados e plano do aluno
- exclusão de aluno
- indicadores financeiros
- exportação de relatório em PDF
- automações de ciclos de pagamento com `n8n`

## Stack

- PHP
- PDO
- PostgreSQL
- Docker Compose
- n8n
- Dompdf
- Composer com autoload `PSR-4`

## Project structure

O projeto segue uma organização baseada em MVC, com front controller em `public/index.php`, controllers em `src/Controller`, views em `views` e exemplos antigos separados em `legacy/`:

```text
.
|-- public/
|   |-- css/                    # arquivos CSS públicos
|   |-- img/                    # imagens e ícones públicos
|   `-- index.php               # front controller e roteador principal
|-- src/
|   |-- Controller/             # controllers da aplicação
|   |-- Domain/                 # entidades de domínio
|   |-- Infra/                  # conexão e infraestrutura
|   `-- Repository/             # acesso a dados com PDO
|-- views/
|   |-- auth/                   # telas de autenticação
|   |-- dashboard/              # view do dashboard
|   |-- financial/              # view do financeiro
|   `-- users/                  # telas de usuário
|-- partials/                   # trechos reutilizáveis das views
|-- n8n/
|   `-- workflows/              # fluxos importáveis do n8n
|-- legacy/                     # arquivos antigos preservados para estudo
|-- index.php                   # compatibilidade para servir pela raiz
|-- init.sql                    # schema inicial + seed do admin master
|-- docker-compose.yml          # PostgreSQL + n8n
`-- README.md                   # documentação principal do projeto
```

## MVC atual

### Model

A camada de dados e regras de negócio está distribuída em:

- `src/Domain`: entidades como `User`, `Plan` e `UserSubscription`
- `src/Repository`: consultas e persistência com PDO
- `src/Infra`: conexão com banco

### View

As telas renderizadas ficam principalmente em:

- `views/auth/login.php`
- `views/dashboard/index.php`
- `views/financial/index.php`
- `views/users/create.php`
- `views/users/edit.php`
- `views/users/pdf.php`
- `partials/app-header.php`
- `public/css/*.css`

### Controller

Os controllers recebem a requisição, validam os dados e chamam os repositórios:

- `src/Controller/AuthController.php`
- `src/Controller/DashboardController.php`
- `src/Controller/FinancialController.php`
- `src/Controller/UserController.php`

### Observação importante

O fluxo principal já está centralizado em MVC. O arquivo [public/index.php](C:\Users\LucasEduardo\Desktop\php\projeto_pdo\public\index.php) funciona como front controller, distribui as rotas para os controllers e mantém a aplicação protegida por sessão.

Os arquivos antigos foram movidos para `legacy/` somente para estudo. Eles não fazem mais parte do fluxo ativo da aplicação.

## Dependencias do controller

Nem toda dependencia usada em uma ação precisa ficar no `__construct()` do controller.

Regra pratica adotada no projeto:

- dependencias usadas por varias ações do controller podem ficar no construtor
- dependencias usadas apenas em uma ação especifica podem ser instanciadas dentro do proprio método

Exemplo com o PDF:

- `UserRepository`, `PlanRepository` e `SubscriptionRepository` fazem sentido no construtor do `UserController`, porque são usados em vários fluxos como cadastro, edicão e atualização
- `Dompdf` so é necessário na geração do PDF, então pode ser criado apenas em `downloadPdf()`

Essa separacao reduz acoplamento, evita inicializacao desnecessaria em rotas que nao geram PDF e deixa mais claro quais dependencias pertencem ao controller inteiro e quais pertencem apenas a uma funcionalidade especifica.

## Fluxo principal

### Login administrativo

1. A rota `/` ou `/login` exibe a tela de login.
2. O e-mail informado é buscado na tabela `adms`.
3. A senha é validada com `password_verify()`.
4. Uma sessão de administrador é criada.
5. As rotas protegidas passam a exigir autenticação.

Administrador inicial:

- nome: `Administrador Principal`
- e-mail: `admin@gymmanager.local`
- senha: `admin123`

Esse usuário master é criado diretamente pelo `init.sql` na primeira inicialização do banco.

### Cadastro de usuário

1. O formulário é exibido por `UserController::create()`.
2. O envio `POST /register` é tratado por `UserController::store()`.
3. A senha do aluno é armazenada com hash.
4. O usuário é salvo na tabela `users`.
5. Um ciclo inicial é criado em `users_plans`.

### Dashboard

O dashboard administrativo exibe:

- total de alunos ativos
- receita mensal
- quantidade de usuários no plano premium
- assinaturas vencidas ou vencendo
- tabela com usuários cadastrados

### Financeiro

A tela financeira trabalha com os status:

- `paid`
- `pending`
- `vencido`

## Rotas atuais

| Rota | Método | Responsável |
|---|---|---|
| `/` | `GET` | login administrativo |
| `/login` | `GET` | login administrativo |
| `/login` | `POST` | autenticação do administrador |
| `/logout` | `GET` | logout |
| `/adm` | `GET` | `DashboardController::index()` |
| `/register` | `GET` | `UserController::create()` |
| `/register` | `POST` | `UserController::store()` |
| `/edit` | `GET` | `UserController::edit()` |
| `/update` | `POST` | `UserController::update()` |
| `/delete` | `GET` | `UserController::destroy()` |
| `/financial` | `GET` | `FinancialController::index()` |
| `/download` | `GET` | `UserController::downloadPdf()` |

## Banco de dados

O `init.sql` cria as tabelas:

- `users`
- `plans`
- `users_plans`
- `adms`

### `users`

- `id`
- `full_name`
- `email`
- `password`
- `phone`
- `birth_date`
- `created_at`
- `status`

### `plans`

- `id`
- `name`
- `durantio_days`
- `description`
- `active`
- `price`

### `users_plans`

- `id`
- `user_id`
- `plan_id`
- `start_date`
- `end_date`
- `payment_status`

Status aceitos:

- `pending`
- `paid`
- `vencido`

### `adms`

- `id`
- `name`
- `email`
- `password`

## n8n

O projeto usa `n8n` para automatizar os ciclos de pagamento e manter o histórico de assinaturas consistente.

Fluxos disponíveis em `n8n/workflows`:

- `gymGatewayPaid.json`
- `payment-received-webhook.json`
- `generate-next-cycle-cron.json`
- `expire-subscriptions-cron.json`

### Comportamento esperado

- quando um ciclo é pago, ele pode ser marcado como `paid`
- o próximo ciclo pode ser criado automaticamente como `pending`
- ciclos pendentes vencidos podem ser marcados como `vencido`

Essas automações impactam diretamente:

- receita mensal do dashboard
- contador de vencimentos
- filtros da tela financeira

### Como importar

1. Suba os containers com `docker compose up -d`.
2. Acesse o n8n em `http://localhost:5678`.
3. Crie uma credencial Postgres apontando para o mesmo banco da aplicação.
4. Importe os arquivos JSON de `n8n/workflows`.
5. Associe a credencial nos nós Postgres e ative os fluxos.

## Variáveis de ambiente

Crie o arquivo `.env` com base em `.env.example`.

### Banco

```env
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=gym
DB_USERNAME=gym_user
DB_PASSWORD=gym_password
```

### n8n

```env
N8N_DB_SCHEMA=n8n
N8N_HOST=localhost
N8N_PORT=5678
N8N_PROTOCOL=http
N8N_WEBHOOK_URL=http://localhost:5678/
N8N_EDITOR_BASE_URL=http://localhost:5678
N8N_BASIC_AUTH_ACTIVE=true
N8N_BASIC_AUTH_USER=admin
N8N_BASIC_AUTH_PASSWORD=admin123
N8N_ENCRYPTION_KEY=change_this_to_a_long_random_string
```

## Como executar

### 1. Instalar dependências

```bash
composer install
```

### 2. Criar o `.env`

Use `.env.example` como base.

### 3. Subir os containers

```bash
docker compose up -d
```

Na primeira inicialização, o PostgreSQL executa o `init.sql`, criando schema, planos iniciais e o administrador master.

Se o volume do banco já existia antes de alguma alteração no `init.sql`, recrie o volume ou aplique manualmente as mudanças no banco.

### 4. Iniciar o servidor PHP

```bash
php -S localhost:8000 index.php
```

### 5. Acessar

- aplicação: `http://localhost:8000`
- n8n: `http://localhost:5678`

## Estado atual do projeto

### O que já está consistente

- conexão PDO centralizada
- autenticação administrativa com sessão
- fluxo principal em MVC
- repositórios com prepared statements
- integração com PostgreSQL via Docker
- automações n8n separadas em workflows importáveis

### O que ainda pode evoluir

- padronizar encoding UTF-8 em todos os arquivos
- expor planos dinamicamente nas telas
- adicionar testes automatizados
- ampliar regras de validação

## Resumo

O projeto já funciona como uma base real de gestão de academia e agora possui uma documentação única na raiz, alinhada com a estrutura MVC atual e com os arquivos legados isolados para estudo.
