<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Domain\Professional;
use App\Infra\Connection;
use App\Repository\ProfessionalRepository;

$pdo = Connection::getConnection();

$profissional = new Professional(
    id: null,
    name: 'Professor Julio Balestrini',
    specialty: 'Personal Trainer',
    phone: '(11)98765-4222',
    email: 'balestrini@personaltrainer.com',
    active: false
); 


$professionalRepository = new ProfessionalRepository($pdo);
$professionalRepository->createProfessional($profissional);