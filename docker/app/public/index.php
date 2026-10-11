<?php
declare(strict_types=1);

use App\App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/App.php';

$available_clients =
[
    'REDIS' => true,
];

$client_params =
[
    'REDIS' =>
    [
        'REDIS_HOST',
        'REDIS_PORT'
    ],
    
];

$current = [];

$current_client = getenv('CLIENT');

if (!isset($available_clients[$current_client]) || false === $available_clients[$current_client]) {
    $current_client = 'REDIS';
}

$current['client'] = [];
foreach ($client_params[$current_client] as $client_param) {
    $current['client'][] = getenv($client_param);
}
//$current['client'] = array_filter($current, fn(string|false $param) => false !== $param);
$current['client_mode'] = $current_client;

$app = new App();
$app->run($current);

