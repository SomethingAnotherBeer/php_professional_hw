<?php
declare(strict_types=1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

abstract class Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

}