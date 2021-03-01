<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="frontend")
     */
    public function index(): Response
    {
        $today = new DateTime('today');
        $target = new DateTime('2021-05-15'); // del 15 al 19 de mayo
        $countdownTimer = $today->diff($target);

        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
            'countdownTimer' => $countdownTimer
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
}
