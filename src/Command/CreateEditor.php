<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEditor extends Command
{
    protected static $defaultName = 'app:create-editor';

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    protected function configure()
    {
        $this->setDescription('create editor account')->addArgument(
            'email',
            InputArgument::REQUIRED,
            'email address of the new account'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = (new User())
            ->setEmail($input->getArgument('email'))
            ->setPassword('!')
            ->setRegistrationComplete(true)
            ->setRoles([User::ROLE_EDITOR]);
        $this->userService->startPasswortResetForUser($user);
    }
}
