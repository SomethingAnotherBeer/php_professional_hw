<?php
declare(strict_types=1);
namespace App\Interface;


/** 
 * @template T
 */
interface IdentityItemInterface
{
    public function getId(): int|string;

    public function isDeleted(): bool;

    public function setDeleted(): void;

}