<?php

namespace App\Entity;

use App\Repository\CommandeAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeAchatRepository::class)]
class CommandeAchat
{
    // statut de la commande
    public const STATUT_BROUILLON     = 'DRAFT';
    public const STATUT_ENVOYEE       = 'SENT';
    public const STATUT_RECEPTIONNEE  = 'RECEIVED';
    public const STATUT_ANNULEE       = 'annulee';

 
  

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'commandeAchats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $fournisseur = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = self::STATUT_BROUILLON;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'commande', orphanRemoval: true, cascade: ['persist'])]
    private Collection $lignesCommande;

    public function __construct()
    {
        $this->lignesCommande = new ArrayCollection();
       if ($this->statut === null) {
           $this->statut = self::STATUT_BROUILLON;
       } 
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }
    

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    // Helpers lisibles pour twig / controleur 
    public function isBrouillon(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }

    public function isEnvoyee(): bool
    {
        return $this->statut === self::STATUT_ENVOYEE;
    }

    public function isReceptionnee(): bool
    {
        return $this->statut === self::STATUT_RECEPTIONNEE;
    }

    public function isAnnulee(): bool
    {
        return $this->statut === self::STATUT_ANNULEE;
    }




    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignesCommande(): Collection
    {
        return $this->lignesCommande;
    }

    public function addLignesCommande(LigneCommande $lignesCommande): static
    {
        if (!$this->lignesCommande->contains($lignesCommande)) {
            $this->lignesCommande->add($lignesCommande);
            $lignesCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLignesCommande(LigneCommande $lignesCommande): static
    {
        if ($this->lignesCommande->removeElement($lignesCommande)) {
            // set the owning side to null (unless already changed)
            if ($lignesCommande->getCommande() === $this) {
                $lignesCommande->setCommande(null);
            }
        }

        return $this;
    }
}
