<?php

namespace App\Core\Entity;

use App\Core\Utility\Image;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CreationEntite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CreationEntiteRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CreationEntite
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
    private $photo;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $coordonneeX;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $coordonneeY;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $largeur;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $hauteur;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $tailleFixe = true;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $symetrie = false;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $zindex;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $visibilite = true;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $largeurPhoto;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteurPhoto;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typePhoto;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default" : 1})
     */
    private $versionPhoto = 1;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $rotation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $transformation;

    /**
     * @var EntitePhoto
     *
     * @ORM\ManyToOne(targetEntity="EntitePhoto", inversedBy="creationEntites")
     */
    private $entitePhoto;

    /**
     * @var Composition
     *
     * @ORM\ManyToOne(targetEntity="Composition", inversedBy="creationEntites")
     */
    private $composition;

    /**
     * @var CompositionVue
     *
     * @ORM\ManyToOne(targetEntity="CompositionVue", inversedBy="creationEntites")
     */
    private $compositionVue;

    /**
     * @var Creation
     *
     * @ORM\ManyToOne(targetEntity="Creation", inversedBy="creationEntites")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $creation;

    /**
     * @var Entite
     *
     * @ORM\ManyToOne(targetEntity="Entite", inversedBy="creationEntites")
     */
    private $entite;

    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $lasso = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCoordonneeX(): ?float
    {
        return $this->coordonneeX;
    }

    public function setCoordonneeX(float $coordonneeX): self
    {
        $this->coordonneeX = $coordonneeX;

        return $this;
    }

    public function getCoordonneeY(): ?float
    {
        return $this->coordonneeY;
    }

    public function setCoordonneeY(float $coordonneeY): self
    {
        $this->coordonneeY = $coordonneeY;

        return $this;
    }

    public function getLargeur(): ?float
    {
        return $this->largeur;
    }

    public function setLargeur(float $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getHauteur(): ?float
    {
        return $this->hauteur;
    }

    public function setHauteur(float $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getTailleFixe(): ?bool
    {
        return $this->tailleFixe;
    }

    public function setTailleFixe(?bool $tailleFixe): self
    {
        $this->tailleFixe = $tailleFixe;

        return $this;
    }

    public function getSymetrie(): ?bool
    {
        return $this->symetrie;
    }

    public function setSymetrie(?bool $symetrie): self
    {
        $this->symetrie = $symetrie;

        return $this;
    }

    public function getZindex(): ?int
    {
        return $this->zindex;
    }

    public function setZindex(int $zindex): self
    {
        $this->zindex = $zindex;

        return $this;
    }

    public function getVisibilite(): ?bool
    {
        return $this->visibilite;
    }

    public function setVisibilite(?bool $visibilite): self
    {
        $this->visibilite = $visibilite;

        return $this;
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getLargeurPhoto(): ?int
    {
        return $this->largeurPhoto;
    }

    public function setLargeurPhoto(?int $largeurPhoto): self
    {
        $this->largeurPhoto = $largeurPhoto;

        return $this;
    }

    public function getHauteurPhoto(): ?int
    {
        return $this->hauteurPhoto;
    }

    public function setHauteurPhoto(?int $hauteurPhoto): self
    {
        $this->hauteurPhoto = $hauteurPhoto;

        return $this;
    }

    public function getTypePhoto(): ?string
    {
        return $this->typePhoto;
    }

    public function setTypePhoto(?string $typePhoto): self
    {
        $this->typePhoto = $typePhoto;

        return $this;
    }

    public function getVersionPhoto(): ?int
    {
        return $this->versionPhoto;
    }

    public function setVersionPhoto(int $versionPhoto): self
    {
        $this->versionPhoto = $versionPhoto;

        return $this;
    }

    public function getRotation(): ?float
    {
        return $this->rotation;
    }

    public function setRotation(float $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getTransformation(): ?string
    {
        return $this->transformation;
    }

    public function setTransformation(?string $transformation): self
    {
        $this->transformation = $transformation;

        return $this;
    }

    public function getEntitePhoto(): ?EntitePhoto
    {
        return $this->entitePhoto;
    }

    public function setEntitePhoto(?EntitePhoto $entitePhoto): self
    {
        $this->entitePhoto = $entitePhoto;

        return $this;
    }

    public function getComposition(): ?Composition
    {
        return $this->composition;
    }

    public function setComposition(?Composition $composition): self
    {
        $this->composition = $composition;

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

    public function getCreation(): ?Creation
    {
        return $this->creation;
    }

    public function setCreation(?Creation $creation): self
    {
        $this->creation = $creation;

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

    private function dirIsEmpty($dir): bool
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    public function getAbsolutePath()
    {
        return null === $this->getPhoto() ? null : $this->getUploadRootDir() . '/' . $this->getVersionPhoto(
            ) . '-' . $this->getPhoto();
    }

    public function getTmpAbsolutePath()
    {
        return null === $this->getPhoto() ? null : $this->getTmpUploadRootDir() . '/' . $this->getVersionPhoto(
            ) . '-' . $this->getPhoto();
    }

    public function getWebPath()
    {
        return null === $this->getPhoto() ? null : $this->getUploadDir() . '/' . $this->getArchitecture(
            ) . '/' . $this->getVersionPhoto() . '-' . $this->getPhoto();
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getUploadDir() . '/' . $this->getArchitecture();
    }

    protected function getTmpUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getTmpUploadDir();
    }

    protected function getArchitecture()
    {
        $tabNomDossier = str_split($this->getId());
        $path = implode('/', $tabNomDossier);

        return $path . '/images';
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return '/uploads/creation_entite';
    }

    protected function getTmpUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return $this->getUploadDir() . '/tmp';
    }

    /**
     * @ORM\PostPersist
     */
    public function moveUpload()
    {
        if (null === $this->getPhoto() || null === $this->getEntite()) {
            return;
        }

        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0755, true);
        }

        if (file_exists($this->getEntitePhoto()->getAbsolutePath())) {
            copy($this->getEntitePhoto()->getAbsolutePath(), $this->getAbsolutePath());
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function removeUpload()
    {
        if ($this->getPhoto() != null) {
            for ($i = $this->getVersionPhoto(); $i > 0; $i--) {
                $path = $this->getUploadRootDir() . '/' . $i . '-' . $this->getPhoto();
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        if (file_exists($this->getUploadRootDir()) && $this->dirIsEmpty($this->getUploadRootDir())) {
            rmdir($this->getUploadRootDir());
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function initialiserPhoto()
    {
        $this->setCreated(new DateTime());
        $this->setUpdated(new DateTime());
        if ($this->getEntite() != null) {
            if ($this->getComposition()) {
                $this->setEntitePhoto($this->getEntite()->getCompositionPhoto($this->getCompositionVue()->getId()));
            } else {
                $this->setEntitePhoto($this->getEntite()->getPhotoPrincipale());
            }
            $this->setPhoto($this->getEntitePhoto()->getPhoto());
            $this->setLargeurPhoto($this->getEntitePhoto()->getLargeur());
            $this->setHauteurPhoto($this->getEntitePhoto()->getHauteur());
            $this->setTypePhoto($this->getEntitePhoto()->getType());
            $this->setVersionPhoto(1);
        }
    }

    public function saveNewVersionImage($dataUrl)
    {
        if (empty($dataUrl)) {
            return false;
        }

        // extrait les données base64
        $parts = explode(',', $dataUrl);

        if (empty($parts[1])) {
            return false;
        }

        // Décode les données Base64
        $data = base64_decode($parts[1]);

        $previousVersion = $this->getVersionPhoto();
        $version = $previousVersion + 1;

        $path = $this->getUploadRootDir() . '/' . $version . '-' . $this->getPhoto();
        if (($fp = fopen($path, 'w+')) !== false) {
            fwrite($fp, $data);
            fclose($fp);

            $this->setVersionPhoto($version);
        }
    }

    private function imageFlip($img, $type = '')
    {
        $width = imagesx($img);
        $height = imagesy($img);
        $dest = imagecreatetruecolor($width, $height);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        switch ($type) {
            case 'v':
                for ($i = 0; $i < $height; $i++) {
                    imagecopy($dest, $img, 0, ($height - $i - 1), 0, $i, $width, 1);
                }
                break;
            case 'h':
                for ($i = 0; $i < $width; $i++) {
                    imagecopy($dest, $img, ($width - $i - 1), 0, $i, 0, 1, $height);
                }
                break;
            default :
                return $img;
                break;
        }

        return $dest;
    }

    public function appliquerSymetrieVerticale()
    {
        $img = Image::imageCreateFromAny($this->getAbsolutePath());

        if ($img == null) {
            return false;
        }

        $symetrie = 'h';
        $img = $this->imageFlip($img, $symetrie);
        // Désactivation de l'alphablending
        imagealphablending($img, false);
        // Sauvegarde des données alpha pour conserver la transparence
        imagesavealpha($img, true);

        $previousVersion = $this->getVersionPhoto();
        $version = $previousVersion + 1;

        $path = $this->getUploadRootDir() . '/' . $version . '-' . $this->getPhoto();

        // on crée le fichier en png
        imagepng($img, $path);
        // destruction de l'image gd
        imagedestroy($img);

        $this->setVersionPhoto($version);
    }

    public function getLasso(): ?bool
    {
        return $this->lasso;
    }

    public function setLasso(?bool $lasso): self
    {
        $this->lasso = $lasso;

        return $this;
    }
}
