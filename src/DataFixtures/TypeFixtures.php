<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $types = ['avatar', 'image', 'video'];

        foreach ($types as $value) {
            $type = new Type();
            $type->setType($value);
            $manager->persist($type);

            $this->addReference('type_' . $value, $type);
        }

        $manager->flush();
    }
}
