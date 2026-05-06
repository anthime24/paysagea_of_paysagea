<?php

namespace App\Core\Entity;

use App\Core\Utility\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;

/**
 * Composition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\CompositionRepository")
 * @Gedmo\TranslationEntity(class="App\Core\Entity\Translation\Composition")
 */
class Composition extends AbstractPersonalTranslatable implements TranslatableInterface
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
     *     targetEntity="App\Core\Entity\Translation\Composition",
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
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoAvant;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoDroite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoGauche;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoArriere;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbCasesX;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbCasesY;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $espacementObjet;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteurCompositionAvant;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteurCompositionDroite;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteurCompositionGauche;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hauteurCompositionArriere;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $gratuit = false;

    /**
     * @var float
     *
     * @ORM\Column(type="float", options={"default" : 2})
     */
    private $hauteurFuite;

    /**
     * @var BesoinEau
     *
     * @ORM\ManyToOne(targetEntity="BesoinEau", inversedBy="compositions")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $besoinEau;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="BesoinEau", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="composition_besoin_eau",
     *      joinColumns={@ORM\JoinColumn(name="composition_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="besoin_eau_id", referencedColumnName="id")}
     * )
     */
    private $besoinEauMultiples;

    /**
     * @var Entretien
     *
     * @ORM\ManyToOne(targetEntity="Entretien", inversedBy="compositions")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $entretien;

    /**
     * @var Rusticite
     *
     * @ORM\ManyToOne(targetEntity="Rusticite", inversedBy="compositions")
     */
    private $rusticite;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Rusticite", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="composition_rusticite",
     *      joinColumns={@ORM\JoinColumn(name="composition_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="rusticite_id", referencedColumnName="id")}
     * )
     */
    private $rusticiteMultiples;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CompositionEntite", mappedBy="composition")
     */
    private $compositionEntites;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CreationEntite", mappedBy="composition")
     */
    private $creationEntites;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Style", inversedBy="compositions")
     * @ORM\OrderBy({"nom"="ASC"})
     */
    private $styles;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Categorie", inversedBy="compositions")
     * @ORM\OrderBy({"nom"="ASC"})
     */
    private $categories;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Ensoleillement", inversedBy="compositions")
     * @ORM\OrderBy({"nom"="ASC"})
     */
    private $ensoleillements;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="TypeSol", inversedBy="compositions")
     * @ORM\OrderBy({"nom"="ASC"})
     */
    private $typeSols;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CreationType", inversedBy="compositions")
     * @ORM\JoinTable(name="creation_type_composition",
     *      joinColumns={@ORM\JoinColumn(name="composition_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="creation_type_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $creationTypes;

    /**
     * @var Entite
     *
     * @ORM\OneToOne(targetEntity="Entite", mappedBy="composition")
     */
    private $entite;

    public function __construct()
    {
        $this->compositionEntites = new ArrayCollection();
        $this->creationEntites = new ArrayCollection();
        $this->styles = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->ensoleillements = new ArrayCollection();
        $this->typeSols = new ArrayCollection();
        $this->creationTypes = new ArrayCollection();
        $this->rusticiteMultiples = new ArrayCollection();
        $this->besoinEauMultiples = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPhotoAvant(): ?string
    {
        return $this->photoAvant;
    }

    public function setPhotoAvant(?string $photoAvant): self
    {
        $this->photoAvant = $photoAvant;

        return $this;
    }

    public function getPhotoDroite(): ?string
    {
        return $this->photoDroite;
    }

    public function setPhotoDroite(?string $photoDroite): self
    {
        $this->photoDroite = $photoDroite;

        return $this;
    }

    public function getPhotoGauche(): ?string
    {
        return $this->photoGauche;
    }

    public function setPhotoGauche(?string $photoGauche): self
    {
        $this->photoGauche = $photoGauche;

        return $this;
    }

    public function getPhotoArriere(): ?string
    {
        return $this->photoArriere;
    }

    public function setPhotoArriere(?string $photoArriere): self
    {
        $this->photoArriere = $photoArriere;

        return $this;
    }

    public function getNbCasesX(): ?int
    {
        return $this->nbCasesX;
    }

    public function setNbCasesX(?int $nbCasesX): self
    {
        $this->nbCasesX = $nbCasesX;

        return $this;
    }

    public function getNbCasesY(): ?int
    {
        return $this->nbCasesY;
    }

    public function setNbCasesY(?int $nbCasesY): self
    {
        $this->nbCasesY = $nbCasesY;

        return $this;
    }

    public function getEspacementObjet(): ?int
    {
        return $this->espacementObjet;
    }

    public function setEspacementObjet(?int $espacementObjet): self
    {
        $this->espacementObjet = $espacementObjet;

        return $this;
    }

    public function getHauteurCompositionAvant(): ?int
    {
        return $this->hauteurCompositionAvant;
    }

    public function setHauteurCompositionAvant(?int $hauteurCompositionAvant): self
    {
        $this->hauteurCompositionAvant = $hauteurCompositionAvant;

        return $this;
    }

    public function getHauteurCompositionDroite(): ?int
    {
        return $this->hauteurCompositionDroite;
    }

    public function setHauteurCompositionDroite(?int $hauteurCompositionDroite): self
    {
        $this->hauteurCompositionDroite = $hauteurCompositionDroite;

        return $this;
    }

    public function getHauteurCompositionGauche(): ?int
    {
        return $this->hauteurCompositionGauche;
    }

    public function setHauteurCompositionGauche(?int $hauteurCompositionGauche): self
    {
        $this->hauteurCompositionGauche = $hauteurCompositionGauche;

        return $this;
    }

    public function getHauteurCompositionArriere(): ?int
    {
        return $this->hauteurCompositionArriere;
    }

    public function setHauteurCompositionArriere(?int $hauteurCompositionArriere): self
    {
        $this->hauteurCompositionArriere = $hauteurCompositionArriere;

        return $this;
    }

    public function getGratuit(): ?bool
    {
        return $this->gratuit;
    }

    public function setGratuit(?bool $gratuit): self
    {
        $this->gratuit = $gratuit;

        return $this;
    }

    public function getHauteurFuite(): ?float
    {
        return $this->hauteurFuite;
    }

    public function setHauteurFuite(float $hauteurFuite): self
    {
        $this->hauteurFuite = $hauteurFuite;

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
            $compositionEntite->setComposition($this);
        }

        return $this;
    }

    public function removeCompositionEntite(CompositionEntite $compositionEntite): self
    {
        if ($this->compositionEntites->contains($compositionEntite)) {
            $this->compositionEntites->removeElement($compositionEntite);
            // set the owning side to null (unless already changed)
            if ($compositionEntite->getComposition() === $this) {
                $compositionEntite->setComposition(null);
            }
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
            $creationEntite->setComposition($this);
        }

        return $this;
    }

    public function removeCreationEntite(CreationEntite $creationEntite): self
    {
        if ($this->creationEntites->contains($creationEntite)) {
            $this->creationEntites->removeElement($creationEntite);
            // set the owning side to null (unless already changed)
            if ($creationEntite->getComposition() === $this) {
                $creationEntite->setComposition(null);
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

    public function getEntite(): ?Entite
    {
        return $this->entite;
    }

    public function setEntite(?Entite $entite): self
    {
        $this->entite = $entite;

        // set (or unset) the owning side of the relation if necessary
        $newComposition = null === $entite ? null : $this;
        if ($entite && $entite->getComposition() !== $newComposition) {
            $entite->setComposition($newComposition);
        }

        return $this;
    }

    public function compositionEntitesAsJson()
    {
        $json = array();
        foreach ($this->getCompositionEntites() as $compositionEntite) {
            $json[] = $compositionEntite->toArray();
        }

        return json_encode($json);
    }

    /**
     * Génération des images de rendu de la composition (avant, arrière, gauche, droite)
     */
    public function generateImages()
    {
        // On récupère les informations des entités une fois pour générer les quatre vues
        $entites = array();

        $hauteurMaxEntites = 0;
        foreach ($this->getCompositionEntites() as $compositionEntite) {
            if ($compositionEntite->getEntite()->getPhotoPrincipale()) {
                $photo = $compositionEntite->getEntite()->getPhotoPrincipale();

                $hauteurMaxEntites = $hauteurMaxEntites < $photo->getHauteurEntite() ? $photo->getHauteurEntite(
                ) : $hauteurMaxEntites;
                $entites[$compositionEntite->getPositionY()][$compositionEntite->getPositionX()] = array(
                    'path' => $photo->getAbsolutePath(),
                    'hauteur_entite' => $photo->getHauteurEntite(),
                    'diametre_entite' => $photo->getDiametreEntite(),
                    'largeur' => $photo->getLargeur(),
                    'hauteur' => $photo->getHauteur(),
                );
            }
        }

        $references = $this->recupererReferences();
        $vues = $references['vues'];

        if (!empty($entites)) {
            // On parcourt les vues
            foreach ($vues as $vueCle => $vue) {
                $imagesEntites = array();
                $i = 1;

                // On détermine les tailles des entités suivant la vue
                $compositionLargeur = $vue['largeur'];
                $compositionHauteur = $vue['hauteur'];
                $compositionHauteurPx = 0;
                $decalageX = 0;
                $decalageY = 0;

                $entiteY = $vue['reperes']['haut_gauche']['y']; // $references['reperes'][$vue['haut_gauche']]['y'];
                $y = $vue['y'];
                $yMax = $vue['y_max'] > $vue['y'] ? ($vue['y_max'] + 1) : ($vue['y_max'] - 1);
                $espaceY = floor(
                    ($vue['reperes']['bas_gauche']['y'] - $vue['reperes']['haut_gauche']['y']) / (($vue['y_columns'] > 1) ? ($vue['y_columns'] - 1) : $vue['y_columns'])
                );

                while ($y != $yMax) {
                    if ($y == $vue['y']) {
                        $entiteXStart = $vue['reperes']['haut_gauche']['x'];
                    } else {
                        $entiteXStart -= abs(
                            ($vue['reperes']['haut_gauche']['x'] - $vue['reperes']['bas_gauche']['x']) / $vue['x_columns']
                        );
                    }

                    $espaceX = ($vue['largeur'] - ($entiteXStart * 2)) / (($vue['x_columns'] > 1) ? ($vue['x_columns'] - 1) : $vue['x_columns']);

                    $x = $vue['x'];
                    $xMax = $vue['x_max'] > $vue['x'] ? ($vue['x_max'] + 1) : ($vue['x_max'] - 1);
                    $entiteX = $entiteXStart;

                    while ($x != $xMax) {
                        $entite = null;
                        if (in_array($vueCle, array('avant', 'arriere')) && !empty($entites[$y][$x])) {
                            $entite = $entites[$y][$x];
                        } else {
                            if (in_array($vueCle, array('droite', 'gauche')) && !empty($entites[$x][$y])) {
                                $entite = $entites[$x][$y];
                            }
                        }

                        if (!empty($entite)) {
                            if (file_exists($entite['path']) && is_file($entite['path'])) {
                                $imageEntiteTaille = $this->recupererTailleEntite(
                                    $vueCle,
                                    $entiteX,
                                    $entiteY,
                                    $entite['hauteur_entite'],
                                    $entite['largeur'],
                                    $entite['hauteur']
                                );
                                $imagesEntites[$i]['path'] = $entite['path'];
                                $imagesEntites[$i]['x'] = ceil($entiteX - ($imageEntiteTaille['width'] / 2));
                                $imagesEntites[$i]['y'] = ceil($entiteY - $imageEntiteTaille['height']);
                                $imagesEntites[$i]['width'] = $imageEntiteTaille['width'];
                                $imagesEntites[$i]['height'] = $imageEntiteTaille['height'];

                                // Si l'image sort du plan en hauteur alors on agrandit la taille finale de l'image
                                if ($imagesEntites[$i]['y'] < 0 && abs($imagesEntites[$i]['y']) > $decalageY) {
                                    $compositionHauteur += ceil(abs($imagesEntites[$i]['y']));
                                    $decalageY += ceil(abs($imagesEntites[$i]['y']));
                                }

                                // Si l'image sort du plan en largeur alors on agrandit la taille finale de l'image
                                if ($imagesEntites[$i]['x'] < 0 && abs($imagesEntites[$i]['x']) > $decalageX) {
                                    $compositionLargeur += ceil(abs($imagesEntites[$i]['x']));
                                    $decalageX += ceil(abs($imagesEntites[$i]['x']));
                                }
                                if ($imagesEntites[$i]['x'] + $imagesEntites[$i]['width'] > ($compositionLargeur - $decalageX)) {
                                    $compositionLargeur += ceil(
                                        abs($imagesEntites[$i]['width'] + $imagesEntites[$i]['x'] - $vue['largeur'])
                                    );
                                }

                                if ($compositionHauteurPx < ($vue['hauteur'] - $imagesEntites[$i]['y'])) {
                                    $compositionHauteurPx = $vue['hauteur'] - $imagesEntites[$i]['y'];
                                }

                                $i++;
                            }
                        }
                        $entiteX += $espaceX;
                        $x = $vue['x_max'] > $vue['x'] ? ($x + 1) : ($x - 1);
                    }
                    $entiteY += $espaceY;
                    $y = $vue['y_max'] > $vue['y'] ? ($y + 1) : ($y - 1);
                }

                // Création de l'image de la composition
                $compositionLargeur = ceil($compositionLargeur);
                $decalageY = $decalageY - ($compositionHauteur - $compositionHauteurPx);
                $compositionHauteur = ceil($compositionHauteurPx);

                $compositionImage = imagecreatetruecolor($compositionLargeur, $compositionHauteur);
                imagealphablending($compositionImage, false);
                $color = imagecolorallocatealpha($compositionImage, 255, 255, 255, 127);
                imagefilledrectangle($compositionImage, 0, 0, $compositionLargeur, $compositionHauteur, $color);
                imagealphablending($compositionImage, true);

                foreach ($imagesEntites as $key => $imageEntite) {
                    $ie = Image::imageCreateFromAny($imageEntite['path']);

                    if ($ie != null) {
                        imagecopyresampled(
                            $compositionImage,
                            $ie,
                            intval($decalageX + $imageEntite['x']),
                            intval($decalageY + $imageEntite['y']),
                            0,
                            0,
                            $imageEntite['width'],
                            $imageEntite['height'],
                            imagesx($ie),
                            imagesy($ie)
                        );
                        //imagealphablending($compositionImage, true);
                        imagedestroy($ie);
                    }
                }
                imagealphablending($compositionImage, false);
                imagesavealpha($compositionImage, true);

                // On sauvegarde l'image de la vue de la composition
                $this->setPhoto($vueCle);
                $hauteur = ceil(($compositionHauteurPx * 170) / $vue['reperes']['bas_gauche']['hauteur']);
                $hauteur = ($vue['y_columns'] > 1 && $hauteurMaxEntites > 0) ? $hauteur : $hauteurMaxEntites;
                $this->setHauteurComposition($vueCle, $hauteur);
                imagepng($compositionImage, $this->getAbsolutePath($vueCle), 0);
                imagedestroy($compositionImage);
            }
        }
    }

    public function getAbsolutePath($vue = 'avant')
    {
        return $this->getUploadRootDir() . '/' . $this->getPhoto($vue);
    }

    public function setPhoto($vue)
    {
        $photo = time() . '-composition-';

        switch ($vue) {
            case 'arriere':
                $photo = $this->setPhotoArriere($photo . 'arriere.png');
                break;
            case 'droite':
                $photo = $this->setPhotoDroite($photo . 'droite.png');
                break;
            case 'gauche':
                $photo = $this->setPhotoGauche($photo . 'gauche.png');
                break;
            case 'avant':
                $photo = $this->setPhotoAvant($photo . 'avant.png');
                break;
        }
    }

    public function getPhoto($vue = 'avant')
    {
        $photo = null;

        switch ($vue) {
            case 'arriere':
                $photo = $this->getPhotoArriere();
                break;
            case 'droite':
                $photo = $this->getPhotoDroite();
                break;
            case 'gauche':
                $photo = $this->getPhotoGauche();
                break;

            default:
                $photo = $this->getPhotoAvant();
                break;
        }

        return $photo;
    }

    public function setHauteurComposition($vue, $hauteur)
    {
        switch ($vue) {
            case 'arriere':
                $hauteur = $this->setHauteurCompositionArriere($hauteur);
                break;
            case 'droite':
                $hauteur = $this->setHauteurCompositionDroite($hauteur);
                break;
            case 'gauche':
                $hauteur = $this->setHauteurCompositionGauche($hauteur);
                break;
            case 'avant':
                $hauteur = $this->setHauteurCompositionAvant($hauteur);
                break;
        }
    }

    public function getHauteurComposition($vue = 'avant')
    {
        $hauteurComposition = null;

        switch ($vue) {
            case 'arriere':
                $hauteurComposition = $this->getHauteurCompositionArriere();
                break;
            case 'droite':
                $hauteurComposition = $this->getHauteurCompositionDroite();
                break;
            case 'gauche':
                $hauteurComposition = $this->getHauteurCompositionGauche();
                break;

            default:
                $hauteurComposition = $this->getHauteurCompositionAvant();
                break;
        }

        return $hauteurComposition;
    }

    public function getWebPath($vue = 'avant')
    {
        return $this->getUploadDir() . '/' . $this->getPhoto($vue);
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        $dir = __DIR__ . '/../../../public' . $this->getUploadDir();

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return '/uploads/composition/' . $this->getId();
    }

    /**
     * Retourne le plan de référence pour générer les compositions
     * @return array
     */
    private function recupererReferences()
    {
        $reperes = array(
            // Bas gauche
            'bas_gauche' => array(
                'largeur' => 666,
                'hauteur' => 2000
            ),
            // Bas droit
            'bas_droit' => array(
                'largeur' => 666,
                'hauteur' => 2000
            ),
            // Haut gauche (arrière plan)
            'haut_gauche' => array(
                'largeur' => 402,
                'hauteur' => 1200
            ),
            // Haut droit (arrière plan)
            'haut_droit' => array(
                'largeur' => 402,
                'hauteur' => 1200
            ),
        );

        $hauteur = $this->nbCasesY * (($this->espacementObjet * $reperes['bas_gauche']['hauteur']) / 170);
        $largeur = $this->nbCasesX * (($this->espacementObjet * $reperes['bas_gauche']['hauteur']) / 170);

        return array(
            'vues' => array(
                'avant' => array(
                    'y' => 1,
                    'x' => 1,
                    'y_max' => $this->nbCasesY,
                    'x_max' => $this->nbCasesX,
                    'x_columns' => $this->nbCasesX,
                    'y_columns' => $this->nbCasesY,
                    'largeur' => $largeur,
                    'hauteur' => $hauteur,
                    'reperes' => array(
                        'bas_gauche' => array_merge(
                            $reperes['bas_gauche'],
                            array(
                                'x' => 0,
                                'y' => $hauteur
                            )
                        ),
                        'bas_droit' => array_merge(
                            $reperes['bas_droit'],
                            array(
                                'x' => $largeur,
                                'y' => $hauteur
                            )
                        ),
                        'haut_gauche' => array_merge(
                            $reperes['haut_gauche'],
                            array(
                                'x' => ceil($largeur / 4),
                                'y' => ceil($hauteur / $this->hauteurFuite)
                            )
                        ),
                        'haut_droit' => array_merge(
                            $reperes['haut_droit'],
                            array(
                                'x' => ceil($largeur - ($largeur / 4)),
                                'y' => ceil($hauteur / $this->hauteurFuite)
                            )
                        ),
                    )
                ),
                'arriere' => array(
                    'y' => $this->nbCasesY,
                    'x' => $this->nbCasesX,
                    'y_max' => 1,
                    'x_max' => 1,
                    'x_columns' => $this->nbCasesX,
                    'y_columns' => $this->nbCasesY,
                    'largeur' => $largeur,
                    'hauteur' => $hauteur,
                    'reperes' => array(
                        'bas_gauche' => array_merge(
                            $reperes['bas_gauche'],
                            array(
                                'x' => 0,
                                'y' => $hauteur
                            )
                        ),
                        'bas_droit' => array_merge(
                            $reperes['bas_droit'],
                            array(
                                'x' => $largeur,
                                'y' => $hauteur
                            )
                        ),
                        'haut_gauche' => array_merge(
                            $reperes['haut_gauche'],
                            array(
                                'x' => ceil($largeur / 4),
                                'y' => ceil($hauteur / $this->hauteurFuite)
                            )
                        ),
                        'haut_droit' => array_merge(
                            $reperes['haut_droit'],
                            array(
                                'x' => ceil($largeur - ($largeur / 4)),
                                'y' => ceil($hauteur / $this->hauteurFuite)
                            )
                        ),
                    )
                ),
                'droite' => array(
                    'y' => 1,
                    'x' => $this->nbCasesY,
                    'y_max' => $this->nbCasesX,
                    'x_max' => 1,
                    'x_columns' => $this->nbCasesY,
                    'y_columns' => $this->nbCasesX,
                    'largeur' => $hauteur,
                    'hauteur' => $largeur,
                    'reperes' => array(
                        'bas_gauche' => array_merge(
                            $reperes['bas_gauche'],
                            array(
                                'x' => 0,
                                'y' => $largeur
                            )
                        ),
                        'bas_droit' => array_merge(
                            $reperes['bas_droit'],
                            array(
                                'x' => $hauteur,
                                'y' => $largeur
                            )
                        ),
                        'haut_gauche' => array_merge(
                            $reperes['haut_gauche'],
                            array(
                                'x' => ceil($hauteur / 4),
                                'y' => ceil($largeur / $this->hauteurFuite)
                            )
                        ),
                        'haut_droit' => array_merge(
                            $reperes['haut_droit'],
                            array(
                                'x' => ceil($hauteur - ($hauteur / 4)),
                                'y' => ceil($largeur / $this->hauteurFuite)
                            )
                        ),
                    )
                ),
                'gauche' => array(
                    'y' => $this->nbCasesX,
                    'x' => 1,
                    'y_max' => 1,
                    'x_max' => $this->nbCasesY,
                    'x_columns' => $this->nbCasesY,
                    'y_columns' => $this->nbCasesX,
                    'largeur' => $hauteur,
                    'hauteur' => $largeur,
                    'reperes' => array(
                        'bas_gauche' => array_merge(
                            $reperes['bas_gauche'],
                            array(
                                'x' => 0,
                                'y' => $largeur
                            )
                        ),
                        'bas_droit' => array_merge(
                            $reperes['bas_droit'],
                            array(
                                'x' => $hauteur,
                                'y' => $largeur
                            )
                        ),
                        'haut_gauche' => array_merge(
                            $reperes['haut_gauche'],
                            array(
                                'x' => ceil($hauteur / 4),
                                'y' => ceil($largeur / $this->hauteurFuite)
                            )
                        ),
                        'haut_droit' => array_merge(
                            $reperes['haut_droit'],
                            array(
                                'x' => ceil($hauteur - ($hauteur / 4)),
                                'y' => ceil($largeur / $this->hauteurFuite)
                            )
                        ),
                    )
                )
            )
        );
    }

    private function recupererTailleEntite($vue, $xO, $yO, $entityHeight, $entityImageWidth, $entityImageHeight)
    {
        // Distance (pythagore) = a² + b² = c²
        // Hauteur objet en pixel sur bonhomme = (Hauteur de l'objet en cm * Hauteur du bonhonne en pixel) /  Hauteur du bonhomme
        // Hauteur objet en pixel = (E(1,n)(Hauteur de l'objet en cm * Hauteur du bonhonne en pixel) /  (Distanvce du bonhomme en pixel * Hauteur du bonhomme))) / (E(1,n) 1 / Distance du bonhomme)
        $sommeHauteurOPixel = 0;
        $sommeDistanceBPixel = 0;
        $height = 0;
        $references = $this->recupererReferences();
        $reperes = $references['vues'][$vue]['reperes'];

        $mapping = array(1 => 'bas_gauche', 2 => 'bas_droit', 3 => 'haut_gauche', 4 => 'haut_droit');

        for ($i = 1; $i <= 4; $i++) {
            $xB = $reperes[$mapping[$i]]['x'];
            $yB = $reperes[$mapping[$i]]['y'];
            $dB = sqrt(pow(($xO - $xB), 2) + pow(($yO - $yB), 2));

            $dBBas = abs($references['vues'][$vue]['hauteur'] - $yB);
            $dOBas = abs($references['vues'][$vue]['hauteur'] - $yO);
            $dB = $dB * abs($dBBas - $dOBas);

            if ($dB == 0) {
                $height = $entityHeight * $reperes[$mapping[$i]]['hauteur'] / 170;
                break;
            } else {
                $sommeDistanceBPixel += 1 / $dB;
                $sommeHauteurOPixel += (($entityHeight * $reperes[$mapping[$i]]['hauteur']) / ($dB * 170));
            }
        }

        if ($height == 0) {
            $height = $sommeHauteurOPixel / $sommeDistanceBPixel;
        }
        $height = ceil($height);

        $pourcentage = ($height * 100) / $entityImageHeight;
        $width = ceil(($entityImageWidth * $pourcentage) / 100);

        return array('width' => intval($width), 'height' => intval($height));
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getCompositionEntites() as $compositionEntite) {
            $total += $compositionEntite->getEntite()->getPrixMini();
        }

        return $total;
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
}
