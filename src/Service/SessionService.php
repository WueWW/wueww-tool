<?php


namespace App\Service;


use App\Entity\Session;
use App\Entity\User;
use App\Repository\SessionRepository;

class SessionService
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    /**
     * @return Session[]
     */
    public function findAll(): array {
        return $this->sessionRepository->findAll();
    }

    public function findByUser(User $user)
    {
        return $this->sessionRepository->findByUser($user);
    }
}