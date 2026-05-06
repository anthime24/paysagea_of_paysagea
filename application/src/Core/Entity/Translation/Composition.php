<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="composition_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="composition_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class Composition extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\Composition", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
