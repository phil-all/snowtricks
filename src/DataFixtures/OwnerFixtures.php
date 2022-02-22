<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OwnerFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * OwnerFixtures constructor
     *
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Load Owner
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $faker = Factory::create('fr_FR');
        $registration = $faker->dateTimeBetween('-' . rand(31, 60) . ' day');

        $user->setRoles(['ROLE_ADMIN'])
            ->setStatus($this->getReference('status_2'))
            ->setGender($this->getReference('gender_male'))
            ->setFirstName('Jimmy')
            ->setLastName('Sweat')
            ->setEmail('jimmy.sweat@fake.com')
            ->setPassword($this->hasher->hashPassword($user, 'pass1234'))
            ->setRgpd(true)
            ->setRegistredAt(\DateTimeImmutable::createFromMutable($registration));

        $manager->persist($user);

        $this->addReference('validUser_1', $user);
        $this->addReference('nonPendingUser_1', $user);

        $manager->flush();
    }

    /**
     * Get owner dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            StatusFixtures::class,
            CategoryFixtures::class,
            TypeFixtures::class,
            GenderFixtures::class
        ];
    }
}
