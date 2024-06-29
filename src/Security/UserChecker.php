<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof \App\Entity\User) {
            return;
        }

        if (!$user->getIsActive()) {
            // l'utilisateur est inactif, lancer une exception
            throw new CustomUserMessageAccountStatusException('Your user account is disabled.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // cette méthode peut rester vide
    }
}
