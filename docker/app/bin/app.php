<?php
declare(strict_types=1);

use App\App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/App.php';
$options = 
[
    'path:'
];

$options = getopt('', $options);

$storage_env = [];


if (array_key_exists('elastic_url', $_ENV)) {
    $storage_env['elastic']['elastic_url'] = $_ENV['elastic_url'];
}

if (array_key_exists('elastic_user', $_ENV)) {
    $storage_env['elastic']['elastic_user'] = $_ENV['elastic_user'];
}

if (array_key_exists('elastic_password', $_ENV)) {
    $storage_env['elastic']['elastic_password'] = $_ENV['elastic_password'];
}

if (array_key_exists('elastic_index', $_ENV)) {
    $storage_env['elastic']['elastic_index'] = $_ENV['elastic_index'];
}

$params =
[
    'storage' => $storage_env,
    'options' => $options
];


$app = new App();
$app->execute($params);