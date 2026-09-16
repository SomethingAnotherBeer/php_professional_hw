<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use App\Exception\Event\EventUidCannotBeEmptyException;

class EventUidValue
{
    private string $uid;

    public static function makeInstance(string $uid): EventUidValue
    {
        return new EventUidValue($uid);
    }

    public function __construct(string $uid)
    {
        $this->checkUid($uid);
        $this->uid = $uid;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    private function checkUid(string $uid): void
    {
        if ('' === trim($uid)) {
            throw new EventUidCannotBeEmptyException("uid события не может быть пустым");
        }
    }

}