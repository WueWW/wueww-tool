<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FinishPasswordReset
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     * @Assert\Length(min = 8, minMessage="Das Passwort muss mindestens acht Zeichen lang sein.")
     */
    private $password;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
}
