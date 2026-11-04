<?php
declare(strict_types=1);
namespace App\Exception\Event;

use App\Exception\ConflictExceptionInterface;


class EventAlreadyExistsException extends \Exception implements ConflictExceptionInterface
{
    
}