<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuario - Academia</title>
  <link rel="stylesheet" href="/public/css/style.css">
  <link rel="stylesheet" href="/public/css/form.css">
</head>
<body>
  <?php require dirname(__DIR__, 2) . '/partials/app-header.php'; ?>

  <main class="page-center">
    <div class="card">
      <div class="card-header">
        <span class="card-icon">Editar</span>
        <h2>Editar Usuario</h2>
      </div>

      <form class="form" action="/update" method="POST">
        <input type="hidden" name="id" value="<?= $user->id() ?? '' ?>">

        <div class="field-group">
          <label for="name">Nome completo</label>
          <input
            type="text"
            id="name"
            name="name"
            placeholder="Ex: Joao da Silva"
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
            <?php foreach ($plans as $plan): ?>
              <option value="<?= $plan->getId() ?>" <?= $currentPlanId === $plan->getId() ? 'selected' : '' ?>>
                <?= htmlspecialchars($plan->getName()) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-actions">
          <a href="/" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Salvar alteracoes</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
