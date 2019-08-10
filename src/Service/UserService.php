<?php


namespace App\Service;


use App\DTO\UserRegistration;
use App\Entity\User;
use App\Repository\UserRepository;
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

    public function __construct(UserRepository $repository, MailerService $mailerService,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->repository = $repository;
        $this->mailerService = $mailerService;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function registerUser(UserRegistration $dto)
    {
        $user = new User();

        $user
            ->setEmail($dto->getEmail())
            ->setPassword($this->passwordEncoder->encodePassword($user, $dto->getPassword()));

        $this->repository->save($user);

        $this->mailerService->sendUserRegistrationMail($user);
    }
}