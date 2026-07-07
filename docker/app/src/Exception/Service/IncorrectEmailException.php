<?php
declare(strict_types=1);
namespace App\Exception\Service;

class IncorrectEmailException extends EmailValidationException
{
    protected int $http_code = 400;
    
}