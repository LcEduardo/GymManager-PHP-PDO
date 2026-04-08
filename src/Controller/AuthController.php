<?php

namespace App\Controller;

use App\Infra\Connection;
use App\Repository\AdminRepository;

class AuthController
{
    private AdminRepository $adminRepository;

    public function __construct()
    {
        $connection = Connection::getConnection();
        $this->adminRepository = new AdminRepository($connection);
    }

    public function login(): void
    {
        $error = $_SESSION['auth_error'] ?? null;
        unset($_SESSION['auth_error']);

        require dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function authenticate(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

        if (!$email || !$password) {
            $_SESSION['auth_error'] = 'Informe um e-mail valido e a senha do administrador.';
            header('Location: /login');
            return;
        }

        $admin = $this->adminRepository->findByEmail($email);

        if (!$admin || !password_verify($password, $admin['password'])) {
            $_SESSION['auth_error'] = 'Credenciais invalidas.';
            header('Location: /login');
            return;
        }

        session_regenerate_id(true);

        $_SESSION['admin'] = [
            'id' => (int) $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
        ];

        header('Location: /adm');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: /login');
    }
}
