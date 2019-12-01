<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistration
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     * @Assert\Length(min = 8, minMessage="Das Passwort muss mindestens acht Zeichen lang sein.")
     */
    private $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserRegistration
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserRegistration
    {
        $this->password = $password;
        return $this;
    }
}
