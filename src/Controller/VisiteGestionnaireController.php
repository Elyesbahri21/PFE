<?php

namespace App\Controller;

use App\Repository\VisiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Visite;
use App\Form\VisiteType;
use App\Repository\UserRepository;

class VisiteGestionnaireController extends AbstractController
{
    private $visiteRepository;

    #[Route('/visite_gestionnaire', name: 'app_Visite_Gestionnaire')]
    public function index(VisiteRepository $visiteRepository): Response
    {
        $this->visiteRepository = $visiteRepository;
        $visites = $this->visiteRepository->findAll();
        $user = $this->getUser();
        $currentDate = new \DateTime(); // Date actuelle
        $totalVisites = count($visites);
        $completedVisites = $this->visiteRepository->countCompletedVisites($user, $currentDate);
        $upcomingVisites = $this->visiteRepository->countUpcomingVisites($user, $currentDate);
        $preventives = $this->visiteRepository->countVisitesByType($user, 'Préventive');
        $curatives = $this->visiteRepository->countVisitesByType($user, 'Curative');
        $evolutives = $this->visiteRepository->countVisitesByType($user, 'Évolutive');

        return $this->render('dashboard gestionnaire/Visite_Gestionnaire.html.twig', [
            'visites' => $visites,
            'totalVisites' => $totalVisites,
            'completedVisites' => $completedVisites,
            'upcomingVisites' => $upcomingVisites,
            'preventives' => $preventives,
            'curatives' => $curatives,
            'evolutives' => $evolutives,
        ]);
    }
}
