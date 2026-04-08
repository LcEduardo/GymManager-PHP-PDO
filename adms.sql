CREATE TABLE IF NOT EXISTS adms (
    id       SERIAL PRIMARY KEY,
    name     VARCHAR(150)        NOT NULL,
    email    VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255)        NOT NULL
);
