<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Precipitation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\PrecipitationRepository")
 */
class Precipitation
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
     * @ORM\Column(type="float")
     */
    private $min;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $max;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $codeCouleur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $unite;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="precipitation")
     */
    private $projets;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="BesoinEau", inversedBy="precipitations")
     * @ORM\JoinTable(name="precipitation_besoineau",
     *      joinColumns={@ORM\JoinColumn(name="precipitation_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="besoineau_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $besoinEaux;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->besoinEaux = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getMin() . ' - ' . $this->getMax() . ' ' . $this->getUnite();
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

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(float $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getCodeCouleur(): ?string
    {
        return $this->codeCouleur;
    }

    public function setCodeCouleur(string $codeCouleur): self
    {
        $this->codeCouleur = $codeCouleur;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): self
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * @return Collection|Projet[]
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->projets->contains($projet)) {
            $this->projets[] = $projet;
            $projet->setPrecipitation($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getPrecipitation() === $this) {
                $projet->setPrecipitation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BesoinEau[]
     */
    public function getBesoinEaux(): Collection
    {
        return $this->besoinEaux;
    }

    public function addBesoinEaux(BesoinEau $besoinEaux): self
    {
        if (!$this->besoinEaux->contains($besoinEaux)) {
            $this->besoinEaux[] = $besoinEaux;
        }

        return $this;
    }

    public function removeBesoinEaux(BesoinEau $besoinEaux): self
    {
        if ($this->besoinEaux->contains($besoinEaux)) {
            $this->besoinEaux->removeElement($besoinEaux);
        }

        return $this;
    }
}
