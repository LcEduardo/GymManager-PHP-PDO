-- Criação das tabelas do projeto Academia
-- Executado automaticamente na primeira inicialização do container

CREATE TABLE IF NOT EXISTS users (
    id          SERIAL PRIMARY KEY,
    full_name   VARCHAR(150)        NOT NULL,
    email       VARCHAR(150) UNIQUE NOT NULL,
    password    VARCHAR(255)        NOT NULL,
    phone       VARCHAR(20),
    created_at  DATE                NOT NULL DEFAULT CURRENT_DATE,
    status      CHAR(1)             NOT NULL DEFAULT 'S'
);

CREATE TABLE IF NOT EXISTS plans (
    id             SERIAL PRIMARY KEY,
    name           VARCHAR(50)      NOT NULL,
    durantio_days  INT              NOT NULL,
    description    TEXT,
    active         BOOLEAN          NOT NULL DEFAULT TRUE,
    price          NUMERIC(10, 2)   NOT NULL
);

CREATE TABLE IF NOT EXISTS users_plans (
    id              SERIAL PRIMARY KEY,
    user_id         INT          NOT NULL REFERENCES users(id)  ON DELETE CASCADE,
    plan_id         INT          NOT NULL REFERENCES plans(id)  ON DELETE RESTRICT,
    start_date      DATE         NOT NULL,
    end_date        DATE         NOT NULL,
    payment_status  VARCHAR(20)  NOT NULL DEFAULT 'pending'
);

-- Dados iniciais de planos
INSERT INTO plans (name, durantio_days, description, active, price) VALUES
    ('Basic',   30,  'Acesso à musculação',              TRUE,  89.90),
    ('Premium', 30,  'Acesso completo + aulas coletivas', TRUE, 150.49),
    ('VIP',     30,  'Premium + personal trainer',        TRUE, 299.00)
ON CONFLICT DO NOTHING;
