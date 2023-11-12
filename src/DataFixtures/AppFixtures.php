<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
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
        


    // tricks
        for ($m = 0; $m < 30; $m++) {
            $trick = new Tricks();
            $trick->setTitle($this->faker->word(6))
                ->setContent($this->faker->paragraphs(5, true))
                ->setImage($this->faker->imageUrl())
                ->setVideo("https://youtu.be/mBB7CznvSPQ");
             for ($c = 0; $c < 7; $c++) {
                $trick->addCategory($category);
            } 
            $manager->persist($trick);
        }
    }


        //user
       for ($u=0; $u < 10 ; $u++) { 
            $user = new User();
            $user->setUsername($this->faker->name())
            ->setEmail($this->faker->email())
            ->setPassword("password")
            ->setAvatar($this->faker->imageUrl())
            ->setEnabled("")
            ->setRoles(['ROLE_USER'])
            ->setResetToken("password");

            $manager->persist($user);
        }

        $days = (new \DateTime())->diff($trick->getCreatedAt())->days;

        //comment
        for ($c=1; $c < mt_rand(3, 10); $c++) { 
           $comment = new Comment();
           $comment->setAuthor($this->faker->name)
           ->setContent($this->faker->paragraphs(2, true))
           ->setCreatedAt($this->faker->dateTimeBetween('-' . $days . 'days'))
           ->setTrick($trick);
           $manager->persist($comment);
        }

    

        $manager->flush();
    }
}
