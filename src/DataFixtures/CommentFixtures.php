<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends TrickFixtures implements DependentFixtureInterface
{
    /**
     * Load comments
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $commentNumber = 0;

        for ($i = 1; $i <= $this->getCount('trick'); $i++) {
            $trick = $this->getReference('trick_' . $i);

            $commentCount = rand(0, 6);

            if ($commentCount > 0) {
                for ($j = 1; $j <= $commentCount; $j++) {
                    $comment = new Comment();
                    $creation = $faker->dateTimeBetween('-' . rand(1, 9) . ' day');
                    $commentNumber++;

                    $comment->setContent($faker->text(rand(15, 250)))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($creation))
                        ->setStatus($this->getReference('status_' . rand(1, 3)))
                        ->setTrick($trick)
                        ->setUser($this->getReference('validUser_' . rand(1, $this->getCount('validUser'))));

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }

    /**
     * Get comment dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            TrickFixtures::class
        ];
    }
}
