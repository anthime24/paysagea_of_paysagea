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
 * Offre
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\OffreRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Offre")
 */
class Offre extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\Offre",
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
    private $prix;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $nbPhotoMax;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accesBanquePhotosPublic;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accesCompletPlantesObjets;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $aidePaysagiste;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $conseilsProfessionnel;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $alerteMail;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ClientOffre", mappedBy="offre")
     */
    private $clientOffres;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CodePromo", mappedBy="offres")
     */
    private $codePromos;

    public function __construct()
    {
        $this->clientOffres = new ArrayCollection();
        $this->codePromos = new ArrayCollection();
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNbPhotoMax(): ?int
    {
        return $this->nbPhotoMax;
    }

    public function setNbPhotoMax(int $nbPhotoMax): self
    {
        $this->nbPhotoMax = $nbPhotoMax;

        return $this;
    }

    public function getAccesBanquePhotosPublic(): ?bool
    {
        return $this->accesBanquePhotosPublic;
    }

    public function setAccesBanquePhotosPublic(?bool $accesBanquePhotosPublic): self
    {
        $this->accesBanquePhotosPublic = $accesBanquePhotosPublic;

        return $this;
    }

    public function getAccesCompletPlantesObjets(): ?bool
    {
        return $this->accesCompletPlantesObjets;
    }

    public function setAccesCompletPlantesObjets(?bool $accesCompletPlantesObjets): self
    {
        $this->accesCompletPlantesObjets = $accesCompletPlantesObjets;

        return $this;
    }

    public function getAidePaysagiste(): ?bool
    {
        return $this->aidePaysagiste;
    }

    public function setAidePaysagiste(?bool $aidePaysagiste): self
    {
        $this->aidePaysagiste = $aidePaysagiste;

        return $this;
    }

    public function getConseilsProfessionnel(): ?bool
    {
        return $this->conseilsProfessionnel;
    }

    public function setConseilsProfessionnel(?bool $conseilsProfessionnel): self
    {
        $this->conseilsProfessionnel = $conseilsProfessionnel;

        return $this;
    }

    public function getAlerteMail(): ?bool
    {
        return $this->alerteMail;
    }

    public function setAlerteMail(?bool $alerteMail): self
    {
        $this->alerteMail = $alerteMail;

        return $this;
    }

    /**
     * @return Collection|ClientOffre[]
     */
    public function getClientOffres(): Collection
    {
        return $this->clientOffres;
    }

    public function addClientOffre(ClientOffre $clientOffre): self
    {
        if (!$this->clientOffres->contains($clientOffre)) {
            $this->clientOffres[] = $clientOffre;
            $clientOffre->setOffre($this);
        }

        return $this;
    }

    public function removeClientOffre(ClientOffre $clientOffre): self
    {
        if ($this->clientOffres->contains($clientOffre)) {
            $this->clientOffres->removeElement($clientOffre);
            // set the owning side to null (unless already changed)
            if ($clientOffre->getOffre() === $this) {
                $clientOffre->setOffre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CodePromo[]
     */
    public function getCodePromos(): Collection
    {
        return $this->codePromos;
    }

    public function addCodePromo(CodePromo $codePromo): self
    {
        if (!$this->codePromos->contains($codePromo)) {
            $this->codePromos[] = $codePromo;
            $codePromo->addOffre($this);
        }

        return $this;
    }

    public function removeCodePromo(CodePromo $codePromo): self
    {
        if ($this->codePromos->contains($codePromo)) {
            $this->codePromos->removeElement($codePromo);
            $codePromo->removeOffre($this);
        }

        return $this;
    }
}
