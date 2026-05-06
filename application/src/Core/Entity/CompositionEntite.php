<?php

namespace App\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompositionEntite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CompositionEntiteRepository")
 */
class CompositionEntite
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $positionX;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $positionY;

    /**
     * @var Composition
     *
     * @ORM\ManyToOne(targetEntity="Composition", inversedBy="compositionEntites")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $composition;

    /**
     * @var Entite
     *
     * @ORM\ManyToOne(targetEntity="Entite", inversedBy="compositionEntites")
     */
    private $entite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPositionX(): ?int
    {
        return $this->positionX;
    }

    public function setPositionX(int $positionX): self
    {
        $this->positionX = $positionX;

        return $this;
    }

    public function getPositionY(): ?int
    {
        return $this->positionY;
    }

    public function setPositionY(int $positionY): self
    {
        $this->positionY = $positionY;

        return $this;
    }

    public function getComposition(): ?Composition
    {
        return $this->composition;
    }

    public function setComposition(?Composition $composition): self
    {
        $this->composition = $composition;

        return $this;
    }

    public function getEntite(): ?Entite
    {
        return $this->entite;
    }

    public function setEntite(?Entite $entite): self
    {
        $this->entite = $entite;

        return $this;
    }

    public function toArray()
    {
        $couleurs = array();
        foreach ($this->entite->getCouleurs() as $couleur) {
            $couleurs[] = $couleur->getNom();
        }

        $couleursFleurs = array();
        foreach ($this->entite->getCouleurFleurs() as $couleur) {
            $couleursFleurs[] = $couleur->getNom();
        }

        return array(
            'id' => $this->getId(),
            'position_x' => $this->getPositionX(),
            'position_y' => $this->getPositionY(),
            'entite_id' => $this->getEntite()->getId(),
            'type' => $this->getEntite()->getEntiteType()->getNom(),
            'sous_type' => $this->getEntite()->getEntiteSousType() ? $this->getEntite()->getEntiteSousType()->getNom(
            ) : '',
            'acronyme' => $this->getEntite()->getAcronyme(),
            'nom_vernaculaire' => $this->getEntite()->getNomVernaculaire(),
            'nom' => $this->getEntite()->getNom(),
            'pot' => $this->getEntite()->getPot(),
            'diametre_pot' => $this->getEntite()->getDiametrePot(),
            'composition_id' => $this->getComposition()->getId(),
            'couleurs' => implode(', ', $couleurs),
            'couleurs_fleurs' => implode(', ', $couleursFleurs)
        );
    }
}
