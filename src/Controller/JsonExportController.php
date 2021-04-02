<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Organization;
use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $latestUpdate = time();

        if (\count($sessions) > 0) {
            $latestUpdate = max(
                array_map(function (Session $session): int {
                    return $session->getAcceptedAt()->getTimestamp();
                }, $sessions)
            );
        }

        $json = [
            'format' => '0.5.0',
            'sessions' => array_map([$this, 'mapSession'], $sessions),
        ];

        return JsonResponse::create($json, Response::HTTP_OK, [
            'access-control-allow-origin' => '*',
            'Date' => \gmdate('D, d M Y H:i:s T', $latestUpdate),
        ]);
    }

    function mapSession(Session $session): array
    {
        $result = [
            'id' => $session->getId(),
            'start' => $session->getStart()->format('Y-m-d\\TH:i:sP'),
            'end' => $session->getStop() ? $session->getStop()->format('Y-m-d\\TH:i:sP') : null,
            'cancelled' => $session->getCancelled(),
            'onlineOnly' => $session->getAcceptedDetails()->getOnlineOnly(),
            'host' => $this->mapHost($session->getOrganization()),
            'title' => trim(str_replace("\n", '', $session->getAcceptedDetails()->getTitle())),
        ];

        if (!$session->getAcceptedDetails()->getOnlineOnly()) {
            $result['location'] = $this->mapLocation(
                $session->getAcceptedDetails()->getLocation(),
                $session->getAcceptedDetails()->getLocationLat(),
                $session->getAcceptedDetails()->getLocationLng()
            );
        }

        if ($session->getAcceptedDetails()->getShortDescription()) {
            $result['description']['short'] = trim($session->getAcceptedDetails()->getShortDescription());
        }

        if ($session->getAcceptedDetails()->getLongDescription()) {
            $result['description']['long'] = trim($session->getAcceptedDetails()->getLongDescription());
        }

        if ($session->getAcceptedDetails()->getLink()) {
            $result['links']['event'] = trim($session->getAcceptedDetails()->getLink());
        }

        return $result;
    }

    function mapHost(Organization $organization): array
    {
        $details = $organization->getAcceptedOrganizationDetails();

        $result = [
            'id' => $organization->getId(),
            'name' => trim(str_replace("\n", '', $details->getTitle())),
        ];

        if ($details->getDescription()) {
            $result['infotext'] = $details->getDescription();
        }

        if ($organization->getLogoFileName()) {
            $result['logo'] = $this->urlForLogo($organization->getLogoFileName());
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

    function mapLocation(?Location $location, ?float $lat, ?float $lng): array
    {
        $result = [
            'name' => $location->getName(),
            'streetNo' => $location->getStreetNo(),
            'zipcode' => $location->getZipcode(),
            'city' => $location->getCity(),
        ];

        if ($lat && $lng) {
            $result['lat'] = $lat;
            $result['lng'] = $lng;
        }

        return $result;
    }

    private function urlForLogo(string $fileName): string
    {
        /** @var Request $request */
        $request = $this->get('request_stack')->getMasterRequest();
        $scheme = $request->getHost() === 'localhost' ? 'http' : 'https';

        return $scheme . '://' . $request->getHttpHost() . $request->getBasePath() . '/logos/' . $fileName;
    }
}
