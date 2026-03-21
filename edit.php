<?php

require 'vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);

$user = $repository->searchUser($_GET['id']);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário – Academia</title>
  <link rel="stylesheet" href="css/style.css">
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

      <!-- action e method serão preenchidos pelo PHP -->
      <form class="form" action="update.php" method="POST">

        <!-- id oculto para identificar o registro -->
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
          <label for="phone">Telefone</label>
          <input
            type="text"
            id="phone"
            name="phone"
            placeholder="(11) 99999-9999"
            value="<?= htmlspecialchars($user->phone() ?? '') ?>">
        </div>

        <div class="field-group">
          <label for="status">Plano / Status</label>
          <select id="status" name="status">
            <option value="basico"   <?= ($user->status() ?? '') === 'basico'   ? 'selected' : '' ?>>Plano Básico</option>
            <option value="premium"  <?= ($user->status() ?? '') === 'premium'  ? 'selected' : '' ?>>Plano Premium</option>
            <option value="inativo"  <?= ($user->status() ?? '') === 'inativo'  ? 'selected' : '' ?>>Inativo</option>
          </select>
        </div>

        <div class="form-actions">
          <a href="index.php" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </div>

      </form>
    </div>
  </main>

</body>
</html>
