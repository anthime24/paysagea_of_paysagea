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
 * TypeSol
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\TypeSolRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\TypeSol")
 */
class TypeSol extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\TypeSol",
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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Ph", mappedBy="typeSol")
     */
    private $phs;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Entite", mappedBy="typeSols")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Composition", mappedBy="typeSols")
     */
    private $compositions;

    public function __construct()
    {
        $this->phs = new ArrayCollection();
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

    /**
     * @return Collection|Ph[]
     */
    public function getPhs(): Collection
    {
        return $this->phs;
    }

    public function addPh(Ph $ph): self
    {
        if (!$this->phs->contains($ph)) {
            $this->phs[] = $ph;
            $ph->setTypeSol($this);
        }

        return $this;
    }

    public function removePh(Ph $ph): self
    {
        if ($this->phs->contains($ph)) {
            $this->phs->removeElement($ph);
            // set the owning side to null (unless already changed)
            if ($ph->getTypeSol() === $this) {
                $ph->setTypeSol(null);
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
            $entite->addTypeSol($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            $entite->removeTypeSol($this);
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
            $composition->addTypeSol($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->contains($composition)) {
            $this->compositions->removeElement($composition);
            $composition->removeTypeSol($this);
        }

        return $this;
    }
}
