<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\EmailSubscriptions;
use App\Form\ContactMessageType;
use App\Form\EmailSubscriptionType;
use App\Message\SubscriptionNotification;
use DateTime;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
            'yenis-laura-prieto.jpg',
        ];
        shuffle($poetPictures);

        $subscription = new EmailSubscriptions();
        $subscribeForm = $this->createForm(EmailSubscriptionType::class, $subscription, [
            'action' => $this->generateUrl('festival_subscribe'),
        ]);

        $contact = new ContactMessage();
        $contactForm = $this->createForm(ContactMessageType::class, $contact, [
            'action' => $this->generateUrl('contact'),
        ]);

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'countdownTimer' => $countdownTimer,
            'poetPictures' => $poetPictures,
            'subscribeForm' => $subscribeForm->createView(),
            'contactForm' => $contactForm->createView(),
        ]);
    }

    /**
     * @Route("/contact", name="contact", methods={"POST"})
     *
     * @param Request $request
     * @param MailerInterface $mailer
     * @param Recaptcha3Validator $recaptcha3Validator
     * @param TranslatorInterface $translator
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function contact(
        Request $request,
        MailerInterface $mailer,
        Recaptcha3Validator $recaptcha3Validator,
        TranslatorInterface $translator
    ): Response {
        $contact = new ContactMessage();

        $form = $this->createForm(ContactMessageType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $score = $recaptcha3Validator->getLastResponse()->getScore();
            if ($score < 0.5) {
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
     * @param TranslatorInterface $translator
     * @param Recaptcha3Validator $recaptcha3Validator
     * @return Response
     */
    public function festivalSubscribe(
        Request $request,
        TranslatorInterface $translator,
        Recaptcha3Validator $recaptcha3Validator
    ): Response {
        $subscription = new EmailSubscriptions();

        $form = $this->createForm(EmailSubscriptionType::class, $subscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $score = $recaptcha3Validator->getLastResponse()->getScore();
            if ($score < 0.5) {
                $this->redirectToRoute('frontend');
            }
            // $form->getData() holds the submitted values
            // but, the original `$subscription` variable has also been updated
            /** @var EmailSubscriptions $subscription */
            $subscription = $form->getData();

            // ... perform some action, such as saving the $subscription to the database
            // if $subscription is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subscription);

            try {
                $entityManager->flush();
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->addFlash(
                    'error',
                    $translator->trans('Esa dirección de correo ya fue registrada, revise su bandeja de email')
                );

                return $this->redirectToRoute('frontend');
            }

            $this->dispatchMessage(new SubscriptionNotification($subscription->getUniqueId()));
            $this->addFlash('info', $translator->trans('Mensaje enviado correctamente'));

            return $this->redirectToRoute('frontend');
        }

        $this->addFlash(
            'error',
            $translator->trans('Error al agregar el email. Por favor, intente nuevamente')
        );

        return $this->redirectToRoute('frontend');
    }

    /**
     * @Route("/verify/email_subscription/{uniqueId}", name="verify_email_subscription", methods={"GET"})
     * @param EmailSubscriptions $emailSubscriptions
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function activateSubscription(
        EmailSubscriptions $emailSubscriptions,
        TranslatorInterface $translator
    ): Response {
        $emailSubscriptions->setActive();
        $this->getDoctrine()->getManager()->persist($emailSubscriptions);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('info', $translator->trans('Su email fue verificado correctamente'));

        return $this->redirectToRoute('frontend');
    }
}
