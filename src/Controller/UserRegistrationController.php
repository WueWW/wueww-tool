<?php

namespace App\Controller;

use App\DTO\SessionWithDetail;
use App\DTO\UserRegistration;
use App\Form\SessionWithDetailType;
use App\Form\UserRegistrationType;
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
            } catch (UsernameNotUniqueException $ex) {
                $this->addFlash('danger', 'Diese E-Mail Adresse ist bereits in Verwendung.');
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
        $userService->finishRegistration($token);
        $this->addFlash('success', 'Deine E-Mail Adresse wurde erfolgreich bestätigt. Du kannst dich jetzt anmelden.');
        return $this->redirectToRoute('app_login');
    }
}
