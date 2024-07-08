<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardUtilisateursController extends AbstractController
{

    #[Route('/dashboard/utilisateurs', name: 'app_dashboard_utilisateurs')]
    public function index(UserRepository $userRepository): Response
    {
        $totalUsers = $userRepository->count([]); 
        $users = $userRepository->findAll();
        $this->addFlash('success', 'Ajout avec succès');


        

        return $this->render('dashboard/dashboard_utilisateurs.html.twig', [
            'controller_name' => 'DashboardController',
            'users' => $users,
            'totalUsers' => $totalUsers,

        ]);
    }
    
}
