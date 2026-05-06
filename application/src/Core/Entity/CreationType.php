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
 * CreationType
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CreationTypeRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\CreationType")
 */
class CreationType extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\CreationType",
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
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="projetType")
     */
    private $projets;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Creation", mappedBy="creationType")
     */
    private $creations;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="BanquePhoto", mappedBy="banquePhotoType")
     */
    private $banquePhotos;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Entite", mappedBy="creationTypes")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Composition", mappedBy="creationTypes")
     */
    private $compositions;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->creations = new ArrayCollection();
        $this->banquePhotos = new ArrayCollection();
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
            $projet->setProjetType($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getProjetType() === $this) {
                $projet->setProjetType(null);
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
            $creation->setCreationType($this);
        }

        return $this;
    }

    public function removeCreation(Creation $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            // set the owning side to null (unless already changed)
            if ($creation->getCreationType() === $this) {
                $creation->setCreationType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BanquePhoto[]
     */
    public function getBanquePhotos(): Collection
    {
        return $this->banquePhotos;
    }

    public function addBanquePhoto(BanquePhoto $banquePhoto): self
    {
        if (!$this->banquePhotos->contains($banquePhoto)) {
            $this->banquePhotos[] = $banquePhoto;
            $banquePhoto->setBanquePhotoType($this);
        }

        return $this;
    }

    public function removeBanquePhoto(BanquePhoto $banquePhoto): self
    {
        if ($this->banquePhotos->contains($banquePhoto)) {
            $this->banquePhotos->removeElement($banquePhoto);
            // set the owning side to null (unless already changed)
            if ($banquePhoto->getBanquePhotoType() === $this) {
                $banquePhoto->setBanquePhotoType(null);
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
            $entite->addCreationType($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            $entite->removeCreationType($this);
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
            $composition->addCreationType($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->contains($composition)) {
            $this->compositions->removeElement($composition);
            $composition->removeCreationType($this);
        }

        return $this;
    }
}
