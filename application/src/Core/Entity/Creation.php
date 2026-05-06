<?php

namespace App\Core\Entity;

use App\Core\Utility\Image;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

/**
 * Creation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CreationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Creation
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
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $photoPoids;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $photoLargeur;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $photoHauteur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoType;

    /**
     * @var float
     *
     * @ORM\Column(name="repere1_x", type="float", nullable=true)
     */
    private $repere1X;

    /**
     * @var float
     *
     * @ORM\Column(name="repere1_y", type="float", nullable=true)
     */
    private $repere1Y;

    /**
     * @var float
     *
     * @ORM\Column(name="repere1_largeur", type="float", nullable=true)
     */
    private $repere1Largeur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere1_hauteur", type="float", nullable=true)
     */
    private $repere1Hauteur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere2_x", type="float", nullable=true)
     */
    private $repere2X;

    /**
     * @var float
     *
     * @ORM\Column(name="repere2_y", type="float", nullable=true)
     */
    private $repere2Y;

    /**
     * @var float
     *
     * @ORM\Column(name="repere2_largeur", type="float", nullable=true)
     */
    private $repere2Largeur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere2_hauteur", type="float", nullable=true)
     */
    private $repere2Hauteur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere3_x", type="float", nullable=true)
     */
    private $repere3X;

    /**
     * @var float
     *
     * @ORM\Column(name="repere3_y", type="float", nullable=true)
     */
    private $repere3Y;

    /**
     * @var float
     *
     * @ORM\Column(name="repere3_largeur", type="float", nullable=true)
     */
    private $repere3Largeur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere3_hauteur", type="float", nullable=true)
     */
    private $repere3Hauteur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere4_x", type="float", nullable=true)
     */
    private $repere4X;

    /**
     * @var float
     *
     * @ORM\Column(name="repere4_y", type="float", nullable=true)
     */
    private $repere4Y;

    /**
     * @var float
     *
     * @ORM\Column(name="repere4_largeur", type="float", nullable=true)
     */
    private $repere4Largeur;

    /**
     * @var float
     *
     * @ORM\Column(name="repere4_hauteur", type="float", nullable=true)
     */
    private $repere4Hauteur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referenceLecture;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referenceEcriture;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $surface;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmer;

    /**
     * @var float
     *
     * @ORM\Column(type="integer", options={"default" : 1})
     */
    private $versionImage = 1;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $enregistree = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $pdfGenere = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $provenance;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $duplicateAdmin = false;

    /**
     * @var Projet
     *
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="creations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $projet;

    /**
     * @var Style
     *
     * @ORM\ManyToOne(targetEntity="Style", inversedBy="creations")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $style;

    /**
     * @var Ensoleillement
     *
     * @ORM\ManyToOne(targetEntity="Ensoleillement", inversedBy="creations")
     */
    private $ensoleillement;

    /**
     * @var CreationType
     *
     * @ORM\ManyToOne(targetEntity="CreationType", inversedBy="creations")
     */
    private $creationType;

    /**
     * @var BanquePhoto
     *
     * @ORM\ManyToOne(targetEntity="BanquePhoto", inversedBy="creations")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $banquePhoto;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CreationEntite", mappedBy="creation")
     */
    private $creationEntites;

    private $inDuplication = false;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $screenShotHtmlContent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->referenceEcriture = hash('sha512', mt_rand() . microtime() . '_EcrItuRe!');
        $this->referenceLecture = hash('sha512', mt_rand() . microtime() . '_LECture!');
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

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(?DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhotoPoids(): ?float
    {
        return $this->photoPoids;
    }

    public function setPhotoPoids(?float $photoPoids): self
    {
        $this->photoPoids = $photoPoids;

        return $this;
    }

    public function getPhotoLargeur(): ?int
    {
        return $this->photoLargeur;
    }

    public function setPhotoLargeur(?int $photoLargeur): self
    {
        $this->photoLargeur = $photoLargeur;

        return $this;
    }

    public function getPhotoHauteur(): ?int
    {
        return $this->photoHauteur;
    }

    public function setPhotoHauteur(?int $photoHauteur): self
    {
        $this->photoHauteur = $photoHauteur;

        return $this;
    }

    public function getPhotoType(): ?string
    {
        return $this->photoType;
    }

    public function setPhotoType(?string $photoType): self
    {
        $this->photoType = $photoType;

        return $this;
    }

    public function getRepere1X(): ?float
    {
        return $this->repere1X;
    }

    public function setRepere1X(?float $repere1X): self
    {
        $this->repere1X = $repere1X;

        return $this;
    }

    public function getRepere1Y(): ?float
    {
        return $this->repere1Y;
    }

    public function setRepere1Y(?float $repere1Y): self
    {
        $this->repere1Y = $repere1Y;

        return $this;
    }

    public function getRepere1Largeur(): ?float
    {
        return $this->repere1Largeur;
    }

    public function setRepere1Largeur(?float $repere1Largeur): self
    {
        $this->repere1Largeur = $repere1Largeur;

        return $this;
    }

    public function getRepere1Hauteur(): ?float
    {
        return $this->repere1Hauteur;
    }

    public function setRepere1Hauteur(?float $repere1Hauteur): self
    {
        $this->repere1Hauteur = $repere1Hauteur;

        return $this;
    }

    public function getRepere2X(): ?float
    {
        return $this->repere2X;
    }

    public function setRepere2X(?float $repere2X): self
    {
        $this->repere2X = $repere2X;

        return $this;
    }

    public function getRepere2Y(): ?float
    {
        return $this->repere2Y;
    }

    public function setRepere2Y(?float $repere2Y): self
    {
        $this->repere2Y = $repere2Y;

        return $this;
    }

    public function getRepere2Largeur(): ?float
    {
        return $this->repere2Largeur;
    }

    public function setRepere2Largeur(?float $repere2Largeur): self
    {
        $this->repere2Largeur = $repere2Largeur;

        return $this;
    }

    public function getRepere2Hauteur(): ?float
    {
        return $this->repere2Hauteur;
    }

    public function setRepere2Hauteur(?float $repere2Hauteur): self
    {
        $this->repere2Hauteur = $repere2Hauteur;

        return $this;
    }

    public function getRepere3X(): ?float
    {
        return $this->repere3X;
    }

    public function setRepere3X(?float $repere3X): self
    {
        $this->repere3X = $repere3X;

        return $this;
    }

    public function getRepere3Y(): ?float
    {
        return $this->repere3Y;
    }

    public function setRepere3Y(?float $repere3Y): self
    {
        $this->repere3Y = $repere3Y;

        return $this;
    }

    public function getRepere3Largeur(): ?float
    {
        return $this->repere3Largeur;
    }

    public function setRepere3Largeur(?float $repere3Largeur): self
    {
        $this->repere3Largeur = $repere3Largeur;

        return $this;
    }

    public function getRepere3Hauteur(): ?float
    {
        return $this->repere3Hauteur;
    }

    public function setRepere3Hauteur(?float $repere3Hauteur): self
    {
        $this->repere3Hauteur = $repere3Hauteur;

        return $this;
    }

    public function getRepere4X(): ?float
    {
        return $this->repere4X;
    }

    public function setRepere4X(?float $repere4X): self
    {
        $this->repere4X = $repere4X;

        return $this;
    }

    public function getRepere4Y(): ?float
    {
        return $this->repere4Y;
    }

    public function setRepere4Y(?float $repere4Y): self
    {
        $this->repere4Y = $repere4Y;

        return $this;
    }

    public function getRepere4Largeur(): ?float
    {
        return $this->repere4Largeur;
    }

    public function setRepere4Largeur(?float $repere4Largeur): self
    {
        $this->repere4Largeur = $repere4Largeur;

        return $this;
    }

    public function getRepere4Hauteur(): ?float
    {
        return $this->repere4Hauteur;
    }

    public function setRepere4Hauteur(?float $repere4Hauteur): self
    {
        $this->repere4Hauteur = $repere4Hauteur;

        return $this;
    }

    public function getReferenceLecture(): ?string
    {
        return $this->referenceLecture;
    }

    public function setReferenceLecture(?string $referenceLecture): self
    {
        $this->referenceLecture = $referenceLecture;

        return $this;
    }

    public function getReferenceEcriture(): ?string
    {
        return $this->referenceEcriture;
    }

    public function setReferenceEcriture(?string $referenceEcriture): self
    {
        $this->referenceEcriture = $referenceEcriture;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(?float $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getConfirmer(): ?bool
    {
        return $this->confirmer;
    }

    public function setConfirmer(?bool $confirmer): self
    {
        $this->confirmer = $confirmer;

        return $this;
    }

    public function getVersionImage(): ?int
    {
        return $this->versionImage;
    }

    public function setVersionImage(int $versionImage): self
    {
        $this->versionImage = $versionImage;

        return $this;
    }

    public function getEnregistree(): ?bool
    {
        return $this->enregistree;
    }

    public function setEnregistree(bool $enregistree): self
    {
        $this->enregistree = $enregistree;

        return $this;
    }

    public function getPdfGenere(): ?bool
    {
        return $this->pdfGenere;
    }

    public function setPdfGenere(bool $pdfGenere): self
    {
        $this->pdfGenere = $pdfGenere;

        return $this;
    }

    public function getProvenance(): ?string
    {
        return $this->provenance;
    }

    public function setProvenance(?string $provenance): self
    {
        $this->provenance = $provenance;

        return $this;
    }

    public function getDuplicateAdmin(): ?bool
    {
        return $this->duplicateAdmin;
    }

    public function setDuplicateAdmin(bool $duplicateAdmin): self
    {
        $this->duplicateAdmin = $duplicateAdmin;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getStyle(): ?Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getEnsoleillement(): ?Ensoleillement
    {
        return $this->ensoleillement;
    }

    public function setEnsoleillement(?Ensoleillement $ensoleillement): self
    {
        $this->ensoleillement = $ensoleillement;

        return $this;
    }

    public function getCreationType(): ?CreationType
    {
        return $this->creationType;
    }

    public function setCreationType(?CreationType $creationType): self
    {
        $this->creationType = $creationType;

        return $this;
    }

    public function getBanquePhoto(): ?BanquePhoto
    {
        return $this->banquePhoto;
    }

    public function setBanquePhoto(?BanquePhoto $banquePhoto): self
    {
        $this->banquePhoto = $banquePhoto;

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
            $creationEntite->setCreation($this);
        }

        return $this;
    }

    public function removeCreationEntite(CreationEntite $creationEntite): self
    {
        if ($this->creationEntites->contains($creationEntite)) {
            $this->creationEntites->removeElement($creationEntite);
            // set the owning side to null (unless already changed)
            if ($creationEntite->getCreation() === $this) {
                $creationEntite->setCreation(null);
            }
        }

        return $this;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return $this->getBanquePhoto() === null ? null : $this->getUploadRootDir() . '/' . $this->getVersionImage(
            ) . '-' . $this->getBanquePhoto()->getPhoto();
    }

    public function getAbsolutePathResize()
    {
        $path = null;
        if ($this->getBanquePhoto()) {
            $path = $this->getUploadRootDir() . '/resize-' . $this->getBanquePhoto()->getPhoto();

            if (!file_exists($path) || filemtime($path) < filemtime($this->getAbsolutePath())) {
                $imagine = new Imagine();
                $image = $imagine->open($this->getAbsolutePath());
                $image->thumbnail(new Box(1200, 720), ImageInterface::THUMBNAIL_INSET)
                    ->save($path);
            }
        }

        return $path;
    }

    public function getAbsolutePathLogo()
    {
        return __DIR__ . '/../../../public/front/images/logo.png';
    }

    public function getAbsolutePathRenderedImage()
    {
        return $this->getUploadRootDir() . '/' . $this->getVersionImage() . '-rendu-final-creation.jpg';
    }

    public function getAbsolutePathPlanMasseImage()
    {
        return $this->getUploadRootDir() . '/' . $this->getVersionImage() . '-rendu-final-plan-masse.jpg';
    }

    public function getAbsolutePathPdf()
    {
        return $this->getUploadRootDir() . '/informations-creation.pdf';
    }

    public function getWebPath()
    {
        return $this->getBanquePhoto() === null ? null : $this->getUploadDir() . '/' . $this->getArchitecture(
            ) . '/' . $this->getVersionImage() . '-' . $this->getBanquePhoto()->getPhoto();
    }


    public function getWebPathRenderedImage()
    {
        return $this->getUploadDir() . '/' . $this->getArchitecture() . '/' . $this->getVersionImage(
            ) . '-rendu-final-creation.jpg';
    }

    public function getWebPathPlanMasseImage()
    {
        return $this->getUploadDir() . '/' . $this->getArchitecture() . '/' . $this->getVersionImage(
            ) . '-rendu-final-plan-masse.jpg';
    }

    public function getFullPathRenderedImage()
    {
        return $this->getUploadRootDir() .  '/' . $this->getVersionImage() . '-rendu-final-creation.jpg';
    }

    public function getFullPathRenderedPlanMasse()
    {
        return $this->getUploadRootDir() .  '/' . $this->getVersionImage() . '-rendu-final-plan-masse.jpg';
    }

    public function getWebPathPdf()
    {
        return $this->getUploadDir() . '/' . $this->getArchitecture() . '/informations-creation.pdf';
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getUploadDir() . '/' . $this->getArchitecture();
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
        return '/uploads/creation';
    }

    public function moveUpload()
    {
        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0755, true);
        }

        if ($this->getVersionImage() == null) {
            $this->setVersionImage(1);
        }

        if ($this->getBanquePhoto() != null &&
            file_exists($this->getBanquePhoto()->getAbsolutePath()) &&
            !file_exists($this->getAbsolutePath())) {
            copy($this->getBanquePhoto()->getAbsolutePath(), $this->getAbsolutePath());
        }
    }

    public function removeUpload()
    {
        if (file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }

        $path = $this->getUploadRootDir();

        if (file_exists($path) && is_dir($path)) {
            foreach (scandir($path) as $value) {
                if ($value != "." && $value != ".." && is_file($path . DIRECTORY_SEPARATOR . $value)) {
                    unlink($path . DIRECTORY_SEPARATOR . $value);
                }
            }
            rmdir($this->getUploadRootDir());
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

        $previousVersion = $this->getVersionImage();
        $version = $previousVersion + 1;

        $path = $this->getUploadRootDir() . '/' . $version . '-' . $this->getBanquePhoto()->getPhoto();
        if (($fp = fopen($path, 'w+')) !== false) {
            fwrite($fp, $data);
            fclose($fp);

            $this->setVersionImage($version);
        }
    }

    public function getHeightRenderedImage()
    {
        $height = 0;

        if (file_exists($this->getAbsolutePathRenderedImage())) {
            $size = getimagesize($this->getAbsolutePathRenderedImage());
            $height = $size[1];
        }

        return $height;
    }

    public function getWidthRenderedImage()
    {
        $width = 0;

        if (file_exists($this->getAbsolutePathRenderedImage())) {
            $size = getimagesize($this->getAbsolutePathRenderedImage());
            $width = $size[0];
        }

        return $width;
    }

    public function generateRenderedImageFromDataURL($data)
    {
        if ($data == null || $data == '') {
            return false;
        }

        list($type, $data) = explode(';', $data);
        list($o, $data) = explode(',', $data);
        $imageData = base64_decode($data);
        $source = imagecreatefromstring($imageData);
        $imageSave = imagejpeg($source, $this->getAbsolutePathRenderedImage(), 100);

        $imageWidth = imagesx($source);

        // On met le logo
//        $imageLogo = Image::imageCreateFromAny($this->getAbsolutePathLogo());
//        if ($imageLogo) {
//            imagecopy($source, $imageLogo, $imageWidth - imagesx($imageLogo) - 20, 10, 0, 0, imagesx($imageLogo), imagesy($imageLogo));
//            imagedestroy($imageLogo);
//        }

        // On sauvegarde
        imagejpeg($source, $this->getAbsolutePathRenderedImage());
        imagedestroy($source);
    }

    public function generateRenderedImageFromScreenshot($screenshot)
    {
        $screenshotPath =  __DIR__ . '/../../../tmp';
        copy($screenshotPath . DIRECTORY_SEPARATOR . $screenshot, $this->getAbsolutePathRenderedImage());
        unlink($screenshotPath . DIRECTORY_SEPARATOR . $screenshot);
    }

    public function createRenderedImage($cachedImage = null)
    {
        // On crée l'image de base avec l'arrière plan
        $image = Image::imageCreateFromAny($this->getAbsolutePathResize());

        if ($image == null) {
            return false;
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $listEntites = $this->getCreationEntites();
        $listEntitesImage = array();

        // On parcourt les entités, on garde que les visibles et on les
        foreach ($listEntites as $ce) {
            if ($ce->getVisibilite() == 1) {
                $listEntitesImage[$ce->getZindex()]['path'] = $ce->getAbsolutePath();
                $listEntitesImage[$ce->getZindex()]['x'] = $ce->getCoordonneeX();
                $listEntitesImage[$ce->getZindex()]['y'] = $ce->getCoordonneeY();
                $listEntitesImage[$ce->getZindex()]['largeur'] = $ce->getLargeur();
                $listEntitesImage[$ce->getZindex()]['hauteur'] = $ce->getHauteur();
            }
        }

        if (count($listEntitesImage) > 0) {
            ksort($listEntitesImage);
        }

        foreach ($listEntitesImage as $ei) {
            $imageEntite = Image::imageCreateFromAny($ei['path']);

            if ($imageEntite != null) {
                imagecopyresampled(
                    $image,
                    $imageEntite,
                    $ei['x'],
                    $ei['y'],
                    0,
                    0,
                    $ei['largeur'],
                    $ei['hauteur'],
                    imagesx($imageEntite),
                    imagesy($imageEntite)
                );
                imagedestroy($imageEntite);
            }
        }

        // On met le logo
        $imageLogo = Image::imageCreateFromAny($this->getAbsolutePathLogo());
        if ($imageLogo) {
            imagecopy(
                $image,
                $imageLogo,
                $imageWidth - imagesx($imageLogo) - 20,
                10,
                0,
                0,
                imagesx($imageLogo),
                imagesy($imageLogo)
            );
            imagedestroy($imageLogo);
        }

        // On sauvegarde
        imagejpeg($image, $this->getAbsolutePathRenderedImage());
        imagedestroy($image);
    }

    public function createPlanMasseImage($cachedImage = null)
    {
        // On crée l'image de base avec l'arrière plan
        $image = Image::imageCreateFromAny($this->getAbsolutePathResize());

        if ($image == null) {
            return false;
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $circle_width = 30;
        $circle_color = imagecolorallocate($image, 255, 255, 255);
        $font_size = 12;
        $char_decal_x = -4;
        $char_decal_y = -7;
        $black = imagecolorallocate($image, 0, 0, 0);

        // On parcourt les entités
        $listEntites = $this->getCreationEntites();

        $mappingNumeroEntite = array();
        $numero = 1;

        // On parcourt les entités
        foreach ($listEntites as $ce) {
            if ($ce->getVisibilite() == 1) {
                if (!array_key_exists($ce->getEntite()->getId(), $mappingNumeroEntite)) {
                    $mappingNumeroEntite[$ce->getEntite()->getId()] = $numero++;
                }

                $x = $ce->getCoordonneeX() + ($ce->getLargeur() / 2);
                $y = $ce->getCoordonneeY() + $ce->getHauteur();

                if ($x > ($imageWidth - $circle_width)) {
                    $x = $imageWidth - $circle_width;
                } else {
                    if ($x < 0) {
                        $x = 0;
                    }
                }

                if ($y > ($imageHeight - $circle_width)) {
                    $y = $imageHeight - $circle_width;
                } else {
                    if ($y < 0) {
                        $y = 0;
                    }
                }

                imagefilledellipse($image, $x, $y, $circle_width, $circle_width, $circle_color);
                imagestring(
                    $image,
                    $font_size,
                    $x + $char_decal_x - ($mappingNumeroEntite[$ce->getEntite()->getId()] > 9 ? 3 : 0),
                    $y + $char_decal_y,
                    $mappingNumeroEntite[$ce->getEntite()->getId()],
                    $black
                );
            }
        }

        // On sauvegarde
        imagejpeg($image, $this->getAbsolutePathPlanMasseImage());
        imagedestroy($image);
    }
    
    public function createRotation($rotation) {
        $imageOrigine = Image::imageCreateFromAny($this->getAbsolutePath());
        if($imageOrigine == null) {
            return false;
        }
        
        $previousVersion = $this->getVersionImage();
        $version = $previousVersion + 1;
        $previousPath = $this->getAbsolutePath();
        $newPath = $this->getUploadRootDir() . '/' . $version . '-' . $this->getBanquePhoto()->getPhoto();
        
        $source = Image::imageCreateFromAny($previousPath);
        $rotate = imagerotate($source, -$rotation, 0);
        Image::saveImageFromAny($rotate, $previousPath, $newPath);
        
        
        $this->setVersionImage($version);
    }

    public function createCropedImage($x, $y, $width, $height, $originalWidth, $originalHeight)
    {
        //On récupère la taille de l'image brute (non resizé par avalanche)
        $size = getimagesize($this->getAbsolutePath());
        $normalWidth = $size[0];
        $normalHeight = $size[1];

        $originalWidth = intval($originalWidth);
        $originalHeight = intval($originalHeight);

        //On redéfintit les coordonées récupéré pour les adapater à l'image brute
        if ($width != '') {
            $width = ($normalWidth * $width) / $originalWidth;
        } else {
            $width = $normalWidth;
        }
        if ($height != '') {
            $height = ($normalHeight * $height) / $originalHeight;
        } else {
            $height = $normalHeight;
        }
        if ($x != '') {
            $x = ($normalWidth * $x) / $originalWidth;
        } else {
            $x = 0;
        }
        if ($x != '') {
            $y = ($normalHeight * $y) / $originalHeight;
        } else {
            $y = 0;
        }

        $imageOrigine = Image::imageCreateFromAny($this->getAbsolutePath());

        if ($imageOrigine == null) {
            return false;
        }

        $imageNouvelle = imagecreatetruecolor($width, $height);

        $copy = imagecopy($imageNouvelle, $imageOrigine, 0, 0, $x, $y, $width, $height);

        if ($copy) {
            $previousVersion = $this->getVersionImage();
            $version = $previousVersion + 1;

            $path = $this->getUploadRootDir() . '/' . $version . '-' . $this->getBanquePhoto()->getPhoto();
            Image::saveImageFromAny($imageNouvelle, $this->getAbsolutePath(), $path);

            $this->setVersionImage($version);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Intialise les informations de la création avec les informations du projet
     * @return Projet
     */
    public function initialiserInformationsProjet()
    {
        if ($this->getProjet() != null) {
            if (!$this->getNom()) {
                $this->setNom($this->getProjet()->getNom());
            }

            if (!is_null($this->getProjet()->getStyle())) {
                $this->setStyle($this->getProjet()->getStyle());
            }
            if (!is_null($this->getProjet()->getEnsoleillement())) {
                $this->setEnsoleillement($this->getProjet()->getEnsoleillement());
            }
            if (!is_null($this->getProjet()->getProjetType())) {
                $this->setCreationType($this->getProjet()->getProjetType());
            }
            if (!is_null($this->getProjet()->getSurface())) {
                $this->setSurface($this->getProjet()->getSurface());
            }
        }

        return $this;
    }

    /**
     * Intialise les informations de la création avec les informations de la banque de photo
     * @return Projet
     */
    public function initialiserInformationsBanquePhoto()
    {
        if ($this->getBanquePhoto() != null) {
            $this->setRepere1X($this->getBanquePhoto()->getRepere1X());
            $this->setRepere1Y($this->getBanquePhoto()->getRepere1Y());
            $this->setRepere2X($this->getBanquePhoto()->getRepere2X());
            $this->setRepere2Y($this->getBanquePhoto()->getRepere2Y());
            $this->setRepere3X($this->getBanquePhoto()->getRepere3X());
            $this->setRepere3Y($this->getBanquePhoto()->getRepere3Y());
            $this->setRepere4X($this->getBanquePhoto()->getRepere4X());
            $this->setRepere4Y($this->getBanquePhoto()->getRepere4Y());
            $this->setRepere1Largeur($this->getBanquePhoto()->getRepere1Largeur());
            $this->setRepere1Hauteur($this->getBanquePhoto()->getRepere1Hauteur());
            $this->setRepere2Largeur($this->getBanquePhoto()->getRepere2Largeur());
            $this->setRepere2Hauteur($this->getBanquePhoto()->getRepere2Hauteur());
            $this->setRepere3Largeur($this->getBanquePhoto()->getRepere3Largeur());
            $this->setRepere3Hauteur($this->getBanquePhoto()->getRepere3Hauteur());
            $this->setRepere4Largeur($this->getBanquePhoto()->getRepere4Largeur());
            $this->setRepere4Hauteur($this->getBanquePhoto()->getRepere4Hauteur());
        }

        return $this;
    }

    /**
     * Lifecycle callback pour reprendre les informations du projet
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateCreation(new DateTime());
        $this->setDateModification(new DateTime());
        $this->initialiserInformationsProjet();
        $this->initialiserInformationsBanquePhoto();

        return $this;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setDateModification(new DateTime());

        return $this;
    }


    /**
     * Lifecycle callback
     *
     * @ORM\PostPersist()
     */
    public function postPersist()
    {
        $this->moveUpload();
        if ($this->getInDuplication() !== true) {
            $this->createRenderedImage();
        }
    }

    /**
     * Retourne vrai si toutes les plantes sont compatibles au caractéristiques terrain du projet
     * @return Boolean
     */
    public function isGeolocalisationCompatible()
    {
        $compatible = true;
        $projet = $this->getProjet();
        if ($projet != null) {
            $rusticiteProjet = $projet->getRusticite();
            $typeSolProjet = $projet->getPh() ? $projet->getPh()->getTypeSol() : null;

            foreach ($this->getCreationEntites() as $entite) {
                if ($entite->getEntite()->getRusticite() != null && $rusticiteProjet != null && $entite->getEntite(
                    )->getRusticite()->getMin() > $rusticiteProjet->getMin()) {
                    $compatible = false;
                } else {
                    $typeSolCompatible = false;
                    foreach ($entite->getEntite()->getTypeSols() as $entiteTypeSol) {
                        if ($entiteTypeSol == $typeSolProjet) {
                            $typeSolCompatible = true;
                        }
                    }
                    $compatible = $typeSolCompatible;
                }

                if ($compatible == false) {
                    break;
                }
            }
        }

        return $compatible;
    }

    public function getInDuplication()
    {
        return $this->inDuplication;
    }

    public function setInDuplication($inDuplication)
    {
        $this->inDuplication = $inDuplication;
        return $this;
    }

    /**
     * @return string
     */
    public function getScreenShotHtmlContent()
    {
        return $this->screenShotHtmlContent;
    }

    /**
     * @param string $screenShot
     * @return Creation
     */
    public function setScreenShotHtmlContent($screenShotHtmlContent): Creation
    {
        $this->screenShotHtmlContent = $screenShotHtmlContent;
        return $this;
    }


}
