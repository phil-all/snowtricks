<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenderFixtures extends Fixture
{
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
