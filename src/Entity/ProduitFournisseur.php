<?php

namespace App\Entity;

use App\Repository\ProduitFournisseurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitFournisseurRepository::class)]
class ProduitFournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $delaisLivraison = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'produitFournisseur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produits = null;

    #[ORM\ManyToOne(inversedBy: 'produitFournisseur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $fournisseurs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDelaisLivraison(): ?\DateTime
    {
        return $this->delaisLivraison;
    }

    public function setDelaisLivraison(?\DateTime $delaisLivraison): static
    {
        $this->delaisLivraison = $delaisLivraison;

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

    public function getProduits(): ?Produit
    {
        return $this->produits;
    }

    public function setProduits(?Produit $produits): static
    {
        $this->produits = $produits;

        return $this;
    }

    public function getFournisseurs(): ?Fournisseur
    {
        return $this->fournisseurs;
    }

    public function setFournisseurs(?Fournisseur $fournisseurs): static
    {
        $this->fournisseurs = $fournisseurs;

        return $this;
    }
}
