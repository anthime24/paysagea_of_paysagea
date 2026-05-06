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
 * EntitePhotoEtat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\EntitePhotoEtatRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\EntitePhotoEtat")
 */
class EntitePhotoEtat extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\EntitePhotoEtat",
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
     * @ORM\OneToMany(targetEntity="EntitePhoto", mappedBy="entitePhotoEtat")
     */
    private $entitePhotos;

    public function __construct()
    {
        $this->entitePhotos = new ArrayCollection();
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
     * @return Collection|EntitePhoto[]
     */
    public function getEntitePhotos(): Collection
    {
        return $this->entitePhotos;
    }

    public function addEntitePhoto(EntitePhoto $entitePhoto): self
    {
        if (!$this->entitePhotos->contains($entitePhoto)) {
            $this->entitePhotos[] = $entitePhoto;
            $entitePhoto->setEntitePhotoEtat($this);
        }

        return $this;
    }

    public function removeEntitePhoto(EntitePhoto $entitePhoto): self
    {
        if ($this->entitePhotos->contains($entitePhoto)) {
            $this->entitePhotos->removeElement($entitePhoto);
            // set the owning side to null (unless already changed)
            if ($entitePhoto->getEntitePhotoEtat() === $this) {
                $entitePhoto->setEntitePhotoEtat(null);
            }
        }

        return $this;
    }
}
