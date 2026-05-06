<?php

namespace App\Core\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="video_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="video_translation_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class Video extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\Video", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
