<?php

namespace App\Controller;

use App\Repository\ContratRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardGestionnaireController extends AbstractController
{
    #[Route('/dashboard_gestionnaire', name: 'app_dashboard_gestionnaire')]
    public function index(ContratRepository $contratRepository, ProduitRepository $produitRepository): Response
    {
        $contrats = $contratRepository->findAll();
        $totalContrats = count($contrats);
        $produits = $produitRepository->findAll();
        $totalproduits = count($produits);
        $availableContrats = count(array_filter($contrats, fn($contrat) => $contrat->getStatus() === 'Disponible'));
        $unavailableContrats = count(array_filter($contrats, fn($contrat) => $contrat->getStatus() === 'Indisponible'));
 
        return $this->render('dashboard gestionnaire/Dashboard_Gestionnaire.twig', [
            'controller_name' => 'DashboardController',
            'totalContrats' => $totalContrats,
            'totalproduits' => $totalproduits,
            'availableContrats' => $availableContrats,
            'unavailableContrats' => $unavailableContrats,

        ]);
    }


}
