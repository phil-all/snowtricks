<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class GenderFixtures extends Fixture
{
    /**
     * Load genders
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // create status : pending / active / suspended
        $genderList = ['male', 'female'];

        foreach ($genderList as $value) {
            $gender = new Gender();
            $gender->setGender($value);
            $manager->persist($gender);

            $this->addReference('gender_' . $value, $gender);
        }

        $manager->flush();
    }
}
