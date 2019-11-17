<?php

namespace App\DTO;

class StartPasswordReset
{
    /**
     * @var string
     */
    private $email;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
