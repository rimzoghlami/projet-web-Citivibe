<?php

namespace App\Entity;

use App\Entity\Page;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;    
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "evenement")]
#[ORM\Index(name: "fk_evenement", columns: ["IdP"])]
#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ide', type: 'integer')]
    private ?int $ide = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    private ?string $nome =  null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La catégorie ne peut pas être vide')]
    private ?string $categoriee =  null;

    #[ORM\Column(type: "date", nullable: true)]
    #[Assert\NotBlank(message: 'La date ne peut pas être vide')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Assert\NotBlank(message: 'L\'heure ne peut pas être vide')]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La page ne peut pas être vide')]
    private ?string $page =  null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide')]
    private ?string $description =  null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le nombre de places ne peut pas être vide')]
    #[Assert\Type(type: 'integer', message: 'Le nombre de places doit être un entier')]
    #[Assert\GreaterThan(value: 0, message: 'Le nombre de places doit être supérieur à zéro')]
    private ?int $nbrplaces =  null;

    #[ORM\Column(length: 255)]
    private ?string $photo =  null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La latitude ne peut pas être vide')]
    #[Assert\Type(type: 'float', message: 'La latitude doit être un nombre décimal')]
    private ?float $latitude =  null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La longitude ne peut pas être vide')]
    #[Assert\Type(type: 'float', message: 'La longitude doit être un nombre décimal')]
    private ?float $longitude =  null;

    #[ORM\JoinColumn(name: 'idp', referencedColumnName: 'idp', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'evenements')]
    private ?Page $pageRelation = null;
   
    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Commentaire::class)]
    private ?Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getIde(): ?int
    {
        return $this->ide;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getCategoriee(): ?string
    {
        return $this->categoriee;
    }

    public function setCategoriee(string $categoriee): static
    {
        $this->categoriee = $categoriee;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(?\DateTimeInterface $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): static
    {
        $this->page = $page;

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

    public function getNbrplaces(): ?int
    {
        return $this->nbrplaces;
    }

    public function setNbrplaces(int $nbrplaces): static
    {
        $this->nbrplaces = $nbrplaces;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPageRelation(): ?Page
    {
        return $this->pageRelation;
    }

    public function setPageRelation(?Page $pageRelation): static
    {
        $this->pageRelation = $pageRelation;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setEvenement($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getEvenement() === $this) {
                $commentaire->setEvenement(null);
            }
        }

        return $this;
    }

  
}
