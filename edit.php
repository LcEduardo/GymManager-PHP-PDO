<?php

require 'vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;
use App\Repository\SubscriptionRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);
$subscriptionRepository = new SubscriptionRepository($connection);

$user = $repository->searchUser(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT));
$subscription = $subscriptionRepository->findByUserId($user->id());
$currentPlanId = $subscription['plan_id'] ?? null;

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário – Academia</title>
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
        <span class="card-icon">✏️</span>
        <h2>Editar Usuário</h2>
      </div>

      <form class="form" action="/update" method="POST">

        <input type="hidden" name="id" value="<?= $user->id() ?? '' ?>">

        <div class="field-group">
          <label for="name">Nome completo</label>
          <input
            type="text"
            id="name"
            name="name"
            placeholder="Ex: João da Silva"
            value="<?= htmlspecialchars($user->fullName() ?? '') ?>"
            required>
        </div>

        <div class="field-group">
          <label for="email">E-mail</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="email@exemplo.com"
            value="<?= htmlspecialchars($user->email() ?? '') ?>"
            required>
        </div>

        <div class="field-group">
          <label for="birthdate">Data de nascimento</label>
          <input
            type="date"
            id="birthdate"
            name="birthdate"
            value="<?= htmlspecialchars($user->birthDate() ?? '') ?>">
        </div>

        <div class="field-group">
          <label for="phone">Telefone</label>
          <input
            type="text"
            id="phone"
            name="phone"
            placeholder="(11) 99999-9999"
            value="<?= htmlspecialchars($user->phone() ?? '') ?>">
        </div>

        <div class="field-group">
          <label for="plan">Plano</label>
          <select id="plan" name="plan_id">
              <option value="1" <?= $currentPlanId === 1 ? 'selected' : '' ?>>Plano Básico</option>
              <option value="2" <?= $currentPlanId === 2 ? 'selected' : '' ?>>Plano Premium</option>
          </select>
        </div>

        <div class="form-actions">
          <a href="/" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </div>

      </form>
    </div>
  </main>

</body>
</html>
