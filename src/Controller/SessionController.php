<?php

namespace App\Controller;

use App\DTO\SessionWithDetail;
use App\Entity\Organization;
use App\Entity\Session;
use App\Entity\User;
use App\Event\SessionCancelledEvent;
use App\Event\SessionModifiedEvent;
use App\Form\SessionWithDetailType;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/session")
 * @method User getUser()
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/", name="session_index", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function index(Request $request, SessionRepository $sessionRepository): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            $sessions = $sessionRepository->findAllWithProposedDetails(
                $request->query->has('has_changes'),
                $request->query->has('not_approved')
            );
        } else {
            $sessions = $sessionRepository->findByUser($this->getUser());
        }

        return $this->render('session/index.html.twig', ['sessions' => $sessions]);
    }

    /**
     * @Route("/{excludeId}/parallel/{date}/{startStr}/{endStr}", defaults={"endStr"="", "excludeId"=""})
     * @return Response
     */
    public function countParallelSessions(
        string $excludeId,
        string $date,
        string $startStr,
        string $endStr,
        SessionRepository $sessionRepository
    ): Response {
        $dateFmt = 'Y-m-d H:i';
        $start = \DateTimeImmutable::createFromFormat($dateFmt, $date . ' ' . $startStr);

        if ($start === false) {
            $dateFmt = 'd.m.Y H:i';
            $start = \DateTimeImmutable::createFromFormat($dateFmt, $date . ' ' . $startStr);
        }

        if (!$start) {
            throw new BadRequestException('Invalid start date');
        }

        $end = $endStr
            ? \DateTimeImmutable::createFromFormat($dateFmt, $date . ' ' . $endStr)
            : $start->add(new \DateInterval('PT2H'));

        return new JsonResponse([
            'count' => $sessionRepository->countParallelSession(
                $start,
                $end,
                $excludeId === '-' ? null : (int) $excludeId
            ),
        ]);
    }

    /**
     * @Route("/new", name="session_new", methods={"GET","POST"})
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            throw new \LogicException('session_new route not expected to be called by editor');
        }

        $sessionWithDetail = (new SessionWithDetail())->setOrganization(
            $this->getUser()
                ->getOrganizations()
                ->first()
        );

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = (new Session())->applyDetails($sessionWithDetail);
            $isDraft = $request->request->has('draft');

            if ($session->getOrganization()->getOwner() !== $this->getUser()) {
                throw new AccessDeniedException();
            }

            if (!$isDraft) {
                $session->propose();
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($session);
            $entityManager->flush();

            if ($this->isGranted(User::ROLE_EDITOR) || $isDraft) {
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } else {
                $eventDispatcher->dispatch(new SessionModifiedEvent($session));
                $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');
            }

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/new.html.twig', [
            'session' => $sessionWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/organization/{id}/new", name="session_editor_create", methods={"GET","POST"})
     * @param Organization $organization
     * @param Request $request
     * @return Response
     */
    public function editorCreate(Organization $organization, Request $request): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $sessionWithDetail = (new SessionWithDetail())->setOrganization($organization);

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = (new Session())->applyDetails($sessionWithDetail);
            $session->propose();
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
        if (!$this->isGranted(User::ROLE_EDITOR) && $session->getOrganization()->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/diff", name="session_diff", methods={"GET"})
     * @param Session $session
     * @return Response
     */
    public function diff(Session $session): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if (!$session->isAcceptedAndChanged()) {
            throw new \LogicException('cannot diff a non-accepted session');
        }

        return $this->render('session/diff.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Session $session
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function edit(Request $request, Session $session, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR) && $session->getOrganization()->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $sessionWithDetail = $session->toSessionWithDetail($this->isGranted(User::ROLE_EDITOR));

        $form = $this->createForm(SessionWithDetailType::class, $sessionWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->applyDetails($sessionWithDetail);
            $isDraft = $request->request->has('draft');

            if (!$isDraft) {
                $session->propose();
            }

            if ($this->isGranted(User::ROLE_EDITOR)) {
                $session->accept();
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } elseif ($session->getOrganization()->getOwner() !== $this->getUser()) {
                throw new AccessDeniedException();
            } elseif ($isDraft) {
                $this->addFlash('success', 'Die Änderungen wurden als Entwurf gespeichert.');
            } elseif ($session->getAcceptedDetails() === $session->getProposedDetails()) {
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } else {
                $eventDispatcher->dispatch(new SessionModifiedEvent($session));
                $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');
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

            $this->addFlash('success', 'Das Event wurde gelöscht.');
        }

        return $this->redirectToRoute('session_index');
    }

    /**
     * @Route("/{id}/approval", name="session_remove_approval", methods={"DELETE"})
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function removeApproval(Request $request, Session $session): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('remove_approval' . $session->getId(), $request->request->get('_token'))) {
            $session->setAcceptedDetails(null)->setAcceptedAt(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Die Freigabe des Events wurde zurückgezogen.');
        }

        return $this->redirectToRoute('session_index');
    }

    /**
     * @Route("/{id}", name="session_cancel", methods={"POST"})
     * @param Request $request
     * @param Session $session
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function cancel(Request $request, Session $session, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR) && $session->getOrganization()->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('cancel' . $session->getId(), $request->request->get('_token'))) {
            $session->setCancelled(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            if (!$this->isGranted(User::ROLE_EDITOR)) {
                $eventDispatcher->dispatch(new SessionCancelledEvent($session));
            }

            $this->addFlash('success', 'Das Event wurde als abgesagt markiert.');
        }

        return $this->redirectToRoute('session_index');
    }

    /**
     * @Route("/{id}/highlight", name="session_toggle_highlight", methods={"POST"})
     * @param Request $request
     * @param Session $session
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function toggleHighlight(
        Request $request,
        Session $session,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('toggle_highlight' . $session->getId(), $request->request->get('_token'))) {
            $session->setHighlight(!$session->isHighlight());

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

            $this->addFlash('success', 'Das Event wurde freigegeben.');
        }

        return $this->redirectToRoute('session_index');
    }
}
