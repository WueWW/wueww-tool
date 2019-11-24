<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonExportController extends AbstractController
{
    /**
     * @Route("/export/session.json", name="export_session_json", methods={"GET"})
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findFullyAccepted();

        $json = [
            'format' => '0.4.0',
            'sessions' => array_map([self::class, 'mapSession'], $sessions),
        ];

        return JsonResponse::create($json);
    }

    static function mapSession(Session $session): array
    {
        $result = [
            'key' => $session->getId(),
            'start' => $session->getStart()->format('Y-m-d\\TH:i:sZ'),
            'end' => $session->getStop() ? $session->getStop()->format('Y-m-d\\TH:i:sZ') : null,
            'cancelled' => $session->getCancelled(),
            'host' => self::mapHost($session),
            'title' => $session->getAcceptedDetails()->getTitle(),
            'location' => self::mapLocation($session),
        ];

        if ($session->getAcceptedDetails()->getShortDescription()) {
            $result['description']['short'] = $session->getAcceptedDetails()->getShortDescription();
        }

        if ($session->getAcceptedDetails()->getLongDescription()) {
            $result['description']['long'] = $session->getAcceptedDetails()->getLongDescription();
        }

        if ($session->getAcceptedDetails()->getLink()) {
            $result['links']['event'] = $session->getAcceptedDetails()->getLink();
        }

        return $result;
    }

    /**
     * @param Session $session
     * @return array
     */
    static function mapHost(Session $session): array
    {
        $details = $session->getOrganization()->getAcceptedOrganizationDetails();

        $result = [
            'key' => $session->getOrganization()->getId(),
            'name' => $details->getTitle(),
        ];

        if ($details->getDescription()) {
            $result['infotext'] = $details->getDescription();
        }

        if ($details->getLink()) {
            $result['links']['host'] = $details->getLink();
        }

        if ($details->getFacebookUrl()) {
            $result['links']['facebook'] = $details->getFacebookUrl();
        }

        if ($details->getTwitterUrl()) {
            $result['links']['twitter'] = $details->getTwitterUrl();
        }

        if ($details->getYoutubeUrl()) {
            $result['links']['youtube'] = $details->getYoutubeUrl();
        }

        if ($details->getInstagramUrl()) {
            $result['links']['instagram'] = $details->getInstagramUrl();
        }

        if ($details->getXingUrl()) {
            $result['links']['xing'] = $details->getXingUrl();
        }

        if ($details->getLinkedinUrl()) {
            $result['links']['linkedIn'] = $details->getLinkedinUrl();
        }

        return $result;
    }

    /**
     * @param Session $session
     * @return array
     */
    static function mapLocation(Session $session): array
    {
        $location = $session->getAcceptedDetails()->getLocation();

        $result = [
            'name' => $location->getName(),
            'streetNo' => $location->getStreetNo(),
            'zipcode' => $location->getZipcode(),
            'city' => $location->getCity(),
        ];

        if ($session->getAcceptedDetails()->getLocationLat() && $session->getAcceptedDetails()->getLocationLng()) {
            $result['lat'] = $session->getAcceptedDetails()->getLocationLat();
            $result['lng'] = $session->getAcceptedDetails()->getLocationLng();
        }

        return $result;
    }
}
