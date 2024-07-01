<?php
// src/Security/UserChecker.php
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
            throw new CustomUserMessageAccountStatusException('Your account is currently inactive.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // This method can remain empty
    }
}