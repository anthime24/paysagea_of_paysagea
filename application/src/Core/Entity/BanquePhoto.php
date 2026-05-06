<?php

namespace App\Core\Entity;

use App\Core\Utility\Image;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BanquePhoto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\BanquePhotoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BanquePhoto
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
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $poids;

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
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmer = false;

    /**
     * @var file
     *
     * @Assert\File(
     *     maxSize = "10242k",
     *     mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png"},
     * )
     */
    private $file;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $public;

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
     * @ORM\Column(type="string", length=255, nullable=true, unique=false)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $uploadedDuringRegistration = false;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="banquePhotos")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $client;

    /**
     * @var CreationType
     *
     * @ORM\ManyToOne(targetEntity="CreationType", inversedBy="banquePhotos")
     */
    private $banquePhotoType;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Creation", mappedBy="banquePhoto")
     */
    private $creations;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $defaultPicture;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $deleted;

    public function __construct()
    {
        $this->creations = new ArrayCollection();
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

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): self
    {
        $this->poids = $poids;

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

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

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

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): self
    {
        $this->public = $public;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getBanquePhotoType(): ?CreationType
    {
        return $this->banquePhotoType;
    }

    public function setBanquePhotoType(?CreationType $banquePhotoType): self
    {
        $this->banquePhotoType = $banquePhotoType;

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
            $creation->setBanquePhoto($this);
        }

        return $this;
    }

    public function removeCreation(Creation $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            // set the owning side to null (unless already changed)
            if ($creation->getBanquePhoto() === $this) {
                $creation->setBanquePhoto(null);
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
     * @return ?UploadedFile
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
        return '/uploads/banque_photo';
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

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues
        $this->setPhoto($this->getNom() . '.' . $this->file->guessExtension());

        $this->setType($this->getFile()->getClientMimeType());
        $this->setPoids($this->getFile()->getSize());

        $imageSize = null;

        if (!$this->getId()) {
            $this->getFile()->move($this->getTmpUploadRootDir(), $this->getPhoto());
            $tmpAbsolutePath = $this->getTmpAbsolutePath();
            $imageSize = !empty($tmpAbsolutePath) && file_exists($tmpAbsolutePath) ? getimagesize(
                $tmpAbsolutePath
            ) : null;
        } else {
            $this->getFile()->move($this->getUploadRootDir(), $this->getPhoto());
            $absolutePath = $this->getAbsolutePath();
            $imageSize = !empty($absolutePath) && file_exists($absolutePath) ? getimagesize($absolutePath) : null;
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
            mkdir($this->getUploadRootDir());
        }

        if (file_exists($this->getTmpAbsolutePath())) {
            copy($this->getTmpAbsolutePath(), $this->getAbsolutePath());
            unlink($this->getTmpAbsolutePath());
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function removeUpload()
    {
        if ($this->getPhoto() && file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }

        if (file_exists($this->getUploadRootDir())) {
            rmdir($this->getUploadRootDir());
        }
    }

    /**
     * Lifecycle callback
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateCreation(new DateTime());

        if ($this->getClient()) {
            $this->setEmail($this->getClient()->getEmail());
        }

        $this->preUpload();
    }

    /**
     * Lifecycle callback
     *
     * @ORM\PostPersist
     */
    public function postPersist()
    {
        $this->moveUpload();
    }

    public function getHasClientProjetRecevoirInfosPartenaire()
    {
        $recevoirInfosPartenaire = 'Non';
        if (count($this->getCreations()) > 0) {
            foreach ($this->getCreations() as $creation) {
                if ($creation->getProjet()->getRecevoirInfosPartenaires()) {
                    $recevoirInfosPartenaire = 'Oui';
                }
            }
        }

        return $recevoirInfosPartenaire;
    }

    public function getAllAdressesClient()
    {
        $tabAdresses = array();
        if (count($this->getCreations()) > 0) {
            foreach ($this->getCreations() as $creation) {
                if (!in_array($creation->getProjet()->getAdresse(), $tabAdresses)) {
                    $tabAdresses[] = $creation->getProjet()->getAdresse();
                }
            }
        }

        return implode(' - ', $tabAdresses);
    }

    public function createRotation($rotation)
    {
        $filename = $this->getAbsolutePath();
        if (file_exists($filename)) {
            $source = Image::imageCreateFromAny($filename);
            $rotate = imagerotate($source, -$rotation, 0);
            Image::saveImageFromAny($rotate, $filename, $filename);
        }
    }

    public function initialiserNom()
    {
        $this->setNom(time() . '-' . ($this->getNom() ? $this->getNom() : 'Upload'));
    }

    public function getLabel()
    {
        $label = $this->getNom();
        if(preg_match('/^[0-9]{8,}-/', $label, $matches) == 1) {
            $label = str_replace($matches[0], '', $label);
        }

        return $label;
    }

    /**
     * @return bool
     */
    public function isUploadedDuringRegistration(): bool
    {
        return $this->uploadedDuringRegistration;
    }

    /**
     * @param bool $uploadedDuringRegistration
     * @return BanquePhoto
     */
    public function setUploadedDuringRegistration(bool $uploadedDuringRegistration): BanquePhoto
    {
        $this->uploadedDuringRegistration = $uploadedDuringRegistration;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDefaultPicture(): bool
    {
        return $this->defaultPicture !== null ? $this->defaultPicture : false;
    }

    /**
     * @param bool $defaultPicture
     * @return BanquePhoto
     */
    public function setDefaultPicture(bool $defaultPicture): BanquePhoto
    {
        $this->defaultPicture = $defaultPicture;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDeleted(): bool
    {
        return $this->deleted !== null ? $this->deleted : false;
    }

    /**
     * @param bool $deleted
     * @return BanquePhoto
     */
    public function setDeleted(bool $deleted): BanquePhoto
    {
        $this->deleted = $deleted;
        return $this;
    }
}
