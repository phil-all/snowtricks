<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $types = [
            'avatar',
            'thumbnail',
            'image',
            'video'
        ];

        foreach ($types as $value) {
            $type = new Type();
            $type->setType($value);
            $manager->persist($type);

            $this->addReference('type_' . $value, $type);
        }

        $manager->flush();
    }
}
