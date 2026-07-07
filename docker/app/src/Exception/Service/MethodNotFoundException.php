<?php
declare(strict_types=1);
namespace App\Exception\Service;

use App\Interface\Exception\ServerExceptionInterface;

class MethodNotFoundException extends \Exception implements ServerExceptionInterface
{
    
}