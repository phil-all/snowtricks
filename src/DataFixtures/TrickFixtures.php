<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugger = new AsciiSlugger();
        $trickNumber = 0;

        for ($i = 1; $i <= $this->getCount('validUser'); $i++) {
            $user = $this->getReference('validUser_' . $i);

            $trickCount = rand(0, 4);

            if ($trickCount > 0) {
                for ($j = 1; $j <= $trickCount; $j++) {
                    $trick = new Trick();
                    $title = $faker->words(rand(1, 2), true);
                    $creation = $faker->dateTimeBetween('-' . rand(10, 30) . ' day');
                    $randomCategory = rand(1, 5);
                    $trickNumber++;

                    $trick->setTitle($title)
                        ->setSlug($slugger->slug($title, '-'))
                        ->setContent($faker->text(rand(350, 700)))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($creation))
                        ->setUpdateAt(\DateTimeImmutable::createFromMutable($creation))
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
}
