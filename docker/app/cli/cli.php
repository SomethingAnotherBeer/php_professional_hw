<?php
declare(strict_types=1);
use App\App;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/App.php';

$dotenv = Dotenv::createImmutable('/env');
$dotenv->load();

$params =
[
    'DSN' => $_ENV['DSN'] ?? null,
    'DB_USER' => $_ENV['DB_USER'] ?? null,
    'DB_PASSWORD' => $_ENV['DB_PASSWORD'] ?? null,
];



$app = new App();
$app->run($params);