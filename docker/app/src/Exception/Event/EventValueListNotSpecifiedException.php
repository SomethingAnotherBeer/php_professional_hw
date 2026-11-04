<?php
declare(strict_types=1);
namespace App\Exception\Event;

use App\Exception\BadDataExceptionInterface;

class EventValueListNotSpecifiedException extends \Exception implements BadDataExceptionInterface
{
    
}