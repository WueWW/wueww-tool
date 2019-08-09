<?php

namespace App\Controller;

use App\DTO\SessionWithDetail;
use App\Entity\Session;
use App\Entity\User;
use App\Form\SessionWithDetailType;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/session")
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/", name="session_index", methods={"GET"})
     */
    public function index(SessionService $sessionService): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            $sessions = $sessionService->findAll();
        } else {
            $sessions = $sessionService->findByUser($this->getUser());
        }

        return $this->render('session/index.html.twig', ['sessions' => $sessions,]);
    }

    /**
     * @Route("/new", name="session_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sessionWithDetail = new SessionWithDetail();

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = (new Session())
                ->setOwner($this->getUser())
                ->applyDetails($sessionWithDetail);

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
     */
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Session $session): Response
    {
        $sessionWithDetail = $session->toSessionWithDetail();

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->applyDetails($sessionWithDetail);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/edit.html.twig', [
            'session' => $sessionWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Session $session): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }
}
