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
 * @Route("/organization")
 */
class OrganizationController extends AbstractController
{

    /**
     * @Route("/", name="organization_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $organizations = $userRepository->findAllReporters();

        return $this->render('organization/index.html.twig', ['organizations' => $organizations,]);
    }

    /**
     * @Route("/{id}", name="organization_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        return $this->render('organization/show.html.twig', [
            'organization' => $user,
        ]);
    }

    /**
     * @Route("/{id}/accept", name="organization_accept", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function accept(Request $request, User $user): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('accept'.$user->getId(), $request->request->get('_token'))) {
            $user->accept();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('organization_index');
    }

}