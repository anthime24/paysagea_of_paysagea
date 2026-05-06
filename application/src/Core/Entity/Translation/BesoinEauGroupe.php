<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="besoin_eau_groupe_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="besoin_eau_groupe_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class BesoinEauGroupe extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\BesoinEauGroupe", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
