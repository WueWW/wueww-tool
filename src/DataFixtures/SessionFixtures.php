<?php

namespace App\DataFixtures;

use App\Entity\Location;
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
        /** @var User $reporter1 */
        $reporter1 = $this->getReference(UserFixture::REPORTER1_USER_REF);

        $manager->persist($this->createSessionNichtFreigegeben($reporter1));
        $manager->persist($this->createSessionFreigegeben($reporter1));
        $manager->persist($this->createSessionFreigegebenUndGeaendert($reporter1));
        $manager->persist($this->createSessionCancelled($reporter1));

        /** @var User $reporter2 */
        $reporter2 = $this->getReference(UserFixture::REPORTER2_USER_REF);
        $manager->persist($this->createSessionFreigegeben($reporter2));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class];
    }

    private function createSessionNichtFreigegeben(User $reporter): Session
    {
        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2020-04-20 14:00'))
            ->setStop(new \DateTimeImmutable('2020-04-20 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first());

        $session
            ->getProposedDetails()
            ->setTitle('Nicht freigegeben')
            ->setShortDescription('Kurzbeschreibung nicht freigegebener Session')
            ->setLongDescription('Die nicht freigegebene Session hat auch eine Langbeschreibung')
            ->setLocation(
                (new Location())
                    ->setName('Nicht-Freigegeben-Office')
                    ->setStreetNo('Nicht-Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/nicht/freigegeben');

        return $session;
    }

    private function createSessionFreigegeben(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('Freigegebene Session ohne Änderung')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2020-04-21 14:00'))
            ->setStop(new \DateTimeImmutable('2020-04-21 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
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
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $detailProposed = (new SessionDetail())
            ->setTitle('Freigegebene Session geändert')
            ->setShortDescription('geänderte Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('natürlich darf sich auch die Langbeschreibung ändern')
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2020-04-24 14:00'))
            ->setStop(new \DateTimeImmutable('2020-04-24 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
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
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2020-04-26 14:00'))
            ->setStop(new \DateTimeImmutable('2020-04-26 16:00'))
            ->setCancelled(true)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setAcceptedDetails($detail)
            ->setProposedDetails($detail);

        return $session;
    }
}
