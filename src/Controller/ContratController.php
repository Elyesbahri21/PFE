<?php
namespace App\Controller;

use App\Entity\Contrat;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Security;


class ContratController extends AbstractController
{
    private $contratRepository;

    private string $brochuresDirectory;
    private $security;
    public function __construct(string $brochuresDirectory, Security $security,ContratRepository $contratRepository)
    {
        $this->brochuresDirectory = $brochuresDirectory;
        $this->security = $security;
        $this->contratRepository = $contratRepository;

    }
    


    /**
     * @Route("/contrat", name="app_contrat_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager , ContratRepository $contratRepository): Response
    {
        $user = $this->security->getUser();

        // If the user is not logged in, redirect them to the login page
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        

        $contrats = $contratRepository->findBy(['user' => $user]);
        $totalContrats = count($contrats);
        $availableContrats = count(array_filter($contrats, fn($contrat) => $contrat->getStatus() === 'Disponible'));
        $unavailableContrats = count(array_filter($contrats, fn($contrat) => $contrat->getStatus() === 'Indisponible'));

        $contrats = $contratRepository->findBy(['user' => $user]);

        return $this->render('contrat/index.html.twig', [
            'contrats' => $contrats,
            'totalContrats' => $totalContrats,
            'availableContrats' => $availableContrats,
            'unavailableContrats' => $unavailableContrats,
        ]);



    }






    /**
     * @Route("/contrat/show/{id}", name="app_contrat_show", methods={"GET"})
     */
    public function show(Contrat $contrat): Response
    {
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }

        /**
     * @Route("/contrat/edit/{id}", name="app_contrat_edit", methods={"GET", "POST"})
     * @ParamConverter("contrat", class="App\Entity\Contrat")
     */
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        
        // Get the current user
    $user = $this->getUser(); // Assuming you're using Symfony's built-in user authentication
    
    // Ensure the user is set correctly on the Contrat
    if ($contrat->getUser() === null) {
        $contrat->setUser($user);
    }


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contrat/new", name="app_contrat_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);
        $user = $this->getUser();

        $user = $this->getUser(); // Symfony's built-in user authentication
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to create a contrat.');
        }
        $contrat->setUser($user);
        // Automatically set the user to the currently logged-in user
        $user = $this->security->getUser();


        
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);


        if ($user) {
            $contrat->setUser($user);
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            $entityManager = $this->getDoctrine()->getManager();
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

            $this->addFlash('success', 'Contract created successfully!');

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    


    /**
     * @Route("/contrat/delete/{id}", name="app_contrat_delete", methods={"POST"})
     */
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contrat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();

            $this->addFlash('success', 'Contract deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_contrat_index');
    }



    /**
 * @Route("/contrat/renew/{id}", name="app_contrat_renew", methods={"GET", "POST"})
 */
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


 /**
     * @Route("/contrat/modify-pdf/{id}", name="app_contrat_modify_pdf", methods={"GET"})
     */
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
