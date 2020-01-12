<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RequestContext;

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
            'format' => '0.4.1',
            'sessions' => array_map([$this, 'mapSession'], $sessions),
        ];

        return JsonResponse::create($json, Response::HTTP_OK, ['access-control-allow-origin' => '*']);
    }

    function mapSession(Session $session): array
    {
        $result = [
            'id' => $session->getId(),
            'start' => $session->getStart()->format('Y-m-d\\TH:i:sP'),
            'end' => $session->getStop() ? $session->getStop()->format('Y-m-d\\TH:i:sP') : null,
            'cancelled' => $session->getCancelled(),
            'host' => $this->mapHost($session),
            'title' => trim(str_replace("\n", '', $session->getAcceptedDetails()->getTitle())),
            'location' => $this->mapLocation($session),
        ];

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

    /**
     * @param Session $session
     * @return array
     */
    function mapHost(Session $session): array
    {
        $details = $session->getOrganization()->getAcceptedOrganizationDetails();

        $result = [
            'id' => $session->getOrganization()->getId(),
            'name' => trim(str_replace("\n", '', $details->getTitle())),
        ];

        if ($details->getDescription()) {
            $result['infotext'] = $details->getDescription();
        }

        if ($session->getOrganization()->getLogoFileName()) {
            $result['logo'] = $this->urlForLogo($session->getOrganization()->getLogoFileName());
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
    function mapLocation(Session $session): array
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

    private function urlForLogo(string $fileName): string
    {
        /** @var RequestContext $requestContext */
        $requestContext = $this->get('router')->getContext();

        if ($requestContext->getHost() === 'localhost') {
            $scheme = 'http';
            $portExtra = $requestContext->getHttpPort();
        } else {
            $scheme = 'https';
            $portExtra = '';
        }

        return $scheme . '://' . $requestContext->getHost() . $portExtra . '/logos/' . $fileName;
    }
}
