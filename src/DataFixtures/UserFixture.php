<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const EDITOR_USER_REF = 'editor-user';
    public const REPORTER_USER_REF = 'reporter-user';

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
        $manager->persist($editor);

        $reporter = new User();
        $reporter->setEmail('reporter@example.org');
        $reporter->setPassword($this->passwordEncoder->encodePassword($reporter, 'reporter_password'));
        $manager->persist($reporter);

        $manager->flush();

        $this->addReference(self::EDITOR_USER_REF, $editor);
        $this->addReference(self::REPORTER_USER_REF, $reporter);
    }
}
