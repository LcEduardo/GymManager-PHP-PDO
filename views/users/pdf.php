<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #eef1f5;
      color: #2d3748;
      padding: 32px;
    }

    h1 {
      font-size: 1.3rem;
      font-weight: 700;
      color: #2c3e50;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 6px;
    }

    .subtitle {
      font-size: 0.82rem;
      color: #6c7a89;
      margin-bottom: 24px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #ffffff;
      border-radius: 8px;
      overflow: hidden;
    }

    th {
      background: #2c3e50;
      color: #ffffff;
      padding: 12px 14px;
      text-align: left;
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    td {
      padding: 10px 14px;
      border-bottom: 1px solid #dde3ea;
      font-size: 0.9rem;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:nth-child(even) td {
      background: #f7f9fb;
    }

    .footer {
      margin-top: 20px;
      font-size: 0.78rem;
      color: #6c7a89;
      text-align: right;
    }
  </style>
</head>
<body>

  <h1>Lista de Usuários</h1>
  <p class="subtitle">Gerado em <?= date('d/m/Y H:i') ?></p>

  <table>
    <thead>
      <tr>
        <th>Data de cadastro</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $user): ?>
      <tr>
        <td><?= $user->createdAt() ?></td>
        <td><?= $user->fullName() ?></td>
        <td><?= $user->email() ?></td>
        <td><?= $user->phone() ?></td>
        <td><?= $user->status() ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="footer">Academia – <?= date('Y') ?></div>

</body>
</html>