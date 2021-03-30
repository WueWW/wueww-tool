<?php

namespace App\EventSubscriber;

use App\Event\JobDeletedEvent;
use App\Event\JobModifiedEvent;
use App\Event\OrganizationModifiedEvent;
use App\Event\SessionCancelledEvent;
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
            JobDeletedEvent::class => 'onJobDeleted',
            JobModifiedEvent::class => 'onJobModified',
            SessionModifiedEvent::class => 'onSessionModified',
            SessionCancelledEvent::class => 'onSessionCancelled',
            OrganizationModifiedEvent::class => 'onOrganizationModified',
        ];
    }

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function onJobDeleted(JobDeletedEvent $event)
    {
        $this->mailerService->sendJobDeletedMail(self::TARGET_EMAIL_ADDRESS, $event->getJob());
    }

    public function onJobModified(JobModifiedEvent $event)
    {
        $this->mailerService->sendJobAwaitingApprovalMail(self::TARGET_EMAIL_ADDRESS, $event->getJob());
    }

    public function onSessionModified(SessionModifiedEvent $event)
    {
        $this->mailerService->sendSessionAwaitingApprovalMail(self::TARGET_EMAIL_ADDRESS, $event->getSession());
    }

    public function onSessionCancelled(SessionCancelledEvent $event)
    {
        $this->mailerService->sendSessionCancelledMail(self::TARGET_EMAIL_ADDRESS, $event->getSession());
    }

    public function onOrganizationModified(OrganizationModifiedEvent $event)
    {
        $this->mailerService->sendOrganizationAwaitingApprovalMail(
            self::TARGET_EMAIL_ADDRESS,
            $event->getOrganization()
        );
    }
}
