<?php

namespace App\Core\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Core\Repository\ClientRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("email")
 */
class Client implements UserInterface, Serializable, EquatableInterface
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateInscription;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $confirmer = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $tokenValidity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $creditPhotos = false;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $creditConseilsProfessionnel = false;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     */
    private $creditAidePaysagiste = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $accesCompletPlantesObjets = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $creationNonPublique = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typePersonne;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDerniereConnexion;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ClientOffre", mappedBy="client")
     */
    private $clientOffres;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ClientCodePromo", mappedBy="client")
     */
    private $clientCodePromos;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ClientPaiement", mappedBy="client")
     */
    private $clientPaiements;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="client")
     */
    private $projets;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="BanquePhoto", mappedBy="client")
     */
    private $banquePhotos;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $recevoirInformations = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $salt = $this->randomString(22, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./');
        $this->setSalt($salt);
        $this->clientOffres = new ArrayCollection();
        $this->clientCodePromos = new ArrayCollection();
        $this->clientPaiements = new ArrayCollection();
        $this->projets = new ArrayCollection();
        $this->banquePhotos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom() . ' ' . $this->getPrenom();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateInscription(): ?DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenValidity(): ?string
    {
        return $this->tokenValidity;
    }

    public function setTokenValidity(?string $tokenValidity): self
    {
        $this->tokenValidity = $tokenValidity;

        return $this;
    }

    public function getCreditPhotos(): ?int
    {
        return $this->creditPhotos;
    }

    public function setCreditPhotos(int $creditPhotos): self
    {
        $this->creditPhotos = $creditPhotos;

        return $this;
    }

    public function getCreditConseilsProfessionnel(): ?int
    {
        return $this->creditConseilsProfessionnel;
    }

    public function setCreditConseilsProfessionnel(?int $creditConseilsProfessionnel): self
    {
        $this->creditConseilsProfessionnel = $creditConseilsProfessionnel;

        return $this;
    }

    public function getCreditAidePaysagiste(): ?int
    {
        return $this->creditAidePaysagiste;
    }

    public function setCreditAidePaysagiste(?int $creditAidePaysagiste): self
    {
        $this->creditAidePaysagiste = $creditAidePaysagiste;

        return $this;
    }

    public function getAccesCompletPlantesObjets(): ?bool
    {
        return $this->accesCompletPlantesObjets;
    }

    public function setAccesCompletPlantesObjets(?bool $accesCompletPlantesObjets): self
    {
        $this->accesCompletPlantesObjets = $accesCompletPlantesObjets;

        return $this;
    }

    public function getCreationNonPublique(): ?bool
    {
        return $this->creationNonPublique;
    }

    public function setCreationNonPublique(?bool $creationNonPublique): self
    {
        $this->creationNonPublique = $creationNonPublique;

        return $this;
    }

    public function getTypePersonne(): ?string
    {
        return $this->typePersonne;
    }

    public function setTypePersonne(?string $typePersonne): self
    {
        $this->typePersonne = $typePersonne;

        return $this;
    }

    public function getDateDerniereConnexion(): ?DateTimeInterface
    {
        return $this->dateDerniereConnexion;
    }

    public function setDateDerniereConnexion(?DateTimeInterface $dateDerniereConnexion): self
    {
        $this->dateDerniereConnexion = $dateDerniereConnexion;

        return $this;
    }

    /**
     * @return Collection|ClientOffre[]
     */
    public function getClientOffres(): Collection
    {
        return $this->clientOffres;
    }

    public function addClientOffre(ClientOffre $clientOffre): self
    {
        if (!$this->clientOffres->contains($clientOffre)) {
            $this->clientOffres[] = $clientOffre;
            $clientOffre->setClient($this);
        }

        return $this;
    }

    public function removeClientOffre(ClientOffre $clientOffre): self
    {
        if ($this->clientOffres->contains($clientOffre)) {
            $this->clientOffres->removeElement($clientOffre);
            // set the owning side to null (unless already changed)
            if ($clientOffre->getClient() === $this) {
                $clientOffre->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClientCodePromo[]
     */
    public function getClientCodePromos(): Collection
    {
        return $this->clientCodePromos;
    }

    public function addClientCodePromo(ClientCodePromo $clientCodePromo): self
    {
        if (!$this->clientCodePromos->contains($clientCodePromo)) {
            $this->clientCodePromos[] = $clientCodePromo;
            $clientCodePromo->setClient($this);
        }

        return $this;
    }

    public function removeClientCodePromo(ClientCodePromo $clientCodePromo): self
    {
        if ($this->clientCodePromos->contains($clientCodePromo)) {
            $this->clientCodePromos->removeElement($clientCodePromo);
            // set the owning side to null (unless already changed)
            if ($clientCodePromo->getClient() === $this) {
                $clientCodePromo->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClientPaiement[]
     */
    public function getClientPaiements(): Collection
    {
        return $this->clientPaiements;
    }

    public function addClientPaiement(ClientPaiement $clientPaiement): self
    {
        if (!$this->clientPaiements->contains($clientPaiement)) {
            $this->clientPaiements[] = $clientPaiement;
            $clientPaiement->setClient($this);
        }

        return $this;
    }

    public function removeClientPaiement(ClientPaiement $clientPaiement): self
    {
        if ($this->clientPaiements->contains($clientPaiement)) {
            $this->clientPaiements->removeElement($clientPaiement);
            // set the owning side to null (unless already changed)
            if ($clientPaiement->getClient() === $this) {
                $clientPaiement->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Projet[]
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->projets->contains($projet)) {
            $this->projets[] = $projet;
            $projet->setClient($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getClient() === $this) {
                $projet->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BanquePhoto[]
     */
    public function getBanquePhotos(): Collection
    {
        return $this->banquePhotos;
    }

    public function addBanquePhoto(BanquePhoto $banquePhoto): self
    {
        if (!$this->banquePhotos->contains($banquePhoto)) {
            $this->banquePhotos[] = $banquePhoto;
            $banquePhoto->setClient($this);
        }

        return $this;
    }

    public function removeBanquePhoto(BanquePhoto $banquePhoto): self
    {
        if ($this->banquePhotos->contains($banquePhoto)) {
            $this->banquePhotos->removeElement($banquePhoto);
            // set the owning side to null (unless already changed)
            if ($banquePhoto->getClient() === $this) {
                $banquePhoto->setClient(null);
            }
        }

        return $this;
    }

    public function generateNewToken()
    {
        die('test');
        $time = strtotime('+5 minutes');

        $this->setToken(hash('sha256', strtotime('now') . $this->getEmail() . $time));
        $this->setTokenValidity($time);
    }

    public function checkValiditeToken($token)
    {
        die('test');
        $time = strtotime('now');
        return $this->getTokenValidity() <= $time && $token == $this->getToken();
    }

    public function toFullString()
    {
        return $this->getNom() . ' ' . $this->getPrenom() . ' (' . $this->getEmail() . ')';
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        return array('ROLE_APPLICATION');
    }

    public function eraseCredentials()
    {
        // rien à faire ici
    }

    public function serialize()
    {
        return serialize($this->getId());
    }

    public function unserialize($data)
    {
        $this->id = unserialize($data);
    }

    /**
     * @param int $length longueur de la chaine
     * @param string $characters Caractères autorisés. ex: "0123456789abcdef"
     */
    public function randomString(
        $length,
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
    ) {
        $randMax = strlen($characters) - 1;
        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $randMax)];
        }

        return $string;
    }

    public function generationMotDePasse($factory)
    {
        $nouvMDP = $this->randomString(10);
        $this->setPassword($nouvMDP);
        $encoder = $factory->getEncoder($this);
        $password = $encoder->encodePassword($this->getPassword(), $this->getSalt());
        $this->setPassword($password);

        return $nouvMDP;
    }

//    public static function loadValidatorMetadata(ClassMetadata $metadata)
//    {
//        $metadata->addConstraint(
//            new UniqueEntity(
//                array(
//                    'fields' => 'email',
//                    'message' => 'Cette adresse email existe déja.',
//                )
//            )
//        );
//    }

    /**
     * Lifecycle callback pour enregistrer la date de création
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setDateInscription(new DateTime());
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->getId() !== $user->getId()) {
            return false;
        }

        return true;
    }

    public function getRecevoirInformations(): ?bool
    {
        return $this->recevoirInformations;
    }

    public function setRecevoirInformations(?bool $recevoirInformations): self
    {
        $this->recevoirInformations = $recevoirInformations;

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
}
