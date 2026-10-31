<?php
declare(strict_types=1);
namespace App\Service;

use App\Interface\ServiceStrategyInterface;
use Override;

class TableDataGatewayService implements ServiceStrategyInterface
{


    #[Override]
    public function process(): void
    {
        throw new \Exception('Not implemented');
    }
}