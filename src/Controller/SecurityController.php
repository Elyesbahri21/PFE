<?php

namespace App\Controller;

use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Author
{
    // ...

    public static function loadValidatorMetadata(ClassMetadata $metadata, FlashyNotifier $flashy): void
    {
        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'The email "{{ value }}" is not a valid email.',
        ]));

        $flashy->success('Event created!', 'http://your-awesome-link.com');

    }
}


class PagesController extends AbstractController {
    /**
    * @Route("/", name="login")
    */
    public function login()
    {
    
    
        return $this->redirectToRoute('home');
    }
}

