<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Marque
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\MarqueRepository")
 */
class Marque
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Entite", mappedBy="marque")
     */
    private $entites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="EntiteType", inversedBy="marques")
     * @ORM\JoinTable(name="marque_entitetype",
     *      joinColumns={@ORM\JoinColumn(name="marque_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entitetype_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $entiteTypes;

    /**
     * @var UploadedFile
     */
    private $file;

    public function __construct()
    {
        $this->entites = new ArrayCollection();
        $this->entiteTypes = new ArrayCollection();
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

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
            $entite->setMarque($this);
        }

        return $this;
    }

    public function removeEntite(Entite $entite): self
    {
        if ($this->entites->contains($entite)) {
            $this->entites->removeElement($entite);
            // set the owning side to null (unless already changed)
            if ($entite->getMarque() === $this) {
                $entite->setMarque(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EntiteType[]
     */
    public function getEntiteTypes(): Collection
    {
        return $this->entiteTypes;
    }

    public function addEntiteType(EntiteType $entiteType): self
    {
        if (!$this->entiteTypes->contains($entiteType)) {
            $this->entiteTypes[] = $entiteType;
        }

        return $this;
    }

    public function removeEntiteType(EntiteType $entiteType): self
    {
        if ($this->entiteTypes->contains($entiteType)) {
            $this->entiteTypes->removeElement($entiteType);
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
        return null === $this->getLogo() ? null : $this->getUploadRootDir() . '/' . $this->getLogo();
    }

    public function getTmpAbsolutePath()
    {
        return null === $this->getLogo() ? null : $this->getTmpUploadRootDir() . '/' . $this->getLogo();
    }

    public function getWebPath()
    {
        return null === $this->getLogo() ? null : $this->getUploadDir() . '/' . $this->getId() . '/' . $this->getLogo();
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
        return '/uploads/marque';
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
        $this->setLogo(time() . '-' . $this->getNom() . '.' . $this->file->guessExtension());

        if (!$this->getId()) {
            $this->file->move($this->getTmpUploadRootDir(), $this->getLogo());
        } else {
            $this->file->move($this->getUploadRootDir(), $this->getLogo());
        }

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    public function moveUpload()
    {
        if (null === $this->getLogo()) {
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

    public function removeUpload()
    {
        if ($this->getLogo() && file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }

        if (file_exists($this->getUploadRootDir())) {
            rmdir($this->getUploadRootDir());
        }
    }
}
