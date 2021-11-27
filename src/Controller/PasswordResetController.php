<?php

namespace App\Controller;

use App\DTO\FinishPasswordReset;
use App\DTO\StartPasswordReset;
use App\Form\FinishPasswordResetType;
use App\Form\StartPasswordResetType;
use App\Service\Exception\PasswordIsPwnedException;
use App\Service\Exception\TokenNotFoundException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    /**
     * @Route("/user/password-reset", name="password_reset")
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    public function start(Request $request, UserService $userService): Response
    {
        $startPasswordReset = new StartPasswordReset();

        $form = $this->createForm(StartPasswordResetType::class, $startPasswordReset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->startPasswordReset($startPasswordReset);
            $this->addFlash(
                'info',
                'Eine E-Mail mit weiteren Informationen zum Zurücksetzen des Passworts wurde an die angegebene Adresse gesendet.'
            );
        }

        return $this->render('password_reset/start.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/password-reset/{token}", name="passwort_reset_finish")
     * @param Request $request
     * @param string $token
     * @return Response
     */
    public function finish(Request $request, string $token, UserService $userService): Response
    {
        $finishPasswordReset = new FinishPasswordReset($token);

        $form = $this->createForm(FinishPasswordResetType::class, $finishPasswordReset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userService->finishPasswordReset($finishPasswordReset);
                $this->addFlash('success', 'Dein Passwort wurde zurückgesetzt. Du kannst dich jetzt anmelden.');
                return $this->redirectToRoute('app_login');
            } catch (TokenNotFoundException $ex) {
                $this->addFlash('danger', 'Beim Zurücksetzen deines Passworts ist ein Problem aufgetreten.');
            } catch (PasswordIsPwnedException $ex) {
                $this->addFlash(
                    'danger',
                    'Das verwendete Passwort steht auf der Liste bekannt schwacher Passwörter von haveibeenpwned.com. Bitte verwende ein sicheres Passwort.'
                );
            }
        }

        return $this->render('password_reset/finish.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
