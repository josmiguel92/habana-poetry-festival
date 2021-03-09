<?php


namespace App\Message\Handler;


use App\Message\SubscriptionNotification;
use App\Repository\EmailSubscriptionsRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SubscriptionNotificationHandler implements MessageHandlerInterface
{

    /**
     * SubscriptionNotificationHandler constructor.
     * @param EmailSubscriptionsRepository $subscriptionRepository
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     */
    public function __construct(
        private EmailSubscriptionsRepository $subscriptionRepository,
        private MailerInterface $mailer,
        private RouterInterface $router
    )
    {}

    public function __invoke(SubscriptionNotification $notification)
    {
        $subscription = $this->subscriptionRepository->findOneBy(['uniqueId' => $notification->getSubscriptionId()]);
            $emailAddress = $subscription->getEmail();


        //Admin email
        $message = new NotificationEmail();
        $message->addTo('fromhabanafestival@meatmemi.33mail.com', 'cubapoesia@cubarte.cult.cu');
        $message->importance(NotificationEmail::IMPORTANCE_MEDIUM);

        $content = <<<EOT
Tenemos una nueva suscripcion al festival
==================================================

Un usuario, (*$emailAddress*)  se ha suscrito al festival
 
EOT;

        $message->markdown($content);
        $message->subject('Nueva suscripcion en la web festival de poesia de la habana');

        $this->mailer->send($message);


        //user email
        $message = new NotificationEmail();
        $message->addTo($emailAddress);
        $message->importance(NotificationEmail::IMPORTANCE_HIGH);


        $link = $this->router->generate('verify_email_subscription', ['uniqueId'=>$subscription->getUniqueId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $subscription->getUniqueId();
        $content = <<<EOT

---
###Gracias por inscribirte en el Mitin Virtual del Festival de Poesía de La Habana###

Por favor, para completar su inscripción y confirmar esta dirección de email, haga click 
**[en este enlace.]($link "Confirmar email")
**


Una vez confirmado su email, recibirá un nuevo mensaje, con la información necesaria para participar en nuestro Mitin el próximo mes de mayo/2021.

Saludos fraternos

El equipo del Festival de Poesía de La Habana





---
###Thank you for signing up for our Virtual Meeting of the Havana Poetry Festival###

Please, **to complete your registration and confirm this email address**, click 
**[on this link.]($link "Confirm email")
**

Once your email is confirmed, you will receive a new message, with the necessary information to participate in our Meeting next May / 2021.

Fraternal greetings 

The Havana Poetry Festival team 


EOT;

        $message->markdown($content);
        $message->action('Confirmar Email', $link);
        $message->subject('Email Verification - Festival de Poesia de La Habana');

        $this->mailer->send($message);



    }

}