<?php
declare(strict_types=1);
namespace App\Exception\Event;

use App\Exception\NotAllowedExceptionInterface;

class EventAlreadyExistsException extends \Exception implements NotAllowedExceptionInterface
{
    
}