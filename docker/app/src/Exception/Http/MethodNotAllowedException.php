<?php
declare(strict_types=1);
namespace App\Exception\Http;

use App\Interface\Exception\NotAllowedExceptionInterface;

class MethodNotAllowedException extends \Exception implements NotAllowedExceptionInterface
{

}