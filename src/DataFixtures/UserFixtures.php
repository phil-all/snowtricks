<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 2; $i < 26; $i++) {
            $valid_i = 0;
            $nonValid_i = 0;
            $user = new User();

            $randomStatus = rand(1, 3);

            $user->setRoles(['ROLE_USER'])
                ->setStatus($this->getReference('status_' . $randomStatus))
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->freeEmail())
                ->setPassword($this->hasher->hashPassword($user, 'pass1234'))
                ->setRgpd(true);

            $manager->persist($user);

            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            OwnerFixtures::class
        ];
    }
}
