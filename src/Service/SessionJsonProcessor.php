<?php

namespace App\Service;

use App\Entity\Organization;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

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
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * SessionJsonProcessor constructor.
     */
    public function __construct(
        SessionRepository $sessionRepository,
        OrganizationRepository $organizationRepository,
        UserRepository $userRepository,
        ObjectManager $objectManager
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->userRepository = $userRepository;
        $this->objectManager = $objectManager;
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
            $session = $this->sessionRepository->findOneBy(['importKey' => $sessionData->key]);

            if ($session === null) {
                $session = new Session();
                $this->objectManager->persist($session);
            }

            $session
                ->setStart(new \DateTimeImmutable($sessionData->start))
                ->setStop($sessionData->end ? new \DateTimeImmutable($sessionData->end) : null)
                ->setCancelled($sessionData->cancelled);

            $session->getProposedDetails()->setTitle($sessionData->title);

            $locationInfo = explode("\n", $sessionData->location->name);
            [$zipcode, $city] = explode(' ', array_pop($locationInfo), 2);
            $streetNo = array_pop($locationInfo);

            $session
                ->getProposedDetails()
                ->getLocation()
                ->setName(implode(', ', $locationInfo))
                ->setStreetNo($streetNo)
                ->setZipcode($zipcode)
                ->setCity($city);

            $session->setOrganization($this->processHost($sessionData->host));
            $session->accept();

            break;
        }

        $this->objectManager->flush();
    }

    private function processHost($host): Organization
    {
        $organization = $this->organizationRepository->findOneByTitle($host->name);

        if ($organization === null) {
            $organization = (new Organization())->setOwner($this->dummyOwner());

            $this->objectManager->persist($organization);
        }

        $organization
            ->getProposedOrganizationDetails()
            ->setTitle($host->name)
            ->setContactName($host->name)
            ->setDescription($host->infotext);

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

            $this->objectManager->persist($user);
        }

        return $user;
    }
}
