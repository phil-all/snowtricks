<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CommentFixtures extends TrickFixtures implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $commentNumber = 0;

        for ($i = 1; $i <= $this->trickCount(); $i++) {
            $trick = $this->getReference('trick_' . $i);

            if (rand(0, 1)) {
                for ($j = 1; $j <= rand(1, 6); $j++) {
                    $comment = new Comment();
                    $creation = $faker->dateTimeBetween('-' . rand(1, 9) . ' day');
                    $commentNumber++;

                    $comment->setContent($faker->text(rand(15, 250)))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($creation))
                        ->setStatus($this->getReference('status_' . rand(1, 3)))
                        ->setTrick($trick)
                        ->setUser($this->getReference('user_' . rand(1, $this->validUserCount())));

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class
        ];
    }

    /**
     * Get valid trick count
     *
     * @return integer
     */
    protected function trickCount(): int
    {
        $count = 0;

        while ($this->hasReference('trick_' . $count + 1)) {
            $count++;
        }

        return $count;
    }
}
