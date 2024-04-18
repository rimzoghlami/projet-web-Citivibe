<?php

namespace App\Entity;

use App\Entity\Evenement;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentaireRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "commentaire")]
#[ORM\Index(name: "fk_commentaire_evenement", columns: ["idE"])]
#[ORM\Index(name: "fk_commentaire_utilisateur", columns: ["idU"])]
#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idc = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu du commentaire ne peut pas être vide.")]
    private ?string $contenu = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $datecreation;

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(name: 'ide', referencedColumnName: 'ide', nullable: false)]
    private ?Evenement $evenementRelation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(name: 'idu', referencedColumnName: 'idu', nullable: false)]
    private ?Utilisateur $usertRelation = null;

    public function getIdc(): ?int
    {
        return $this->idc;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getEvenementRelation(): ?Evenement
    {
        return $this->evenementRelation;
    }

    public function setEvenementRelation(?Evenement $evenementRelation): static
    {
        $this->evenementRelation = $evenementRelation;

        return $this;
    }

    public function getUsertRelation(): ?Utilisateur
    {
        return $this->usertRelation;
    }

    public function setUsertRelation(?Utilisateur $usertRelation): static
    {
        $this->usertRelation = $usertRelation;

        return $this;
    }


  
   
}
