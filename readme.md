# Projeto Academia com PDO

Aplicação PHP para gerenciamento básico de alunos de academia, com cadastro de usuários, vínculo com planos, dashboard administrativo, visão financeira e exportação em PDF.

O projeto foi construído com foco em estudo de `PDO`, organização em camadas simples (`Domain`, `Repository`, `Infra`) e evolução gradual de `SQLite` para `PostgreSQL`.

## Objetivo atual

Até este ponto, o sistema já permite:

- cadastrar alunos
- listar alunos no painel administrativo
- editar dados do aluno
- alterar o plano atual do aluno
- excluir aluno
- gerar PDF com a lista de usuários
- visualizar indicadores financeiros e status das assinaturas
- integrar ciclos de pagamento com `n8n`

## Stack

- PHP
- PDO
- PostgreSQL
- Docker Compose
- n8n
- Dompdf
- Composer com autoload `PSR-4`

## Estrutura do projeto

```text
.
├── css/                       # estilos das telas
├── img/                       # ícones usados na interface
├── n8n/                       # documentação e workflows importáveis
├── src/
│   ├── Domain/                # entidades de negócio
│   ├── Infra/                 # conexão com banco
│   └── Repository/            # acesso a dados
├── adm.php                    # dashboard principal
├── register-user.php          # cadastro de aluno
├── edit.php                   # formulário de edição
├── update.php                 # persistência da edição
├── delete.php                 # exclusão de usuário
├── financial.php              # visão financeira
├── pdf.php                    # template HTML do PDF
├── download-pdf.php           # geração do PDF
├── index.php                  # roteador simples
├── init.sql                   # criação inicial do banco
├── docker-compose.yml         # PostgreSQL + n8n
└── .env.example               # variáveis de ambiente de exemplo
```

## Arquitetura atual

### `src/Infra`

- `Connection.php`: centraliza a criação da conexão PDO.
- Lê variáveis do arquivo `.env`.
- Suporta `pgsql` e `sqlite`.
- Hoje o fluxo principal do projeto está preparado para `PostgreSQL`.

### `src/Domain`

Representa os objetos de negócio da aplicação:

- `User`
- `Plan`
- `UserSubscription`
- `Professional`

Observação: a entidade `Professional` já existe no código, mas ainda não está integrada ao fluxo principal da aplicação nem ao `init.sql`.

### `src/Repository`

Responsável pela comunicação com o banco:

- `UserRepository`
- `PlanRepository`
- `SubscriptionRepository`
- `ProfessionalRepository`

## Fluxo principal da aplicação

### Cadastro de aluno

Ao cadastrar um aluno:

1. os dados do formulário são recebidos em `register-user.php`
2. a senha é armazenada com `password_hash`
3. o usuário é salvo na tabela `users`
4. o plano selecionado é buscado na tabela `plans`
5. é criado um ciclo inicial em `users_plans` com status `pending`

### Dashboard

O painel em `adm.php` exibe:

- total de alunos ativos
- receita mensal baseada em assinaturas `paid`
- quantidade de alunos com plano premium
- quantidade de assinaturas vencidas ou vencendo hoje
- tabela com todos os usuários cadastrados

### Financeiro

A tela `financial.php` lista assinaturas com filtro por status:

- `todos`
- `paid`
- `pending`
- `vencido`

Essa visão cruza usuários, planos e ciclos de assinatura para mostrar a situação atual de cada cobrança.

### PDF

`download-pdf.php` usa `Dompdf` para gerar um relatório em PDF com a lista de usuários.

## Rotas disponíveis

O arquivo `index.php` faz um roteamento simples por URL.

| Rota | Arquivo | Função |
|---|---|---|
| `/` | `adm.php` | dashboard |
| `/adm` | `adm.php` | dashboard |
| `/register` | `register-user.php` | cadastro de aluno |
| `/edit` | `edit.php` | formulário de edição |
| `/update` | `update.php` | atualização de dados |
| `/delete` | `delete.php` | exclusão de aluno |
| `/download` | `download-pdf.php` | exportação em PDF |
| `/financial` | `financial.php` | visão financeira |

## Banco de dados

### Tabelas criadas em `init.sql`

#### `users`

Armazena os dados dos alunos:

- `id`
- `full_name`
- `email`
- `password`
- `phone`
- `birth_date`
- `created_at`
- `status`

### `plans`

Armazena os planos disponíveis:

- `id`
- `name`
- `durantio_days`
- `description`
- `active`
- `price`

Observação: o nome da coluna está como `durantio_days` no SQL atual. A documentação mantém esse nome porque é assim que o banco está definido hoje.

### `users_plans`

Representa os ciclos de assinatura de cada aluno:

- `id`
- `user_id`
- `plan_id`
- `start_date`
- `end_date`
- `payment_status`

Status aceitos atualmente:

- `pending`
- `paid`
- `vencido`

## Planos iniciais

O `init.sql` já popula os seguintes planos:

- `Basic`
- `Premium`
- `VIP`

No formulário atual da interface, apenas `Basic` e `Premium` aparecem para seleção.

## Variáveis de ambiente

Crie um arquivo `.env` na raiz com base no `.env.example`.

### Banco

```env
DB_DRIVER=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
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

## Como executar o projeto

### 1. Instalar dependências

```bash
composer install
```

### 2. Criar o arquivo de ambiente

Use o `.env.example` como base para criar o `.env`.

### 3. Subir os containers

```bash
docker compose up -d
```

Isso sobe:

- PostgreSQL
- n8n

Na primeira inicialização, o `PostgreSQL` executa automaticamente o arquivo `init.sql`.

### 4. Rodar o servidor PHP

Exemplo com servidor embutido do PHP:

```bash
php -S localhost:8000
```

Depois, acesse:

- aplicação: `http://localhost:8000`
- n8n: `http://localhost:5678`

## n8n

O projeto usa `n8n` para automatizar o histórico de ciclos de pagamento.

Os workflows prontos estão em `n8n/workflows`:

- `gymGatewayPaid.json`
- `payment-received-webhook.json`
- `generate-next-cycle-cron.json`
- `expire-subscriptions-cron.json`

### Comportamento esperado

- quando um ciclo é pago, ele pode ser marcado como `paid`
- o próximo ciclo pode ser criado automaticamente como `pending`
- ciclos pendentes vencidos podem ser marcados como `vencido`

Isso impacta diretamente:

- receita mensal do dashboard
- contador de vencimentos
- filtros da tela financeira

## Situação atual do projeto

### O que já está consistente

- conexão centralizada com PDO
- separação básica entre domínio, infraestrutura e acesso a dados
- uso de prepared statements nos repositórios
- hash de senha no cadastro
- integração com PostgreSQL via Docker
- automações com n8n pensadas para ciclos recorrentes

### Pontos em evolução

- não há suíte de testes automatizados
- a entidade `Professional` ainda não está conectada ao schema principal
- existem alguns textos com problema de acentuação em arquivos HTML e no projeto
- o campo `VIP` existe no banco, mas ainda não está exposto no formulário atual
- o projeto usa um roteador simples em `index.php`, sem framework

## Possíveis próximos passos

- padronizar encoding UTF-8 em todos os arquivos
- adicionar validações de domínio e mensagens de erro mais claras
- incluir tela e tabela para profissionais
- expor todos os planos ativos no formulário a partir do banco
- criar testes para repositórios e fluxos principais
- separar melhor camadas de apresentação e regras de negócio

## Resumo

Este projeto já funciona como uma base real de administração de alunos para academia, com cadastro, assinatura, financeiro, PDF e automações externas via n8n. Ele ainda está em fase de evolução, mas já tem uma estrutura boa para continuar crescendo de forma organizada.
