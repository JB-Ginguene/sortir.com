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
        // "Passée" = sortie ayant une date de fin > d'un mois à aujourd'hui
        // "Archivée" = sortie ARCHIVEE, fin de sortie > à un mois
        // "Annulée" = sortie annulée, visible de tous jusqu'à un mois après la sortie, mais non réservable
        $etatTab = ["Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulée", "Archivée"];
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
            'Le Mans' => 72000,
            'Bourgoin-Jallieu' => 38300,
            'Marseille' => 13000,
            "Lille" => 59000,
            "Grenoble"=>38000,
            "Chambéry"=>73000
        ];

        foreach ($villeTab as $ville => $codePostal) {
            $villeCree = new Ville();
            $villeCree->setNom($ville)->setCodePostal($codePostal);
            $manager->persist($villeCree);
        }
        $manager->flush();

        // Lieu
        $lieuTab = [
            'Ubu' => ['1 Rue Saint-Hélier', 'Rennes', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2664.1193690682544!2d-1.6753732851400955!3d48.10793297922107!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480ede4ab549957d%3A0xfc80ffddb3eb3f65!2sUbu!5e0!3m2!1sen!2sfr!4v1623136161175!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Trempolino' => ['6 Boulevard Léon Bureau', 'Nantes', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2710.6089525798293!2d-1.5651436851852008!3d47.20466597916004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4805ec7140f99ef5%3A0xd90c9bc18aff33fb!2sTrempolino!5e0!3m2!1sen!2sfr!4v1623136139451!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Novomax' => ['2 Boulevard Dupleix', 'Quimper', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2669.9864877707696!2d-4.099770785145794!3d47.99464827921225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4810d5903ceebf77%3A0xd55b284f0a3ba8ba!2zTGUgTm92b21heCAvIFBvbGFyaXTDqVtTXQ!5e0!3m2!1sen!2sfr!4v1623136181788!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Le Transbo' => ['3 Boulevard de Stalingrad', 'Lyon', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2782.365816463682!2d4.858259314745117!3d45.78389847910598!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47f4ea9662b489a1%3A0xaf591eb6032d14e7!2sLe%20Transbordeur!5e0!3m2!1sen!2sfr!4v1623136209135!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Le Mistral Palace' => ['12 Rue Pasteur,', 'Valence', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2824.7293858247713!2d4.889578414704018!3d44.928838479098175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47f5582db8aba3b3%3A0x84ccb56ee2177460!2sMistral%20Palace!5e0!3m2!1sen!2sfr!4v1623136248496!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'La Gaitée Lyrique' => ['3bis Rue Papin', 'Paris', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.5621452460146!2d2.3512054148983212!3d48.86655927928828!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e1088588271%3A0x7d95ac3236605c3a!2sGait%C3%A9%20Lyrique!5e0!3m2!1sen!2sfr!4v1623136265888!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Excelsior' => ['Rue de la Raterie', 'Le Mans', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2670.9842883259657!2d0.15767681485324728!3d47.97536227921079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e2858e7ce2352f%3A0x8ee5594c43437a0c!2sLa%20P%C3%A9niche%20Excelsior!5e0!3m2!1sen!2sfr!4v1623136292257!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Le Gazoline' => ['24 Rue Nantaise', 'Rennes', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d791.9839635870387!2d-1.684413691143654!3d48.11205617253473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480ede3206c19b1d%3A0xf32c2f269f219e31!2sLe%20Gazoline!5e0!3m2!1sfr!2sfr!4v1623395501584!5m2!1sfr!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Les Abattoirs' => ["18 Route de l'Isle d'Abeau", 'Bourgoin-Jallieu', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2791.4147910458605!2d5.264082614746214!3d45.60230783223961!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478b2dd0813fa4ab%3A0x25981c375366b7d0!2sLes%20Abattoirs%20-%20Sc%C3%A8ne%20de%20Musiques%20Actuelles!5e0!3m2!1sfr!2sfr!4v1623398729675!5m2!1sfr!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'Le Poste à Galène' => ["103 Rue Ferrari", 'Marseille', '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d11616.009910261697!2d5.3917483!3d43.2932698!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x59ad61aa1a873e9b!2zTGUgUG9zdGUgw6AgR2Fsw6huZQ!5e0!3m2!1sen!2sfr!4v1623398858607!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            "L'Aéronef" => ["168 Avenue Willy Brandt Centre Commercial - 59777, 59777 Lille", 'Lille', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2530.4738301200946!2d3.0713761149728778!3d50.63689068179548!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c2d58ad501e803%3A0x11b7fe335dbc3367!2sL&#39;A%C3%A9ronef!5e0!3m2!1sen!2sfr!4v1623398885793!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'La belle Electrique' => ["12 Espl. Andry Farcy", 'Grenoble', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2811.993004100488!2d5.701993014728507!3d45.1872378599036!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478af37b73b3b29b%3A0x16952b4feb50ba3b!2sLa%20Belle%20Electrique!5e0!3m2!1sen!2sfr!4v1623399233812!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            'La Soute' => ["Jardin du Verney", 'Chambéry', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d44985.307422983205!2d5.662316677982152!3d45.195562910466194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478ba857b69fb2fb%3A0xb917c87cb0a35549!2sLA%20SOUTE!5e0!3m2!1sen!2sfr!4v1623399289658!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>']
        ];

        $repoVilles = $manager->getRepository(Ville::class);
        $villes = $repoVilles->findAll();
        $i = 0;
        foreach ($lieuTab as $nom => $data) {
            $lieuCree = new Lieu();
            $lieuCree->setNom($nom)->setRue($data[0])->setVille($repoVilles->findOneBy(['nom' => $data[1]]))->setUrlMap($data[2])->setLatitude($generator->randomFloat())->setLongitude($generator->randomFloat());;
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
            'Duhamel' => ['Pierrot35', 'Pierre', '0299754085', 'pierre.duhamel@gmail.com', '123456Aa&', 'ROLE_USER', 'GerardDepardieu.jpg'],
            'Misthrow' => ['Kat212', 'Kathy', '0166559896', 'kat212@mail.com', '123456Aa&', 'ROLE_USER', 'nicoleKidman.jpg'],
            'Guillou' => ['Gégé_Guigui', 'Gérard', '0612345678', 'gg@ggmail.com', '123456Aa&', 'ROLE_USER', 'jeandujardin.jpg'],
            'Morvan' => ['Morv666', 'Constance', '0299788956', 'constance.morvan@hotmail.com', '123456Aa&', 'ROLE_USER', 'elisabethmoss.jpg'],
            'Costa' => ['MauroCosta', 'Mauro', '0212457889', 'mauro.costa@hotmail.it', '123456Aa&', 'ROLE_USER', 'patrick.jpg'],
            'Teault' => ['Toinou123', 'Antoine', '0611223344', 'toinou@mail.com', '123456Aa&', 'ROLE_USER', 'philippepoutou.jpg'],
            'Galaxy' => ['Guillaume56', 'Guillaume', '0299887744', 'guillaume@mail.com', '123456Aa&', 'ROLE_USER', 'Steven-Wilson.jpg'],
            'LEBRANCHU' => ['PierreLB', 'Pierre', '0299756633', 'pierre@mail.com', '123456Aa&', 'ROLE_ADMIN', 'rami_malek_v3.png'],
            'ARESU' => ['AntoineAR', 'Antoine', '0623451289', 'antoine@mail.com', '123456Aa&', 'ROLE_ADMIN', 'Walter.png'],
            'GINGUENE' => ['JBG', 'Jean-Baptiste', '0279895645', 'jb@mail.com', '123456Aa&', 'ROLE_ADMIN', 'davymourier.jpg']
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
                ->setSite($generator->randomElement($sites))
                ->setActif(true)
                ->setAvatar($data[6]);
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
            "Concert  d'Henri Dès" => [new \DateTime('2021-06-11 10:30:00'), 90, new \DateTime('2021-06-11 09:30:00'), 'Super concert pour enfant !!!', $etatEnCours],
            "Pétanque party" => [new \DateTime('2021-07-20 16:30:00'), 360, new \DateTime('2021-07-19 16:30:00'), 'Tournoi de pétanque inter-régional, pensez à prendre votre pastis.', $etatOuverte],
            "Tournoi de ping-pong" => [new \DateTime('2021-06-09 20:30:00'), 90, new \DateTime('2021-06-08 20:30:00'), 'Apportez vos raquettes et venez participer au tournoi de ping-pong!', $etatPassee],
            "Dev PL/SQL Anonymes" => [new \DateTime('2021-06-20 20:30:00'), 90, new \DateTime('2021-06-19 20:30:00'), 'Soirée animée par Anthony Cosson, after-suicide', $etatOuverte],
            "Soirée marathon Star Wars" => [new \DateTime('2021-06-12 15:30:00'), 3600, new \DateTime('2021-06-12 10:30:00'), 'TA TA TA TA TATA TA TATA', $etatOuverte],
            "Soirée à theme, avoir la frite" => [new \DateTime('2021-07-11 15:30:00'), 240, new \DateTime('2021-07-07 10:30:00'), 'Cette soirée aura lieu, une fois!', $etatOuverte],
            "Concours de gaubage de flamby" => [new \DateTime('2021-06-11 15:30:00'), 240, new \DateTime('2021-06-11 10:30:00'), 'Nous rappelons aux participants que la technique dite de Beigbeder, consistant a gober son flamby par inspiration nasale, est non reglementaire.', $etatOuverte],
            "Bottleflip challenge world cup" => [new \DateTime('2021-06-11 15:30:00'), 240, new \DateTime('2021-06-11 10:30:00'), 'Rempotez une cup', $etatOuverte],
            "Pour ou contre - la vie" => [new \DateTime('2021-06-11 15:30:00'), 240, new \DateTime('2021-06-11 10:30:00'), 'mouais', $etatOuverte],
            "Pour ou contre - la mort" => [new \DateTime('2021-06-11 15:30:00'), 666, new \DateTime('2021-06-11 10:30:00'), 'Vous pourriez vous montrer courtois quand je vous invite à un gouter', $etatOuverte],
            "Pour ou contre - les lobes" => [new \DateTime('2021-06-11 15:30:00'), 30, new \DateTime('2021-06-11 10:30:00'), 'Conférence animée par Sebastien', $etatOuverte],
            "Initiation au saut en largeur" => [new \DateTime('2021-06-11 15:30:00'), 300, new \DateTime('2021-06-11 10:30:00'), "Vous n'en menerez pas large", $etatOuverte],
            "Combat de sumo" => [new \DateTime('2021-10-11 14:30:00'), 240, new \DateTime('2021-10-05 10:30:00'), 'Du lourd', $etatOuverte],
            "AG des GPCMVPCH!" => [new \DateTime('2021-08-15 15:30:00'), 240, new \DateTime('2021-08-15 10:30:00'), 'Assemblée Générale des gens pas contants, mais vraiment pas content hein! ', $etatOuverte],
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