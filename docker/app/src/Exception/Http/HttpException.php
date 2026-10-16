<?php
declare(strict_types=1);
namespace App\Exception\Http;

use Throwable;
use Override;

class HttpException extends \Exception
{
    protected int $status_code;

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    

}