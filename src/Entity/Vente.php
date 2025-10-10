<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
#[ORM\Table(name: 'vente')]
#[ORM\HasLifecycleCallbacks]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateVente = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $montantTotal = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateModification;

   
    /**
     * @var Collection<int, DetailVente>
     */
    #[ORM\OneToMany(targetEntity: DetailVente::class, mappedBy: 'vente', orphanRemoval: true, cascade: ['persist'])]
    private Collection $detailVentes;

    #[ORM\ManyToOne(inversedBy: 'venteEffectue')]
    private ?Utilisateur $venteTermine = null;
    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public function __construct()
    {
        $this->detailVentes = new ArrayCollection();
        $now = new \DateTimeImmutable();
        $this->dateCreation = $now;
        $this->dateModification = $now;
    
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->dateCreation = $this->dateCreation ?? $now;
        $this->dateModification = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->dateModification = new \DateTimeImmutable();
    }

    // --- Getters / Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }
    
    

    public function getDateVente(): ?\DateTimeImmutable
    {
        return $this->dateVente;
    }

    public function setDateVente(?\DateTimeImmutable $dateVente): static
    {
        $this->dateVente = $dateVente;
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

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateModification(): \DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeImmutable $dateModification): static
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Collection<int, DetailVente>
     */
    public function getDetailVentes(): Collection
    {
        return $this->detailVentes;
    }

    public function addDetailVente(DetailVente $detailVente): static
    {
        if (!$this->detailVentes->contains($detailVente)) {
            $this->detailVentes->add($detailVente);
            $detailVente->setVente($this);
        }
        return $this;
    }

    public function removeDetailVente(DetailVente $detailVente): static
    {
        if ($this->detailVentes->removeElement($detailVente)) {
            if ($detailVente->getVente() === $this) {
                $detailVente->setVente(null);
            }
        }
        return $this;
    }

    public function getVenteTermine(): ?Utilisateur
    {
        return $this->venteTermine;
    }

    public function setVenteTermine(?Utilisateur $venteTermine): static
    {
        $this->venteTermine = $venteTermine;

        return $this;
    }

    #[ORM\PrePersist]
    public function setDatCreationValue(): void
    {
        if ($this->dateCreation === null) {
            $this->dateCreation = new \DateTimeImmutable();
        }
        
    }

    #[ORM\PreUpdate]
    public function setDatModificationValue(): void
    {
        if ($this->dateModification === null) {
            $this->dateModification = new \DateTimeImmutable();
        }
    }

// renvoie les taux de TVA distincts de la vente, formatés pour l’index (ex. "6 %, 21 %").

    public function getTauxTvaLabel(): string
{
    $unique = [];

    foreach ($this->detailVentes as $detail) {
        $tvaStr = $detail->getProduit()?->getTva();
        if ($tvaStr === null || $tvaStr === '') {
            continue;
        }
        $raw = (float) $tvaStr;                 // "21.00" -> 21 ; "0.21" -> 0.21
        $pct = $raw > 1 ? $raw : $raw * 100;    // normalise en pourcentage
        $key = number_format($pct, 2, ',', ''); // "21,00"
        $unique[$key] = true;
    }

    if (!$unique) {
        return '-';
    }

    $rates = array_keys($unique);
    $rates = array_map(static fn(string $s) => preg_replace('/,00$/', '', $s).' %', $rates);

    return implode(', ', $rates);
}






}
