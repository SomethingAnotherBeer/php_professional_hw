<?php
declare(strict_types=1);
require_once "vendor/autoload.php";
require_once "ini.php";

$app = App\App::makeInstance();
$app->process();