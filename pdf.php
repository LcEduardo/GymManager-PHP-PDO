<?php
  require 'vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);
$users = $repository->getAllUsers();


?>

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
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
