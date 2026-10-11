<?php
declare(strict_types=1);
namespace App\Exception\Event;

use App\Exception\NotFoundExceptionInterface;

class EventNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    
}