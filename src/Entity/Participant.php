<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte avec cet email")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo est déjà utilisé")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Veuillez renseigner votre email ")
     * @Assert\Email(message="Veuillez renseigner un email valide")
     *
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Length(min=6,
     *                max=15,
     *                minMessage="Le mot de passe doit contenir au minimum 6 caractères",
     *                maxMessage="Le mot de passe doit contenir au maximum 20 caractères")
     *
     * @Assert\Regex(pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,15}$^",
     *              match=true,
     *              message="Votre mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom ne peut contenir un chiffre"
     * )
     * @Assert\Length(max=30,
     *                maxMessage="Votre nom ne peut dépasser 30 caractères")
     *
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre prénom ne peut contenir un chiffre"
     * )
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Length(max=30,
     *                maxMessage="Votre prenom ne peut dépasser 30 caractères")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[0-9]/",
     *     match=true,
     *     message="Entrez un telephone valide"
     * )
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesOrganisee;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="participants")
     */
    private $inscriptionSorties;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @Assert\NotBlank(message="Ce champs ne peut être vide")
     * @Assert\Length(max=20,
     *                maxMessage="Votre pseudo ne peut dépasser 20 caractères")
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $avatar;


    public function __construct()
    {
        $this->sortiesOrganisee = new ArrayCollection();
        $this->inscriptionSorties = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }


    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesOrganisee(): Collection
    {
        return $this->sortiesOrganisee;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if (!$this->sortiesOrganisee->contains($sortiesOrganisee)) {
            $this->sortiesOrganisee[] = $sortiesOrganisee;
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if ($this->sortiesOrganisee->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getInscriptionSorties(): Collection
    {
        return $this->inscriptionSorties;
    }

    public function addInscriptionSorty(Sortie $inscriptionSorty): self
    {
        if (!$this->inscriptionSorties->contains($inscriptionSorty)) {
            $this->inscriptionSorties[] = $inscriptionSorty;
            $inscriptionSorty->addParticipant($this);
        }

        return $this;
    }

    public function removeInscriptionSorty(Sortie $inscriptionSorty): self
    {
        if ($this->inscriptionSorties->removeElement($inscriptionSorty)) {
            $inscriptionSorty->removeParticipant($this);
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

}
