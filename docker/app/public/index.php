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
        'REDIS_HOST' => fn(string $redis_host) => trim($redis_host),
        'REDIS_PORT' => fn(string $redis_port) => (int)trim($redis_port),
    ],
    
];

$current = [];

$current_client = getenv('CLIENT');

if (!isset($available_clients[$current_client]) || false === $available_clients[$current_client]) {
    $current_client = 'REDIS';
}

$current['client'] = [];
foreach ($client_params[$current_client] as $client_param_key => $client_param_prepare) {
    $current['client'][$client_param_key] =  $client_param_prepare(getenv($client_param_key));
}
//$current['client'] = array_filter($current, fn(string|false $param) => false !== $param);
$current['client_mode'] = $current_client;


$app = new App();
$app->run($current);

