<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CompositionVue
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CompositionVueRepository")
 */
class CompositionVue
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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="EntitePhoto", mappedBy="compositionVue")
     */
    private $entitePhotos;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CreationEntite", mappedBy="compositionVue")
     */
    private $creationEntites;

    public function __construct()
    {
        $this->entitePhotos = new ArrayCollection();
        $this->creationEntites = new ArrayCollection();
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
            $entitePhoto->setCompositionVue($this);
        }

        return $this;
    }

    public function removeEntitePhoto(EntitePhoto $entitePhoto): self
    {
        if ($this->entitePhotos->contains($entitePhoto)) {
            $this->entitePhotos->removeElement($entitePhoto);
            // set the owning side to null (unless already changed)
            if ($entitePhoto->getCompositionVue() === $this) {
                $entitePhoto->setCompositionVue(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CreationEntite[]
     */
    public function getCreationEntites(): Collection
    {
        return $this->creationEntites;
    }

    public function addCreationEntite(CreationEntite $creationEntite): self
    {
        if (!$this->creationEntites->contains($creationEntite)) {
            $this->creationEntites[] = $creationEntite;
            $creationEntite->setCompositionVue($this);
        }

        return $this;
    }

    public function removeCreationEntite(CreationEntite $creationEntite): self
    {
        if ($this->creationEntites->contains($creationEntite)) {
            $this->creationEntites->removeElement($creationEntite);
            // set the owning side to null (unless already changed)
            if ($creationEntite->getCompositionVue() === $this) {
                $creationEntite->setCompositionVue(null);
            }
        }

        return $this;
    }
}
