<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
#[ORM\UniqueConstraint(name: 'uniq_produit_reference', columns: ['reference'])]
#[ORM\HasLifecycleCallbacks]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Conseil: rends le nom obligatoire
    #[ORM\Column(length: 255)]
    private string $nom;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    // Argent -> DECIMAL côté DB, string côté PHP
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private string $prixUnitaire = '0.00';

    // Stratégie B: stock courant non nul
    #[ORM\Column(type: Types::INTEGER)]
    private int $quantiteStock = 0;

    // SKU / référence obligatoire + unique (voir UniqueConstraint ci-dessus)
    #[ORM\Column(length: 64)]
    private string $reference;

    // Dates immuables, non nulles (+ callbacks)
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateModification;

    // TVA: si c'est un taux, DECIMAL(5,2) en string (ex: "21.00")
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $tva = null;

    #[ORM\ManyToOne(targetEntity: CategorieProduit::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieProduit $categorieProduit = null;

    /**
     * @var Collection<int, ProduitFournisseur>
     */
    #[ORM\OneToMany(targetEntity: ProduitFournisseur::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $produitFournisseurs;

    /**
     * @var Collection<int, DetailAchat>
     */
    #[ORM\OneToMany(targetEntity: DetailAchat::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $detailAchats;

    /**
     * @var Collection<int, DetailVente>
     */
    #[ORM\OneToMany(targetEntity: DetailVente::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $detailVentes;

    /**
     * @var Collection<int, MouvementStock>
     */
    #[ORM\OneToMany(targetEntity: MouvementStock::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $mouvementStocks;

    public function __construct()
    {
        // init collections
        $this->produitFournisseurs = new ArrayCollection();
        $this->detailAchats = new ArrayCollection();
        $this->detailVentes = new ArrayCollection();
        $this->mouvementStocks = new ArrayCollection();

        // timestamps
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

    // --- Getters/Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }
    // (pas de setId())

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrixUnitaire(): string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getQuantiteStock(): int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): static
    {
        $this->quantiteStock = $quantiteStock;
        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
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

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): static
    {
        $this->tva = $tva;
        return $this;
    }

    public function getCategorieProduit(): ?CategorieProduit
    {
        return $this->categorieProduit;
    }

    public function setCategorieProduit(?CategorieProduit $categorieProduit): static
    {
        $this->categorieProduit = $categorieProduit;
        return $this;
    }

    /**
     * @return Collection<int, ProduitFournisseur>
     */
    public function getProduitFournisseurs(): Collection
    {
        return $this->produitFournisseurs;
    }

    public function addProduitFournisseur(ProduitFournisseur $produitFournisseur): static
    {
        if (!$this->produitFournisseurs->contains($produitFournisseur)) {
            $this->produitFournisseurs->add($produitFournisseur);
            $produitFournisseur->setProduit($this);
        }
        return $this;
    }

    public function removeProduitFournisseur(ProduitFournisseur $produitFournisseur): static
    {
        if ($this->produitFournisseurs->removeElement($produitFournisseur)) {
            if ($produitFournisseur->getProduit()
                !== $this) {
                $produitFournisseur->setProduit(null);
            }
        }
        return $this;
    }
}