<?php

namespace App\Command;

use App\Repository\SessionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetTimes extends Command
{
    protected static $defaultName = 'app:reset-times';

    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        parent::__construct();

        $this->sessionRepository = $sessionRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('reset start/end times of all sessions, aka corona mode')->addOption(
            'force',
            'f',
            null,
            'Force operation'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force')) {
            $output->writeln('This command does nothing unless you force it to do');
            return 0;
        }

        $this->sessionRepository->resetAllStartAndEndTimes();
        return 0;
    }
}
