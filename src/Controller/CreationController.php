<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreationController extends AbstractController
{
    #[Route('/creation', name: 'app_creation')]
    public function index(): Response
    {
        return $this->render('Creation_contrat/index.html.twig', [
            'controller_name' => 'CreationController',
        ]);
    }
}
