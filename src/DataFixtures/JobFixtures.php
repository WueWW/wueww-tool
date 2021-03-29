<?php

namespace App\DataFixtures;

use App\Entity\Job;
use App\Entity\JobDetail;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var User $reporter1 */
        $reporter1 = $this->getReference(UserFixture::REPORTER1_USER_REF);

        $manager->persist($this->createJobNichtFreigegeben($reporter1));
        $manager->persist($this->createJobFreigegeben($reporter1));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixture::class];
    }

    private function createJobNichtFreigegeben(User $reporter): Job
    {
        $job = (new Job())->setOrganization($reporter->getOrganizations()->first());

        $job
            ->getProposedDetails()
            ->setTitle('Pixelschubser:in (m/w/d)')
            ->setShortDescription(
                'Pixelschubsen macht uns Spaß, und weil wir so viel Spaß haben, möchten wir den gerne mit dir teilen.'
            )
            ->setLink('http://wueww.de/job/nicht/freigegeben');

        return $job;
    }

    private function createJobFreigegeben(User $reporter): Job
    {
        $detail = (new JobDetail())
            ->setTitle('Cobol Hacker (m/w/d)')
            ->setShortDescription(
                'Wir sind **die** Cobol-Schmiede in der Stadt.  Löten war gestern, heute wird feinstes Cobol gehackt.'
            )
            ->setLink('http://wueww.de/job/freigegeben');

        $job = (new Job())->setOrganization($reporter->getOrganizations()->first())->setProposedDetails($detail);

        $job->accept();

        return $job;
    }
}
