<?php
declare(strict_types=1);
namespace App\Exception\Http;

use Throwable;
use Override;

class ConflictHttpException extends HttpException
{
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->status_code = 409;
    }
}