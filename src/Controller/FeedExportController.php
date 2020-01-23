<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedExportController extends AbstractController
{
    /**
     * @Route("/export/feed/new-sessions.xml", name="rss_feed_approved_sessions", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findRecentlyApprovedSessions();

        $response = $this->render('feeds/approved-sessions.xml.twig', ['sessions' => $sessions]);
        $response->headers->add(['Content-Type' => 'application/rss+xml']);

        return $response;
    }
}
