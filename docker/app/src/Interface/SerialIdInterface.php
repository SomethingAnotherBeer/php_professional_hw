<?php
declare(strict_types=1);
namespace App\Interface;

interface SerialIdInterface
{
    public function getTableName(): string;

    public function getIdName(): string;
}