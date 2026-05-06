<?php

namespace App\Core\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Projet
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\ProjetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Projet
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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $budgetMax;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codePostal;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $longitude = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $latitude = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitude = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $surface;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $confirmer = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $arrosageDeuxFoisParSemaine;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recevoirInfosPartenaires;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Creation", mappedBy="projet")
     */
    private $creations;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="projets")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $client;

    /**
     * @var Rusticite
     *
     * @ORM\ManyToOne(targetEntity="Rusticite", inversedBy="projets")
     */
    private $rusticite;

    /**
     * @var Precipitation
     *
     * @ORM\ManyToOne(targetEntity="Precipitation", inversedBy="projets")
     */
    private $precipitation;

    /**
     * @var Style
     *
     * @ORM\ManyToOne(targetEntity="Style", inversedBy="projets")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $style;

    /**
     * @var Ph
     *
     * @ORM\ManyToOne(targetEntity="Ph", inversedBy="projets")
     */
    private $ph;

    /**
     * @var Ensoleillement
     *
     * @ORM\ManyToOne(targetEntity="Ensoleillement", inversedBy="projets")
     */
    private $ensoleillement;

    /**
     * @var CreationType
     *
     * @ORM\ManyToOne(targetEntity="CreationType", inversedBy="projets")
     */
    private $projetType;

    /**
     * @var bool
     */
    private $arrosage_deux_fois_par_semaine_jardin;

    /**
     * @var bool
     */
    private $arrosage_deux_fois_par_semaine_terrasse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $origineCarte = "FR";

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $test = false;

    public function __construct()
    {
        $this->creations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom() ? $this->getNom() : '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getBudgetMax(): ?float
    {
        return $this->budgetMax;
    }

    public function setBudgetMax(?float $budgetMax): self
    {
        $this->budgetMax = $budgetMax;

        return $this;
    }

    public function getAdresse1(): ?string
    {
        return $this->adresse1;
    }

    public function setAdresse1(?string $adresse1): self
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    public function getAdresse2(): ?string
    {
        return $this->adresse2;
    }

    public function setAdresse2(?string $adresse2): self
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function setAltitude(?float $altitude): self
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(?float $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getConfirmer(): ?bool
    {
        return $this->confirmer;
    }

    public function setConfirmer(?bool $confirmer): self
    {
        $this->confirmer = $confirmer;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getArrosageDeuxFoisParSemaine(): ?bool
    {
        return $this->arrosageDeuxFoisParSemaine;
    }

    public function setArrosageDeuxFoisParSemaine(?bool $arrosageDeuxFoisParSemaine): self
    {
        $this->arrosageDeuxFoisParSemaine = $arrosageDeuxFoisParSemaine;

        return $this;
    }

    public function getRecevoirInfosPartenaires(): ?bool
    {
        return $this->recevoirInfosPartenaires;
    }

    public function setRecevoirInfosPartenaires(?bool $recevoirInfosPartenaires): self
    {
        $this->recevoirInfosPartenaires = $recevoirInfosPartenaires;

        return $this;
    }

    /**
     * @return Collection|Creation[]
     */
    public function getCreations(): Collection
    {
        return $this->creations;
    }

    public function addCreation(Creation $creation): self
    {
        if (!$this->creations->contains($creation)) {
            $this->creations[] = $creation;
            $creation->setProjet($this);
        }

        return $this;
    }

    public function removeCreation(Creation $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            // set the owning side to null (unless already changed)
            if ($creation->getProjet() === $this) {
                $creation->setProjet(null);
            }
        }

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

    public function getRusticite(): ?Rusticite
    {
        return $this->rusticite;
    }

    public function setRusticite(?Rusticite $rusticite): self
    {
        $this->rusticite = $rusticite;

        return $this;
    }

    public function getPrecipitation(): ?Precipitation
    {
        return $this->precipitation;
    }

    public function setPrecipitation(?Precipitation $precipitation): self
    {
        $this->precipitation = $precipitation;

        return $this;
    }

    public function getStyle(): ?Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getPh(): ?Ph
    {
        return $this->ph;
    }

    public function setPh(?Ph $ph): self
    {
        $this->ph = $ph;

        return $this;
    }

    public function getEnsoleillement(): ?Ensoleillement
    {
        return $this->ensoleillement;
    }

    public function setEnsoleillement(?Ensoleillement $ensoleillement): self
    {
        $this->ensoleillement = $ensoleillement;

        return $this;
    }

    public function getProjetType(): ?CreationType
    {
        return $this->projetType;
    }

    public function setProjetType(?CreationType $projetType): self
    {
        $this->projetType = $projetType;

        return $this;
    }

    public function getOrderedCreations()
    {
        $temp = $this->getCreations()->toArray();

        usort($temp, array($this, "cmpCreations"));

        return new ArrayCollection($temp);
    }

    protected function cmpCreations($a, $b)
    {
        if ($a->getDateModification() == $b->getDateModification()) {
            return 0;
        }
        return ($a->getDateModification() > $b->getDateModification()) ? -1 : 1;
    }

    /**
     * Lifecycle callback pour enregistrer la date de création du projet et forcer de l'arrosage deux fois par semaine si Terrasse
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateCreation(new DateTime());
        if ($this->getProjetType() && $this->getProjetType()->getId() == 2) {
            $this->setArrosageDeuxFoisParSemaine(1);
        }

        return $this;
    }

    /**
     * Lifecycle callback pour forcer l'arrosage deux fois par semaine si Terrasse
     *
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        if ($this->getProjetType() && $this->getProjetType()->getId() == 2) {
            $this->setArrosageDeuxFoisParSemaine(1);
        }

        return $this;
    }

    public function getMoisOrderByNomAsc()
    {
        $tabMois = array();

        if (count($this->getCreations()) > 0) {
            foreach ($this->getCreations() as $creation) {
                if (count($creation->getCreationEntites()) > 0) {
                    foreach ($creation->getCreationEntites() as $creationEntite) {
                        if (count($creationEntite->getEntite()->getMois()) > 0) {
                            foreach ($creationEntite->getEntite()->getMoisOrderByIdAsc() as $mois) {
                                if (!in_array($mois->getNom(), $tabMois)) {
                                    $tabMois[] = $mois->getNom();
                                }
                            }
                        }
                    }
                }
            }
        }

        return count($tabMois) > 0 ? implode(', ', $tabMois) : '';
    }

    public function getArrosageDeuxFoisParSemaineJardin(): ?bool
    {
        return $this->arrosage_deux_fois_par_semaine_jardin;
    }

    public function setArrosageDeuxFoisParSemaineJardin(bool $arrosage_deux_fois_par_semaine_jardin)
    {
        $this->arrosage_deux_fois_par_semaine_jardin = $arrosage_deux_fois_par_semaine_jardin;
    }

    public function getArrosageDeuxFoisParSemaineTerrasse(): ?bool
    {
        return $this->arrosage_deux_fois_par_semaine_terrasse;
    }

    public function setArrosageDeuxFoisParSemaineTerrasse(bool $arrosage_deux_fois_par_semaine_terrasse)
    {
        $this->arrosage_deux_fois_par_semaine_terrasse = $arrosage_deux_fois_par_semaine_terrasse;
    }

    public function initialiserNom()
    {
        $this->setNom($this->getAdresse() . ' ' . date('j/m/Y'));
    }

    /**
     * @return string
     */
    public function getOrigineCarte()
    {
        return $this->origineCarte;
    }

    /**
     * @param string $origineCarte
     * @return Projet
     */
    public function setOrigineCarte($origineCarte): Projet
    {
        $this->origineCarte = $origineCarte;
        return $this;
    }

    public function getTest(): ?bool
    {
        return $this->test;
    }

    public function setTest(?bool $test): self
    {
        $this->test = $test;

        return $this;
    }
}
