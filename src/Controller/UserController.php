<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password before saving
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Ajout avec succès');

            return $this->redirectToRoute('app_dashboard_utilisateurs', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Modifié avec succès');
            // Check if password is changed
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                // Hash the new password
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }


        
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_utilisateurs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->addFlash('success', 'Supprimer avec succès');
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard_utilisateurs', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/user/{id}/activate', name: 'app_user_activate', methods: ['POST'])]
    public function activate(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(true);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_user_index');
    }
    
    #[Route('/user/{id}/ActivateDeactivate', name: 'app_user_Status', methods: ['POST'])]
    public function ActivateDeactivate(User $user, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        if($user->getIsActive())
        $user->setIsActive(false);
        else
        $user->setIsActive(true);

        $entityManager->flush();
    
        return $this->redirectToRoute('app_dashboard_utilisateurs');
    }
    
    

    
}
