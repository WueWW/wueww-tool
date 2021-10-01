<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/user_approval")
 */
class UserApprovalController extends AbstractController
{
    /**
     * @Route("/", name="user_approval_index")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->isGranted(User::ROLE_USER_APPROVER)) {
            throw new AccessDeniedException();
        }

        $users = $userRepository->findAllIncomplete();

        return $this->render('user_approval/index.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/{id}", name="user_approval_override", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function approveUser(Request $request, User $user): Response
    {
        if (!$this->isGranted(User::ROLE_USER_APPROVER)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('user_approval' . $user->getId(), $request->request->get('_token'))) {
            $user->setRegistrationComplete(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Der Benutzer wurde freigegeben.');
        }

        return $this->redirectToRoute('user_approval_index');
    }
}
