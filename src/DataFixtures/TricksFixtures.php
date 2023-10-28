<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Tricks;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 10; $i++) {
            $trick = new Tricks();
            $trick->setTitle($i)
            ->setContent($i)
            ->setGroupeTrick("")
            ->setImage("data:image/jpeg;base64,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")
            ->setVideo("https://youtu.be/FppENwvgRZ0")
            ->setCreatedAt( new \DateTime());
            $manager->persist($trick);
        
        }

        $manager->flush();
    }
}