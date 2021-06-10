<?php


namespace App\ManageEntity;


use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;

class CheckVilleDuplicate
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Fonction qui permet de vérifier si la ville à insérer est déjà présente en base de donnée.
     * Retourne true si le nom de ville existe déjà
     * @param Ville $villeAInserer
     * @return bool
     */
    public function checkDuplicate(Ville $villeAInserer){
        $isDuplicate = false;
        $villeAInsererCaps = strtoupper($villeAInserer->getNom());
        $listeVille = $this->entityManager->getRepository(Ville::class)->findAll();
        /**@var \App\Entity\Ville $ville**/
        foreach ($listeVille as $ville){
            $villeCaps = strtoupper($ville->getNom());
            if ($villeAInsererCaps == $villeCaps){
                $isDuplicate = true;
            }
        }

        return $isDuplicate;
    }
}