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

        //category
        $groupTrick = ['La manière de rider', 'Les grabs', 'Les rotations', 'Les flips', 'Les rotations désaxées', 'Les slides', 'Les one foot tricks', 'Old school'];
        for ($i = 0; $i < count($groupTrick); ++$i) {
            $category = new Category();
            $title  = $groupTrick[$i];
            $category->setName($title);
            $manager->persist($category);
        }


        // tricks
        for ($m = 0; $m < 20; $m++) {
            $trick = new Tricks();
            $trick->setTitle($this->faker->word(6))
                ->setContent($this->faker->text)
                ->setImage($this->faker->imageUrl(360, 360, 'sports'))
                ->setVideo("https://youtu.be/mBB7CznvSPQ");
            for ($c = 0; $c < 7; $c++) {
                $trick->addCategory($category);
            }
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
