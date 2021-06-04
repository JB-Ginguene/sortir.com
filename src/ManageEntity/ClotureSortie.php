<?php


namespace App\ManageEntity;


use App\Entity\Etat;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClotureSortie
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function actualisationCloture($sortiesHomePage){

        $etatOuverte = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        $etatCloture = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);

        /**@var \App\Entity\Sortie $sortie */
        foreach ($sortiesHomePage as $sortie){

            if ($sortie->getNbInscriptionsMax() == count($sortie->getParticipants()) && $sortie->getEtat() == $etatOuverte){

                $sortie->setEtat($etatCloture);
            }

            if ($sortie->getNbInscriptionsMax() > count($sortie->getParticipants()) && $sortie->getEtat() == $etatCloture){

                $sortie->setEtat($etatOuverte);
            }
        }

        return $sortiesHomePage;
    }

}