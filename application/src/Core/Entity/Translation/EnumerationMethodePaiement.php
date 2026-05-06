<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="enumeration_methode_paiement_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="enumeration_methode_paiement_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class EnumerationMethodePaiement extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\EnumerationMethodePaiement", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
