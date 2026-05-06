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
 * BesoinEauGroupe
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\BesoinEauGroupeRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\BesoinEauGroupe")
 */
class BesoinEauGroupe extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\BesoinEauGroupe",
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
     * @ORM\OneToMany(targetEntity="App\Core\Entity\BesoinEau", mappedBy="besoinEauGroupe", cascade={"persist"})
     */
    private $besoinEaux;

    public function __construct()
    {
        $this->besoinEaux = new ArrayCollection();
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
            $besoinEaux->setBesoinEauGroupe($this);
        }

        return $this;
    }

    public function removeBesoinEaux(BesoinEau $besoinEaux): self
    {
        if ($this->besoinEaux->contains($besoinEaux)) {
            $this->besoinEaux->removeElement($besoinEaux);
            // set the owning side to null (unless already changed)
            if ($besoinEaux->getBesoinEauGroupe() === $this) {
                $besoinEaux->setBesoinEauGroupe(null);
            }
        }

        return $this;
    }
}
