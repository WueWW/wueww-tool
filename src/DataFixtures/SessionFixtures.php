<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Session;
use App\Entity\SessionDetail;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

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
        $manager->persist($this->createOnlineOnlySessionFreigegeben($reporter2));
        $manager->persist($this->createSessionFreigegebenWithoutStartDate($reporter2));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class];
    }

    private function createSessionNichtFreigegeben(User $reporter): Session
    {
        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2021-10-22 14:00'))
            ->setStop(new \DateTimeImmutable('2021-10-22 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first());

        $session
            ->getProposedDetails()
            ->setTitle('Nicht freigegeben')
            ->setShortDescription('Kurzbeschreibung nicht freigegebener Session')
            ->setLongDescription('Die nicht freigegebene Session hat auch eine Langbeschreibung')
            ->setOnlineOnly(false)
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

    private function createOnlineOnlySessionFreigegeben(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('Online-Only Session')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen, online-only Session')
            ->setLongDescription(
                'Die freigegebene Session hat natürlich auch eine Langbeschreibung, und hier würden noch Zugangsdaten stehen.  Und vieles Weitere mehr.'
            )
            ->setOnlineOnly(true)
            ->setLocation(
                (new Location())
                    ->setName('nix da')
                    ->setStreetNo('')
                    ->setZipcode('')
                    ->setCity('')
            )
            ->setLink('http://wueww.de/session/online-only');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2021-10-23 14:00'))
            ->setStop(new \DateTimeImmutable('2021-10-23 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setProposedDetails($detail);

        $session->accept();

        return $session;
    }

    private function createSessionFreigegeben(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('Freigegebene Session o. Ä.')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2021-10-23 14:00'))
            ->setStop(new \DateTimeImmutable('2021-10-23 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setProposedDetails($detail);

        $session->accept();

        return $session;
    }

    private function createSessionFreigegebenWithoutStartDate(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('Alte Session o. Ä.')
            ->setShortDescription('Kurzbeschreibung einer alten Session')
            ->setLongDescription(
                'Die alte Session hat natürlich auch eine Langbeschreibung, und wartet darauf, dass sie wieder einen Startzeitpunkt bekommt.'
            )
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/alt');

        $session = (new Session())
            ->setStart(null)
            ->setStop(null)
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setProposedDetails($detail);

        $session->accept();

        return $session;
    }

    private function createSessionFreigegebenUndGeaendert(User $reporter): Session
    {
        $detailAccepted = (new SessionDetail())
            ->setTitle('Freigegebene Session')
            ->setShortDescription('Kurzbeschreibung einer freigegebenen Session')
            ->setLongDescription('Die freigegebene Session hat natürlich auch eine Langbeschreibung')
            ->setOnlineOnly(false)
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
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2021-10-27 14:00'))
            ->setStop(new \DateTimeImmutable('2021-10-27 16:00'))
            ->setCancelled(false)
            ->setOrganization($reporter->getOrganizations()->first())
            ->setProposedDetails($detailAccepted);

        $session->accept();

        $session->setProposedDetails($detailProposed);

        return $session;
    }

    private function createSessionCancelled(User $reporter): Session
    {
        $detail = (new SessionDetail())
            ->setTitle('abgesagte Session')
            ->setShortDescription('Kurzbeschreibung einer abgesagten Session')
            ->setLongDescription('Die abgesagte Session hat natürlich auch eine Langbeschreibung')
            ->setOnlineOnly(false)
            ->setLocation(
                (new Location())
                    ->setName('Freigegeben-Office')
                    ->setStreetNo('Freigegeben-Straße 17a')
                    ->setZipcode('97072')
                    ->setCity('Würzburg')
            )
            ->setLink('http://wueww.de/session/freigegeben');

        $session = (new Session())
            ->setStart(new \DateTimeImmutable('2021-10-29 14:00'))
            ->setStop(new \DateTimeImmutable('2021-10-29 16:00'))
            ->setOrganization($reporter->getOrganizations()->first())
            ->setProposedDetails($detail);

        $session->accept();
        $session->setCancelled(true);

        return $session;
    }
}
