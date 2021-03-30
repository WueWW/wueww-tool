<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\Organization;
use App\Entity\Session;
use App\Entity\Token;
use App\Entity\User;
use Twig\Environment;

class MailerService
{
    const FROM_ADDRESS = 'noreply@tool.wueww.de';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(Environment $twig, \Swift_Mailer $swiftMailer)
    {
        $this->twig = $twig;
        $this->mailer = $swiftMailer;
    }

    public function sendUserRegistrationMail(User $user, Token $token)
    {
        $message = (new \Swift_Message('Deine Registrierung beim WueWW Tool'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('emails/user_registration.txt.twig', [
                    'user' => $user,
                    'token' => $token->getToken(),
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }

    public function sendPasswordResetMail(?User $user, Token $token)
    {
        $message = (new \Swift_Message('WueWW Tool Passwort zurücksetzen'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('emails/password_reset.txt.twig', [
                    'user' => $user,
                    'token' => $token->getToken(),
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }

    public function sendSessionAwaitingApprovalMail(string $toAddress, Session $session)
    {
        $message = (new \Swift_Message('Event geändert'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($toAddress)
            ->setBody(
                $this->twig->render('emails/session_awaiting_approval.txt.twig', [
                    'session' => $session,
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }

    public function sendOrganizationAwaitingApprovalMail(string $toAddress, Organization $organization)
    {
        $message = (new \Swift_Message('Veranstalter geändert'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($toAddress)
            ->setBody(
                $this->twig->render('emails/organization_awaiting_approval.txt.twig', [
                    'organization' => $organization,
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }

    public function sendSessionCancelledMail(string $toAddress, Session $session)
    {
        $message = (new \Swift_Message('Event abgesagt'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($toAddress)
            ->setBody(
                $this->twig->render('emails/session_cancelled.txt.twig', [
                    'session' => $session,
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }

    public function sendJobDeletedMail(string $toAddress, Job $job)
    {
        $message = (new \Swift_Message('Job gelöscht'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($toAddress)
            ->setBody(
                $this->twig->render('emails/job_deleted.txt.twig', [
                    'job' => $job,
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }

    public function sendJobAwaitingApprovalMail(string $toAddress, Job $job)
    {
        $message = (new \Swift_Message('Job geändert'))
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($toAddress)
            ->setBody(
                $this->twig->render('emails/job_awaiting_approval.txt.twig', [
                    'job' => $job,
                ]),
                'text/plain'
            );

        $this->mailer->send($message);
    }
}
