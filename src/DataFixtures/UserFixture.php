<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use App\Entity\OrganizationDetail;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const EDITOR_USER_REF = 'editor-user';
    public const REPORTER1_USER_REF = 'reporter1-user';
    public const REPORTER2_USER_REF = 'reporter2-user';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $editor = new User();
        $editor->setEmail('editor@example.org');
        $editor->setPassword($this->passwordEncoder->encodePassword($editor, 'editor_password'));
        $editor->setRoles([User::ROLE_EDITOR]);
        $editor->setRegistrationComplete(true);
        $manager->persist($editor);

        $organization1 = new Organization();
        $organization1->setProposedOrganizationDetails(
            (new OrganizationDetail())->setTitle('Organisator Eins')->setContactName('Ansprechpartner Organisator 1')
        );
        $organization1->accept();

        $reporter1 = new User();
        $reporter1->setEmail('reporter1@example.org');
        $reporter1->setPassword($this->passwordEncoder->encodePassword($reporter1, 'reporter1_password'));
        $reporter1->setRegistrationComplete(true);
        $reporter1->addOrganization($organization1);
        $manager->persist($reporter1);

        $organization2 = new Organization();
        $organization2->setProposedOrganizationDetails(
            (new OrganizationDetail())->setTitle('Organisator Zwei')->setContactName('Ansprechpartner Organisator 2')
        );
        $organization2->accept();

        $reporter2 = new User();
        $reporter2->setEmail('reporter2@example.org');
        $reporter2->setPassword($this->passwordEncoder->encodePassword($reporter2, 'reporter2_password'));
        $reporter2->setRegistrationComplete(true);
        $reporter2->addOrganization($organization2);
        $manager->persist($reporter2);

        $manager->flush();

        $this->addReference(self::EDITOR_USER_REF, $editor);
        $this->addReference(self::REPORTER1_USER_REF, $reporter1);
        $this->addReference(self::REPORTER2_USER_REF, $reporter2);
    }
}
