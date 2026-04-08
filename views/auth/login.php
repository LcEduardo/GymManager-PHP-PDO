<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Administrativo - Academia</title>
  <link rel="stylesheet" href="/public/css/login.css">
</head>
<body>
  <main class="login-page">
    <section class="login-panel">
      <div class="brand-block">
        <span class="eyebrow">Acesso administrativo</span>
        <h1>Controle quem entra no sistema da academia</h1>
        <p>Entre com o e-mail e a senha do administrador para liberar o dashboard e as operacoes de gestao.</p>
      </div>

      <div class="login-card">
        <div class="card-header">
          <h2>Fazer login</h2>
          <p>Use o administrador cadastrado na tabela <code>adms</code>.</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="/login">
          <div class="field-group">
            <label for="email">E-mail</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="admin@gymmanager.local"
              autocomplete="email"
              required
            >
          </div>

          <div class="field-group">
            <label for="password">Senha</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Digite sua senha"
              autocomplete="current-password"
              required
            >
          </div>

          <button type="submit" class="btn-login">Entrar</button>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
