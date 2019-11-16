<?php

namespace App\Controller;

use App\DTO\SessionWithDetail;
use App\Entity\Session;
use App\Entity\User;
use App\Form\SessionWithDetailType;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/session")
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/", name="session_index", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            $sessions = $sessionRepository->findAll();
        } else {
            $sessions = $sessionRepository->findByUser($this->getUser());
        }

        return $this->render('session/index.html.twig', ['sessions' => $sessions]);
    }

    /**
     * @Route("/new", name="session_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            throw new \LogicException('session_new route not expected to be called by editor');
        }

        $sessionWithDetail = new SessionWithDetail();

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = (new Session())->setOwner($this->getUser())->applyDetails($sessionWithDetail);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/new.html.twig', [
            'session' => $sessionWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/organization/{id}/new", name="session_editor_create", methods={"GET","POST"})
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editorCreate(User $user, Request $request): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $sessionWithDetail = new SessionWithDetail();

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = (new Session())->setOwner($user)->applyDetails($sessionWithDetail);
            $session->accept();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/new.html.twig', [
            'session' => $sessionWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_show", methods={"GET"})
     * @param Session $session
     * @return Response
     */
    public function show(Session $session): Response
    {
        if ($session->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function edit(Request $request, Session $session): Response
    {
        if ($session->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $sessionWithDetail = $session->toSessionWithDetail();

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->applyDetails($sessionWithDetail);

            if ($this->isGranted(User::ROLE_EDITOR)) {
                $session->accept();
            }

            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/edit.html.twig', [
            'session' => $sessionWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_delete", methods={"DELETE"})
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function delete(Request $request, Session $session): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $session->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }

    /**
     * @Route("/{id}", name="session_cancel", methods={"POST"})
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function cancel(Request $request, Session $session): Response
    {
        if ($session->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('cancel' . $session->getId(), $request->request->get('_token'))) {
            $session->setCancelled(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }

    /**
     * @Route("/{id}/accept", name="session_accept", methods={"POST"})
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function accept(Request $request, Session $session): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('accept' . $session->getId(), $request->request->get('_token'))) {
            $session->accept();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }
}
