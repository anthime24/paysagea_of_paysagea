<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\StyleRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Style")
 */
class Style extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\Style",
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
    private $photo;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $largeur;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $poids;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="style")
     */
    private $projets;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Creation", mappedBy="style")
     */
    private $creations;

    /**
     * @var Collection
     *
     *
     * @ORM\ManyToMany(targetEntity="Entite", mappedBy="styles")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Composition", mappedBy="styles")
     */
    private $compositions;

    /**
     * @var UploadedFile
     */
    private $file;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getLargeur(): ?int
    {
        return $this->largeur;
    }

    public function setLargeur(?int $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getHauteur(): ?int
    {
        return $this->hauteur;
    }

    public function setHauteur(?int $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): self
    {
        $this->poids = $poids;

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
            $projet->setStyle($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getStyle() === $this) {
                $projet->setStyle(null);
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
            $creation->setStyle($this);
        }

        return $this;
    }

    public function removeCreation(Creation $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            // set the owning side to null (unless already changed)
            if ($creation->getStyle() === $this) {
                $creation->setStyle(null);
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
            $entite->addStyle($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            $entite->removeStyle($this);
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
            $composition->addStyle($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->contains($composition)) {
            $this->compositions->removeElement($composition);
            $composition->removeStyle($this);
        }

        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(?UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->getPhoto() ? null : $this->getUploadRootDir() . '/' . $this->getPhoto();
    }

    public function getTmpAbsolutePath()
    {
        return null === $this->getPhoto() ? null : $this->getTmpUploadRootDir() . '/' . $this->getPhoto();
    }

    public function getWebPath()
    {
        return null === $this->getPhoto() ? null : $this->getUploadDir() . '/' . $this->getId() . '/' . $this->getPhoto(
            );
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getUploadDir() . '/' . $this->getId();
    }

    protected function getTmpUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getTmpUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return '/uploads/style';
    }

    protected function getTmpUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return $this->getUploadDir() . '/tmp';
    }

    public function preUpload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        $this->removeUpload();

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues
        $this->setPhoto(time() . '-' . $this->getId() . '.' . $this->getFile()->guessExtension());

        $this->setType($this->getFile()->getClientOriginalExtension());
        $this->setPoids($this->getFile()->getSize());

        if (!$this->id) {
            $this->getFile()->move($this->getTmpUploadRootDir(), $this->getPhoto());
            $imageSize = getimagesize($this->getTmpAbsolutePath());
        } else {
            $this->getFile()->move($this->getUploadRootDir(), $this->getPhoto());
            $imageSize = getimagesize($this->getAbsolutePath());
        }

        if (!empty($imageSize)) {
            $this->setLargeur($imageSize[0]);
            $this->setHauteur($imageSize[1]);
        }

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    public function moveUpload()
    {
        if (null === $this->getPhoto()) {
            return;
        }

        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0777, true);
        }

        if (file_exists($this->getTmpAbsolutePath())) {
            copy($this->getTmpAbsolutePath(), $this->getAbsolutePath());
            unlink($this->getTmpAbsolutePath());
        }
    }

    public function removeUpload()
    {
        if ($this->getPhoto() && file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }

        if (file_exists($this->getUploadRootDir())) {
            rmdir($this->getUploadRootDir());
        }
    }

    public function testMkdirUpload()
    {
        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0777, true);
        }
    }
}
