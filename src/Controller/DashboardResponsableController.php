<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardResponsableController extends AbstractController
{
    #[Route('/dashboard_responsable', name: 'app_dashboard_responsable')]
    public function index(): Response
    {

        
        return $this->render('dashboard Responsable/Dashboard_Responsable.twig', [
            'controller_name' => 'DashboardController',
         
        ]);
    }


}
