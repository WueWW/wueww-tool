<?php


namespace App\Service;


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


}