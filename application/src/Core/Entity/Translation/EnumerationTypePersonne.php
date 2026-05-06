<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="enumeration_type_personne_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="enumeration_type_personne_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class EnumerationTypePersonne extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\EnumerationTypePersonne", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
