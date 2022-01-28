<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Exception\RegistrationIncompleteException;
use App\Security\Exception\UnsupportedUserTypeException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserTypeException();
        }

        if (!$user->isRegistrationComplete()) {
            $ex = new RegistrationIncompleteException();
            $ex->setUser($user);
            throw $ex;
        }
    }
}
