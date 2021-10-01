<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrantRoleFranzi extends Command
{
    protected static $defaultName = 'app:grant-role:franzi';

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('grant role "franzi"')->addArgument(
            'email',
            InputArgument::REQUIRED,
            'email address of the account'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userRepository->findOneBy(['email' => $input->getArgument('email')]);

        if ($user === null) {
            $output->writeln('User not found.');
            return 1;
        }

        $user->setRoles(array_merge($user->getRoles(), ['ROLE_FRANZI']));
        $this->userRepository->save($user);

        return 0;
    }
}
