<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker       = Factory::create('fr_FR');
        $slugger     = new AsciiSlugger();
        $trickNumber = 0;

        for ($i = 1; $i <= $this->getCount('validUser'); $i++) {
            $user = $this->getReference('validUser_' . $i);

            $trickCount = rand(0, 5);

            if ($trickCount > 0) {
                for ($j = 1; $j <= $trickCount; $j++) {
                    $trick = new Trick();
                    $title = $faker->unique()->words(rand(1, 2), true);
                    $create = $this->randomDate(10, 30, $faker);
                    $update = (rand(0, 1)) ? $this->randomDate(10, 30, $faker) : $create;
                    $randomCategory = rand(1, 5);
                    $trickNumber++;

                    $trick->setTitle($title)
                        ->setSlug($slugger->slug($title, '-'))
                        ->setContent($faker->text(rand(350, 700)))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($create))
                        ->setUpdateAt(\DateTimeImmutable::createFromMutable($update))
                        ->setUser($user)
                        ->setCategory($this->getReference('category_' . $randomCategory));

                    $manager->persist($trick);

                    $this->addReference('trick_' . $trickNumber, $trick);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }

    /**
     * Get count of a reference fixture
     *
     * @param string $subject subject name of reference
     *
     * @return integer
     */
    protected function getCount(string $subject): int
    {
        $count = 0;

        while ($this->hasReference($subject . '_' . $count + 1)) {
            $count++;
        }

        return $count;
    }

    /**
     * Returns a random date between a min and max gap arguments
     *
     * @param integer $min minimum gap
     * @param integer $max maximum gap
     * @param Generator $faker
     *
     * @return DateTime
     */
    private function randomDate(int $min, int $max, Generator $faker): DateTime
    {
        return $faker->dateTimeBetween('-' . rand($min, $max) . ' day');
    }
}
