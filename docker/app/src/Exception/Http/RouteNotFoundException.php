<?php
declare(strict_types=1);
namespace App\Exception\Http;

use App\Interface\Exception\NotFoundExceptionInterface;

class RouteNotFoundException extends \Exception implements NotFoundExceptionInterface
{

}