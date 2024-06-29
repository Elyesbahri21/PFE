<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ContratRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ContratRepository $contratRepository, UserRepository $UserRepository, ClientRepository $ClientRepository): Response
    {
        $contrats = $contratRepository->findAll();
        $totalContrats = count($contrats);
        $users = $UserRepository->findAll();
        $totalUsers = count($users);
        $Clients = $ClientRepository->findAll();
        $totalClients = count($Clients);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'totalContrats' => $totalContrats,
            'totalUsers' => $totalUsers,
            'totalClients' => $totalClients,

        ]);
    }


}
