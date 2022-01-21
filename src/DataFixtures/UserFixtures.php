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
        $registration = $faker->dateTimeBetween('-' . rand(31, 60) . ' day');
        $countValid = 1;
        $countNonPending = 1;

        for ($i = 2; $i <= 25; $i++) {
            $user = new User();
            $randomStatus = rand(1, 3);
            $gender = $this->randomGender();

            $user->setRoles(['ROLE_USER'])
                ->setStatus($this->getReference('status_' . $randomStatus))
                ->setGender($this->getReference('gender_' . $gender))
                ->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName())
                ->setEmail($faker->freeEmail())
                ->setPassword($this->hasher->hashPassword($user, 'pass1234'))
                ->setRgpd(true);

            if ($randomStatus !== 1) {
                $user->setRegistredAt(\DateTimeImmutable::createFromMutable($registration));
            }

            $manager->persist($user);

            // add valid user reference for tricks and comments
            if ($randomStatus === 2) {
                $countValid++;
                $this->addReference('validUser_' . $countValid, $user);
            }

            // add non pending user reference for avatar
            if ($randomStatus !== 1) {
                $countNonPending++;
                $this->addReference('nonPendingUser_' . $countNonPending, $user);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            OwnerFixtures::class
        ];
    }

    /**
     * Returns a random user gender
     *
     * @return string
     */
    private function randomGender(): string
    {
        return rand(0, 1) ? 'male' : 'female';
    }
}
