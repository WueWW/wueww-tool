<?php

namespace App\Controller;

use App\DTO\UserRegistration;
use App\Form\UserRegistrationType;
use App\Service\Exception\PasswordIsPwnedException;
use App\Service\Exception\TokenNotFoundException;
use App\Service\Exception\UsernameNotUniqueException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserRegistrationController extends AbstractController
{
    /**
     * @Route("/user/registration", name="user_registration")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, UserService $userService): Response
    {
        $userRegistration = new UserRegistration();

        $form = $this->createForm(UserRegistrationType::class, $userRegistration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userService->registerUser($userRegistration);
                $this->addFlash(
                    'success',
                    'Dein Benutzerkonto wurde angelegt. Bitte klicke jetzt auf den Link, den wir dir eben per E-Mail gesendet haben.'
                );
                return $this->redirectToRoute('app_login');
            } catch (UsernameNotUniqueException $ex) {
                $this->addFlash('danger', 'Diese E-Mail-Adresse ist bereits in Verwendung.');
            } catch (PasswordIsPwnedException $ex) {
                $this->addFlash(
                    'danger',
                    'Das verwendete Passwort steht auf der Liste bereits geleakten Passwörter von haveibeenpwned.com. Bitte verwende ein sicheres Passwort.'
                );
            }
        }

        return $this->render('user_registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/registration/{token}", name="finish_registration")
     * @param string $token
     * @param UserService $userService
     * @return Response
     */
    public function finish(string $token, UserService $userService): Response
    {
        try {
            $userService->finishRegistration($token);
            $this->addFlash(
                'success',
                'Deine E-Mail-Adresse wurde erfolgreich bestätigt. Du kannst dich jetzt anmelden.'
            );
        } catch (TokenNotFoundException $ex) {
            $this->addFlash('warning', 'Das übermittelte Token ist ungültig, bzw. wurde bereits verwendet.');
        }

        return $this->redirectToRoute('app_login');
    }
}
