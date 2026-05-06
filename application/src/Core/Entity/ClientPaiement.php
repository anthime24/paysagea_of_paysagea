<?php

namespace App\Core\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * ClientPaiement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\ClientPaiementRepository")
 */
class ClientPaiement
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
    private $datePaiement;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $montantPaiement;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numTransaction;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    private $reponseCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reponseCodeTexte;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $valide = false;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="clientPaiements")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $client;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentProcessor = "paypal";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePaiement(): ?DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getMontantPaiement(): ?float
    {
        return $this->montantPaiement;
    }

    public function setMontantPaiement(float $montantPaiement): self
    {
        $this->montantPaiement = $montantPaiement;

        return $this;
    }

    public function getNumTransaction(): ?string
    {
        return $this->numTransaction;
    }

    public function setNumTransaction(?string $numTransaction): self
    {
        $this->numTransaction = $numTransaction;

        return $this;
    }

    public function getReponseCode(): ?string
    {
        return $this->reponseCode;
    }

    public function setReponseCode(string $reponseCode): self
    {
        $this->reponseCode = $reponseCode;

        return $this;
    }

    public function getReponseCodeTexte(): ?string
    {
        return $this->reponseCodeTexte;
    }

    public function setReponseCodeTexte(?string $reponseCodeTexte): self
    {
        $this->reponseCodeTexte = $reponseCodeTexte;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(?bool $valide): self
    {
        $this->valide = $valide;

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

    /**
     * @return string
     */
    public function getPaymentProcessor()
    {
        return $this->paymentProcessor;
    }

    /**
     * @param string $paymentProcessor
     * @return ClientPaiement
     */
    public function setPaymentProcessor($paymentProcessor): ClientPaiement
    {
        $this->paymentProcessor = $paymentProcessor;
        return $this;
    }
}
