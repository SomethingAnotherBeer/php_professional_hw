<?php
declare(strict_types=1);
namespace App\Exception\Service;

class UndefinedMXRecordException extends EmailValidationException
{
    protected int $http_code = 422;
}