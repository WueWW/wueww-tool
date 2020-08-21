<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainpostExportController extends AbstractController
{
    const WOCHENTAGE = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
    const MONATSNAMEN = [
        'Januar',
        'Februar',
        'März',
        'April',
        'Mai',
        'Juni',
        'Juli',
        'August',
        'September',
        'Oktober',
        'November',
        'Dezember',
    ];

    /**
     * @Route("/export/mainpost.txt", name="export_mainpost", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findFullyAccepted(true);
        $partitionedSessions = $this->partitionSessionsByDate($sessions);

        $response = $this->render('feeds/mainpost.txt.twig', [
            'sessions' => $partitionedSessions,
            'num_sessions' => \count($sessions),
        ]);
        $response->headers->add(['Content-Type' => 'text/plain']);

        return $response;
    }

    /**
     * @param Session[] $sessions
     * @return Session[][]
     */
    private function partitionSessionsByDate(array $sessions): array
    {
        $result = [];

        foreach ($sessions as $session) {
            $key = $this->formatDate($session->getStart());

            if (!isset($result[$key])) {
                $result[$key] = [];
            }

            $result[$key][] = $session;
        }

        return $result;
    }

    private function formatDate(\DateTimeInterface $date): string
    {
        $weekdayName = self::WOCHENTAGE[(int) $date->format('w')];
        $monthName = self::MONATSNAMEN[(int) $date->format('n') - 1];

        return \sprintf('%s, %s. %s', $weekdayName, $date->format('d'), $monthName);
    }
}
