<?php

namespace App\DataFixtures;

use App\Entity\Session;
use App\Entity\SessionDetail;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SessionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var User $reporter */
        $reporter = $this->getReference(UserFixture::REPORTER_USER_REF);

        $manager->persist($this->createSessionNichtFreigegeben($reporter));
        $manager->persist($this->createSessionFreigegeben($reporter));
        $manager->persist($this->createSessionFreigegebenUndGeaendert($reporter));
        $manager->persist($this->createSessionCancelled($reporter));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class];
    }

    private function createSessionNichtFreigegeben(User $reporter): Session
    {
        $session = (new Session())
            ->setStart(new \DateTimeImmutable("2019-04-01 14:00"))
            ->setStop(new \DateTimeImmutable("2019-04-01 16:00"))
            ->setCancelled(false)
            ->setOwner($reporter);

        $session
            ->getProposedDetails()
            ->setTitle('Nicht freigegeben')
            ->setShortDescription('Kurzbeschreibung nicht freigegebener Session')
            ->setLongDescription('Die nicht freigegebene Session hat auch eine Langbeschreibung')
            ->setLocationName('Nicht-Freigegeben-Office')
            ->setLink('http://wueww.de/session/nicht/freigegeben');

        return $session;
    }

    private function createSessionFreigegeben(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('Freigegebene Session ohne Änderung')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setLocationName('Freigegeben-Office')
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable("2019-04-02 14:00"))
            ->setStop(new \DateTimeImmutable("2019-04-02 16:00"))
            ->setCancelled(false)
            ->setOwner($reporter)
            ->setAcceptedDetails($detail)
            ->setProposedDetails($detail);

        return $session;
    }

    private function createSessionFreigegebenUndGeaendert(User $reporter): Session
    {
        $detailAccepted = (new SessionDetail())
            ->setTitle('Freigegebene Session')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setLocationName('Freigegeben-Office')
            ->setLink('http://wueww.de/session/freigegeben');

        $detailProposed = (new SessionDetail())
            ->setTitle('Freigegebene Session geändert')
            ->setShortDescription('geänderte Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('natürlich darf sich auch die Langbeschreibung ändern')
            ->setLocationName('Freigegeben-Office')
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable("2019-04-02 14:00"))
            ->setStop(new \DateTimeImmutable("2019-04-02 16:00"))
            ->setCancelled(false)
            ->setOwner($reporter)
            ->setAcceptedDetails($detailAccepted)
            ->setProposedDetails($detailProposed);

        return $session;
    }

    private function createSessionCancelled(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('abgesagte Session')
            ->setShortDescription('Kurzbeschreibung einer abgesagten Session')
            ->setLongDescription('Die abgesagte Session hat natürlich auch eine Langbeschreibung')
            ->setLocationName('Freigegeben-Office')
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable("2019-04-02 14:00"))
            ->setStop(new \DateTimeImmutable("2019-04-02 16:00"))
            ->setCancelled(true)
            ->setOwner($reporter)
            ->setAcceptedDetails($detail)
            ->setProposedDetails($detail);

        return $session;
    }
}