<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\LigneCommande;

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

 
    #[ORM\Column(length: 255)]
    private string $nom;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private string $prixUnitaire = '0.00';

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantiteStock = 0;

   
    #[ORM\Column(length: 64)]
    private string $reference;

   
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $dateModification;

   
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

    #[ORM\Column(nullable: true)]
    private ?int $stockMin = null;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'produit')]
    private Collection $lignesCommande;

    // la quantité en commande (car le stock ne peut pas etre augmenté directement apres la commande)

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $enCommande = 0;

    public function __construct()
    {
        // init collections
        $this->produitFournisseurs = new ArrayCollection();
        $this->detailAchats = new ArrayCollection();
        $this->detailVentes = new ArrayCollection();
        $this->mouvementStocks = new ArrayCollection();
        $this->lignesCommande = new ArrayCollection();

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

    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStockMin(): ?int
    {
        return $this->stockMin;
    }

    public function setStockMin(?int $stockMin): static
    {
        $this->stockMin = $stockMin;

        return $this;
    }

    public function getValeurStock():float
    {
        return $this->quantiteStock * (float)$this->getPrixUnitaire();
    }

    // État basé sur le stock physique uniquement

    public function getEtatStock():string
    {
        if ($this->getQuantiteStock() === 0) {
            return 'rupture';
        }

        if ($this->getStockMin() !==null && $this->getQuantiteStock() < $this->getStockMin()) {
            return 'sous_seuil';
        }
        return 'ok';
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
            $lignesCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLignesCommande(LigneCommande $lignesCommande): static
    {
        if ($this->lignesCommande->removeElement($lignesCommande)) {
            // set the owning side to null (unless already changed)
            if ($lignesCommande->getProduit() === $this) {
                $lignesCommande->setProduit(null);
            }
        }

        return $this;
    }



// getters/setters pour enCommande

 public function getEnCommande(): int { return $this->enCommande; }
 
 public function setEnCommande(int $v): self
  { $this->enCommande = max(0, $v); 
    return $this; }

 public function incEnCommande(int $v): self 
 { $this->enCommande = max(0, $this->enCommande + $v);
     return $this; }

 public function decEnCommande(int $v): self
  { $this->enCommande = max(0, $this->enCommande - $v);
     return $this; }


//état projeté = stock + enCommande
    public function getEtatStockProjete(): string
    {$projete = $this->getQuantiteStock() + $this->getEnCommande();

    if ($projete === 0) return 'rupture';
    if ($this->getStockMin() !== null && $projete < $this->getStockMin()) return 'sous_seuil';
    return 'ok';

    }
   

}