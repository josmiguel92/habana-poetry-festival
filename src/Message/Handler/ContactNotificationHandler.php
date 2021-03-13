<?php


namespace App\Message\Handler;

use App\Message\ContactNotification;
use App\Repository\ContactMessageRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

class ContactNotificationHandler implements MessageHandlerInterface
{

    /**
     * SubscriptionNotificationHandler constructor.
     * @param ContactMessageRepository $contactMessageRepository
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     */
    public function __construct(
        private ContactMessageRepository $contactMessageRepository,
        private MailerInterface $mailer,
        private RouterInterface $router
    )
    {}

    public function __invoke(ContactNotification $notification)
    {
        $contactMessage = $this->contactMessageRepository->findOneBy(['uniqueId' => $notification->getContactId()]);
        $emailAddress = $contactMessage->getEmail();
        $name = $contactMessage->getAuthorName();
        $subject = $contactMessage->getSubject();
        $messageContent = $contactMessage->getMessage();


        //Admin email
        $message = new NotificationEmail();
        $message->importance(NotificationEmail::IMPORTANCE_MEDIUM);

        $content = <<<EOT
Tenemos un nuevo comentario en la web del festival
==================================================

Un usuario, llamado **$name** (*$emailAddress*)  ha dejado el siguiente mensaje

**$subject**

---

$messageContent
 
EOT;
        $message->markdown($content);
        $message->subject('Tenemos un nuevo comentario en la web del festival');

        $this->mailer->send($message);
    }

}
