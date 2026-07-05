<?php
declare(strict_types=1);
namespace App\Exception\Service;

use App\Interface\Exception\BadDataExceptionInterface;

class RequestStringNotFoundException extends \Exception implements BadDataExceptionInterface
{
    
}