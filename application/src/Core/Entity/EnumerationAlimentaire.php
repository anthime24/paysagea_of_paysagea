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
 * EnumerationAlimentaire
 *
 * @ORM\Table(name="enumeration_alimentaire")
 * @ORM\Entity()
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\EnumerationAlimentaire")
 */
class EnumerationAlimentaire extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\EnumerationAlimentaire",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $cle;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $nom;

    public function __construct()
    {
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

    /**
     * @return string
     */
    public function getCle(): string
    {
        return $this->cle;
    }

    /**
     * @param string $cle
     */
    public function setCle(string $cle): self
    {
        $this->cle = $cle;
        return $this;
    }
}
