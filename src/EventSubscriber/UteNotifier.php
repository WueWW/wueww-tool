<?php

namespace App\EventSubscriber;

use App\Event\OrganizationModifiedEvent;
use App\Event\SessionModifiedEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UteNotifier implements EventSubscriberInterface
{
    const TARGET_EMAIL_ADDRESS = 'ute.muendlein@wueww.de';

    /**
     * @var MailerService
     */
    private $mailerService;

    public static function getSubscribedEvents()
    {
        return [
            SessionModifiedEvent::class => 'onSessionModified',
            OrganizationModifiedEvent::class => 'onOrganizationModified',
        ];
    }

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function onSessionModified(SessionModifiedEvent $event)
    {
        $this->mailerService->sendSessionAwaitingApprovalMail(self::TARGET_EMAIL_ADDRESS, $event->getSession());
    }

    public function onOrganizationModified(OrganizationModifiedEvent $event)
    {
        $this->mailerService->sendOrganizationAwaitingApprovalMail(
            self::TARGET_EMAIL_ADDRESS,
            $event->getOrganization()
        );
    }
}