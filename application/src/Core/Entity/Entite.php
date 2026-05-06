<?php

namespace App\Core\Entity;

use App\Core\Utility\Slug;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;


/**
 * Entite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\EntiteRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Entite")
 */
class Entite extends AbstractPersonalTranslatable implements TranslatableInterface
{
    use PersonalTranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Core\Entity\Translation\Entite",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $acronyme;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $prixMini;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $gratuit = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $nouveau;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $conseilAutomne;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $conseilHiver;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $conseilPrintemps;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $conseilEte;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $divers;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $toxiqueAlimentaire;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $caducPersistant;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $temperatureMinimale;

    /**
     * @Gedmo\Translatable
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomVernaculaire;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rusticiteValeur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pot;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $diametrePot;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $hauteurPot;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $diametreFinal;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $hauteurFinale;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toxiqueAlimentaireInformation;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $annuelle;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $conseilGeneral;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptif;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptifPdf;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $profondeurFinale;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $actif = true;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ecoParticipation;

    /**
     * @var Entite
     *
     * @ORM\OneToOne(targetEntity="Entite", inversedBy="entite")
     * @ORM\JoinColumn(name="entite_id", referencedColumnName="id")
     */
    private $entiteSelf;

    /**
     * @var EntiteType
     *
     * @ORM\ManyToOne(targetEntity="EntiteType", inversedBy="entites")
     */
    private $entiteType;

    /**
     * @var Entretien
     *
     * @ORM\ManyToOne(targetEntity="Entretien", inversedBy="entites")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $entretien;

    /**
     * @var Rusticite
     *
     * @ORM\ManyToOne(targetEntity="Rusticite", inversedBy="entites")
     */
    private $rusticite;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Rusticite", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="entite_rusticite",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="rusticite_id", referencedColumnName="id")}
     * )
     */
    private $rusticiteMultiples;

    /**
     * @var BesoinEau
     *
     * @ORM\ManyToOne(targetEntity="BesoinEau", inversedBy="entites")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $besoinEau;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="BesoinEau", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="entite_besoin_eau",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="besoin_eau_id", referencedColumnName="id")}
     * )
     */
    private $besoinEauMultiples;

    /**
     * @var EntiteSousType
     *
     * @ORM\ManyToOne(targetEntity="EntiteSousType", inversedBy="entites")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $entiteSousType;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="EntiteSousType", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="entite_entitesoustype",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sous_type_id", referencedColumnName="id")}
     * )
     */
    private $entiteSousTypeMultiples;

    /**
     * @var Marque
     *
     * @ORM\ManyToOne(targetEntity="Marque", inversedBy="entites")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $marque;

    /**
     * @var Composition
     *
     * @ORM\OneToOne(targetEntity="Composition", inversedBy="entite")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $composition;

    /**
     * @var VariantTaille
     *
     * @ORM\ManyToOne(targetEntity="VariantTaille", inversedBy="entites")
     * @ORM\JoinColumn(name="entite_variant_taille", referencedColumnName="id")
     */
    private $variantTaille;

    /**
     * @var VariantCouleur
     *
     * @ORM\ManyToOne(targetEntity="VariantCouleur", inversedBy="entites")
     * @ORM\JoinColumn(name="entite_variant_couleur", referencedColumnName="id")
     */
    private $variantCouleur;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="EntitePhoto", mappedBy="entite")
     */
    private $entitePhotos;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Style", inversedBy="entites")
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $styles;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Couleur", inversedBy="entites")
     * @ORM\JoinTable(name="entite_couleur",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="couleur_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $couleurs;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Categorie", inversedBy="entites")
     * @ORM\JoinTable(name="entite_categorie",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="categorie_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $categories;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Mois", inversedBy="entites")
     * @ORM\JoinTable(name="entite_mois",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="mois_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $mois;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Ensoleillement", inversedBy="entites")
     * @ORM\JoinTable(name="entite_ensoleillement",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ensoleillement_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $ensoleillements;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="TypeSol", inversedBy="entites")
     * @ORM\JoinTable(name="entite_type_sol",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="type_sol_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $typeSols;

    /**
     * @var Collection
     *
     * @ORM\OneToOne(targetEntity="Entite", mappedBy="entiteSelf")
     */
    private $entite;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CreationEntite", mappedBy="entite")
     */
    private $creationEntites;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CompositionEntite", mappedBy="entite")
     */
    private $compositionEntites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Matiere", inversedBy="entites")
     * @ORM\JoinTable(name="entite_matiere",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="matiere_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @ORM\OrderBy({"nom": "ASC"})
     */
    private $matieres;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Couleur", inversedBy="entiteFleurs")
     * @ORM\JoinTable(name="entite_couleur_fleur",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="couleur_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $couleurFleurs;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CreationType", inversedBy="entites")
     * @ORM\JoinTable(name="creation_type_entite",
     *      joinColumns={@ORM\JoinColumn(name="entite_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="creation_type_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $creationTypes;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $lasso = false;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $lassoPhoto;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $lassoPhotoHauteur;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $lassoPhotoLargeur;

    /**
     * @var UploadedFile
     */
    private $fileLasso;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="picto_nouveau", options={"default" : 0})
     */
    private $pictoNouveau = false;

    /**
     * Champ en base de donnée permettant de savoir si l'entitée possède actuellement un pictogrammeNouveau
     * pictoNouveauComputedFlag vaut true, si pictoNouveau vaut true et la dateCourante est comprise entre datedebut et dateFin
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", name="picto_nouveau_computed_flag", options={"default" : 0})
     */
    private $pictoNouveauComputedFlag = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="picto_nouveau_date_debut", nullable=true)
     */
    private $dateDebutPictoNouveau;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="picto_nouveau_date_fin", nullable=true)
     */
    private $dateFinPictoNouveau;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="picto_promo", options={"default" : 0})
     */
    private $pictoPromo = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="picto_promo_computed_flag", options={"default" : 0})
     */
    private $pictoPromoComputedFlag = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="picto_promo_date_debut", nullable=true)
     */
    private $dateDebutPictoPromo;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="picto_promo_date_fin", nullable=true)
     */
    private $dateFinPictoPromo;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="picto_coup_coeur", options={"default" : 0})
     */
    private $pictoCoupCoeur = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="picto_coup_coeur_computed_flag", options={"default" : 0})
     */
    private $pictoCoupCoeurComputedFlag = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="picto_coup_coeur_date_debut", nullable=true)
     */
    private $dateDebutPictoCoupCoeur;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="picto_coup_coeur_date_fin", nullable=true)
     */
    private $dateFinPictoCoupCoeur;

    /**
     * @ORM\Column(name="imported_at", type="datetime", nullable=true)
     */
    private $importedAt = null;

    /**
     * @ORM\Column(name="disabled_at_previous_import", type="boolean", nullable=true, options={"default": null})
     */
    private $disabledAtPreviousImport = false;

    /**
     * @ORM\ManyToOne(targetEntity="EntitePhoto")
     * @ORM\JoinColumn(name="derniere_photo_principale_importe", referencedColumnName="id", nullable=true)
     */
    private $dernierePhotoPrincipaleImporte = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": null})
     */
    private $identifiantDernierImport = null;

    public function __construct()
    {
        $this->entitePhotos = new ArrayCollection();
        $this->styles = new ArrayCollection();
        $this->couleurs = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->mois = new ArrayCollection();
        $this->ensoleillements = new ArrayCollection();
        $this->typeSols = new ArrayCollection();
        $this->creationEntites = new ArrayCollection();
        $this->compositionEntites = new ArrayCollection();
        $this->matieres = new ArrayCollection();
        $this->couleurFleurs = new ArrayCollection();
        $this->creationTypes = new ArrayCollection();
        $this->rusticiteMultiples = new ArrayCollection();
        $this->besoinEauMultiples = new ArrayCollection();
        $this->entiteSousTypeMultiples = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom() ? $this->getNom() : '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAcronyme(): ?string
    {
        return $this->acronyme;
    }

    public function setAcronyme(string $acronyme): self
    {
        $this->acronyme = $acronyme;

        return $this;
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

    public function getPrixMini(): ?string
    {
        return $this->composition != null ? $this->composition->getTotal() : $this->prixMini;
    }

    public function setPrixMini(?string $prixMini): self
    {
        $this->prixMini = $prixMini;

        return $this;
    }

    public function getGratuit(): ?bool
    {
        return $this->gratuit;
    }

    public function setGratuit(bool $gratuit): self
    {
        $this->gratuit = $gratuit;

        return $this;
    }

    public function getNouveau(): ?bool
    {
        return $this->nouveau;
    }

    public function setNouveau(bool $nouveau): self
    {
        $this->nouveau = $nouveau;

        return $this;
    }

    public function getConseilAutomne(): ?string
    {
        return $this->conseilAutomne;
    }

    public function setConseilAutomne(?string $conseilAutomne): self
    {
        $this->conseilAutomne = $conseilAutomne;

        return $this;
    }

    public function getConseilHiver(): ?string
    {
        return $this->conseilHiver;
    }

    public function setConseilHiver(?string $conseilHiver): self
    {
        $this->conseilHiver = $conseilHiver;

        return $this;
    }

    public function getConseilPrintemps(): ?string
    {
        return $this->conseilPrintemps;
    }

    public function setConseilPrintemps(?string $conseilPrintemps): self
    {
        $this->conseilPrintemps = $conseilPrintemps;

        return $this;
    }

    public function getConseilEte(): ?string
    {
        return $this->conseilEte;
    }

    public function setConseilEte(?string $conseilEte): self
    {
        $this->conseilEte = $conseilEte;

        return $this;
    }

    public function getDivers(): ?string
    {
        return $this->divers;
    }

    public function setDivers(?string $divers): self
    {
        $this->divers = $divers;

        return $this;
    }

    public function getToxiqueAlimentaire(): ?string
    {
        return $this->toxiqueAlimentaire;
    }

    public function setToxiqueAlimentaire(?string $toxiqueAlimentaire): self
    {
        $this->toxiqueAlimentaire = $toxiqueAlimentaire;

        return $this;
    }

    public function getCaducPersistant(): ?string
    {
        return $this->caducPersistant;
    }

    public function setCaducPersistant(?string $caducPersistant): self
    {
        $this->caducPersistant = $caducPersistant;

        return $this;
    }

    public function getTemperatureMinimale(): ?float
    {
        return $this->temperatureMinimale;
    }

    public function setTemperatureMinimale(?float $temperatureMinimale): self
    {
        $this->temperatureMinimale = $temperatureMinimale;

        return $this;
    }

    public function getNomVernaculaire(): ?string
    {
        return $this->nomVernaculaire;
    }

    public function setNomVernaculaire(?string $nomVernaculaire): self
    {
        $this->nomVernaculaire = $nomVernaculaire;

        return $this;
    }

    public function getRusticiteValeur(): ?int
    {
        return $this->rusticiteValeur;
    }

    public function setRusticiteValeur(?int $rusticiteValeur): self
    {
        $this->rusticiteValeur = $rusticiteValeur;

        return $this;
    }

    public function getPot(): ?string
    {
        return $this->pot;
    }

    public function setPot(?string $pot): self
    {
        $this->pot = $pot;

        return $this;
    }

    public function getDiametrePot(): ?string
    {
        return $this->diametrePot;
    }

    public function setDiametrePot(?string $diametrePot): self
    {
        $this->diametrePot = $diametrePot;

        return $this;
    }

    public function getHauteurPot(): ?string
    {
        return $this->hauteurPot;
    }

    public function setHauteurPot(?string $hauteurPot): self
    {
        $this->hauteurPot = $hauteurPot;

        return $this;
    }

    public function getDiametreFinal(): ?string
    {
        return $this->diametreFinal;
    }

    public function setDiametreFinal(?string $diametreFinal): self
    {
        $this->diametreFinal = $diametreFinal;

        return $this;
    }

    public function getHauteurFinale(): ?string
    {
        return $this->hauteurFinale;
    }

    public function setHauteurFinale(?string $hauteurFinale): self
    {
        $this->hauteurFinale = $hauteurFinale;

        return $this;
    }

    public function getToxiqueAlimentaireInformation(): ?string
    {
        return $this->toxiqueAlimentaireInformation;
    }

    public function setToxiqueAlimentaireInformation(?string $toxiqueAlimentaireInformation): self
    {
        $this->toxiqueAlimentaireInformation = $toxiqueAlimentaireInformation;

        return $this;
    }

    public function getAnnuelle(): ?bool
    {
        return $this->annuelle;
    }

    public function setAnnuelle(?bool $annuelle): self
    {
        $this->annuelle = $annuelle;

        return $this;
    }

    public function getConseilGeneral(): ?string
    {
        return $this->conseilGeneral;
    }

    public function setConseilGeneral(?string $conseilGeneral): self
    {
        $this->conseilGeneral = $conseilGeneral;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getDescriptifPdf(): ?string
    {
        return $this->descriptifPdf;
    }

    public function setDescriptifPdf(?string $descriptifPdf): self
    {
        $this->descriptifPdf = $descriptifPdf;

        return $this;
    }

    public function getProfondeurFinale(): ?string
    {
        return $this->profondeurFinale;
    }

    public function setProfondeurFinale(?string $profondeurFinale): self
    {
        $this->profondeurFinale = $profondeurFinale;

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

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getEcoParticipation(): ?string
    {
        return $this->ecoParticipation;
    }

    public function setEcoParticipation(?string $ecoParticipation): self
    {
        $this->ecoParticipation = $ecoParticipation;

        return $this;
    }

    public function getEntiteSelf(): ?self
    {
        return $this->entiteSelf;
    }

    public function setEntiteSelf(?self $entiteSelf): self
    {
        $this->entiteSelf = $entiteSelf;

        return $this;
    }

    public function getEntiteType(): ?EntiteType
    {
        return $this->entiteType;
    }

    public function setEntiteType(?EntiteType $entiteType): self
    {
        $this->entiteType = $entiteType;

        return $this;
    }

    public function getEntretien(): ?Entretien
    {
        return $this->entretien;
    }

    public function setEntretien(?Entretien $entretien): self
    {
        $this->entretien = $entretien;

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

    public function getBesoinEau(): ?BesoinEau
    {
        return $this->besoinEau;
    }

    public function setBesoinEau(?BesoinEau $besoinEau): self
    {
        $this->besoinEau = $besoinEau;

        return $this;
    }

    public function getEntiteSousType(): ?EntiteSousType
    {
        return $this->entiteSousType;
    }

    public function setEntiteSousType(?EntiteSousType $entiteSousType): self
    {
        $this->entiteSousType = $entiteSousType;

        return $this;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

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

    public function getVariantTaille(): ?VariantTaille
    {
        return $this->variantTaille;
    }

    public function setVariantTaille(?VariantTaille $variantTaille): self
    {
        $this->variantTaille = $variantTaille;

        return $this;
    }

    public function getVariantCouleur(): ?VariantCouleur
    {
        return $this->variantCouleur;
    }

    public function setVariantCouleur(?VariantCouleur $variantCouleur): self
    {
        $this->variantCouleur = $variantCouleur;

        return $this;
    }

    /**
     * @return Collection|EntitePhoto[]
     */
    public function getEntitePhotos(): Collection
    {
        return $this->entitePhotos;
    }

    public function addEntitePhoto(EntitePhoto $entitePhoto): self
    {
        if (!$this->entitePhotos->contains($entitePhoto)) {
            $this->entitePhotos[] = $entitePhoto;
            $entitePhoto->setEntite($this);
        }

        return $this;
    }

    public function removeEntitePhoto(EntitePhoto $entitePhoto): self
    {
        if ($this->entitePhotos->contains($entitePhoto)) {
            $this->entitePhotos->removeElement($entitePhoto);
            // set the owning side to null (unless already changed)
            if ($entitePhoto->getEntite() === $this) {
                $entitePhoto->setEntite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Style[]
     */
    public function getStyles(): Collection
    {
        return $this->styles;
    }

    public function addStyle(Style $style): self
    {
        if (!$this->styles->contains($style)) {
            $this->styles[] = $style;
        }

        return $this;
    }

    public function removeStyle(Style $style): self
    {
        if ($this->styles->contains($style)) {
            $this->styles->removeElement($style);
        }

        return $this;
    }

    /**
     * @return Collection|Couleur[]
     */
    public function getCouleurs(): Collection
    {
        return $this->couleurs;
    }

    public function addCouleur(Couleur $couleur): self
    {
        if (!$this->couleurs->contains($couleur)) {
            $this->couleurs[] = $couleur;
        }

        return $this;
    }

    public function removeCouleur(Couleur $couleur): self
    {
        if ($this->couleurs->contains($couleur)) {
            $this->couleurs->removeElement($couleur);
        }

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Mois[]
     */
    public function getMois(): Collection
    {
        return $this->mois;
    }

    public function addMois(Mois $mois): self
    {
        if (!$this->mois->contains($mois)) {
            $this->mois[] = $mois;
        }

        return $this;
    }

    public function removeMois(Mois $mois): self
    {
        if ($this->mois->contains($mois)) {
            $this->mois->removeElement($mois);
        }

        return $this;
    }

    /**
     * @return Collection|Ensoleillement[]
     */
    public function getEnsoleillements(): Collection
    {
        return $this->ensoleillements;
    }

    public function addEnsoleillement(Ensoleillement $ensoleillement): self
    {
        if (!$this->ensoleillements->contains($ensoleillement)) {
            $this->ensoleillements[] = $ensoleillement;
        }

        return $this;
    }

    public function removeEnsoleillement(Ensoleillement $ensoleillement): self
    {
        if ($this->ensoleillements->contains($ensoleillement)) {
            $this->ensoleillements->removeElement($ensoleillement);
        }

        return $this;
    }

    /**
     * @return Collection|TypeSol[]
     */
    public function getTypeSols(): Collection
    {
        return $this->typeSols;
    }

    public function addTypeSol(TypeSol $typeSol): self
    {
        if (!$this->typeSols->contains($typeSol)) {
            $this->typeSols[] = $typeSol;
        }

        return $this;
    }

    public function removeTypeSol(TypeSol $typeSol): self
    {
        if ($this->typeSols->contains($typeSol)) {
            $this->typeSols->removeElement($typeSol);
        }

        return $this;
    }

    public function getEntite(): ?self
    {
        return $this->entite;
    }

    public function setEntite(?self $entite): self
    {
        $this->entite = $entite;

        // set (or unset) the owning side of the relation if necessary
        $newEntiteSelf = null === $entite ? null : $this;
        if ($entite !== null && $entite->getEntiteSelf() !== $newEntiteSelf) {
            $entite->setEntiteSelf($newEntiteSelf);
        }

        return $this;
    }

    /**
     * @return Collection|CreationEntite[]
     */
    public function getCreationEntites(): Collection
    {
        return $this->creationEntites;
    }

    public function addCreationEntite(CreationEntite $creationEntite): self
    {
        if (!$this->creationEntites->contains($creationEntite)) {
            $this->creationEntites[] = $creationEntite;
            $creationEntite->setEntite($this);
        }

        return $this;
    }

    public function removeCreationEntite(CreationEntite $creationEntite): self
    {
        if ($this->creationEntites->contains($creationEntite)) {
            $this->creationEntites->removeElement($creationEntite);
            // set the owning side to null (unless already changed)
            if ($creationEntite->getEntite() === $this) {
                $creationEntite->setEntite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CompositionEntite[]
     */
    public function getCompositionEntites(): Collection
    {
        return $this->compositionEntites;
    }

    public function addCompositionEntite(CompositionEntite $compositionEntite): self
    {
        if (!$this->compositionEntites->contains($compositionEntite)) {
            $this->compositionEntites[] = $compositionEntite;
            $compositionEntite->setEntite($this);
        }

        return $this;
    }

    public function removeCompositionEntite(CompositionEntite $compositionEntite): self
    {
        if ($this->compositionEntites->contains($compositionEntite)) {
            $this->compositionEntites->removeElement($compositionEntite);
            // set the owning side to null (unless already changed)
            if ($compositionEntite->getEntite() === $this) {
                $compositionEntite->setEntite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matiere[]
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matiere $matiere): self
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres[] = $matiere;
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): self
    {
        if ($this->matieres->contains($matiere)) {
            $this->matieres->removeElement($matiere);
        }

        return $this;
    }

    /**
     * @return Collection|Couleur[]
     */
    public function getCouleurFleurs(): Collection
    {
        return $this->couleurFleurs;
    }

    public function addCouleurFleur(Couleur $couleurFleur): self
    {
        if (!$this->couleurFleurs->contains($couleurFleur)) {
            $this->couleurFleurs[] = $couleurFleur;
        }

        return $this;
    }

    public function removeCouleurFleur(Couleur $couleurFleur): self
    {
        if ($this->couleurFleurs->contains($couleurFleur)) {
            $this->couleurFleurs->removeElement($couleurFleur);
        }

        return $this;
    }

    /**
     * @return Collection|CreationType[]
     */
    public function getCreationTypes(): Collection
    {
        return $this->creationTypes;
    }

    public function addCreationType(CreationType $creationType): self
    {
        if (!$this->creationTypes->contains($creationType)) {
            $this->creationTypes[] = $creationType;
        }

        return $this;
    }

    public function removeCreationType(CreationType $creationType): self
    {
        if ($this->creationTypes->contains($creationType)) {
            $this->creationTypes->removeElement($creationType);
        }

        return $this;
    }

    public function getPhotoPrincipale()
    {
        $photo = null;
        foreach ($this->getEntitePhotos() as $entitePhoto) {
            if (is_null($photo) || ($entitePhoto->getPrincipale() && $entitePhoto->getPhoto() != 'rien.png')) {
                $photo = $entitePhoto;
            }
        }

        return $photo;
    }

    public function getLasso(): ?bool
    {
        return $this->lasso;
    }

    public function setLasso(bool $lasso): self
    {
        $this->lasso = $lasso;

        return $this;
    }

    public function getLassoPhoto(): ?string
    {
        return $this->lassoPhoto;
    }

    public function setLassoPhoto(?string $lassoPhoto): self
    {
        $this->lassoPhoto = $lassoPhoto;

        return $this;
    }

    public function getLassoPhotoHauteur(): ?string
    {
        return $this->lassoPhotoHauteur;
    }

    public function setLassoPhotoHauteur(?string $lassoPhotoHauteur): self
    {
        $this->lassoPhotoHauteur = $lassoPhotoHauteur;

        return $this;
    }

    public function getLassoPhotoLargeur(): ?string
    {
        return $this->lassoPhotoLargeur;
    }

    public function setLassoPhotoLargeur(?string $lassoPhotoLargeur): self
    {
        $this->lassoPhotoLargeur = $lassoPhotoLargeur;

        return $this;
    }

    /**
     * Sets fileLasso
     *
     * @param UploadedFile $fileLasso
     */
    public function setFileLasso(?UploadedFile $fileLasso = null)
    {
        $this->fileLasso = $fileLasso;
    }

    /**
     * Get fileLasso
     *
     * @return UploadedFile
     */
    public function getFileLasso(): ?UploadedFile
    {
        return $this->fileLasso;
    }

    /**
     * Get Dimension Entite (Human size)
     *
     * @return int
     */
    public function getDimensions()
    {
        $dimension = '';

        if ($this->getDiametrePot() > 0 && $this->getDiametrePot() < 100) {
            $dimension .= (!empty($dimension) ? 'x' : '') . $this->getDiametrePot() . 'cm';
        } else {
            if ($this->getDiametrePot() > 0) {
                $m = intval($this->getDiametrePot() / 100);
                $cm = intval($this->getDiametrePot() - ($m * 100));
                $dimension .= (!empty($dimension) ? 'x' : '') . $m . 'm' . (!empty($cm) ? sprintf("%02d", $cm) : '');
            }
        }

        if ($this->getHauteurPot() > 0 && $this->getHauteurPot() < 100) {
            $dimension .= (!empty($dimension) ? 'x' : '') . $this->getHauteurPot() . 'cm';
        } else {
            if ($this->getHauteurPot() > 0) {
                $m = intval($this->getHauteurPot() / 100);
                $cm = intval($this->getHauteurPot() - ($m * 100));
                $dimension .= (!empty($dimension) ? 'x' : '') . $m . 'm' . (!empty($cm) ? sprintf("%02d", $cm) : '');
            }
        }

        return $dimension;
    }

    /* Retroune le entitePhoto de la vue correspondante de l'entite */
    public function getCompositionPhoto($CompositionVueId)
    {
        foreach ($this->getEntitePhotos() as $entitePhoto) {
            if ($entitePhoto->getCompositionVue()->getId() == $CompositionVueId) {
                $compositionPhoto = $entitePhoto;
            }
        }

        return $compositionPhoto;
    }

    /* Retourne la hauteur pot en cm ou m*/
    public function getHauteurPotAvecUnite()
    {
        if ($this->getHauteurPot() > 0 && $this->getHauteurPot() < 100) {
            return $this->getHauteurPot() . 'cm';
        } else {
            if ($this->getHauteurPot() > 0) {
                $m = intval($this->getHauteurPot() / 100);
                $cm = intval($this->getHauteurPot() - ($m * 100));

                return $m . 'm' . (!empty($cm) ? $cm : '');
            }
        }
    }

    /* Retourne le diametre pot en cm ou m*/
    public function getDiametrePotAvecUnite()
    {
        if ($this->getDiametrePot() > 0 && $this->getDiametrePot() < 100) {
            return $this->getDiametrePot() . 'cm';
        } else {
            if ($this->getDiametrePot() > 0) {
                $m = intval($this->getDiametrePot() / 100);
                $cm = intval($this->getDiametrePot() - ($m * 100));

                return $m . 'm' . (!empty($cm) ? $cm : '');
            }
        }
    }

    public function getMoisOrderByIdAsc()
    {
        if (count($this->mois) > 0) {
            $moisTemp = new ArrayCollection();
            $moisOrder = array();
            foreach ($this->mois as $mois) {
                $moisOrder[$mois->getId()] = $mois;
            }
            ksort($moisOrder);
            foreach ($moisOrder as $mois) {
                $moisTemp->add($mois);
            }
            $tabMois = $moisTemp;
        } else {
            $tabMois = $this->mois;
        }

        return $tabMois;
    }

    public function getAbsolutePath()
    {
        return null === $this->getLassoPhoto() ? null : $this->getUploadRootDir() . '/' . $this->getLassoPhoto();
    }

    public function getTmpAbsolutePath()
    {
        return null === $this->getLassoPhoto() ? null : $this->getTmpUploadRootDir() . '/' . $this->getLassoPhoto();
    }

    public function getWebPath()
    {
        return null === $this->getLassoPhoto() ? null : $this->getUploadDir() . '/' . $this->getId(
            ) . '/' . $this->getLassoPhoto();
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getUploadDir() . '/' . $this->getId();
    }

    protected function getTmpUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../public' . $this->getTmpUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return '/uploads/entite_lasso';
    }

    protected function getTmpUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return $this->getUploadDir() . '/tmp';
    }

    public function preUpload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->fileLasso) {
            return;
        }
        $this->removeUpload();
        $nomfileSlug = Slug::slug('lasso_photo');
        $this->setLassoPhoto(time() . '-' . $nomfileSlug . '.' . $this->getFileLasso()->guessExtension());

        if (!$this->id) {
            $this->getFileLasso()->move($this->getTmpUploadRootDir(), $this->getLassoPhoto());
            $imageSize = getimagesize($this->getTmpAbsolutePath());
        } else {
            $this->getFileLasso()->move($this->getUploadRootDir(), $this->getLassoPhoto());
            $imageSize = getimagesize($this->getAbsolutePath());
        }
        if (!empty($imageSize)) {
            $this->setLassoPhotoLargeur($imageSize[0]);
            $this->setLassoPhotoHauteur($imageSize[1]);
        }
        // clean up the file property as you won't need it anymore
        $this->setFileLasso(null);
    }

    public function moveUpload()
    {
        if (null === $this->getLassoPhoto()) {
            return;
        }
        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0777, true);
        }
        if (file_exists($this->getTmpAbsolutePath())) {
            copy($this->getTmpAbsolutePath(), $this->getAbsolutePath());
            unlink($this->getTmpAbsolutePath());
        }
    }

    public function removeUpload()
    {
        if ($this->getLassoPhoto() && file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }
        if (!is_null($this->getId()) && file_exists($this->getUploadRootDir())) {
            rmdir($this->getUploadRootDir());
        }
    }

    public function testMkdirUpload()
    {
        if (!is_dir($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0777, true);
        }
    }

    public function getPictoNouveau(): bool
    {
        return $this->pictoNouveau;
    }

    public function getDateDebutPictoNouveau(): ?DateTime
    {
        return $this->dateDebutPictoNouveau;
    }

    public function getDateFinPictoNouveau(): ?DateTime
    {
        return $this->dateFinPictoNouveau;
    }

    public function hasPictoNouveau(): bool
    {
        $dateCourante = new DateTime();
        $hasPicto = false;

        $dateFinPictoNouveau = $this->getDateFinPictoNouveau();
        if ($dateFinPictoNouveau !== null) {
            $timeString = $this->getDateFinPictoNouveau()->format('Y') . '-' . $this->getDateFinPictoNouveau()->format(
                    'm'
                ) . '-' . $this->getDateFinPictoNouveau()->format('d') . ' 23:59:00';
            $dateFinPictoNouveau = DateTime::createFromFormat('Y-m-d H:i:s', $timeString);
        }

        if ($this->pictoNouveau == true) {
            $hasPicto = true;
            if ($this->getDateDebutPictoNouveau() !== null && $this->getDateDebutPictoNouveau()->format(
                    'U'
                ) > $dateCourante->format('U')) {
                $hasPicto = false;
            }

            if ($dateFinPictoNouveau !== null && $dateFinPictoNouveau->format('U') < $dateCourante->format('U')) {
                $hasPicto = false;
            }
        }

        return $hasPicto;
    }

    public function getPictoPromo(): bool
    {
        return $this->pictoPromo;
    }

    public function getDateDebutPictoPromo(): ?DateTime
    {
        return $this->dateDebutPictoPromo;
    }

    public function getDateFinPictoPromo(): ?DateTime
    {
        return $this->dateFinPictoPromo;
    }

    public function hasPictoPromo(): bool
    {
        $dateCourante = new DateTime();
        $hasPicto = false;

        $dateFinPictoPromo = $this->getDateFinPictoPromo();
        if ($dateFinPictoPromo !== null) {
            $timeString = $this->getDateFinPictoPromo()->format('Y') . '-' . $this->getDateFinPictoPromo()->format(
                    'm'
                ) . '-' . $this->getDateFinPictoPromo()->format('d') . ' 23:59:00';
            $dateFinPictoPromo = DateTime::createFromFormat('Y-m-d H:i:s', $timeString);
        }

        if ($this->pictoPromo == true) {
            $hasPicto = true;
            if ($this->getDateDebutPictoPromo() !== null && $this->getDateDebutPictoPromo()->format(
                    'U'
                ) > $dateCourante->format('U')) {
                $hasPicto = false;
            }

            if ($dateFinPictoPromo !== null && $dateFinPictoPromo->format('U') < $dateCourante->format('U')) {
                $hasPicto = false;
            }
        }

        return $hasPicto;
    }

    public function getPictoCoupCoeur(): bool
    {
        return $this->pictoCoupCoeur;
    }

    public function getDateDebutPictoCoupCoeur(): ?DateTime
    {
        return $this->dateDebutPictoCoupCoeur;
    }

    public function getDateFinPictoCoupCoeur(): ?DateTime
    {
        return $this->dateFinPictoCoupCoeur;
    }

    public function hasPictoCoupCoeur(): bool
    {
        $dateCourante = new DateTime();
        $hasPicto = false;

        $dateFinPictoCoupCoeur = $this->getDateFinPictoCoupCoeur();
        if ($dateFinPictoCoupCoeur !== null) {
            $timeString = $this->getDateFinPictoCoupCoeur()->format('Y') . '-' . $this->getDateFinPictoCoupCoeur(
                )->format('m') . '-' . $this->getDateFinPictoCoupCoeur()->format('d') . ' 23:59:00';
            $dateFinPictoCoupCoeur = DateTime::createFromFormat('Y-m-d H:i:s', $timeString);
        }

        if ($this->pictoCoupCoeur == true) {
            $hasPicto = true;
            if ($this->getDateDebutPictoCoupCoeur() !== null && $this->getDateDebutPictoCoupCoeur()->format(
                    'U'
                ) > $dateCourante->format('U')) {
                $hasPicto = false;
            }

            if ($dateFinPictoCoupCoeur !== null && $dateFinPictoCoupCoeur->format('U') < $dateCourante->format('U')) {
                $hasPicto = false;
            }
        }

        return $hasPicto;
    }

    public function setPictoNouveau(bool $pictoNouveau)
    {
        $this->pictoNouveau = $pictoNouveau;
        return $this;
    }

    public function setDateDebutPictoNouveau(?DateTime $dateDebutPictoNouveau)
    {
        $this->dateDebutPictoNouveau = $dateDebutPictoNouveau;
        return $this;
    }

    public function setDateFinPictoNouveau(?DateTime $dateFinPictoNouveau)
    {
        $this->dateFinPictoNouveau = $dateFinPictoNouveau;
        return $this;
    }

    public function setPictoPromo(bool $pictoPromo)
    {
        $this->pictoPromo = $pictoPromo;
        return $this;
    }

    public function setDateDebutPictoPromo(?DateTime $dateDebutPictoPromo)
    {
        $this->dateDebutPictoPromo = $dateDebutPictoPromo;
        return $this;
    }

    public function setDateFinPictoPromo(?DateTime $dateFinPictoPromo)
    {
        $this->dateFinPictoPromo = $dateFinPictoPromo;
        return $this;
    }

    public function setPictoCoupCoeur(bool $pictoCoupCoeur)
    {
        $this->pictoCoupCoeur = $pictoCoupCoeur;
        return $this;
    }

    public function setDateDebutPictoCoupCoeur(?DateTime $dateDebutPictoCoupCoeur)
    {
        $this->dateDebutPictoCoupCoeur = $dateDebutPictoCoupCoeur;
        return $this;
    }

    public function setDateFinPictoCoupCoeur(?DateTime $dateFinPictoCoupCoeur)
    {
        $this->dateFinPictoCoupCoeur = $dateFinPictoCoupCoeur;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPictoNouveauComputedFlag(): bool
    {
        return $this->pictoNouveauComputedFlag;
    }

    /**
     * @param bool $pictoNouveauComputedFlag
     * @return Entite
     */
    public function setPictoNouveauComputedFlag(bool $pictoNouveauComputedFlag): Entite
    {
        $this->pictoNouveauComputedFlag = $pictoNouveauComputedFlag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPictoPromoComputedFlag(): bool
    {
        return $this->pictoPromoComputedFlag;
    }

    /**
     * @param bool $pictoPromoComputedFlag
     * @return Entite
     */
    public function setPictoPromoComputedFlag(bool $pictoPromoComputedFlag): Entite
    {
        $this->pictoPromoComputedFlag = $pictoPromoComputedFlag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPictoCoupCoeurComputedFlag(): bool
    {
        return $this->pictoCoupCoeurComputedFlag;
    }

    /**
     * @param bool $pictoCoupCoeurComputedFlag
     * @return Entite
     */
    public function setPictoCoupCoeurComputedFlag(bool $pictoCoupCoeurComputedFlag): Entite
    {
        $this->pictoCoupCoeurComputedFlag = $pictoCoupCoeurComputedFlag;
        return $this;
    }

    /**
     * @return Collection|Rusticite[]
     */
    public function getRusticiteMultiples(): Collection
    {
        return $this->rusticiteMultiples;
    }

    public function addRusticiteMultiple(Rusticite $rusticite): self
    {
        if (!$this->rusticiteMultiples->contains($rusticite)) {
            //$rusticite->setEntite($this);
            $this->rusticiteMultiples[] = $rusticite;
        }

        return $this;
    }

    public function removeRusticiteMultiple(Rusticite $rusticite): self
    {
        if ($this->rusticiteMultiples->contains($rusticite)) {
            $this->rusticiteMultiples->removeElement($rusticite);
        }

        return $this;
    }

    /**
     * @return Collection|BesoinEau[]
     */
    public function getBesoinEauMultiples(): Collection
    {
        return $this->besoinEauMultiples;
    }

    public function addBesoinEauMultiple(BesoinEau $besoinEau): self
    {
        if (!$this->besoinEauMultiples->contains($besoinEau)) {
            //$besoinEau->setEntite($this);
            $this->besoinEauMultiples[] = $besoinEau;
        }

        return $this;
    }

    public function removeBesoinEauMultiple(BesoinEau $besoinEau): self
    {
        if ($this->besoinEauMultiples->contains($besoinEau)) {
            $this->besoinEauMultiples->removeElement($besoinEau);
        }

        return $this;
    }

    /**
     * @return Collection|EntiteSousType[]
     */
    public function getEntiteSousTypeMultiples(): Collection
    {
        return $this->entiteSousTypeMultiples;
    }

    public function addEntiteSousTypeMultiple(EntiteSousType $entiteSousType): self
    {
        if (!$this->entiteSousTypeMultiples->contains($entiteSousType)) {
            //$entiteSousType->setEntite($this);
            $this->entiteSousTypeMultiples[] = $entiteSousType;
        }

        return $this;
    }

    public function removeEntiteSousTypeMultiple(EntiteSousType $entiteSousType): self
    {
        if ($this->entiteSousTypeMultiples->contains($entiteSousType)) {
            $this->entiteSousTypeMultiples->removeElement($entiteSousType);
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getImportedAt()
    {
        return $this->importedAt;
    }

    /**
     * @param null $importedAt
     */
    public function setImportedAt($importedAt): void
    {
        $this->importedAt = $importedAt;
    }

    public function getDisabledAtPreviousImport()
    {
        return $this->disabledAtPreviousImport;
    }

    public function setDisabledAtPreviousImport($disabledAtPreviousImport)
    {
        $this->disabledAtPreviousImport = $disabledAtPreviousImport;
        return $this;
    }

    /**
     * @return null
     */
    public function getDernierePhotoPrincipaleImporte()
    {
        return $this->dernierePhotoPrincipaleImporte;
    }

    /**
     * @param null $dernierePhotoPrincipaleImporte
     */
    public function setDernierePhotoPrincipaleImporte($dernierePhotoPrincipaleImporte)
    {
        $this->dernierePhotoPrincipaleImporte = $dernierePhotoPrincipaleImporte;
        return $this;
    }

    /**
     * @return null
     */
    public function getIdentifiantDernierImport()
    {
        return $this->identifiantDernierImport;
    }

    /**
     * @param null $identifiantDernierImport
     */
    public function setIdentifiantDernierImport($identifiantDernierImport)
    {
        $this->identifiantDernierImport = $identifiantDernierImport;
        return $this;
    }
    public function translate($propertyName, $locale, $translationContent) {
        $translationFound = false;

        foreach($this->getTranslations() as $translatedItem) {
            if($translatedItem->getLocale() == $locale && $translatedItem->getField() == $propertyName) {
                $translationFound = true;
                $translatedItem->setContent($translationContent);
            }
        }

        if($translationFound === false) {
            $translation = new \App\Core\Entity\Translation\Entite();
            $translation->setObject($this);
            $translation->setLocale($locale);
            $translation->setContent($translationContent);
            $translation->setField($propertyName);

            $this->getTranslations()->add($translation);
        }
    }

}
