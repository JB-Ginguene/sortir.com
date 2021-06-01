<?php

namespace App\ResearchFilter;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class ResearchFilter
{
    private $site;
    private $nomSortie;
    private $dateMin;
    private $dateMax;
    private $specificitees;

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getNomSortie()
    {
        return $this->nomSortie;
    }

    /**
     * @param mixed $nomSortie
     */
    public function setNomSortie($nomSortie): void
    {
        $this->nomSortie = $nomSortie;
    }

    /**
     * @return mixed
     */
    public function getDateMin()
    {
        return $this->dateMin;
    }

    /**
     * @param mixed $dateMin
     */
    public function setDateMin($dateMin): void
    {
        $this->dateMin = $dateMin;
    }

    /**
     * @return mixed
     */
    public function getDateMax()
    {
        return $this->dateMax;
    }

    /**
     * @param mixed $dateMax
     */
    public function setDateMax($dateMax): void
    {
        $this->dateMax = $dateMax;
    }

    /**
     * @return mixed
     */
    public function getSpecificitees()
    {
        return $this->specificitees;
    }

    /**
     * @param mixed $specificitees
     */
    public function setSpecificitees($specificitees): void
    {
        $this->specificitees = $specificitees;
    }

}
