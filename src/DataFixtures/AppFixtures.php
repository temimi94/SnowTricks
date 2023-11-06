<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tricks;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $hashePassword;

    public function __construct(UserPasswordHasherInterface $hashePassword)
    {
        $this->faker = Factory::create('fr_FR');
        $this->hashePassword = $hashePassword;
    }
    public function load(ObjectManager $manager): void
    {

        //category
        $groupTrick = new ArrayCollection(['La manière de rider', 'Les grabs', 'Les rotations', 'Les flips', 'Les rotations désaxées', 'Les slides', 'Les one foot tricks', 'Old school']);
        $filteredCollection = $groupTrick->filter(function($element) {
            return $element;
        });
        
        for ($i = 0; $i < count($filteredCollection); ++$i) {
            $category = new Category();
            $title  = $filteredCollection[$i];
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


        //user
        for ($u=0; $u < 10 ; $u++) { 
            $user = new User();
            $user->setUsername($this->faker->name())
            ->setEmail($this->faker->email())
            ->setPassword("password")
            ->setAvatar($this->faker->imageUrl())
            ->setEnabled("");

        $hasher = $this->hashePassword->hashPassword(
            $user,
            "password"
        );
        $user->setPassword($hasher);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
