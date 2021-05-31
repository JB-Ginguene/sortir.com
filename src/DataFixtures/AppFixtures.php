<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $generator = Faker\Factory::create('fr_FR');

        // Etat
        $etatTab = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        foreach ($etatTab as $etatItem) {
            $etat = new Etat();
            $etat->setLibelle($etatItem);
            $manager->persist($etat);
        }
        $manager->flush();

        // Ville
        for ($i=0;$i<3;$i++){

            $ville = new Ville();

            switch ($i){
                case 0 : $ville->setNom("Rennes")->setCodePostal("35000");
                    break;
                case 1 : $ville->setNom("Nantes")->setCodePostal("44000");
                    break;
                case 2 : $ville->setNom("Quimper")->setCodePostal("29000");
            }

            $manager->persist($ville);
            $manager->flush();
        }

        // Lieu
        for ($i=0;$i<7;$i++){

            $repoVilles = $manager->getRepository(Ville::class);
            $villes = $repoVilles->findAll();

            $lieu = new Lieu();

            $lieu->setVille($generator->randomElement($villes))
                    ->setNom($generator->word())
                    ->setRue($generator->streetName)
                    ->setLatitude($generator->randomFloat())
                    ->setLongitude($generator->randomFloat());


            $manager->persist($lieu);
            $manager->flush();
        }

        // Site
        for ($i=0;$i<3;$i++){

            $site = new Site();

            switch ($i){
                case 0 : $site->setNom("CHARTRES DE BRETAGNE");
                    break;
                case 1 : $site->setNom("SAINT-HERBLAIN");
                    break;
                case 2 : $site->setNom("QUIMPER");
            }

            $manager->persist($site);
            $manager->flush();
        }

        //Participant
        for ($i = 0; $i < 10; $i++) {
            $sites = $manager->getRepository(Site::class)->findAll();
            $participant = new Participant();
            $participant->setNom($generator->word())
                ->setPrenom($generator->word())
                ->setTelephone($generator->phoneNumber)
                ->setEmail($generator->email)
                ->setPassword($generator->password(7,20))
                ->setRoles(['ROLE_USER'])
                ->setSite($generator->randomElement($sites));
            // on prépare
            $manager->persist($participant);

            $manager->flush();
        }

        // Sortie
        $etats = $manager->getRepository(Etat::class)->findAll();
        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $sites = $manager->getRepository(Site::class)->findAll();
        $participants = $manager->getRepository(Participant::class)->findAll();
        for ($i = 0; $i < 5; $i++) {
            $sortie = new Sortie();
            $sortie->setNom($generator->sentence(3))
                ->setEtat($generator->randomElement($etats))
                ->setLieu($generator->randomElement($lieux))
                ->setSite($generator->randomElement($sites))
                ->setOrganisateur($generator->randomElement($participants));
            // on ajoute 3 participants pour chaque sortie :

            for ($j = 0; $j < 3; $j++) {
                $sortie->addParticipant($generator->randomElement($participants));
            }
            $sortie->setDateHeureDebut($generator->dateTime)
                ->setDuree($generator->numberBetween(1, 5))
                ->setDateLimiteInscription($generator->dateTimeAD($sortie->getDateHeureDebut()->format('Y-m-d H:i:s')))
                ->setInfosSortie($generator->paragraph(2))
                ->setNbInscriptionsMax(6);


        $manager->persist($sortie);
        }

        $manager->flush();
    }
}