<?php

namespace App\Service;

use App\DTO\FinishPasswordReset;
use App\DTO\StartPasswordReset;
use App\DTO\UserRegistration;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Service\Exception\PasswordIsPwnedException;
use App\Service\Exception\TokenNotFoundException;
use App\Service\Exception\UsernameNotUniqueException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @var PwnedService
     */
    private $pwnedService;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    public function __construct(
        UserRepository $repository,
        MailerService $mailerService,
        UserPasswordHasherInterface $passwordHasher,
        PwnedService $pwnedService,
        TokenRepository $tokenRepository
    ) {
        $this->repository = $repository;
        $this->mailerService = $mailerService;
        $this->passwordHasher = $passwordHasher;
        $this->pwnedService = $pwnedService;
        $this->tokenRepository = $tokenRepository;
    }

    public function registerUser(UserRegistration $dto): void
    {
        $user = new User();

        $user->setEmail($dto->getEmail())->setRegistrationComplete(false);
        $user->addOrganization(new Organization());

        $this->changePassword($user, $dto->getPassword());

        $token = $user->createToken();

        try {
            $this->repository->save($user);
        } catch (UniqueConstraintViolationException $ex) {
            throw new UsernameNotUniqueException($ex);
        }

        $this->mailerService->sendUserRegistrationMail($user, $token);
    }

    public function finishRegistration(string $token): void
    {
        $tokenEntity = $this->tokenRepository->findOneBy(['token' => $token]);

        if ($tokenEntity === null) {
            throw new TokenNotFoundException();
        }

        $user = $tokenEntity->getOwner();
        $user->removeToken($tokenEntity);
        $user->setRegistrationComplete(true);

        $this->repository->save($user);
    }

    public function startPasswordReset(StartPasswordReset $startPasswordReset): void
    {
        $user = $this->repository->findOneBy(['email' => $startPasswordReset->getEmail()]);

        if ($user === null) {
            return;
        }

        $this->startPasswortResetForUser($user);
    }

    public function finishPasswordReset(FinishPasswordReset $dto): void
    {
        $tokenEntity = $this->tokenRepository->findOneBy(['token' => $dto->getToken()]);

        if ($tokenEntity === null) {
            throw new TokenNotFoundException();
        }

        $user = $tokenEntity->getOwner();
        $user->removeToken($tokenEntity);
        $user->setRegistrationComplete(true);

        $this->changePassword($user, $dto->getPassword());
        $this->repository->save($user);
    }

    public function startPasswortResetForUser(User $user): void
    {
        $token = $user->createToken();
        $this->repository->save($user);

        $this->mailerService->sendPasswordResetMail($user, $token);
    }

    public function changePassword(User $user, string $password): void
    {
        if ($this->pwnedService->isPwned($password)) {
            throw new PasswordIsPwnedException();
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
    }
}
