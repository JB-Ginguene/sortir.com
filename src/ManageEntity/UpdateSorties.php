<?php


namespace App\ManageEntity;


use App\Entity\Etat;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateSorties
{

    private $entityManager;
    private $updateEntity;

    public function __construct(EntityManagerInterface $entityManager, UpdateEntity $updateEntity)
    {
        $this->entityManager = $entityManager;
        $this->updateEntity=$updateEntity;
    }

    /**
     * Fonction qui permet d'actualiser les états d'une liste de sorties
     * @param $listeSorties
     * @return mixed
     */
    public function actualisationSorties($listeSorties){

        $listeEtat = $this->entityManager->getRepository(Etat::class)->findAll();

        $sortiesActualisees = null;

        $etatOuverte = null;
        $etatCloture = null;
        $etatPassee = null;
        $etatArchivee = null;
        $etatEnCours = null;

        /**@var Etat $etat **/
        foreach ($listeEtat as $etat){

            switch ($etat->getLibelle()){

                case 'Ouverte': $etatOuverte = $etat;
                    break;
                case 'Clôturée': $etatCloture = $etat;
                    break;
                case 'Passée': $etatPassee = $etat;
                    break;
                case 'Archivée': $etatArchivee = $etat;
                    break;
                case 'Activité en cours' : $etatEnCours = $etat;
            }
        }


        $sortiesActualisees = $this->actualisationCloture($listeSorties, $etatOuverte, $etatCloture);

        $sortiesActualisees = $this->actualisationPasseeArchivee($listeSorties, $etatPassee, $etatArchivee);

        $sortiesActualisees =$this->actualisationEnCours($listeSorties,$etatEnCours,$etatOuverte);

        $sortiesActualisees = $this->hideArchives($listeSorties, $etatArchivee);

        return $sortiesActualisees;

    }

    /**
     * Fonction qui permet de retirer les sorties Archivées d'une liste de sorties
     * @param $listeSorties
     * @return mixed
     */
    public function retirerArchives($listeSorties){
        $etatArchivee = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Archivée']);

        if($listeSorties){
        for ($i=0;$i <= count($listeSorties);$i++){
            if ($listeSorties[$i]->getEtat()->getId() == $etatArchivee->getId()){
                unset($listeSorties[$i]);
            }
        }}

        return $listeSorties;
    }



    /**
     * Fonction qui permet d'actualiser l'état "Cloturée" sur une liste de sorties
     * @param $listeSorties
     * @param $etatOuverte
     * @param $etatCloture
     * @return mixed
     */
    private function actualisationCloture($listeSorties, $etatOuverte, $etatCloture ){

        //$etatOuverte = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        //$etatCloture = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);

        /**@var \App\Entity\Sortie $sortie */
        foreach ($listeSorties as $sortie){

            if ($sortie->getNbInscriptionsMax() == count($sortie->getParticipants()) && $sortie->getEtat() == $etatOuverte){

                $sortie->setEtat($etatCloture);
            }

            if ($sortie->getNbInscriptionsMax() > count($sortie->getParticipants()) && $sortie->getEtat() == $etatCloture){

                $sortie->setEtat($etatOuverte);
            }

            $this->updateEntity->save($sortie);
        }

        return $listeSorties;
    }

    /**
     * Fonction qui permet d'actualiser les états "Passée" et "Archivée" sur une liste de sorties
     * @param $listeSorties
     * @param $etatPassee
     * @param $etatArchivee
     * @return mixed
     * @throws \Exception
     */
    private function actualisationPasseeArchivee($listeSorties, $etatPassee, $etatArchivee){

        /**@var \App\Entity\Sortie $sortie */
        foreach ($listeSorties as $sortie){
            $now = new \DateTime();

            $dateDebut = new \DateTime($sortie->getDateHeureDebut()->format('Y-m-d H:i')) ;


            $dureeSortie = $sortie->getDuree();

            /**@var \DateTime $dateDebut
             *
             */
            if ($dateDebut->modify('+'.$dureeSortie.' minutes') < $now && $sortie->getEtat() != $etatArchivee){
                $sortie->setEtat($etatPassee);
                //dd($dateDebut);
            }

            //Si la date de fin est supérieur à 1 mois : on set l'état à Archivee
            if ($sortie->getDateHeureDebut() < $now->modify('-1month') && $sortie->getEtat() == $etatPassee){
                $sortie->setEtat($etatArchivee);
            }

            $this->updateEntity->save($sortie);
        }

        return $listeSorties;
    }

    private function actualisationEnCours($listeSorties, $etatEnCours, $etatOuverte){
        $now = new \DateTime();

        /**@var \App\Entity\Sortie $sortie */
        foreach ($listeSorties as $sortie){
            if ($sortie->getDateHeureDebut() < $now && $sortie->getEtat() == $etatOuverte ){
                $sortie->setEtat($etatEnCours);
            }
            $this->updateEntity->save($sortie);
        }
    }

    /**
     * Fonction qui permet de retirer les sorties Archivées d'une liste de sorties
     * @param $listeSorties
     * @param $etatArchivee
     * @return mixed
     */
    private function hideArchives($listeSorties, $etatArchivee){

        for ($i=0;$i < count($listeSorties);$i++){
            if ($listeSorties[$i]->getEtat() == $etatArchivee){
                unset($listeSorties[$i]);
            }
        }

        return $listeSorties;
    }

}