<?php
declare(strict_types=1);
namespace App\Model\Event;

use App\Exception\Event\EventUidCantBeEmptyException;

class EventUid
{
    private string $event_uid;

    public static function makeInstance(string $event_uid): EventUid
    {
        return new EventUid($event_uid);
    }

    public function __construct(string $event_uid)
    {
        $this->checkEventUidIsNotEmpty($event_uid);
        $this->event_uid = $event_uid;
    }

    public function getEventUid(): string
    {
        return $this->event_uid;
    }

    private function checkEventUidIsNotEmpty(string $event_uid): void
    {
        if ('' === trim($event_uid)) {
            throw new EventUidCantBeEmptyException("Идентификатор события не может быть пустым");
        }
    }

}