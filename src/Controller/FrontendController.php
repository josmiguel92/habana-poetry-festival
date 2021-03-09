<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\EmailSubscriptions;
use App\Form\ContactMessageType;
use App\Form\EmailSubscriptionType;
use App\Message\SubscriptionNotification;
use DateTime;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;

class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="frontend")
     */
    public function index(): Response
    {
        $today = new DateTime('today');
        $target = new DateTime('2021-05-25'); // del 25 al 30 de mayo de 2021
        $countdownTimer = $today->diff($target);

        //poets photos list
        $poetPictures = [
            'fure.jpg',
            'georgina.jpg',
            'karel.jpg',
            'lina-de-feria.jpg',
            'marrero.jpg',
            'pierre.jpg',
            'morejon.jpg',
            'pausides.jpg',
            'elaine-vilar.jpg',
            'waldo.jpg',
            'giselle-lucia.jpg',
            'yenis-laura-prieto.jpg'
        ];
        shuffle($poetPictures);


        $subscription = new EmailSubscriptions();
        $subscribeForm = $this->createForm(EmailSubscriptionType::class, $subscription, [
            'action' => $this->generateUrl('festival_subscribe'),
        ]);

        $contact = new ContactMessage();
        $contactForm = $this->createForm(ContactMessageType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'countdownTimer' => $countdownTimer,
            'poetPictures' => $poetPictures,
            'subscribeForm' => $subscribeForm->createView(),
            'contactForm' => $contactForm->createView()
        ]);
    }

    /**
     * @Route("/gen", name="")
     */
    public function _index(): Response
    {

        $translateX = ['-translate-x-2', '-translate-x-4','-translate-x-8','translate-x-0','translate-x-2', 'translate-x-4','translate-x-8'];
        $translateY = ['-translate-y-2', '-translate-y-4','-translate-y-8','translate-y-0','translate-y-2', 'translate-y-4','translate-y-8'];;
        $skewX = ['-skew-x-12', '-skew-x-6', '-skew-x-3', 'skew-x-0', 'skew-x-3', 'skew-x-6', 'skew-x-12'];
        $skewY = ['-skew-y-12', '-skew-y-6', '-skew-y-3', 'skew-y-0', 'skew-y-3', 'skew-y-6', 'skew-y-12'];;

        $scale = ['scale-90','scale-95','scale-100','scale-105','scale-110'];
        $rotate = ['-rotate-12','-rotate-3', '-rotate-6', 'rotate-0', 'rotate-3', 'rotate-6', 'rotate-12'];

        $val = [
            $translateX[array_rand($translateX)],
            $translateY[array_rand($translateY)],
            $skewX[array_rand($skewX)],
            $skewY[array_rand($skewY)],
            $scale[array_rand($scale)],
            $rotate[array_rand($rotate)]
        ];
        print 'transform '.implode(" ", $val);
        exit();

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'countdownTimer' => $countdownTimer
        ]);
    }


    /**
     * @Route("/contact", name="contact", methods={"POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @param Recaptcha3Validator $recaptcha3Validator
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer, Recaptcha3Validator $recaptcha3Validator): Response
    {
        $contact = new ContactMessage();

        $form = $this->createForm(ContactMessageType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $score = $recaptcha3Validator->getLastResponse()->getScore();
            if($score < 0.5)
            {
                $this->redirectToRoute('frontend');
            }

            /**
             * @var ContactMessage $contact
             */
            $contact = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();


            // Get the form fields and remove whitespace.

            $emailAddress = $contact->getEmail();
            // Get the form fields and remove whitespace.
            $name = strip_tags(trim($contact->getAuthorName()));
            $emailAddress = filter_var(trim($request->get('email')), FILTER_SANITIZE_EMAIL);
            $subject = filter_var(trim($contact->getSubject()), FILTER_SANITIZE_EMAIL);
            $message = trim($contact->getMessage());

            $email = new NotificationEmail();
            $email->addTo('fromhabanafestival@meatmemi.33mail.com', 'cubapoesia@cubarte.cult.cu');
            $email->importance(NotificationEmail::IMPORTANCE_MEDIUM);

            $content = <<<EOT
Tenemos un nuevo comentario en la web del festival
==================================================

Un usuario, llamado **$name** (*$emailAddress*)  ha dejado el siguiente mensaje

**$subject**

---

$message
 
EOT;

            $email->markdown($content);
            $email->subject('Nuevo comentario en la web festival de poesia de la habana');

            $mailer->send($email);

            $this->addFlash('info', 'Message sent');
            return $this->redirectToRoute('frontend');
        }


        $this->addFlash('error', 'Error al enviar el mensaje');
        return $this->redirectToRoute('frontend');

    }


    /**
     * @Route("/festival-subscribe", name="festival_subscribe", methods={"POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @param Recaptcha3Validator $recaptcha3Validator
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function festival_subscribe(Request $request, MailerInterface $mailer, Recaptcha3Validator $recaptcha3Validator): Response
    {

        $subscription = new EmailSubscriptions();

        $form = $this->createForm(EmailSubscriptionType::class, $subscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $score = $recaptcha3Validator->getLastResponse()->getScore();
            if($score < 0.5)
            {
                $this->redirectToRoute('frontend');
            }
            // $form->getData() holds the submitted values
            // but, the original `$subscription` variable has also been updated
            /** @var \App\Entity\EmailSubscriptions $subscription */
            $subscription = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subscription);

            try {
                $entityManager->flush();
            }
            catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
                $this->addFlash('error', 'The email is already registered');
                return $this->redirectToRoute('frontend');
            }


            $this->dispatchMessage(new SubscriptionNotification($subscription->getUniqueId()));
            $this->addFlash('info', 'Message sent');
            return $this->redirectToRoute('frontend');
        }


        $this->addFlash('error', 'Error al agregar el email');
        return $this->redirectToRoute('frontend');
    }

    /**
     * @Route("/verify/email_subscription/{uniqueId}", name="verify_email_subscription", methods={"GET"})
     * @param EmailSubscriptions $emailSubscriptions
     * @return Response
     */
    public function activateSubscription(EmailSubscriptions $emailSubscriptions): Response
    {
        $emailSubscriptions->setActive();
        $this->getDoctrine()->getManager()->persist($emailSubscriptions);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('info', 'Your email was verified!');
        return $this->redirectToRoute('frontend');
    }
}
