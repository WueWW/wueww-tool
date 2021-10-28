<?php

namespace App\Controller;

use App\Repository\OrganizationRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats", methods={"GET"})
     * @param OrganizationRepository $organizationRepository
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function show(OrganizationRepository $organizationRepository, SessionRepository $sessionRepository)
    {
        return $this->render('stats/show.html.twig', [
            'num_sessions' => $sessionRepository->countSessions(),
            'num_sessions_online_only' => $sessionRepository->countSessions(true),
            'num_sessions_cancelled' => $sessionRepository->countSessions(null, true),
            'num_organizations' => $organizationRepository->countOrganizationsWithSessions(false),
            'num_organizations_with_cancelled' => $organizationRepository->countOrganizationsWithSessions(null),
            'num_sessions_by_date' => $sessionRepository->countSessionsByDate(),
        ]);
    }
}
