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
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\EntiteSousTypeRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\EntiteSousType")
 */
class EntiteSousType extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\EntiteSousType",
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
     * @ORM\OneToMany(targetEntity="Entite", mappedBy="entiteSousType")
     */
    private $entites;

    /**
     * @var EntiteType
     *
     * @ORM\ManyToOne(targetEntity="EntiteType", inversedBy="entiteSousTypes")
     */
    private $entiteType;

    public function __construct()
    {
        $this->entites = new ArrayCollection();
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
            $entite->setEntiteSousType($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            // set the owning side to null (unless already changed)
            if ($entite->getEntiteSousType() === $this) {
                $entite->setEntiteSousType(null);
            }
        }

        return $this;
    }

    public function getEntiteType(): ?EntiteType
    {
        return $this->entiteType;
    }

    public function setEntiteType(?EntiteType $entiteType): self
    {
        $this->entiteType = $entiteType;

        return $this;
    }
}
