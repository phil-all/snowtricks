<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\Gender;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MediaFixtures extends TrickFixtures implements DependentFixtureInterface
{
    /**
     * load medias
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // user avatar
        for ($i = 1; $i <= $this->getCount('nonPendingUser'); $i++) {
            $media = new Media();
            $user = $this->getReference('nonPendingUser_' . $i);

            $media->setPath($this->getRandomAvatar($user->getGender()))
                ->setType($this->getReference('type_avatar'))
                ->setUser($user);

            $manager->persist($media);
        }

        // trick media
        for ($j = 1; $j <= $this->getCount('trick'); $j++) {
            // thumbnail
            $media = new Media();

            $media->setPath($this->getRandomImagePath())
                ->setType($this->getReference('type_thumbnail'))
                ->setTrick($this->getReference('trick_' . $j));

            $manager->persist($media);

            // images
            for ($k = 1; $k <= rand(1, 5); $k++) {
                $media = new Media();

                $media->setPath($this->getRandomImagePath())
                    ->setType($this->getReference('type_image'))
                    ->setTrick($this->getReference('trick_' . $j));

                $manager->persist($media);
            }

            // videos
            $videoCount = rand(0, 3);
            if ($videoCount > 0) {
                for ($l = 1; $l <= $videoCount; $l++) {
                    $media = new Media();

                    $media->setPath($this->getRandomYoutubeUri())
                        ->setType($this->getReference('type_video'))
                        ->setTrick($this->getReference('trick_' . $j));

                    $manager->persist($media);
                }
            }
        }
        $manager->flush();
    }

    /**
     * Get medias dependencies
     *
     * @return array
     */
    public function getDependencies(): array
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
        $number = sprintf('%02d', rand(1, 29));

        return 'demopix' . $number . '.jpg';
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

    /**
     * Return random embed youtube uri
     *
     * @return string
     */
    private function getRandomYoutubeUri(): string
    {
        $uriList = [
            0 => 'kOyCsY4rBH0',
            1 => 'Sj7CJH9YvAo',
            2 => 'PCKrzZNwyoQ',
            3 => '9T5AWWDxYM4',
            4 => 'L4bIunv8fHM',
            5 => 'SFYYzy0UF-8',
            6 => 'cGiAFk2adMw',
            7 => '1vtZXU15e38',
            8 => '4_Okz3_ycqE'
        ];

        return 'http://www.youtube.com/embed/' . $uriList[rand(0, 8)];
    }
}
