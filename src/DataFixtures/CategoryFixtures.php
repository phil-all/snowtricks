<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 3 categories : grab / rotations / flip
        $categories = [
            1 => 'grabs',
            2 => 'rotations',
            3 => 'flips',
            4 => 'slides',
            5 => 'old-school'
        ];

        foreach ($categories as $key => $value) {
            $category = new Category();
            $category->setCategory($value);
            $manager->persist($category);

            $this->addReference('category_' . $key, $category);
        }

        $manager->flush();
    }
}
