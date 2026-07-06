<?php
declare(strict_types=1);
require_once "vendor/autoload.php";


$app = App\App::makeInstance();
$app->process();