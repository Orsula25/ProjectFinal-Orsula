<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use App\Entity\Enum\Etat;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatRepository::class)]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateAchat = null;

    #[ORM\Column(type: Types::decimal, precision: 10, scale: 0, nullable: true)]
    private ?string $montantTotal = null;

    #[ORM\Column(Enum: Etat::class, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $fournisseur = null;

    /**
     * @var Collection<int, DetailAchat>
     */
    #[ORM\OneToMany(targetEntity: DetailAchat::class, mappedBy: 'achat', orphanRemoval: true)]
    private Collection $detailAchats;

    public function __construct()
    {
        $this->detailAchats = new ArrayCollection();
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

    public function getDateAchat(): ?\DateTimeImmutable
    {
        return $this->dateAchat;
    }

    public function setDateAchat(?\DateTimeImmutable $dateAchat): static
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }


    public function setMontantTotal(?string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeImmutable $dateModification): static
    {
        $this->dateModification = $dateModification;

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
    
    /**
     * @return Collection<int, DetailAchats>
     
     */
    /**
     * @return Collection<int, DetailAchat>
     */
    public function getDetailAchats(): Collection
    {
        return $this->detailAchats;
    }

    public function addDetailAchat(DetailAchat $detailAchat): static
    {
        if (!$this->detailAchats->contains($detailAchat)) {
            $this->detailAchats->add($detailAchat);
            $detailAchat->setAchat($this);
        }

        return $this;
    }

    public function removeDetailAchat(DetailAchat $detailAchat): static
    {
        if ($this->detailAchats->removeElement($detailAchat)) {
            // set the owning side to null (unless already changed)
            if ($detailAchat->getAchat() === $this) {
                $detailAchat->setAchat(null);
            }
        }

        return $this;
    }
}


