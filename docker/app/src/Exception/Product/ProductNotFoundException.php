<?php
declare(strict_types=1);
namespace App\Exception\Product;

use App\Exception\NotFoundExceptionInterface;

class ProductNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    
}