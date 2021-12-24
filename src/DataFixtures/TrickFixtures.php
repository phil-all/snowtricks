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

        for ($j = 1; $j < 26; $j++) {
            $user = $this->getReference('user_' . $j);

            $status = $user->getStatus();

            if ($status->getId() === 2) {
                for ($i = 1; $i <= 3; $i++) {
                    $trick = new Trick();
                    $title = $faker->words(rand(1, 3), true);
                    $creation = $faker->dateTimeBetween('-' . rand(10, 30) . ' day');
                    $randomCategory = rand(1, 3);
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
}
