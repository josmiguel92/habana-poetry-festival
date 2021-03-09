<?php


namespace App\Message;


class SubscriptionNotification
{
    public function __construct(public string $subscriptionId)
    {}

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

}