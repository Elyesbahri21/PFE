<?php

namespace App\Controller;

use App\Repository\VisiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardResponsableController extends AbstractController
{
    private $visiteRepository;

    #[Route('/dashboard_responsable', name: 'app_dashboard_responsable')]
    public function index(VisiteRepository $visiteRepository,): Response
    {
        $this->visiteRepository = $visiteRepository;
        $visites = $visiteRepository->findAll();


        
        return $this->render('dashboard Responsable/Dashboard_Responsable.twig', [
            'controller_name' => 'DashboardController',
            'visites' => $visites
        ]);
    }


}
