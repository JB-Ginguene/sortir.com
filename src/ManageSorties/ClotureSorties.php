<?php


namespace App\ManageSorties;


use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class ClotureSorties
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function actualisationCloture(){

    }
}