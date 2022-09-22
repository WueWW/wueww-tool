<?php

namespace App\Service;

use App\Entity\Apprenticeship;
use App\Entity\Organization;
use App\Entity\Session;
use App\Entity\Token;
use App\Entity\User;
use App\EventSubscriber\UteNotifier;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    const FROM_ADDRESS = 'noreply@tool.wueww.de';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(Environment $twig, MailerInterface $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function sendUserRegistrationMail(User $user, Token $token): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($user->getEmail())
            ->subject('Deine Registrierung beim WueWW Tool')
            ->text(
                $this->twig->render('emails/user_registration.txt.twig', [
                    'user' => $user,
                    'token' => $token->getToken(),
                ])
            );

        $this->mailer->send($message);
    }

    public function sendPasswordResetMail(?User $user, Token $token): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($user->getEmail())
            ->subject('WueWW Tool Passwort zurücksetzen')
            ->text(
                $this->twig->render('emails/password_reset.txt.twig', [
                    'user' => $user,
                    'token' => $token->getToken(),
                ])
            );

        $this->mailer->send($message);
    }

    public function sendSessionAwaitingApprovalMail(string $toAddress, Session $session): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Event geändert')
            ->text(
                $this->twig->render('emails/session_awaiting_approval.txt.twig', [
                    'session' => $session,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendOrganizationAwaitingApprovalMail(string $toAddress, Organization $organization): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Veranstalter geändert')
            ->text(
                $this->twig->render('emails/organization_awaiting_approval.txt.twig', [
                    'organization' => $organization,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendSessionCancelledMail(string $toAddress, Session $session): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Event abgesagt')
            ->text(
                $this->twig->render('emails/session_cancelled.txt.twig', [
                    'session' => $session,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendApprenticeshipAwaitingApprovalMail(string $toAddress, Apprenticeship $apprenticeship)
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Ausbildungsstätte geändert')
            ->text(
                $this->twig->render('emails/apprenticeship_awaiting_approval.txt.twig', [
                    'apprenticeship' => $apprenticeship,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendBatchMailNotification(string $toAddress, array $mails)
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('E-Mail-Adressen neuer Organisatoren')
            ->text($this->twig->render('emails/batch_mail_notification.txt.twig', ['addrs' => $mails]));
        $this->mailer->send($message);
    }
}
