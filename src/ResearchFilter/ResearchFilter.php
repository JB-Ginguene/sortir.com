<?php

namespace App\ResearchFilter;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class ResearchFilter
{
    private $priceMin;
    private $priceMax;
    private $saleOrRent;
    private $type;
    private $surfaceMin;
    private $surfaceMax;
    private $roomMin;
    private $roomMax;
    private $address;
    private $specificities;
    private $outsideSurfaceMin;
    private $outsideSurfaceMax;

}
