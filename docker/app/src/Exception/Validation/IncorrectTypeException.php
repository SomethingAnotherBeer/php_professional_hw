<?php
declare(strict_types=1);
namespace App\Exception\Validation;

use App\Interface\Exception\BadDataExceptionInterface;

class IncorrectTypeException extends \Exception implements BadDataExceptionInterface
{
    
}