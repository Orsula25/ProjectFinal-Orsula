<?php

namespace App\Entity;

use App\Repository\DetailVenteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailVenteRepository::class)]
class DetailVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detailVentes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vente $vente = null;

    #[ORM\ManyToOne(inversedBy: 'detailVentes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $sousTotal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(?Vente $vente): static
    {
        $this->vente = $vente;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(?string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getSousTotal(): ?string
    {
        return $this->sousTotal;
    }

    public function setSousTotal(?string $sousTotal): static
    {
        $this->sousTotal = $sousTotal;

        return $this;
    }


   public function calculerSousTotal(): void
{
    // Si pas de quantité → rien à faire
    if ($this->quantite === null) {
        $this->sousTotal = null;
        return;
    }

    $quantite = (float) $this->quantite;

    // Si un prix unitaire est renseigné sur la ligne, on l'utilise
    if ($this->prixUnitaire !== null) {
        $prix = (float) $this->prixUnitaire;
    }
    // Sinon, si un produit est lié → on prend son prix de vente
    elseif ($this->produit !== null) {
        $prix = (float) $this->produit->getPrixUnitaire();
        // On garde la trace en base
        $this->prixUnitaire = number_format($prix, 2, '.', '');
    }
    // Sinon, on ne sait pas calculer
    else {
        $this->sousTotal = null;
        return;
    }

    // Sous-total = quantité × prix
    $this->sousTotal = number_format($quantite * $prix, 2, '.', '');
}

}
