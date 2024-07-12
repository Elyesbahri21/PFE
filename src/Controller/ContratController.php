<?php
namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Visite;
use App\Form\ContratRenewType;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use App\Service\PdfModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use DateInterval;


class ContratController extends AbstractController
{
    private $VisiteRepository;
    private $contratRepository;
    private $brochuresDirectory;
    private $security;

    public function __construct(string $brochuresDirectory,Security $security, ContratRepository $contratRepository)
    {
        $this->brochuresDirectory = $brochuresDirectory;
        $this->security = $security;
        $this->contratRepository = $contratRepository;
    }

    #[Route('/contrat', name: 'app_contrat_index', methods: ['GET'])]
    public function index(MailerInterface $mailer): Response
    {
        $contrats = $this->contratRepository->findAll();

        // Counting totals based on status
        $totalContrats = count($contrats);
        $availableContrats = count(array_filter($contrats, fn($contrat) => $contrat->getStatus() === 'Disponible'));
        $unavailableContrats = count(array_filter($contrats, fn($contrat) => $contrat->getStatus() === 'Indisponible'));

        $xcontrats = $this->contratRepository->findExpiringSoon();
        foreach($xcontrats as $xcontrat)
        {
        $message = (new Email())
        ->from('contratlab@gmail.com')
        ->to('elyesbahri.contact@gmail.com')
        ->subject('Votre contrat expire bientôt')
        ->html($this->renderView('contrat/email.html.twig', [
            'contrat' => $xcontrat,
        ]));
        $mailer->send($message);
        }
        

        return $this->render('contrat/index.html.twig', [
            'contrats' => $contrats,
            'totalContrats' => $totalContrats,
            'availableContrats' => $availableContrats,
            'unavailableContrats' => $unavailableContrats,
        ]);
    }

    #[Route('/contrat/show/{id}', name: 'app_contrat_show', methods: ['GET'])]
    public function show(Contrat $contrat): Response
    {
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }

    #[Route('/contrat/edit/{id}', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('contrat', class: 'App\Entity\Contrat')]
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contrat/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager,MailerInterface $mailer): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        $user = $this->getUser(); // Get the current user
        // Add flash message for success notification
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to create a contrat.');
        }

        $contrat->setUser($user); // Set the user to the current logged-in user

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move($this->brochuresDirectory, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred while uploading the file.');
                    return $this->redirectToRoute('app_contrat_new');
                }

                $contrat->setBrochureFilename($newFilename);
            }

            $entityManager->persist($contrat);
            $entityManager->flush();
            
            ////////////////////////////////
            $now = new \DateTime();
            for ($i = 1; $i < 4; $i++) {
                $visite = new Visite();
                $visite->setDate((clone $now)->add(new DateInterval('P' . ($i * 4) . 'M')));
                $visite->setType('préventive'); 
                $visite->setDescription('Description'); 
                $visite->setPv('PV'); 
                $visite->setContrat($contrat);
                $visite->setResponsable(null);
                $entityManager->persist($visite);
                $entityManager->flush();

                $message = (new Email())
                ->from('contratlab@gmail.com')
                ->to('contratlab@contratlab.com')
                ->subject('Nouvelle visite affectée')
                ->html($this->renderView('visite/email.html.twig', [
                    'visite' => $visite,
                ]));
                $mailer->send($message);


            }
            ///////////////////////////////

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contrat/delete/{id}', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();

            $this->addFlash('success', 'Contrat Supprimer avec succées!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_contrat_index');
    }

    #[Route('/contrat/renew/{id}', name: 'app_contrat_renew', methods: ['GET', 'POST'])]
    public function renew(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratRenewType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Contract renewed successfully!');

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/renew.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contrat/modify-pdf/{id}', name: 'app_contrat_modify_pdf', methods: ['GET'])]
    public function modifyPdf(Contrat $contrat, PdfModifier $pdfModifier, LoggerInterface $logger): Response
    {
        $sourceFilePath = $this->getParameter('brochures_directory') . '/' . $contrat->getBrochureFilename();
        $outputFilePath = $this->getParameter('brochures_directory') . '/modified_' . $contrat->getBrochureFilename();

        if (!file_exists($sourceFilePath)) {
            $this->addFlash('error', 'Source PDF file not found!');
            $logger->error("Source PDF file not found: " . $sourceFilePath);
            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        try {
            $pdfModifier->modifyPdf($sourceFilePath, $outputFilePath);
            $this->addFlash('success', 'PDF modifié avec succès !');
            $logger->info("PDF modified successfully: " . $outputFilePath);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error modifying PDF: ' . $e->getMessage());
            $logger->error("Error modifying PDF: " . $e->getMessage());
        }

        return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
    }
}
