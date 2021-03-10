<?php


namespace App\Message;

class ContactNotification
{
    public function __construct(public string $contactId)
    {
    }

    public function getContactId(): string
    {
        return $this->$contactId;
    }
}
