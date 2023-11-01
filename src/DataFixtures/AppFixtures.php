<?php

namespace App\DataFixtures;

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
        $this->faker = Factory::create();
    }
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 20; $i++) {
            $trick = new Tricks();
            $trick->setTitle($this->faker->word(4))
                ->setContent($this->faker->text)
                ->setImage($this->faker->imageUrl(360, 360, 'sports'))
                ->setVideo("https://youtu.be/mBB7CznvSPQ")
                ->setGroupeTrick('sport');
                

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
