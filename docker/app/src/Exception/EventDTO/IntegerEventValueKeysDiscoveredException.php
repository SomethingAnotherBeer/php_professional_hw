<?php
declare(strict_types=1);
namespace App\Exception\EventDTO;

use App\Exception\BadDataExceptionInterface;

class IntegerEventValueKeysDiscoveredException extends \Exception implements BadDataExceptionInterface
{
    
}