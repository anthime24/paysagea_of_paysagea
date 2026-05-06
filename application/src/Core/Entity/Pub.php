<?php

namespace App\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Pub
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\PubRepository")
 */
class Pub
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
    private $lien;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $photo;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @var UploadedFile
     */
    private $file;

    public function __toString()
    {
        return $this->getLien() ? $this->getLien() : '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;

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

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

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
        return '/uploads/photos/pub';
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

        if (!$this->getId()) {
            $this->getFile()->move($this->getTmpUploadRootDir(), $this->getPhoto());
        } else {
            $this->getFile()->move($this->getUploadRootDir(), $this->getPhoto());
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
