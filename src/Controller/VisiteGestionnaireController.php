<?php

namespace App\Controller;

use App\Repository\VisiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Visite;
use App\Form\VisiteType;
use App\Repository\UserRepository;

#[Route('/visite_gestionnaire')]
class VisiteGestionnaireController extends AbstractController
{
    private $visiteRepository;
    private $pvDirectory;

    #[Route('', name: 'app_Visite_Gestionnaire')]
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
    #[Route('/{id}/edit', name: 'app_Visite_Gestionnaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Visite $visite, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(VisiteType::class, $visite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pvFile */
            $pvFile = $form->get('pv')->getData();
            $visite->setResponsable($form->get('responsable')->getData());
            echo $form->get('responsable')->getData();
            if ($pvFile) {
                $originalFilename = pathinfo($pvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pvFile->guessExtension();

                try {
                    $pvFile->move($this->pvDirectory, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the file.');
                    return $this->redirectToRoute('visite_edit', ['id' => $visite->getId()]);
                }

                $visite->setPv($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Visite updated successfully!');
            return $this->redirectToRoute('app_Visite_Gestionnaire');
        }

        return $this->render('visite/edit.html.twig', [
            'visite' => $visite,
            'form' => $form->createView(),
        ]);
    }

}