<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profilee')]
class ProfileeController extends AbstractController
{
 
 

    #[Route('/', name: 'app_profilee_show', methods: ['GET'])]
    public function show(): Response
    {
        $user = $this->getUser();
        return $this->render('profilee/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'app_profilee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_profilee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profilee/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'app_profilee_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if ($this->isCsrfTokenValid('delete'.$user = $this->getUser(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_profilee_index', [], Response::HTTP_SEE_OTHER);
    }
}
