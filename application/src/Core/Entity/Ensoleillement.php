<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;

/**
 * Ensoleillement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\EnsoleillementRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Ensoleillement")
 */
class Ensoleillement extends AbstractPersonalTranslatable implements TranslatableInterface
{
    use PersonalTranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Core\Entity\Translation\Ensoleillement",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @Gedmo\Translatable
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
    private $unite;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="ensoleillement")
     */
    private $projets;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Creation", mappedBy="ensoleillement")
     */
    private $creations;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Entite", mappedBy="ensoleillements")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Composition", mappedBy="ensoleillements")
     */
    private $compositions;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->creations = new ArrayCollection();
        $this->entites = new ArrayCollection();
        $this->compositions = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
            $projet->setEnsoleillement($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getEnsoleillement() === $this) {
                $projet->setEnsoleillement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Creation[]
     */
    public function getCreations(): Collection
    {
        return $this->creations;
    }

    public function addCreation(Creation $creation): self
    {
        if (!$this->creations->contains($creation)) {
            $this->creations[] = $creation;
            $creation->setEnsoleillement($this);
        }

        return $this;
    }

    public function removeCreation(Creation $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            // set the owning side to null (unless already changed)
            if ($creation->getEnsoleillement() === $this) {
                $creation->setEnsoleillement(null);
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
            $entite->addEnsoleillement($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            $entite->removeEnsoleillement($this);
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
            $composition->addEnsoleillement($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->contains($composition)) {
            $this->compositions->removeElement($composition);
            $composition->removeEnsoleillement($this);
        }

        return $this;
    }
}
