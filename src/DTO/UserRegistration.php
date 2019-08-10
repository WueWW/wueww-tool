<?php


namespace App\DTO;


class UserRegistration
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
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