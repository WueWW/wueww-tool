<?php

namespace App\Command;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Service\MailerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendBatchMailNotification extends Command
{
    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var MailerService
     */
    private $mailerService;

    public function __construct(OrganizationRepository $organizationRepository, MailerService $mailerService)
    {
        parent::__construct();

        $this->organizationRepository = $organizationRepository;
        $this->mailerService = $mailerService;
    }

    protected function configure()
    {
        $this->setName('app:send-batch-mail-notification')->setDescription(
            'Send pending organization email address mail notifications to Ute + reset flags'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orgs = $this->organizationRepository->findOrganizationsAwaitingMailNotification();
        $mails = \array_map(function (Organization $organization) {
            return $organization->getOwner()->getEmail();
        }, $orgs);

        $this->mailerService->sendBatchMailNotification($mails);

        foreach ($orgs as $org) {
            $org->setSendBatchMailNotification(false);
        }

        $this->organizationRepository->flush();

        return 0;
    }
}
