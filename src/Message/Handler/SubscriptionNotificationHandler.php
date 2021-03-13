<?php


namespace App\Message\Handler;


use App\Message\SubscriptionNotification;
use App\Repository\EmailSubscriptionsRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriptionNotificationHandler implements MessageHandlerInterface
{

    /**
     * SubscriptionNotificationHandler constructor.
     * @param EmailSubscriptionsRepository $subscriptionRepository
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EmailSubscriptionsRepository $subscriptionRepository,
        private MailerInterface $mailer,
        private RouterInterface $router,
        private TranslatorInterface $translator
    )
    {}

    public function __invoke(SubscriptionNotification $notification)
    {
        $subscription = $this->subscriptionRepository->findOneBy(['uniqueId' => $notification->getSubscriptionId()]);
            $emailAddress = $subscription->getEmail();


        //Admin email
        $message = new NotificationEmail();
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
        $message = new TemplatedEmail();
        $message->addTo($emailAddress);
        $subject = $this->translator->trans('Gracias por inscribirte en el Mitin Virtual del Festival de Poesía de La Habana');
        $link = $this->router->generate(
            'verify_email_subscription',
            ['uniqueId'=>$subscription->getUniqueId()],
            UrlGeneratorInterface::ABSOLUTE_URL);
        $message->context([
            'url' => $link,
            'subject' => $subject
        ]);

        $message->htmlTemplate("email/base.html.twig");
        $message->subject($subject);
        $this->mailer->send($message);

    }

}