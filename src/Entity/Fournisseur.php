<?php

namespace App\Entity;

use App\Repository\FournisseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numTva = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateDerniereCommande = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateModification = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateCreation = null;

    /**
     * @var Collection<int, Achat>
     */
    #[ORM\OneToMany(targetEntity: Achat::class, mappedBy: 'fournisseur')]
    private Collection $achats;

    /**
     * @var Collection<int, ProduitFournisseur>
     */
    #[ORM\OneToMany(targetEntity: ProduitFournisseur::class, mappedBy: 'fournisseur', orphanRemoval: true)]
    private Collection $produitFournisseurs;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
        $this->produitFournisseurs = new ArrayCollection();

       
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
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

    public function getNumTva(): ?string
    {
        return $this->numTva;
    }

    public function setNumTva(?string $numTva): static
    {
        $this->numTva = $numTva;
        return $this;
    }

    public function getDateDerniereCommande(): ?\DateTimeImmutable
    {
        return $this->dateDerniereCommande;
    }

    public function setDateDerniereCommande(\DateTimeImmutable $dateDerniereCommande): static
    {
        $this->dateDerniereCommande = $dateDerniereCommande;
        return $this;
    }

    public function getDateModification(): ?\DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeImmutable $dateModification): static
    {
        $this->dateModification = $dateModification;
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
            if ($achat->getFournisseur() === $this) {
                $achat->setFournisseur(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ProduitFournisseur>
     */
    public function getProduitFournisseurs(): Collection
    {
        return $this->produitFournisseurs;
    }

    public function addProduitFournisseur(ProduitFournisseur $pf): static
    {
        if (!$this->produitFournisseurs->contains($pf)) {
            $this->produitFournisseurs->add($pf);
            $pf->setFournisseur($this);
        }
        return $this;
    }

    public function removeProduitFournisseur(ProduitFournisseur $pf): static
    {
        if ($this->produitFournisseurs->removeElement($pf)) {
            if ($pf->getFournisseur() === $this) {
                $pf->setFournisseur(null);
            }
        }
        return $this;
    }
}
