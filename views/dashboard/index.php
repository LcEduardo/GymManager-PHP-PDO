<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/index.css">
</head>
<body>
    <?php require dirname(__DIR__, 2) . '/partials/app-header.php'; ?>

    <div id="dashboard" class="screen">

    <div class="page-title">Dashboard</div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Alunos Ativos</div>
        <div class="stat-value"><?= $usersActive ?></div>
        <div class="stat-delta"><?= $usersActivesThisMonth ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Receita Mensal</div>
        <div class="stat-value">R$ <?= number_format($monthlyRevenue, 2, ',', '.') ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Planos Premium</div>
        <div class="stat-value"><?= $usersPremium ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Vencidas / Hoje</div>
        <div class="stat-value" style="color: var(--color-danger)"><?= $dueSubscriptions ?></div>
      </div>
    </div>

    <div class="dashboard-table-section">
  <main>
    <section>
      <table>
        <thead>
          <tr>
            <th>Data de cadastro</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Status</th>
            <th>AÃ§Ãµes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?= $user->createdAt() ?></td>
            <td><?= $user->fullName() ?></td>
            <td><?= $user->email() ?></td>
            <td><?= $user->phone() ?></td>
            <td><?= $user->status() ?></td>
            <td>
              <div class="actions">
                <a href="/edit?id=<?= $user->id() ?>"><button><img src="/public/img/icons/edit.png" class="icon-trash" alt="editar"></button></a>
                <a href="/delete?id=<?= $user->id() ?>"><button style="background-color: red;"><img src="/public/img/icons/close.png" class="icon-trash" alt="excluir"></button></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="actions">
        <a href="/register"><button>Cadastrar Usuários</button></a>
        <a href="/download"><button>Baixar PDF</button></a>
        <a href="/financial"><button style="background-color: var(--color-success);">Financeiro</button></a>
      </div>
    </section>
  </main>
</body>
</html>
