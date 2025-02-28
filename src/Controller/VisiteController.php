<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Visite;
use App\Form\VisiteType;
use App\Repository\VisiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/visite')]
class VisiteController extends AbstractController
{
    private $visiteRepository;
    private $pvDirectory;
    private $security;

    public function __construct(string $pvDirectory, VisiteRepository $visiteRepository, Security $security)
    {
        $this->pvDirectory = $pvDirectory;
        $this->visiteRepository = $visiteRepository;
        $this->security = $security;
    }

    #[Route('/', name: 'visite_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $currentDate = new \DateTime(); // Date actuelle
        $totalVisites = $this->visiteRepository->countTotalVisitesForUser($this->getUser());
        $completedVisites = $this->visiteRepository->countCompletedVisites($user, $currentDate);
        $upcomingVisites = $this->visiteRepository->countUpcomingVisites($user, $currentDate);
        $preventives = $this->visiteRepository->countVisitesByType($user, 'Préventive');
        $curatives = $this->visiteRepository->countVisitesByType($user, 'Curative');
        $evolutives = $this->visiteRepository->countVisitesByType($user, 'Évolutive');

        return $this->render('visite/index.html.twig', [
            'totalVisites' => $totalVisites,
            'completedVisites' => $completedVisites,
            'upcomingVisites' => $upcomingVisites,
            'preventives' => $preventives,
            'curatives' => $curatives,
            'evolutives' => $evolutives,
            'visites' => $this->visiteRepository->findByResponsable($user = $this->getUser())
        ]);
    }
    
    #[Route('/new', name: 'visite_new', methods: ['GET', 'POST'])]
    public function new(Request $request,UserRepository $userRepository,EntityManagerInterface $entityManager, SluggerInterface $slugger,MailerInterface $mailer): Response
    {
        /*
                if (!$user instanceof SluggerInterface) {
            return $this->render('visite/index.html.twig');
        }
        */
        $visite = new Visite();
        $form = $this->createForm(VisiteType::class, $visite);
        $form->handleRequest($request);

        


        $user = $this->getUser();
        $userType = $user->getId();
        $visite->setResponsable($user);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pvFile */
            $pvFile = $form->get('pv')->getData();
            $visite->setResponsable($form->get('responsable')->getData());

            if ($pvFile) {
                $originalFilename = pathinfo($pvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pvFile->guessExtension();

                try {
                    $pvFile->move($this->pvDirectory, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the file.');
                    return $this->redirectToRoute('visite_new');
                }

                $visite->setPv($newFilename);
            }

            $entityManager->persist($visite);
            $entityManager->flush();
            $this->addFlash('success', 'Visite created successfully!');
            return $this->redirectToRoute('visite_index');
        }

        return $this->render('visite/new.html.twig', [
            'visite' => $visite,
            'form' => $form->createView(),
            'userType' => $userType,
        ]);
    }

    #[Route('/{id}', name: 'visite_show', methods: ['GET'])]
    public function show(Visite $visite): Response
    {
        return $this->render('visite/show.html.twig', [
            'visite' => $visite,
        ]);
    }

    #[Route('/{id}/edit', name: 'visite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Visite $visite, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(VisiteType::class, $visite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pvFile */
            $pvFile = $form->get('pv')->getData();
            $visite->setResponsable($form->get('responsable')->getData());

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
            return $this->redirectToRoute('visite_index');
        }

        return $this->render('visite/edit.html.twig', [
            'visite' => $visite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'visite_delete', methods: ['POST'])]
    public function delete(Request $request, Visite $visite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $visite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($visite);
            $entityManager->flush();

            $this->addFlash('success', 'Visite deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('visite_index');
    }
}
