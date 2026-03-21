<?php
  require 'vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);

try {

  $users = $repository->getAllUsers();

}catch(PDOException $e){
  echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
    <h1>Academia</h1>
  </header>

  <main>
    <section>
      <table>
        <thead>
          <tr>
            <th>Data</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($users as $user):  ?>
          <tr>
            <td><?= $user->date() ?></td>
            <td><?= $user->fullName() ?></td>
            <td><?= $user->email() ?></td>
            <td><?= $user->phone() ?></td>
            <td><?= $user->status() ?></td>
            <td>
              <div class="actions">
                <a href="edit.php?id=<?= $user->id() ?>"><button>Editar</button></a>
                <a href="delete.php?id=<?= $user->id() ?>"><button>Excluir</button></a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="actions">
        <a href="cadastro.html"><button>Cadastrar usuário</button></a>
        <button>Baixar PDF</button>
      </div>
    </section>
  </main>
</body>
</html>