<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN = 'ADMIN_REFERENCE';
    public const USER = 'USER_REFERENCE';

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ){
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@email.com')
            ->setUsername('admin')
            ->setVerified(true)
            ->setPassword($this->hasher->hashPassword($user, 'admin'));
        $this->addReference(self::ADMIN, $user);
        $manager->persist($user);

        for ($i = 1; $i <= 10; $i++) {
            $user = (new User())
                ->setRoles([])
                ->setEmail('user'.$i.'@email.com')
                ->setUsername('user'.$i)
                ->setVerified(true)
                ->setPassword($this->hasher->hashPassword($user, 'password'));
            $this->addReference(self::USER.$i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
