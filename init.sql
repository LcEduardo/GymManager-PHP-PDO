-- Criação das tabelas do projeto Academia
-- Executado automaticamente na primeira inicialização do container

CREATE TABLE IF NOT EXISTS users (
    id          SERIAL PRIMARY KEY,
    full_name   VARCHAR(150)        NOT NULL,
    email       VARCHAR(150) UNIQUE NOT NULL,
    password    VARCHAR(255)        NOT NULL,
    phone       VARCHAR(20),
    birth_date  DATE,
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
        CHECK (payment_status IN ('pending', 'paid', 'vencido')),
    CONSTRAINT unique_user_cycle UNIQUE (user_id, start_date, end_date)
);

CREATE TABLE IF NOT EXISTS adms (
    id       SERIAL PRIMARY KEY,
    name     VARCHAR(150)        NOT NULL,
    email    VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255)        NOT NULL
);

-- Dados iniciais de planos
INSERT INTO plans (name, durantio_days, description, active, price) VALUES
    ('Basic',   30,  'Acesso à musculação',              TRUE,  89.90),
    ('Premium', 30,  'Acesso completo + aulas coletivas', TRUE, 150.49),
    ('VIP',     30,  'Premium + personal trainer',        TRUE, 299.00)
ON CONFLICT DO NOTHING;

-- Administrador master inicial
INSERT INTO adms (name, email, password) VALUES
    ('Administrador Principal', 'admin@gymmanager.local', '$argon2id$v=19$m=65536,t=4,p=1$QmV1d0lCUFhmZFFzSnBiSA$Z8HVgnRdSZ7FixAxou787k1EuQbfKn2g9SBLIUI+MzY')
ON CONFLICT (email) DO NOTHING;
