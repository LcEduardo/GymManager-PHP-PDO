<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Usuario - Academia</title>
  <link rel="stylesheet" href="/public/css/style.css">
  <link rel="stylesheet" href="/public/css/form.css">
</head>
<body>
  <?php require dirname(__DIR__, 2) . '/partials/app-header.php'; ?>

  <main class="page-center">
    <div class="card">
      <div class="card-header">
        <span class="card-icon">Usuario</span>
        <h2>Cadastro de Usuario</h2>
      </div>

      <form class="form" method="POST" action="/register">
        <div class="field-group">
          <label for="name">Nome completo</label>
          <input type="text" id="name" name="name" placeholder="Ex: Joao da Silva" required>
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
          <input type="password" id="password" name="password" required>
        </div>

        <div class="field-group">
          <label for="plan">Plano</label>
          <select id="plan" name="plan">
            <option value="Basic">Plano Basico</option>
            <option value="Premium">Plano Premium</option>
          </select>
        </div>

        <div class="form-actions">
          <a href="/" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
