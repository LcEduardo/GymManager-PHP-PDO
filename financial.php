<?php

use App\Infra\Connection;
use App\Repository\SubscriptionRepository;

$connection = Connection::getConnection();
$subscriptionRepository = new SubscriptionRepository($connection);

$filtro = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'todos';
$filtrosValidos = ['todos', 'paid', 'pending', 'vencido'];

if (!in_array($filtro, $filtrosValidos)) {
    $filtro = 'todos';
}

$assinaturas = $subscriptionRepository->getAllSubscriptionsWithUsers($filtro);

$labelMap = [
    'todos'   => 'Todos',
    'paid'    => 'Pago',
    'pending' => 'Pendente',
    'vencido' => 'Vencido',
];

$badgeMap = [
    'paid'    => ['classe' => 'badge badge-pago',     'label' => 'Pago'],
    'pending' => ['classe' => 'badge badge-pendente', 'label' => 'Pendente'],
    'vencido' => ['classe' => 'badge badge-vencido',  'label' => 'Vencido'],
];

$activeBtnMap = [
    'todos'   => 'active',
    'paid'    => 'active-pago',
    'pending' => 'active-pendente',
    'vencido' => 'active-vencido',
];

$hoje = date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financeiro – Academia</title>
  <link rel="stylesheet" href="/public/css/style.css">
  <link rel="stylesheet" href="/public/css/financial.css">
  <link rel="stylesheet" href="/public/css/index.css">
</head>
<body>
  <?php require __DIR__ . '/partials/app-header.php'; ?>

  <div class="screen">

    <div class="page-title">Financeiro</div>
    
    <div class="filter-bar">
      <!-- $valor é a chave do filtro (ex: 'paid'), e $label é o texto exibido no botão (ex: 'Pago') -->
      <?php foreach ($labelMap as $valor => $label): ?>
        <a href="/financial?status=<?= $valor ?>">
          <button class="filter-btn <?= $filtro === $valor ? $activeBtnMap[$valor] : '' ?>">
            <?= $label ?>
          </button>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="results-count">
      Exibindo <span><?= count($assinaturas) ?></span> resultado<?= count($assinaturas) !== 1 ? 's' : '' ?>
    </div>

    <div class="dashboard-table-section">
      <table>
        <thead>
          <tr>
            <th>Aluno</th>
            <th>Plano</th>
            <th>Início</th>
            <th>Vencimento</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($assinaturas)): ?>
            <tr>
              <td colspan="5">
                <div class="empty-state">
                  <strong>Nenhuma assinatura encontrada</strong>
                  <p>Não há registros para o filtro "<?= htmlspecialchars($labelMap[$filtro]) ?>".</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($assinaturas as $row): ?>
              <?php
                $status = $row['payment_status'];
                $badge = $badgeMap[$status] ?? ['classe' => 'badge', 'label' => $status];

                $dateClass = '';
                if ($status === 'vencido') {
                    $dateClass = 'date-vencido';
                } elseif ($status === 'pending' && $row['end_date'] === $hoje) {
                    $dateClass = 'date-hoje';
                }
              ?>
              <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['plan_name']) ?></td>
                <td><?= htmlspecialchars($row['start_date']) ?></td>
                <td class="<?= $dateClass ?>"><?= htmlspecialchars($row['end_date']) ?></td>
                <td><span class="<?= $badge['classe'] ?>"><?= $badge['label'] ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="table-footer">
        <a href="/" class="btn btn-secondary">← Voltar ao painel</a>
      </div>
    </div>

  </div>

</body>
</html>
