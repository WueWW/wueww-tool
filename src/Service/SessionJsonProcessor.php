<?php

namespace App\Service;

use App\Entity\Organization;
use App\Entity\OrganizationDetail;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class SessionJsonProcessor
{
    const DUMMY_OWNER_EMAIL = 'dummy-owner@wueww.de';
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        SessionRepository $sessionRepository,
        OrganizationRepository $organizationRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function processFile(string $filename)
    {
        $content = \json_decode(\file_get_contents($filename));

        if ($content->format !== '0.3.0') {
            throw new \RuntimeException('session.json not in 0.3.0 format');
        }

        $this->processData($content->sessions);
    }

    private function processData(array $sessions)
    {
        foreach ($sessions as $sessionData) {
            if (
                !isset($sessionData->location) ||
                !isset($sessionData->key) ||
                !isset($sessionData->start) ||
                !isset($sessionData->end) ||
                !isset($sessionData->cancelled) ||
                !isset($sessionData->title)
            ) {
                // skip record, invalid with this version of WueWW Tool
                continue;
            }

            $session = $this->sessionRepository->findOneBy(['importKey' => $sessionData->key]);

            if ($session === null) {
                $session = (new Session())->setImportKey($sessionData->key);

                $this->entityManager->persist($session);
            }

            $session
                ->setStart(new \DateTimeImmutable($sessionData->start))
                ->setStop($sessionData->end ? new \DateTimeImmutable($sessionData->end) : null)
                ->setCancelled($sessionData->cancelled);

            $session->getDraftDetails()->setTitle($sessionData->title);

            $locationInfo = explode("\n", trim($sessionData->location->name));
            [$zipcode, $city] = explode(' ', array_pop($locationInfo), 2);
            $streetNo = array_pop($locationInfo);

            $session
                ->getDraftDetails()
                ->getLocation()
                ->setName(implode(', ', $locationInfo))
                ->setStreetNo($streetNo ?? '')
                ->setZipcode($zipcode ?? '')
                ->setCity($city ?? '');

            if (isset($sessionData->location->lat)) {
                $session->getDraftDetails()->setLocationLat($sessionData->location->lat);
            }

            if (isset($sessionData->location->lng)) {
                $session->getDraftDetails()->setLocationLng($sessionData->location->lng);
            }

            if (isset($sessionData->links) && isset($sessionData->links->event)) {
                $session->getDraftDetails()->setLink($sessionData->links->event);
            }

            if (isset($sessionData->description) && isset($sessionData->description->short)) {
                $session->getDraftDetails()->setShortDescription($sessionData->description->short);
            }

            if (isset($sessionData->description) && isset($sessionData->description->long)) {
                $session->getDraftDetails()->setLongDescription($sessionData->description->long);
            }

            $session->setOrganization($this->processHost($sessionData));
            $session->propose();
            $session->accept();

            $this->entityManager->flush();
        }
    }

    private function processHost($sessionData): Organization
    {
        $host = $sessionData->host;
        $organization = $this->organizationRepository->findOneByTitle($host->name);

        if ($organization === null) {
            $organization = (new Organization())->setOwner($this->dummyOwner());

            $this->entityManager->persist($organization);
        }

        if ($organization->getProposedOrganizationDetails() === null) {
            $organization->setProposedOrganizationDetails(new OrganizationDetail());
        }

        $organization
            ->getProposedOrganizationDetails()
            ->setTitle($host->name)
            ->setContactName($host->name);

        if (isset($host->infotext)) {
            $organization->getProposedOrganizationDetails()->setDescription($host->infotext);
        }

        if (isset($sessionData->links) && isset($sessionData->links->host)) {
            $organization->getProposedOrganizationDetails()->setLink($sessionData->links->host);
        }

        $organization->accept();

        return $organization;
    }

    private function dummyOwner(): User
    {
        $user = $this->userRepository->findOneBy(['email' => self::DUMMY_OWNER_EMAIL]);

        if ($user === null) {
            $user = (new User())
                ->setEmail(self::DUMMY_OWNER_EMAIL)
                ->setPassword('!')
                ->setRegistrationComplete(true);

            $this->entityManager->persist($user);
        }

        return $user;
    }
}
