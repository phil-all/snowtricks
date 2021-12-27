<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statusList = [
            1 => 'pending',
            2 => 'active',
            3 => 'suspended'
        ];

        foreach ($statusList as $key => $value) {
            $status = new Status();
            $status->setStatus($value);
            $manager->persist($status);

            $this->addReference('status_' . $key, $status);
        }

        $manager->flush();
    }
}
