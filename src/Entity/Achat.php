<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use App\Entity\Fournisseur;
use App\Entity\DetailAchat;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateAchat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montantTotal = null;

    #[ORM\Column(length: 50, nullable: true)]
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
    #[ORM\OneToMany(
        targetEntity: DetailAchat::class,
        mappedBy: 'achat',
        orphanRemoval: true,
        cascade: ['persist', 'remove']
    )]
    private Collection $detailAchats;

    public function __construct()
    {
        $this->detailAchats = new ArrayCollection();
    }

    // GETTERS / SETTERS 

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
            if ($detailAchat->getAchat() === $this) {
                $detailAchat->setAchat(null);
            }
        }

        return $this;
    }

    //renvoie les taux de TVA distincts de l'achat, formatés pour l’index (ex. "6 %, 21 %").
    public function getTauxTvaLabel(): string
    {
        $unique = [];

        foreach ($this->detailAchats as $detail) {
            $tvaStr = $detail->getProduit()?->getTva();
            if ($tvaStr === null || $tvaStr === '') continue;

            $raw = (float) $tvaStr;                 // "21.00" -> 21 ; "0.21" -> 0.21
            $pct = $raw > 1 ? $raw : $raw * 100;    // normalise en %
            $key = number_format($pct, 2, ',', ''); // "21,00"
            $unique[$key] = true;
        }

        if (!$unique) return '-';

        $rates = array_keys($unique);
        $rates = array_map(
            static fn(string $s) => preg_replace('/,00$/', '', $s).' %',
            $rates
        );

        return implode(', ', $rates);
    }

    public function recalculerMontantTotal(): void
    {
        $total = 0.0;

        foreach ($this->detailAchats as $detail) {
            $detail->calculerSousTotal();

            if ($detail->getSousTotal() !== null) {
                $total += (float) $detail->getSousTotal();
            }
        }

        $this->montantTotal = number_format($total, 2, '.', '');
    }

    //LIFECYCLE CALLBACKS

    #[ORM\PrePersist]
    public function beforeInsert(): void
    {
        if ($this->dateCreation === null) {
            $this->dateCreation = new \DateTimeImmutable();
        }

        $this->recalculerMontantTotal();
    }

    #[ORM\PreUpdate]
    public function beforeUpdate(): void
    {
        $this->dateModification = new \DateTimeImmutable();
        $this->recalculerMontantTotal();
    }
}
