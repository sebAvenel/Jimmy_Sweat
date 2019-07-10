<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @param TrickRepository $repository
     * @return Response
     */
    public function homePage(TrickRepository $repository): Response
    {
        return $this->render('home/home.html.twig');
    }
}