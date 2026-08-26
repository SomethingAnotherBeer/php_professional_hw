<?php
declare(strict_types=1);

use App\App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/App.php';
$options = 
[
    'query_file:',
    'query_string:'
];

$options = getopt('', $options);

var_dump($options);

$app = new App();