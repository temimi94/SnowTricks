<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tricks;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {

        // tricks
        for ($i = 0; $i < 20; $i++) {
            $trick = new Tricks();
            $trick->setTitle($this->faker->word(6))
                ->setContent($this->faker->text)
                ->setImage($this->faker->imageUrl(360, 360, 'sports'))
                ->setVideo("https://youtu.be/mBB7CznvSPQ")
                ->setGroupeTrick('sport');


            $manager->persist($trick);
        }

        //category

       
        $groupTrick = ['La manière de rider', 'Les grabs', 'Les rotations', 'Les flips', 'Les rotations désaxées', 'Les slides', 'Les one foot tricks', 'Old school'];
        for ($i = 0; $i < count($groupTrick); ++$i) {
            $category = new Category();
            $title  = $groupTrick[$i];
            $category->setName($title);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
