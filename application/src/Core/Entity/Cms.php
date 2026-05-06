<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;

/**
 * Cms
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CmsRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Cms")
 */
class Cms extends AbstractPersonalTranslatable implements TranslatableInterface
{
    use PersonalTranslatableTrait;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Core\Entity\Translation\Cms",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $url;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $administrable;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre($titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug($slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte($texte): self
    {
        $this->texte = $texte;
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

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle($metaTitle): self
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription($metaDescription): self
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAdministrable()
    {
        return $this->administrable;
    }

    /**
     * @param bool $administrable
     */
    public function setAdministrable(bool $administrable)
    {
        $this->administrable = $administrable;
        return $this;
    }
}
