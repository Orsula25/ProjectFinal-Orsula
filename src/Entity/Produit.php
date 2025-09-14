<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixUnitaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantiteStock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    /**
     * @var Collection<int, Fournisseur>
     */
    #[ORM\ManyToMany(targetEntity: Fournisseur::class, inversedBy: 'produits')]
    private Collection $fournisseurs;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateModification = null;

    #[ORM\Column(nullable: true)]
    private ?int $tva = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?CategorieProduit $categorieProduit = null;

    /**
     * @var Collection<int, Fournisseur>
     */
    #[ORM\ManyToMany(targetEntity: Fournisseur::class)]
    private Collection $Fournisseur;

    /**
     * @var Collection<int, ProduitFournisseur>
     */
    #[ORM\OneToMany(targetEntity: ProduitFournisseur::class, mappedBy: 'produits', orphanRemoval: true)]
    private Collection $produitFournisseur;

    /**
     * @var Collection<int, DetailAchat>
     */
    #[ORM\OneToMany(targetEntity: DetailAchat::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $detailAchats;

    /**
     * @var Collection<int, DetailVente>
     */
    #[ORM\OneToMany(targetEntity: DetailVente::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $detailVente;

    /**
     * @var Collection<int, MouvementStock>
     */
    #[ORM\OneToMany(targetEntity: MouvementStock::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $mouvementStocks;


    public function __construct()
    {
        $this->fournisseurs = new ArrayCollection();
        $this->Fournisseur = new ArrayCollection();
        $this->produitFournisseur = new ArrayCollection();
        $this->detailAchats = new ArrayCollection();
        $this->detailVente = new ArrayCollection();
        $this->mouvementStocks = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
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

    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(?float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(?int $quantiteStock): static
    {
        $this->quantiteStock = $quantiteStock;

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

    /**
     * @return Collection<int, Fournisseur>
     */
    public function getFournisseurs(): Collection
    {
        return $this->fournisseurs;
    }

    public function addFournisseur(Fournisseur $fournisseur): static
    {
        if (!$this->fournisseurs->contains($fournisseur)) {
            $this->fournisseurs->add($fournisseur);
        }

        return $this;
    }

    public function removeFournisseur(Fournisseur $fournisseur): static
    {
        $this->fournisseurs->removeElement($fournisseur);

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getTva(): ?int
    {
        return $this->tva;
    }

    public function setTva(?int $tva): static
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
     * @return Collection<int, Fournisseur>
     */
    public function getFournisseur(): Collection
    {
        return $this->Fournisseur;
    }

    /**
     * @return Collection<int, ProduitFournisseur>
     */
    public function getProduitFournisseur(): Collection
    {
        return $this->produitFournisseur;
    }

    public function addProduitFournisseur(ProduitFournisseur $produitFournisseur): static
    {
        if (!$this->produitFournisseur->contains($produitFournisseur)) {
            $this->produitFournisseur->add($produitFournisseur);
            $produitFournisseur->setProduits($this);
        }

        return $this;
    }

    public function removeProduitFournisseur(ProduitFournisseur $produitFournisseur): static
    {
        if ($this->produitFournisseur->removeElement($produitFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($produitFournisseur->getProduits() === $this) {
                $produitFournisseur->setProduits(null);
            }
        }

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
            $detailAchat->setProduit($this);
        }

        return $this;
    }

    public function removeDetailAchat(DetailAchat $detailAchat): static
    {
        if ($this->detailAchats->removeElement($detailAchat)) {
            // set the owning side to null (unless already changed)
            if ($detailAchat->getProduit() === $this) {
                $detailAchat->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailVente>
     */
    public function getDetailVente(): Collection
    {
        return $this->detailVente;
    }

    public function addDetailVente(DetailVente $detailVente): static
    {
        if (!$this->detailVente->contains($detailVente)) {
            $this->detailVente->add($detailVente);
            $detailVente->setProduit($this);
        }

        return $this;
    }

    public function removeDetailVente(DetailVente $detailVente): static
    {
        if ($this->detailVente->removeElement($detailVente)) {
            // set the owning side to null (unless already changed)
            if ($detailVente->getProduit() === $this) {
                $detailVente->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MouvementStock>
     */
    public function getMouvementStocks(): Collection
    {
        return $this->mouvementStocks;
    }

    public function addMouvementStock(MouvementStock $mouvementStock): static
    {
        if (!$this->mouvementStocks->contains($mouvementStock)) {
            $this->mouvementStocks->add($mouvementStock);
            $mouvementStock->setProduit($this);
        }

        return $this;
    }

    public function removeMouvementStock(MouvementStock $mouvementStock): static
    {
        if ($this->mouvementStocks->removeElement($mouvementStock)) {
            // set the owning side to null (unless already changed)
            if ($mouvementStock->getProduit() === $this) {
                $mouvementStock->setProduit(null);
            }
        }

        return $this;
    }
}
