<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="type_sol_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="typesol_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class TypeSol extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\TypeSol", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
