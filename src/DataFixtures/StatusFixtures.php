<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class StatusFixtures extends Fixture
{
    /**
     * Load status
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
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
