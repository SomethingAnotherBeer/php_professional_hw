<?php
declare(strict_types=1);

use App\App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/App.php';
$options = 
[
    'path:',
    'action:'
];

$options = getopt('', $options);

$storage_env = [];

if (array_key_exists('ELASTIC_URL', $_ENV)) {
    $storage_env['ELASTIC_URL'] = $_ENV['ELASTIC_URL'];
}

if (array_key_exists('ELASTIC_USER', $_ENV)) {
    $storage_env['ELASTIC_USER'] = $_ENV['ELASTIC_USER'];
}

if (array_key_exists('ELASTIC_PASSWORD', $_ENV)) {
    $storage_env['ELASTIC_PASSWORD'] = $_ENV['ELASTIC_PASSWORD'];
}

if (array_key_exists('ELASTIC_INDEX', $_ENV)) {
    $storage_env['ELASTIC_INDEX'] = $_ENV['ELASTIC_INDEX'];
}

$params =
[
    'client_options' => $storage_env,
    'path' => $options['path'] ?? null,
    'action' => $options['action'] ?? null,
];

$app = new App();
$app->execute($params);