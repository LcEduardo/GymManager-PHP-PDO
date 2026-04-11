<?php

namespace App\Controller;

use App\Infra\Connection;
use App\Repository\SubscriptionRepository;

class FinancialController 
{
    private SubscriptionRepository $subscriptionRepository; 
    public function __construct()
    {
        $connection = Connection::getConnection();
        $this->subscriptionRepository = new SubscriptionRepository($connection);
    }

    public function index() {
        $filtro = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'todos';
        $filtrosValidos = ['todos', 'paid', 'pending', 'vencido'];

        if (!in_array($filtro, $filtrosValidos)) {
            $filtro = 'todos';
        }

        $assinaturas = $this->subscriptionRepository->getAllSubscriptionsWithUsers($filtro);

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

        require dirname(__DIR__, 2) . '/views/financial/index.php';
    }
}