<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardsController extends AbstractController
{
    #[Route('/cards', name: 'app_cards')]
    public function index(): Response
    {
        return $this->render('cards/index.html.twig', [
            'controller_name' => 'CardsController',
        ]);
    }
}
