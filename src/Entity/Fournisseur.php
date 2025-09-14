<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FournisseurRepository::class)]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?int $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true)]
    private ?string $numTva = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateDerniereCommande = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateModification = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, mappedBy: 'fournisseurs')]
    private Collection $produits;

    /**
     * @var Collection<int, Achat>
     */
    #[ORM\OneToMany(targetEntity: Achat::class, mappedBy: 'fournisseur', orphanRemoval: true)]
    private Collection $achats;

    /**
     * @var Collection<int, ProduitFournisseur>
     */
    #[ORM\OneToMany(targetEntity: ProduitFournisseur::class, mappedBy: 'fournisseurs', orphanRemoval: true)]
    private Collection $produitFournisseur;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->achats = new ArrayCollection();
        $this->produitFournisseur = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(?int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumTva(): ?int
    {
        return $this->numTva;
    }

    public function setNumTva(?int $numTva): static
    {
        $this->numTva = $numTva;

        return $this;
    }

    public function getDateDerniereCommande(): ?\DateTime
    {
        return $this->dateDerniereCommande;
    }

    public function setDateDerniereCommande(\DateTime $dateDerniereCommande): static
    {
        $this->dateDerniereCommande = $dateDerniereCommande;

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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addFournisseur($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeFournisseur($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Achat>
     
     */
    
    
    public function getAchats(): Collection
    {
        return $this->achats;
    }

    public function addAchat(Achat $achat): static
    {
        if (!$this->achats->contains($achat)) {
            $this->achats->add($achat);
            $achat->setFournisseur($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): static
    {
        if ($this->achats->removeElement($achat)) {
            // set the owning side to null (unless already changed)
            if ($achat->getFournisseur() === $this) {
                $achat->setFournisseur(null);
            }
        }

        return $this;
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
            $produitFournisseur->setFournisseurs($this);
        }

        return $this;
    }

    public function removeProduitFournisseur(ProduitFournisseur $produitFournisseur): static
    {
        if ($this->produitFournisseur->removeElement($produitFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($produitFournisseur->getFournisseurs() === $this) {
                $produitFournisseur->setFournisseurs(null);
            }
        }

        return $this;
    }
}
