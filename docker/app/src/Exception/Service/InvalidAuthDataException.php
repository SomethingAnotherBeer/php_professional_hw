<?php
declare(strict_types=1);
namespace App\Exception\Service;

use App\Interface\Exception\UnauthorizedExceptionInterface;

class InvalidAuthDataException extends \Exception implements UnauthorizedExceptionInterface
{

}