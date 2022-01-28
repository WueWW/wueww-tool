<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class RegistrationIncompleteException extends CustomUserMessageAccountStatusException
{
    public function __construct()
    {
        parent::__construct('Die E-Mail-Adresse wurde noch nicht bestätigt.');
    }
}
