<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="ensoleillement_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ensoleillement_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class Ensoleillement extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\Ensoleillement", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
