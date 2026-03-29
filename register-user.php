<?php

require 'vendor/autoload.php';

use App\Domain\User;
use App\Infra\Connection;
use App\Repository\UserRepository;
use App\Repository\SubscriptionRepository;
use App\Domain\UserSubscription;
use App\Repository\PlanRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);
$subscriptionRepository = new SubscriptionRepository($connection);
$planRepository = new PlanRepository($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $planName = $_POST['plan'];
  $plan = $planRepository->searchPlan($planName);

  $user = new User(
      null,
      $_POST['name'],
      $_POST['email'],
      password_hash($_POST['password'], PASSWORD_DEFAULT),
      date('Y-m-d'),
      $_POST['phone'] ?? null,
      'S'
  );

  $subscription = new UserSubscription(
    null,
    null, 
    $plan['id'], 
    date('Y-m-d'),
    date('Y-m-d', strtotime('+1 month')),
    'pending' 
  );

  $repository->createUser($user);

  $subscription->setUserId($user->id());

  $subscriptionRepository->createSubscription($subscription);

  $result = $subscriptionRepository->createSubscription($subscription);

  if ($result) {
      header('Location: index.php');
  } else {
      echo "Something went wrong. Please try again."; 
  }

}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Usuário – Academia</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/form.css">
</head>
<body>

  <header>
    <h1>Academia</h1>
  </header>

  <main class="page-center">
    <div class="card">

      <div class="card-header">
        <span class="card-icon">👤</span>
        <h2>Cadastro de Usuário</h2>
      </div>

      <form class="form" method="POST">

        <div class="field-group">
          <label for="name">Nome completo</label>
          <input type="text" id="name" name="name" placeholder="Ex: João da Silva" required>
        </div>

        <div class="field-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="email@exemplo.com" required>
        </div>

        <div class="field-group">
          <label for="birthdate">Data de nascimento</label>
          <input type="date" id="birthdate" name="birthdate">
        </div>

        <div class="field-group">
          <label for="phone">Telefone</label>
          <input type="text" id="phone" name="phone" placeholder="(11) 99999-9999">
        </div>

        <div class="field-group">
          <label for="password">Senha</label>
          <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
        </div>

        <div class="field-group">
          <label for="plan">Plano</label>
          <select id="plan" name="plan">
            <option value="Basic">Plano Básico</option>
            <option value="Premium">Plano Premium</option>
          </select>
        </div>

        <div class="form-actions">
          <a href="index.php" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>

      </form>
    </div>
  </main>

</body>
</html>