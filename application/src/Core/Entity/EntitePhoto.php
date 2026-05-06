<?php

namespace App\Core\Entity;

use App\Core\Utility\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * EntitePhoto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\EntitePhotoRepository")
 */
class EntitePhoto
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
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $poids;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $largeur;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $hauteur;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $hauteurEntite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $principale;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $diametreEntite;

    /**
     * @var CompositionVue
     *
     * @ORM\ManyToOne(targetEntity="CompositionVue", inversedBy="entitePhotos")
     */
    private $compositionVue;

    /**
     * @var Entite
     *
     * @ORM\ManyToOne(targetEntity="Entite", inversedBy="entitePhotos")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $entite;

    /**
     * @var Annee
     *
     * @ORM\ManyToOne(targetEntity="Annee", inversedBy="entitePhotos")
     */
    private $annee;

    /**
     * @var EntitePhotoEtat
     *
     * @ORM\ManyToOne(targetEntity="EntitePhotoEtat", inversedBy="entitePhotos")
     */
    private $entitePhotoEtat;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CreationEntite", mappedBy="entitePhoto")
     */
    private $creationEntites;

    /**
     * @var UploadedFile
     */
    private $file;

    public function __construct()
    {
        $this->creationEntites = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getLargeur(): ?string
    {
        return $this->largeur;
    }

    public function setLargeur(?string $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getHauteur(): ?string
    {
        return $this->hauteur;
    }

    public function setHauteur(?string $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getHauteurEntite(): ?string
    {
        return $this->hauteurEntite;
    }

    public function setHauteurEntite(?string $hauteurEntite): self
    {
        $this->hauteurEntite = $hauteurEntite;

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

    public function getPrincipale(): ?bool
    {
        return $this->principale;
    }

    public function setPrincipale(?bool $principale): self
    {
        $this->principale = $principale;

        return $this;
    }

    public function getDiametreEntite(): ?string
    {
        return $this->diametreEntite;
    }

    public function setDiametreEntite(?string $diametreEntite): self
    {
        $this->diametreEntite = $diametreEntite;

        return $this;
    }

    public function getCompositionVue(): ?CompositionVue
    {
        return $this->compositionVue;
    }

    public function setCompositionVue(?CompositionVue $compositionVue): self
    {
        $this->compositionVue = $compositionVue;

        return $this;
    }

    public function getEntite(): ?Entite
    {
        return $this->entite;
    }

    public function setEntite(?Entite $entite): self
    {
        $this->entite = $entite;

        return $this;
    }

    public function getAnnee(): ?Annee
    {
        return $this->annee;
    }

    public function setAnnee(?Annee $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getEntitePhotoEtat(): ?EntitePhotoEtat
    {
        return $this->entitePhotoEtat;
    }

    public function setEntitePhotoEtat(?EntitePhotoEtat $entitePhotoEtat): self
    {
        $this->entitePhotoEtat = $entitePhotoEtat;

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
            $creationEntite->setEntitePhoto($this);
        }

        return $this;
    }

    public function removeCreationEntite(CreationEntite $creationEntite): self
    {
        if ($this->creationEntites->contains($creationEntite)) {
            $this->creationEntites->removeElement($creationEntite);
            // set the owning side to null (unless already changed)
            if ($creationEntite->getEntitePhoto() === $this) {
                $creationEntite->setEntitePhoto(null);
            }
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
        return '/uploads/entite';
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
        $nomfileSlug = Slug::slug($this->getNom());
        //dump($this->file);
        //exit;
        $this->setPhoto(time() . '-' . $nomfileSlug . '.' . $this->getFile()->guessExtension());

        $this->setType($this->getFile()->getClientOriginalExtension());
        $this->setPoids($this->getFile()->getSize());

        if (!$this->getId()) {
            $this->file->move($this->getTmpUploadRootDir(), $this->getPhoto());
            $imageSize = getimagesize($this->getTmpAbsolutePath());
        } else {
            $this->file->move($this->getUploadRootDir(), $this->getPhoto());
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

        if (!is_null($this->getId()) && file_exists($this->getUploadRootDir())) {
            rmdir($this->getUploadRootDir());
        }
    }

    public function testMkdirUpload()
    {
        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0777, true);
        }
    }

    public function cloneFile($object)
    {
        if (file_exists($object->getAbsolutePath())) {
            if (!is_dir($this->getTmpUploadRootDir())) {
                mkdir($this->getTmpUploadRootDir(), 0777, true);
            }

            copy($object->getAbsolutePath(), $this->getTmpAbsolutePath());
            $this->setFile(new UploadedFile($this->getTmpAbsolutePath(), $object->getPhoto(), null, null, null, true));
            $this->preUpload();
        }
    }
}
