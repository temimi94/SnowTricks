<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Comment;
use App\Entity\Images;
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

        //user
       
       for ($u=0; $u < 10 ; $u++) { 
        $user = new User();
        $user->setUsername($this->faker->name())
        ->setEmail($this->faker->email())
        ->setPassword("password")     
        ->setEnabled("")
        ->setRoles(['ROLE_USER'])
        ->setResetToken("password")
        ->setAvatar($this->faker->imageUrl());

       
        $manager->persist($user);
    }


        //category
        $groupTrick = new ArrayCollection(['La manière de rider', 'Les grabs', 'Les rotations', 'Les flips', 'Les rotations désaxées', 'Les slides', 'Les one foot tricks', 'Old school']);
        $filteredCollection = $groupTrick->filter(function($element) {
            return $element;
        });
        
        for ($i = 0; $i < count($filteredCollection); ++$i) {
            $category = new Categorie();
            $title  = $filteredCollection[$i];
            $category->setTitle($title);
            $manager->persist($category);
        

    // tricks
        for ($m = 0; $m <= mt_rand(4, 6); $m++) {
            $trick = new Tricks();
            $trick->setTitle($this->faker->word(6))
                ->setContent($this->faker->paragraphs(5, true))
                ->setVideo("https://youtu.be/mBB7CznvSPQ")
                ->setCategorie($category)
                ->setUser($user);
                
            $manager->persist($trick);
        }
    }


        $days = (new \DateTime())->diff($trick->getCreatedAt())->days;

        //comment
        for ($c=1; $c < mt_rand(3, 10); $c++) { 
           $comment = new Comment();
           $comment->setUser($user)
           ->setContent($this->faker->paragraphs(2, true))
           ->setCreatedAt($this->faker->dateTimeBetween('-' . $days . 'days'))
           ->setTrick($trick);
           $manager->persist($comment);
           
        }

     

        $manager->flush();
    }
}
