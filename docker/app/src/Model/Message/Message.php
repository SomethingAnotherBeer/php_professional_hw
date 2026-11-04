<?php
declare(strict_types=1);
namespace App\Model\Message;

class Message
{
    private string $message;

    private bool $status;

    public static function makeInstance(string $message, bool $status)
    {
        return new Message($message, $status);
    }

    public function __construct(string $message, bool $status)
    {
        $this->message = $message;
        $this->status = $status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function asArray()
    {
        return 
        [
            'message' => $this->message,
        ];
    }

    

}