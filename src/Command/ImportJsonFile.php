<?php

namespace App\Command;

use App\Service\SessionJsonProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportJsonFile extends Command
{
    protected static $defaultName = 'app:import-json-file';

    /**
     * @var SessionJsonProcessor
     */
    private $sessionJsonProcessor;

    public function __construct(SessionJsonProcessor $sessionJsonProcessor)
    {
        parent::__construct();

        $this->sessionJsonProcessor = $sessionJsonProcessor;
    }

    protected function configure()
    {
        $this->setDescription('import session.json 0.3.0 feed')->addArgument(
            'filename',
            InputArgument::REQUIRED,
            'session.json to process'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sessionJsonProcessor->processFile($input->getArgument('filename'));
        return 0;
    }
}
