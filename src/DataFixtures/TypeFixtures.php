<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create types : avatar / image / video
        $types = [
            1 => 'avatar',
            2 => 'image',
            3 => 'video'
        ];

        foreach ($types as $key => $value) {
            $type = new Type();
            $type->setType($value);
            $manager->persist($type);

            $this->addReference('type_' . $key, $type);
        }

        $manager->flush();
    }
}
