# PDO Project 
Fazer um código onde eu consigo mudar o banco de dados de ``sqlite`` para ``postgresql`` e subir o banco via ``Docker``;

Criar um sistema de cadastro de usuários da academia;

O que preciso para criar um cadastro de usuários para academia?
- Informações dos clientes
	- Name e Last Name
	- E-mail
	- Phones
	- Password
- Planos contratados
	- Tipos
	- Valores
- Profissionais disponíveis 
	- Full name
	- Horários
- Quais clientes estão em determinado plano

## Tabelas 

### Users
- id (PK)
- first_name
- last_name
- email (unique)
- password (hashed)
- phone
- created_at
- status (active, inactive, blocked)
### Plans
- id
- name (Basic, Premium, VIP)
- price
- duration_days
- description
- active (boolean)

### User_plans (UserSubscription)
- id
- user_id (FK)
- plan_id (FK)
- start_date
- end_date
- payment_status
- created_at

### Professionals 
- id
- full_name
- specialty (personal trainer, nutritionist, etc.)
- phone
- email
- active

---

## Primeiro Passo:

Vamos usar SQLITE pois é mais simples e como é um projeto para aprender eu não preciso me preocupar com Docker e etc.

O DBEAVER eu só preciso criar uma conexão informar o caminho do meu ``arquivo .sqlite`` e voilá.

Vamos usar o [[Composer]] para trabalhar com ``autoload`` dele para isso usamos um json bem simples:

```json
{

    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "require": {
        "ext-pdo": "*"
    }
}
```

E como só queremos usar o ``autoload`` automático só rodar o comando ``composer dump-autoload``;

Qualquer alteração em ``composer.json`` precisa rodar o comando acima.

## Problema 1:

Criar uma conexão com o banco, eu pesquisei certinho na [documentação do php](https://www.php.net/manual/en/pdo.connections.php) e na hora do gol esqueci ``:`` que deu erro.

```php
databasePath = __DIR__ . '/../gym.sqlite';
try {
    $dbh = new PDO('sqlite:' . $databasePath);
    echo 'Conectei pae';
}catch(PDOException $e) {
    echo $e->getMessage();
}
```

Transformar em uma classe.

## Como pensar sempre

Quando for transformar código em classe, pergunte
1. Qual é a responsabilidade?
2. Essa lógica pode ser reutilizada?
3. Preciso guardar estado?
4. Quero flexibilidade futura?

## Static Method
- Pesquisar o que é **Método Estático** 
	- -> ela pode ser chamada sem instanciar um objeto;
	- -> não tem acesso a nenhuma atributo;


```php
class Connection
{

    public static function getConnection(): PDO {
        $databasePath = __DIR__ . '/../gym.sqlite';
        try {
            return  new PDO('sqlite:' . $databasePath);
        }catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
```
Nesse caso, como já criamos um objeto ``new PDO`` eu não preciso instanciar isso posteriormente. 

# Inserindo clientes

## Prepare()

Ele retorna um objeto [[PDO#prepared statements]] ou se o banco não conseguir preparar a situação ele retorna ``false``. Enviamos um molde do que será executado para o banco preparar, usamos os placeholders em vez de valores.

```php

$sql = "INSERT INTO users (full_name, email, password, phone, created_at, status)
        VALUES (:full_name, :email, :password, :phone, :created_at, :status)";

$stmt = $pdo->prepare($sql);
```

Após tudo já estar preparado usamos o ``bindvalue()`` para passar aos placeholders os valores necessários.

```php
stmt->bindValue(':full_name', $userBatman->fullName(), PDO::PARAM_STR);

$stmt->bindValue(':email', $userBatman->email(), PDO::PARAM_STR);

$stmt->bindValue(':password', $userBatman->password(), PDO::PARAM_STR);

$stmt->bindValue(':phone', $userBatman->phone(), PDO::PARAM_STR);

$stmt->bindValue(':created_at', $userBatman->date(), PDO::PARAM_STR);

$stmt->bindValue(':status', $userBatman->status(), PDO::PARAM_STR);
```

## Ideia de OOP

A solution to make our code more structured is to create a method responsible for creating a user. However, we should not put this method inside the `User` class, because `User` represents a real-world entity, while `createUser()` represents database (SQL) logic, which is an infrastructure concern, not a domain concern.

Visando essa ideia criamos um arquivo chamado ``UserRepository``;
### UserRepository

Basically, is a class responsible to communicate with database. **Therefore, Repository is a class that i use to communicate with database**.

**The next step:** is Plan a real-world entity?

Yes, because type of plan, price and duration represents something that exists in the business domain. Therefore, we need a class. 

### User_plans

> Precisamos nos perguntar, se isso é apenas uma tabela para juntar informações ou representa uma parte do meu negócio?

Como temos ``start_date``, ``end_date`` and ``payment_status``this is not just a simple many-to-many relation. Isso é um domain do meu negócio pois usuários se cadastram em um plano onde tem data de inicio e fim, além se a fatura foi paga ou não. 

Nesse sentido User_plans está errado e deveria ser ``Subscription`` or ``UserSubscriptio``. 

## User float with bindValue

Não tem! Basicamente usamos ``PDO::PARAM_STR`` e o banco fica responsável por converter isso.

## Criando um Subscription

Nesse ponto eu preciso do id do usuário que eu já instanciei e o mesmo para o plano. A pergunta que veio foi: "Eu preciso fazer um select e criar um novo objeto ou apenas uso um select para trazer o id?"

Só precisamos do id então não tem necessidade de criar um novo objeto para tal, isso ajuda na performática do código e deixa mais simples. A outra abordagem precisaria vir caso  necessitamos de regras e comportamentos relacionados com a entidade User, por exemplo, alterar o id dele ou nome e assim vai. Ai é claro, em UserRepository a gente filtraria por esse usuário que desejarmos. 

### Criando um Object User a partir de um SELECT

Eu fiz um insert diretamente pelo banco, com isso vou puxar esses dados especifico do usuário e criar um objeto.

Em primeiro ponto, para rodar um **SELECT** precisamos rodar [[PDO#Funções Embutidas]] como ``query()`` onde retorna um PDOStatement object, not the data itself.

Therefore, utilizar o ``fecth()``junto com o parâmetro PDO::FETCH_ASSOC que retorna um array onde cada índice representa as colunas retornadas no SELECT:

```php
$sqlUser = "SELECT * FROM users WHERE id = 4 ;";

$stmt = $pdo->query($sqlUser);

var_dump($stmt->fetch(PDO::FETCH_ASSOC));
```

Return:

```bash
array(7) {
  ["id"]=>
  int(4)
  ["full_name"]=>
  string(17) "Sergin Bala Tensa"
  ["email"]=>
  string(27) "serginbalatensa@outlook.com"
  ["password"]=>
  string(6) "000021"
  ["phone"]=>
  string(11) "19945678900"
  ["created_at"]=>
  string(10) "2026-02-25"
  ["status"]=>
  string(1) "0"
}
```

**Attention:** pensando em algo mais profissional o ``id = 4`` viria de uma URL ou Form e com isso corremos o risco de [[PDO#SQL Injection]]. Por isso, a forma recomendada quando não vamos rodar um SELECT genérico é usando ``prepare()``

# Create a Professionals
---
Basicamente seguiu a mesma lógica do Users, criamos uma classe que compões os atributos necessários, getters and setter. 

Depois disso, montamos uma estrutura para inserir o profissional no banco de dados. E aplicamos em um repository.

## PostgreSQL com Docker Compose

O projeto agora usa PostgreSQL por padrÃ£o na classe de conexÃ£o.

### Subir o banco

```bash
docker compose up -d postgres
```

### ConfiguraÃ§Ã£o da aplicaÃ§Ã£o

As credenciais ficam no arquivo `.env` da raiz:

```env
DB_DRIVER=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gym
DB_USERNAME=gym_user
DB_PASSWORD=gym_password
```

### InicializaÃ§Ã£o

O arquivo `init.sql` Ã© executado automaticamente na primeira criaÃ§Ã£o do container e cria as tabelas `users`, `plans` e `users_plans`, alÃ©m de popular os planos iniciais.
