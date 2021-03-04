<?php

namespace App\Controller;

use DateTime;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

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

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'countdownTimer' => $countdownTimer,
            'poetPictures' => $poetPictures
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
     * @return Response
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($request->get('name')));
        $name = str_replace(array("\r","\n"),array(" "," "),$name);
        $emailAddress = filter_var(trim($request->get('email')), FILTER_SANITIZE_EMAIL);
        $subject = filter_var(trim($request->get('subject')), FILTER_SANITIZE_EMAIL);
        $message = trim($request->get('message'));

        // Check that data was sent to the mailer.
        if ( empty($name) OR empty($message) OR empty($subject) OR !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.

            return new Response('Please complete the form and try again.', 400);
        }

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

        $this->addFlash('notice', 'Message sent');
        return $this->redirectToRoute('frontend');
    }
}
