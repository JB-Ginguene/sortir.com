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
        // "Créée"= sortie créée, non publiée, tous les participants peuvent la voir dans la liste mais ni accéder aux détails ou réserver
        // "Ouverte" = sortie OUVERTE à la réservation, visible de tous = RESERVABLE
        // "Clôturée" = sortie FERMEE à la réservation, visible de tous
        // "Activité en cours" = sortie en cours
        // "Passée" = sortie ARCHIVEE, ayant une date de fin > d'un mois à aujourd'hui
        // "Annulée" = sortie annulée, visible de tous jusqu'à un mois après la sortie, mais non réservable
        $etatTab = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée"];
        foreach ($etatTab as $etatItem) {
            $etat = new Etat();
            $etat->setLibelle($etatItem);
            $manager->persist($etat);
        }
        $manager->flush();

        // Ville
        $villeTab = ['Rennes' => 35000,
            'Nantes' => 44000,
            'Quimper' => 29000,
            'Lyon' => 69000,
            'Valence' => 26000,
            'Paris' => 75000,
            'Le Mans' => 72000
        ];

        foreach ($villeTab as $ville => $codePostal) {
            $villeCree = new Ville();
            $villeCree->setNom($ville)->setCodePostal($codePostal);
            $manager->persist($villeCree);
        }
        $manager->flush();

        // Lieu
        $lieuTab = [
            'Ubu' => '1 Rue Saint-Hélier',
            'Trempolino' => '6 Boulevard Léon Bureau',
            'Novomax' => '2 Boulevard Dupleix',
            'Le Transbo' => '3 Boulevard de Stalingrad',
            'Le Mistral Palace' => '12 Rue Pasteur,',
            'La Gaitée Lyrique' => '3bis Rue Papin',
            'Excelsior' => 'Rue de la Raterie'
        ];

        $repoVilles = $manager->getRepository(Ville::class);
        $villes = $repoVilles->findAll();
        $i = 0;
        foreach ($lieuTab as $nom => $rue) {
            $lieuCree = new Lieu();
            $lieuCree->setNom($nom)->setRue($rue)->setVille($villes[$i])->setLatitude($generator->randomFloat())->setLongitude($generator->randomFloat());;
            $manager->persist($lieuCree);
            $i++;
        }
        $manager->flush();

        // Site
        $siteTab = ["CHARTRES DE BRETAGNE", "SAINT-HERBLAIN", "QUIMPER", "NIORT"];
        foreach ($siteTab as $siteItem) {
            $site = new Site();
            $site->setNom($siteItem);
            $manager->persist($site);
        }
        $manager->flush();

        //Participant
        $participantTab = [
            'Duhamel' => ['Pierrot35', 'Pierre', '0299754085', 'pierre.duhamel@gmail.com', '123456Aa&', 'ROLE_USER'],
            'Misthrow' => ['Kat212', 'Kathy', '0166559896', 'kat212@mail.com', '123456Aa&', 'ROLE_USER'],
            'Guillou' => ['Gégé_Guigui', 'Gérard', '0612345678', 'gg@ggmail.com', '123456Aa&', 'ROLE_USER'],
            'Morvan' => ['Morv666', 'Constance', '0299788956', 'constance.morvan@hotmail.com', '123456Aa&', 'ROLE_USER'],
            'Costa' => ['MauroCosta', 'Mauro', '0212457889', 'mauro.costa@hotmail.it', '123456Aa&', 'ROLE_USER'],
            'Teault' => ['Toinou123', 'Antoine', '0611223344', 'toinou@mail.com', '123456Aa&', 'ROLE_USER'],
            'Galaxy' => ['Guillaume56', 'Guillaume', '0299887744', 'guillaume@mail.com', '123456Aa&', 'ROLE_USER'],
            'LEBRANCHU' => ['PierreLB', 'Pierre', '0299756633', 'pierre@mail.com', '123456Aa&', 'ROLE_ADMIN'],
            'ARESU' => ['AntoineAR', 'Antoine', '0623451289', 'antoine@mail.com', '123456Aa&', 'ROLE_ADMIN'],
            'GINGUENE' => ['JBG', 'Jean-Baptiste', '0279895645', 'jb@mail.com', '123456Aa&', 'ROLE_ADMIN'],
        ];
        $sites = $manager->getRepository(Site::class)->findAll();
        foreach ($participantTab as $nom => $data) {
            $participant = new Participant();
            $participant->setNom($nom)
                ->setPseudo($data[0])
                ->setPrenom($data[1])
                ->setTelephone($data[2])
                ->setEmail($data[3])
                ->setPassword($data[4])
                ->setRoles([$data[5]])
                ->setSite($generator->randomElement($sites));
            $manager->persist($participant);

        }
        $manager->flush();

        // Sortie
        $etatOuverte = $manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        $etatPassee = $manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Passée']);
        $etatEnCours = $manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']);

        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $sites = $manager->getRepository(Site::class)->findAll();
        $participants = $manager->getRepository(Participant::class)->findAll();

        $sortiesTab = [
            /*"nom"=>['dateDebut', 'dureeEnMin', 'dateLimiteInscription', 'infoSortie'],*/
            "Concert  d'Henri Dès" => [new \DateTime('2021-06-20 20:30:00'), 90, new \DateTime('2021-06-19 20:30:00'), 'Super concert pour enfant !!!', $etatOuverte],
            "Pétanque party" => [new \DateTime('2021-07-20 16:30:00'), 360, new \DateTime('2021-07-19 16:30:00'), 'Tournoi de pétanque inter-régional, pensez à prendre votre pastis.', $etatOuverte],
            "Tournoi de ping-pong" => [new \DateTime('2020-06-20 20:30:00'), 90, new \DateTime('2021-06-19 20:30:00'), 'Apportez vos raquettes et venez participer au tournoi de pétanque!', $etatPassee],
            "Conférence sur le suicide chez les développeur utilisant PL/SQL" => [new \DateTime('2021-06-20 20:30:00'), 90, new \DateTime('2021-06-19 20:30:00'), 'Conférence animée par Anthony Cosson', $etatOuverte],
            "Soirée cinéma : marathon Star Wars" => [new \DateTime('2021-06-02 12:30:00'), 3600, new \DateTime('2021-05-28 20:30:00'), 'TA TA TA TA TATA TA TATA', $etatEnCours]
        ];
        foreach ($sortiesTab as $nom => $data) {
            $sortie = new Sortie();
            $sortie->setNom($nom)
                ->setEtat($data[4])
                ->setLieu($generator->randomElement($lieux))
                ->setSite($generator->randomElement($sites))
                ->setOrganisateur($generator->randomElement($participants));
            // on ajoute entre 3 et 8 participants pour chaque sortie :
            for ($j = 0; $j < $generator->numberBetween(3, 8); $j++) {
                $sortie->addParticipant($generator->randomElement($participants));
            }
            $sortie->setDateHeureDebut($data[0])
                ->setDuree($data[1])
                ->setDateLimiteInscription($data[2])
                ->setInfosSortie($data[3])
                ->setNbInscriptionsMax($generator->numberBetween(8, 15));
            $manager->persist($sortie);
        }
        $manager->flush();
    }

}