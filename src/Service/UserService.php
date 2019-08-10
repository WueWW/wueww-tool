<?php


namespace App\Service;


use App\DTO\UserRegistration;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Service\Exception\TokenNotFoundException;
use App\Service\Exception\UsernameNotUniqueException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    public function __construct(UserRepository $repository, MailerService $mailerService,
                                UserPasswordEncoderInterface $passwordEncoder,
                                TokenRepository $tokenRepository)
    {
        $this->repository = $repository;
        $this->mailerService = $mailerService;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenRepository = $tokenRepository;
    }

    public function registerUser(UserRegistration $dto)
    {
        $user = new User();

        $user
            ->setEmail($dto->getEmail())
            ->setPassword($this->passwordEncoder->encodePassword($user, $dto->getPassword()))
            ->setRegistrationComplete(false);

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
}