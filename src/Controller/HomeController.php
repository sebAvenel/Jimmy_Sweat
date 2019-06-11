<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function homePage(): Response
    {
        return $this->render('home/home.html.twig');
    }
}