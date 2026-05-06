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
 * Couleur
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CouleurRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Couleur")
 */
class Couleur extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\Couleur",
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
     * @ORM\ManyToMany(targetEntity="Entite", mappedBy="couleurs")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Entite", mappedBy="couleurFleurs")
     */
    private $entiteFleurs;

    public function __construct()
    {
        $this->entites = new ArrayCollection();
        $this->entiteFleurs = new ArrayCollection();
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
            $entite->addCouleur($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            $entite->removeCouleur($this);
        }

        return $this;
    }

    /**
     * @return Collection|Entite[]
     */
    public function getEntiteFleurs(): Collection
    {
        return $this->entiteFleurs;
    }

    public function addEntiteFleur(Entite $entiteFleur): self
    {
        if (!$this->entiteFleurs->contains($entiteFleur)) {
            $this->entiteFleurs[] = $entiteFleur;
            $entiteFleur->addCouleurFleur($this);
        }

        return $this;
    }

    public function removeEntiteFleur(Entite $entiteFleur): self
    {
        if ($this->entiteFleurs->contains($entiteFleur)) {
            $this->entiteFleurs->removeElement($entiteFleur);
            $entiteFleur->removeCouleurFleur($this);
        }

        return $this;
    }
}
