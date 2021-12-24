<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OwnerFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // create owner status
        $status = $this->getReference('status_2');

        // create Owner
        $user = new User();

        $user->setRoles(['ROLE_ADMIN'])
            ->setStatus($status)
            ->setFirstName('Jimmy')
            ->setLastName('Sweat')
            ->setEmail('jimmy.sweat@fake.com')
            ->setPassword($this->hasher->hashPassword($user, 'pass1234'))
            ->setRgpd(true);

        $manager->persist($user);

        $this->addReference('user_1', $user);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            StatusFixtures::class,
            CategoryFixtures::class,
            TypeFixtures::class
        ];
    }
}
