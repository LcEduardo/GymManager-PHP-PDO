# Gym Management Project with PDO

PHP application for basic gym member management, including user registration, plan subscriptions, an admin dashboard, a financial view, and PDF export.

The project was built with a focus on learning `PDO`, a simple layered structure (`Domain`, `Repository`, `Infra`), and a PostgreSQL-based architecture.

It now also includes administrator authentication backed by the `adms` table, so the dashboard is protected behind a login screen.

## Current goal

At this stage, the system already allows you to:

- register members
- list members in the admin dashboard
- edit member data
- change the current subscription plan
- delete a member
- generate a PDF with the user list
- view financial indicators and subscription statuses
- integrate payment-cycle automation with `n8n`
- authenticate administrator access before opening the dashboard

## Stack

- PHP
- PDO
- PostgreSQL
- Docker Compose
- n8n
- Dompdf
- Composer with `PSR-4` autoload

## Project structure

```text
.
├── css/                       # original style files
├── img/                       # original UI icons
├── n8n/                       # documentation and importable workflows
├── public/
│   ├── css/                   # public static assets
│   ├── img/                   # public images and icons
│   └── index.php              # recommended front controller
├── src/
│   ├── Domain/                # business entities
│   ├── Infra/                 # database connection
│   └── Repository/            # data access layer
├── adm.php                    # main dashboard
├── register-user.php          # user registration
├── edit.php                   # edit form
├── update.php                 # update persistence
├── delete.php                 # user deletion
├── financial.php              # financial screen
├── pdf.php                    # PDF HTML template
├── download-pdf.php           # PDF generation
├── index.php                  # compatibility entrypoint for root-based serving
├── init.sql                   # initial database setup
├── docker-compose.yml         # PostgreSQL + n8n
└── .env.example               # example environment variables
```

## Public entrypoint and asset strategy

The application now includes a dedicated front controller at `public/index.php`.

Using a `public/` entrypoint is a good practice in PHP projects because it keeps only public files inside the web root. Files such as `src/`, `vendor/`, `.env`, and SQL scripts stay outside direct web access. It also gives the application a single bootstrap point, so Composer autoload is loaded once instead of being repeated in every routed page.

### Approaches adopted

- `public/index.php` is the main router/front controller
- Composer autoload is required once in `public/index.php`
- routed files such as `adm.php`, `register-user.php`, `financial.php`, `edit.php`, `update.php`, `delete.php`, `download-pdf.php`, and `pdf.php` no longer load `vendor/autoload.php` individually
- the root `index.php` was preserved as a compatibility wrapper for environments that still serve the repository root
- static assets are exposed through `public/css` and `public/img`
- the HTML currently references assets with `/public/...` paths so the app works in the existing compatibility setup

### Why keep both `index.php` files

The cleanest setup is to point the web server document root to `public/` and use only `public/index.php`.

However, many local development setups still run the built-in server from the project root. To avoid breaking that workflow, the root `index.php` forwards dynamic requests to `public/index.php`. This keeps routing centralized while maintaining backward compatibility.

### PHP built-in server behavior

When using `php -S`, static files must be served directly instead of being handled by the router. Because of that, both entrypoints check whether the requested path matches a real file. If it does, PHP serves the file directly. Otherwise, the request continues through the router.

This is what prevents asset URLs such as `/public/css/style.css` and `/public/img/icons/edit.png` from returning `404` during local development.

### Recommended direction

For a production-like setup, configure the web server to use `public/` as the document root. That is the preferred architecture because:

- it reduces accidental exposure of internal files
- it keeps static files and the front controller in the expected public location
- it simplifies deployment and web server rules

The compatibility wrapper is kept only to support root-based local serving without breaking the current workflow.

## Current architecture

### `src/Infra`

- `Connection.php`: centralizes PDO connection creation
- reads variables from the `.env` file
- uses `PostgreSQL` as the only supported database

### `src/Domain`

Represents the business objects of the application:

- `User`
- `Plan`
- `UserSubscription`
- `Professional`

Note: the `Professional` entity already exists in the codebase, but it is not yet integrated into the main application flow or `init.sql`.

### `src/Repository`

Responsible for database communication:

- `UserRepository`
- `PlanRepository`
- `SubscriptionRepository`
- `ProfessionalRepository`

## Main application flow

### Administrative login

When the application is opened:

1. the route `/` shows the administrator login form
2. the submitted e-mail is searched in the `adms` table
3. the password is validated with `password_verify()`
4. a PHP session is created for the authenticated administrator
5. the dashboard at `/adm` is released only after successful login

The administrator table bootstrap lives in `adms.sql`, and the initial admin insert is executed by `scripts/seed-admin.php` using `PDO::prepare()` and `password_hash()`.

- name: `Administrador Principal`
- email: `admin@gymmanager.local`
- password: `admin123` for local bootstrap only

The password hash was generated with `PASSWORD_ARGON2ID` instead of `PASSWORD_DEFAULT`.

Why this choice improves security:

- `PASSWORD_ARGON2ID` explicitly uses Argon2id
- Argon2id is memory-hard, which increases brute-force cost
- `PASSWORD_DEFAULT` intentionally varies according to the PHP runtime, so the algorithm is not fixed across environments

### User registration

When registering a user:

1. form data is received in `register-user.php`
2. the password is stored with `password_hash`
3. the user is saved in the `users` table
4. the selected plan is fetched from the `plans` table
5. an initial cycle is created in `users_plans` with status `pending`

### Dashboard

The `adm.php` dashboard displays:

- total active users
- monthly revenue based on `paid` subscriptions
- number of users on the premium plan
- number of subscriptions that are overdue or due today
- a table with all registered users

### Financial

The `financial.php` screen lists subscriptions filtered by status:

- `todos`
- `paid`
- `pending`
- `vencido`

This view combines users, plans, and subscription cycles to show the current state of each charge.

### PDF

`download-pdf.php` uses `Dompdf` to generate a PDF report with the user list.

## Available routes

The application routes requests through `public/index.php`. The root `index.php` currently works as a compatibility wrapper for root-based local serving.

| Route | File | Purpose |
|---|---|---|
| `/` | `views/auth/login.php` | administrator login screen |
| `/login` | `views/auth/login.php` | administrator login screen |
| `/adm` | `adm.php` | dashboard |
| `/register` | `register-user.php` | user registration |
| `/edit` | `edit.php` | edit form |
| `/update` | `update.php` | update data |
| `/delete` | `delete.php` | delete user |
| `/download` | `download-pdf.php` | PDF export |
| `/financial` | `financial.php` | financial view |
| `/logout` | `src/Controller/AuthController.php` | ends administrator session |

## Database

### Tables created in `init.sql`

#### `users`

Stores member data:

- `id`
- `full_name`
- `email`
- `password`
- `phone`
- `birth_date`
- `created_at`
- `status`

### `plans`

Stores available plans:

- `id`
- `name`
- `durantio_days`
- `description`
- `active`
- `price`

Note: the column name is currently `durantio_days` in SQL. This documentation keeps that name because it matches the current database definition.

### `users_plans`

Represents each member subscription cycle:

- `id`
- `user_id`
- `plan_id`
- `start_date`
- `end_date`
- `payment_status`

Currently accepted statuses:

- `pending`
- `paid`
- `vencido`

### `adms`

Stores administrator credentials used to access the system:

- `id`
- `name`
- `email`
- `password`

## Initial plans

`init.sql` already seeds the following plans:

- `Basic`
- `Premium`
- `VIP`

In the current UI form, only `Basic` and `Premium` are available for selection.

## Environment variables

Create a `.env` file in the project root based on `.env.example`.

### Database

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

## How to run the project

### 1. Install dependencies

```bash
composer install
```

### 2. Create the environment file

Use `.env.example` as the base to create `.env`.

### 3. Start the containers

```bash
docker compose up -d
```

This starts:

- PostgreSQL
- n8n

On the first startup, PostgreSQL automatically runs `init.sql`.

Docker now also mounts `adms.sql` into the PostgreSQL initialization directory so the administrator table is created on a fresh database volume.

After the table exists, run the admin seed script:

```bash
php scripts/seed-admin.php
```

### 4. Run the PHP server

Recommended with the PHP built-in server:

```bash
php -S localhost:8000 index.php
```

Then access:

- application: `http://localhost:8000`
- n8n: `http://localhost:5678`

## n8n

The project uses `n8n` to automate payment-cycle history.

Ready-to-import workflows are available in `n8n/workflows`:

- `gymGatewayPaid.json`
- `payment-received-webhook.json`
- `generate-next-cycle-cron.json`
- `expire-subscriptions-cron.json`

### Expected behavior

- when a cycle is paid, it can be marked as `paid`
- the next cycle can be created automatically as `pending`
- overdue pending cycles can be marked as `vencido`

This directly impacts:

- dashboard monthly revenue
- overdue counter
- financial screen filters

## Current project status

### What is already consistent

- centralized PDO connection
- basic separation between domain, infrastructure, and data access
- use of prepared statements in repositories
- password hashing during registration
- PostgreSQL integration through Docker
- `n8n` automations designed for recurring cycles

### Areas still evolving

- there is no automated test suite yet
- the `Professional` entity is not connected to the main schema yet
- some files still contain text-encoding issues
- the `VIP` plan exists in the database but is not yet exposed in the current form
- the project still uses a simple custom router instead of a framework

## Possible next steps

- standardize UTF-8 encoding across all files
- add domain validations and clearer error messages
- include screens and tables for professionals
- expose all active plans dynamically from the database
- create tests for repositories and main flows
- improve separation between presentation and business rules

## Summary

This project already works as a real base for gym member administration, including registration, subscription management, financial tracking, PDF export, and external automation via `n8n`. It is still evolving, but it already has a solid structure for continued growth in an organized way.
