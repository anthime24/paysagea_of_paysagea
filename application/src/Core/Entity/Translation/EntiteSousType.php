<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="entite_sous_type_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="entitesoustype_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class EntiteSousType extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\EntiteSousType", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
