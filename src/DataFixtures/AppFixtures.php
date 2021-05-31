<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create('fr_FR');

        // Etat
        for ($i = 0; $i < 10; $i++) {
            $wish = new Wish();
            $wish->setTitle($generator->sentence(8))
                ->setDescription($generator->paragraph(2))
                ->setAuthor($generator->name)
                ->setDateCreated($generator->dateTime)
                ->setNote($generator->numberBetween(0,10))
                ->setIsPublished($generator->boolean);

            // on prépare
            $manager->persist($wish);
        }

        // Lieu

        // Participant

        // Site

        // Sortie

        // Ville



        $manager->flush();
    }
}
