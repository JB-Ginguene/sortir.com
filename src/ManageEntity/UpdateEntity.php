<?php


namespace App\ManageEntity;


use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UpdateEntity, classe utilitaire
 * @package App\ManageEntity
 */
class UpdateEntity
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager  )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Methode qui permet l'update en base de donnée d'une entité
     * @param $entity
     */
    public function save($entity){
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}