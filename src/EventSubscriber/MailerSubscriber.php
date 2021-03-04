<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 11/19/2019
 * Time: 5:30 PM
 */
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            MessageEvent::class => [
                ['addSenderAddress', 0],
            ],
        ];
    }

    public function addSenderAddress(MessageEvent $event)
    {
        if ($event->getMessage() instanceof Email) {
            $event->getMessage()->from(new Address('no-reply@festivaldepoesiadelahabana.com', 'Festival de Poesía de la Habana'));
        }
    }
}
