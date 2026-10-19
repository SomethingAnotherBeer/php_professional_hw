<?php
declare(strict_types=1);
namespace App\Exception\Client;

use App\Exception\BadDataExceptionInterface;

class ClientPasswordCannotBeEmptyException extends \Exception implements BadDataExceptionInterface
{
    
}