<?php
declare(strict_types=1);
namespace App\Exception\Service;


class EmailValidationException extends \Exception
{
    protected int $http_code;

    public function getHttpCode(): int
    {
        return $this->http_code;
    }
}