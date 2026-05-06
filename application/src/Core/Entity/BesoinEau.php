<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BesoinEau
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\BesoinEauRepository")
 */
class BesoinEau
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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $valeur;

    /**
     * @var BesoinEauGroupe
     *
     * @ORM\ManyToOne(targetEntity="BesoinEauGroupe", inversedBy="besoinEaux")
     */
    private $besoinEauGroupe;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Core\Entity\Precipitation", mappedBy="besoinEaux", cascade={"persist"})
     */
    private $precipitations;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Core\Entity\Entite", mappedBy="besoinEau", cascade={"persist"})
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Core\Entity\Composition", mappedBy="besoinEau", cascade={"persist"})
     */
    private $compositions;

    public function __construct()
    {
        $this->precipitations = new ArrayCollection();
        $this->entites = new ArrayCollection();
        $this->compositions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom() ? $this->getNom() : '';
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

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getBesoinEauGroupe(): ?BesoinEauGroupe
    {
        return $this->besoinEauGroupe;
    }

    public function setBesoinEauGroupe(?BesoinEauGroupe $besoinEauGroupe): self
    {
        $this->besoinEauGroupe = $besoinEauGroupe;

        return $this;
    }

    /**
     * @return Collection|Precipitation[]
     */
    public function getPrecipitations(): Collection
    {
        return $this->precipitations;
    }

    public function addPrecipitation(Precipitation $precipitation): self
    {
        if (!$this->precipitations->contains($precipitation)) {
            $this->precipitations[] = $precipitation;
            $precipitation->addBesoinEaux($this);
        }

        return $this;
    }

    public function removePrecipitation(Precipitation $precipitation): self
    {
        if ($this->precipitations->contains($precipitation)) {
            $this->precipitations->removeElement($precipitation);
            $precipitation->removeBesoinEaux($this);
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
            $entite->setBesoinEau($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            // set the owning side to null (unless already changed)
            if ($entite->getBesoinEau() === $this) {
                $entite->setBesoinEau(null);
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
            $composition->setBesoinEau($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->contains($composition)) {
            $this->compositions->removeElement($composition);
            // set the owning side to null (unless already changed)
            if ($composition->getBesoinEau() === $this) {
                $composition->setBesoinEau(null);
            }
        }

        return $this;
    }
}
