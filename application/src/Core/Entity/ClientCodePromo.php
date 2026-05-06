<?php

namespace App\Core\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * ClientCodePromo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\ClientCodePromoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ClientCodePromo
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
    private $dateUtilisation;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="clientCodePromos")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $client;

    /**
     * @var CodePromo
     *
     * @ORM\ManyToOne(targetEntity="CodePromo", inversedBy="clientCodePromos")
     */
    private $codePromo;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateUtilisation(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateUtilisation(): ?DateTimeInterface
    {
        return $this->dateUtilisation;
    }

    public function setDateUtilisation(DateTimeInterface $dateUtilisation): self
    {
        $this->dateUtilisation = $dateUtilisation;

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

    public function getCodePromo(): ?CodePromo
    {
        return $this->codePromo;
    }

    public function setCodePromo(?CodePromo $codePromo): self
    {
        $this->codePromo = $codePromo;

        return $this;
    }
}
