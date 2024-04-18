<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PageRepository;

#[ORM\Entity(repositoryClass:PageRepository ::class)]
class Page
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idp', type: 'integer')]
    private ?int $idp = null;



   // #[ORM\Column(name: 'idU', type: 'integer')]
   // private ?int $idU = null;



    #[ORM\Column(length: 255)]
   // #[Assert\NotBlank(message:"champ obligatoire")]
   // #[Assert\Length(max:500,maxMessage:"le titre ne doit pas depasser 50 caractaire")]
    private ?string $nom = null;


    #[ORM\Column]
    private ?int $contact =null ;



    #[ORM\Column(length: 255)]
   // #[Assert\NotBlank(message:"champ obligatoire")]
   // #[Assert\Length(max:500,maxMessage:"le titre ne doit pas depasser 50 caractaire")]
    private ?string $categoriep = null;



    #[ORM\Column(length: 255)]
   // #[Assert\NotBlank(message:"champ obligatoire")]
   // #[Assert\Length(max:500,maxMessage:"le titre ne doit pas depasser 50 caractaire")]
    private ?string $localisation = null;


    #[ORM\Column(length: 65535)]
    //#[Assert\NotBlank(message:"champ obligatoire")]
    //#[Assert\Length(min:10,minMessage:"reclamation trop courte",max:500,maxMessage:"reclamation trop long")]
    private ?string $description = null;
    



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $ouverture;



    #[ORM\Column(length : 500)]
    private ?string $image =null;


    #[ORM\Column(length : 500)]
    private ?string $logo =null;

    #[ORM\OneToMany(mappedBy: 'page', targetEntity: Evenement::class)]
private $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    public function getIdp(): ?int
    {
        return $this->idp;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getContact(): ?int
    {
        return $this->contact;
    }

    public function setContact(int $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getCategoriep(): ?string
    {
        return $this->categoriep;
    }

    public function setCategoriep(string $categoriep): static
    {
        $this->categoriep = $categoriep;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOuverture(): ?\DateTimeInterface
    {
        return $this->ouverture;
    }

    public function setOuverture(\DateTimeInterface $ouverture): static
    {
        $this->ouverture = $ouverture;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): static
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setPage($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getPage() === $this) {
                $evenement->setPage(null);
            }
        }

        return $this;
    }


   
  

  
}