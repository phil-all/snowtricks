<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MediaFixtures extends TrickFixtures implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Faker\Provider\Youtube($faker));


        // user avatar
        for ($l = 1; $l <= $this->getCount('nonPendingUser'); $l++) {
            $media = new Media();
            $user = $this->getReference('nonPendingUser_' . $l);

            $media->setFile($this->getRandomAvatar($user->getGender()))
                ->setType($this->getReference('type_avatar'))
                ->setUser($user);

            $manager->persist($media);
        }

        // trick image and video
        for ($i = 1; $i <= $this->getCount('trick'); $i++) {
            for ($j = 1; $j <= rand(1, 4); $j++) {
                $media = new Media();

                $media->setFile($this->getRandomImagePath())
                    ->setType($this->getReference('type_image'))
                    ->setTrick($this->getReference('trick_' . $i));

                $manager->persist($media);
            }

            $videoCount = rand(0, 2);
            if ($videoCount > 0) {
                for ($k = 1; $k <= $videoCount; $k++) {
                    $media = new Media();

                    $media->setFile($faker->youtubeRandomUri())
                        ->setType($this->getReference('type_video'))
                        ->setTrick($this->getReference('trick_' . $i));

                    $manager->persist($media);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CommentFixtures::class
        ];
    }

    /**
     * Returns random image path
     *
     * @return string
     */
    private function getRandomImagePath(): string
    {
        $number = sprintf('%02d', rand(1, 18));

        return 'public/demo-data/demopix' . $number . '.jpg';
    }

    /**
     * Returns random avatar image address from randomuser.me API web site
     *
     * @param Gender $gender
     *
     * @return string
     */
    private function getRandomAvatar(Gender $gender): string
    {
        $page = ($gender->getGender() === 'male') ? 'men/' : 'women/';

        $avatarAdress = 'https://randomuser.me/api/portraits/';

        return $avatarAdress . $page . rand(1, 99) . '.jpg';
    }
}
