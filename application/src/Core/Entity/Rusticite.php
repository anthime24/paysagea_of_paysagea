<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rusticite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\RusticiteRepository")
 */
class Rusticite
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
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="rusticite")
     */
    private $projets;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Entite", mappedBy="rusticite")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Composition", mappedBy="rusticite")
     */
    private $compositions;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->entites = new ArrayCollection();
        $this->compositions = new ArrayCollection();
    }

    public function __toString()
    {
        return 'zone ' . $this->getNom() . ' (' . $this->getMin() . $this->getUnite() . ' / ' . $this->getMax(
            ) . $this->getUnite() . ')';
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
            $projet->setRusticite($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getRusticite() === $this) {
                $projet->setRusticite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Entite[]
     */
    public function getEntites(): Collection
    {
        return $this->entites;
    }

    public function addEntite(Entite $entite): self
    {
        if (!$this->entites->contains($entite)) {
            $this->entites[] = $entite;
            $entite->setRusticite($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            // set the owning side to null (unless already changed)
            if ($entite->getRusticite() === $this) {
                $entite->setRusticite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Composition[]
     */
    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    public function addComposition(Composition $composition): self
    {
        if (!$this->compositions->contains($composition)) {
            $this->compositions[] = $composition;
            $composition->setRusticite($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->contains($composition)) {
            $this->compositions->removeElement($composition);
            // set the owning side to null (unless already changed)
            if ($composition->getRusticite() === $this) {
                $composition->setRusticite(null);
            }
        }

        return $this;
    }
}
