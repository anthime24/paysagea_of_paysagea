<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;

/**
 * EntiteType
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\EntiteTypeRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\EntiteType")
 */
class EntiteType extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\EntiteType",
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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="EntiteSousType", mappedBy="entiteType")
     */
    private $entiteSousTypes;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Marque", mappedBy="entiteTypes")
     */
    private $marques;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Entite", mappedBy="entiteType")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Categorie", mappedBy="entiteType")
     */
    private $categories;

    public function __construct()
    {
        $this->entiteSousTypes = new ArrayCollection();
        $this->marques = new ArrayCollection();
        $this->entites = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

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

    /**
     * @return Collection|EntiteSousType[]
     */
    public function getEntiteSousTypes(): Collection
    {
        return $this->entiteSousTypes;
    }

    public function addEntiteSousType(EntiteSousType $entiteSousType): self
    {
        if (!$this->entiteSousTypes->contains($entiteSousType)) {
            $this->entiteSousTypes[] = $entiteSousType;
            $entiteSousType->setEntiteType($this);
        }

        return $this;
    }

    public function removeEntiteSousType(EntiteSousType $entiteSousType): self
    {
        if ($this->entiteSousTypes->contains($entiteSousType)) {
            $this->entiteSousTypes->removeElement($entiteSousType);
            // set the owning side to null (unless already changed)
            if ($entiteSousType->getEntiteType() === $this) {
                $entiteSousType->setEntiteType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Marque[]
     */
    public function getMarques(): Collection
    {
        return $this->marques;
    }

    public function addMarque(Marque $marque): self
    {
        if (!$this->marques->contains($marque)) {
            $this->marques[] = $marque;
            $marque->addEntiteType($this);
        }

        return $this;
    }

    public function removeMarque(Marque $marque): self
    {
        if ($this->marques->contains($marque)) {
            $this->marques->removeElement($marque);
            $marque->removeEntiteType($this);
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
            $entite->setEntiteType($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            // set the owning side to null (unless already changed)
            if ($entite->getEntiteType() === $this) {
                $entite->setEntiteType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setEntiteType($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getEntiteType() === $this) {
                $category->setEntiteType(null);
            }
        }

        return $this;
    }
}
