<?php

namespace App\Core\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * ClientOffre
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\ClientOffreRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ClientOffre
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
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $dateAjout;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="clientOffres")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $client;

    /**
     * @var Offre
     *
     * @ORM\ManyToOne(targetEntity="Offre", inversedBy="clientOffres")
     */
    private $offre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAjout(): ?DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateAjout(new DateTime());
    }
}
