<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasicQuackController extends AbstractController
{
    #[Route('/basicQuack', name: 'app_quack')]
    public function index(): Response
    {
        return $this->render('basicQuack/index.html.twig', [
            'controller_name' => 'BasicQuackController',
        ]);
    }
}
