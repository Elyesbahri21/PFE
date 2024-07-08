<?php
namespace App\Controller;

use App\Repository\VisiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardResponsableController extends AbstractController
{
    private $visiteRepository;

    public function __construct(VisiteRepository $visiteRepository)
    {
        $this->visiteRepository = $visiteRepository;
    }

    #[Route('/dashboard_responsable', name: 'app_dashboard_responsable')]
    public function index(): Response
    {
        $user = $this->getUser();
        $currentDate = new \DateTime(); // Date actuelle
        $visites = $this->visiteRepository->findAll();
        $totalVisites = $this->visiteRepository->countTotalVisitesForUser($this->getUser());
        $completedVisites = $this->visiteRepository->countCompletedVisites($user, $currentDate);
        $upcomingVisites = $this->visiteRepository->countUpcomingVisites($user, $currentDate);
        $preventives = $this->visiteRepository->countVisitesByType($user, 'Préventive');
        $curatives = $this->visiteRepository->countVisitesByType($user, 'Curative');
        $evolutives = $this->visiteRepository->countVisitesByType($user, 'Évolutive');
        $this->addFlash('success', 'Ajout avec succès');


        return $this->render('dashboard Responsable/Dashboard_Responsable.twig', [
            'controller_name' => 'DashboardController',
            'visites' => $visites,
            'totalVisites' => $totalVisites,
            'completedVisites' => $completedVisites,
            'upcomingVisites' => $upcomingVisites,
            'preventives' => $preventives,
            'curatives' => $curatives,
            'evolutives' => $evolutives,
        ]);
    }

    #[Route('/dashboard_responsable/visites', name: 'dashboard_responsable_visites_json')]
    public function getVisitesJson(): JsonResponse
    {
        $visites = $this->visiteRepository->findAll();
        $data = [];

        foreach ($visites as $visite) {
            $data[] = [
                'title' => $visite->getType(),
                'start' => $visite->getDate()->format('Y-m-d\TH:i:s'),
                'description' => $visite->getDescription(),
                'responsable' => $visite->getResponsable()->getUsername(),
                'contratName' => $visite->getContrat()->getNom(),
                'url' => $this->generateUrl('visite_show', ['id' => $visite->getId()])
            ];
        }

        return new JsonResponse($data);
    }
}
