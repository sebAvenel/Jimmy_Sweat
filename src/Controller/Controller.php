<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends AbstractController
{
    /**
     * @param string $message
     * @return Response
     */
    public function errorViewDisplay(string $message): Response
    {
        return $this->render('error/error.html.twig', [
           'errorMessage' => $message
        ]);
    }

}
