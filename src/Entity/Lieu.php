<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Length(min=6,
     *                max=15,
     *                minMessage="Le nom doit contenir au minimum 6 caractères",
     *                maxMessage="Le nom doit contenir au maximum 30 caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Length(min=6,
     *                max=15,
     *                minMessage="La rue doit contenir au minimum 6 caractères",
     *                maxMessage="La rue doit contenir au maximum 30 caractères")
     */
    private $rue;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Regex(
     *     pattern="/^[0-9]/",
     *     match=true,
     *     message="Entrez une latitude valide")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Regex(
     *     pattern="/^[0-9]/",
     *     match=true,
     *     message="Entrez une longitude valide")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="lieux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="lieux")
     */
    private $sortie;

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie[] = $sortie;
            $sortie->setLieu($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getLieu() === $this) {
                $sortie->setLieu(null);
            }
        }

        return $this;
    }
}
