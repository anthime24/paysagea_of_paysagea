<?php

namespace App\Core\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CodePromo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CodePromoRepository")
 */
class CodePromo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $valeur;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbUtilisations;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $actif = true;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $pourcentage;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbUtilisationsCompteur;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbUtilisationsParClient;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ClientCodePromo", mappedBy="codePromo")
     */
    private $clientCodePromos;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Offre", inversedBy="codePromos")
     * @ORM\JoinTable(name="codepromo_offre",
     *      joinColumns={@ORM\JoinColumn(name="codepromo_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="offre_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $offres;

    public function __construct()
    {
        $this->clientCodePromos = new ArrayCollection();
        $this->offres = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom() ? $this->getNom() : '';
    }

    public function isDatesValid()
    {
        if ($this->getDateDebut() && $this->getDateFin()) {
            return ($this->getDateDebut() < $this->getDateFin());
        } else {
            return true;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(?float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getNbUtilisations(): ?int
    {
        return $this->nbUtilisations;
    }

    public function setNbUtilisations(?int $nbUtilisations): self
    {
        $this->nbUtilisations = $nbUtilisations;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getNbUtilisationsCompteur(): ?int
    {
        return $this->nbUtilisationsCompteur;
    }

    public function setNbUtilisationsCompteur(?int $nbUtilisationsCompteur): self
    {
        $this->nbUtilisationsCompteur = $nbUtilisationsCompteur;

        return $this;
    }

    public function getNbUtilisationsParClient(): ?int
    {
        return $this->nbUtilisationsParClient;
    }

    public function setNbUtilisationsParClient(?int $nbUtilisationsParClient): self
    {
        $this->nbUtilisationsParClient = $nbUtilisationsParClient;

        return $this;
    }

    /**
     * @return Collection|ClientCodePromo[]
     */
    public function getClientCodePromos(): Collection
    {
        return $this->clientCodePromos;
    }

    public function addClientCodePromo(ClientCodePromo $clientCodePromo): self
    {
        if (!$this->clientCodePromos->contains($clientCodePromo)) {
            $this->clientCodePromos[] = $clientCodePromo;
            $clientCodePromo->setCodePromo($this);
        }

        return $this;
    }

    public function removeClientCodePromo(ClientCodePromo $clientCodePromo): self
    {
        if ($this->clientCodePromos->contains($clientCodePromo)) {
            $this->clientCodePromos->removeElement($clientCodePromo);
            // set the owning side to null (unless already changed)
            if ($clientCodePromo->getCodePromo() === $this) {
                $clientCodePromo->setCodePromo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Offre[]
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
        }

        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        if ($this->offres->contains($offre)) {
            $this->offres->removeElement($offre);
        }

        return $this;
    }
}
