<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ph
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\PhRepository")
 */
class Ph
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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="ph")
     */
    private $projets;

    /**
     * @var TypeSol
     *
     * @ORM\ManyToOne(targetEntity="TypeSol", inversedBy="phs")
     */
    private $typeSol;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getMin() . ' - ' . $this->getMax();
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
            $projet->setPh($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getPh() === $this) {
                $projet->setPh(null);
            }
        }

        return $this;
    }

    public function getTypeSol(): ?TypeSol
    {
        return $this->typeSol;
    }

    public function setTypeSol(?TypeSol $typeSol): self
    {
        $this->typeSol = $typeSol;

        return $this;
    }
}
