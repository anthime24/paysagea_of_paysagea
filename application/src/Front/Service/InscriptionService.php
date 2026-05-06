<?php

namespace App\Front\Service;

use App\Core\Entity\BanquePhoto;
use App\Core\Entity\Client;
use App\Core\Entity\ClientCodePromo;
use App\Core\Entity\ClientOffre;
use App\Core\Entity\CodePromo;
use App\Core\Entity\Creation;
use App\Core\Entity\CreationType;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Offre;
use App\Core\Entity\Projet;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Templating\EngineInterface;

class InscriptionService
{
    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     *
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     *
     * @var EngineInterface
     */
    private $templating;

    public function __construct(
        EntityManagerInterface $entityManager,
        Swift_Mailer           $mailer,
        EngineInterface        $templating
    )
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function validate(
        ?Client    $client = null,
        ?Creation  $creation = null,
        ?Offre     $offer = null,
        bool       $privateCreation = false,
        ?CodePromo $promoCode = null
    )
    {
        $em = $this->em;

        $creditPhotoRemovedDuringValidation = 0;
        if ($client && $client->getConfirmer() == 0) {
            $client->setConfirmer(true);
            if ($privateCreation == true) {
                $client->setCreationNonPublique($privateCreation);
            }
            $em->persist($client);
            $em->flush();
        }

        if ($creation && $creation->getConfirmer() == 0) {
            $creation->setConfirmer(true);
            $em->persist($creation);
            $em->flush();
            $creation->createRenderedImage();

            $project = $creation->getProjet();
            $photoBank = $creation->getBanquePhoto();

            if ($project && $project->getConfirmer() == 0) {
                $project->setConfirmer(true);
                if ($client && $project->getClient() == null) {
                    $project->setClient($client);
                }
                $em->persist($project);
                $em->flush();
            }

            if ($photoBank && $photoBank->getConfirmer() == 0) {
                $photoBank->setConfirmer(true);
                if ($client && $photoBank->getClient() == null) {
                    $photoBank->setClient($client);
                }
                $em->persist($photoBank);
                $em->flush();

                if ($client) {
                    $creditPhotoRemovedDuringValidation = 1;
                    $client->setCreditPhotos($client->getCreditPhotos() - 1);
                    $em->persist($client);
                    $em->flush();
                }
            }
        }

        if ($client && $offer) {
            if ($client->getCreditPhotos() < 0) {
                $client->setCreditPhotos(0);
                if ($creditPhotoRemovedDuringValidation > 0) {
                    $client->setCreditPhotos($client->getCreditPhotos() - $creditPhotoRemovedDuringValidation);
                }
            }

            $client->setCreditPhotos($client->getCreditPhotos() + $offer->getNbPhotoMax());

            if ($client->getAccesCompletPlantesObjets() == 0) {
                $client->setAccesCompletPlantesObjets($offer->getAccesCompletPlantesObjets());
            }

            $client->setCreditConseilsProfessionnel(
                $client->getCreditConseilsProfessionnel() + $offer->getConseilsProfessionnel()
            );
            $client->setCreditAidePaysagiste($client->getCreditAidePaysagiste() + $offer->getAidePaysagiste());

            $clientOffer = new ClientOffre();
            $clientOffer->setClient($client);
            $clientOffer->setOffre($offer);
            $clientOffer->setDateAjout(new DateTime());
            $em->persist($client);
            $em->persist($clientOffer);
            $em->flush();

            if ($offer->getAlerteMail() == true) {
                $message = (new Swift_Message())
                    ->setSubject('Une commande nécessite votre intervention [' . $offer->getNom() . ']')
                    ->setFrom('contact@monjardin-materrasse.com')
                    ->setTo('frederic.morisset@paysagea.fr')
                    ->setBcc('log.mjmt@tknoweb.com')
                    ->setBody(
                        $this->templating->render(
                            'front/email/offer_alert.html.twig',
                            array('client' => $client, 'offer' => $offer, 'clientOffer' => $clientOffer)
                        ),
                        'text/html'
                    );

                $this->mailer->send($message);
            }
        }

        if ($client && $promoCode) {
            $promoCode->setNbUtilisationsCompteur($promoCode->getNbUtilisationsCompteur() + 1);

            $clientPromoCode = new ClientCodePromo();
            $clientPromoCode->setClient($client);
            $clientPromoCode->setCodePromo($promoCode);
            $clientPromoCode->setDateUtilisation(new DateTime());

            $em->persist($clientPromoCode);
            $em->persist($promoCode);

            $em->flush();
        }
    }

    public function priceCalculation(
        ?Offre  $offer = null,
        bool    $privateCreation = false,
        ?string $promoCode = null,
        bool    $isNew = true
    ): array
    {
        $priceToPay = 0;
        $needCredit = false;

        $promoCode = $this->em->getRepository(CodePromo::class)->findOneByCode($promoCode);

        if ($offer) {
            $priceToPay += $offer->getPrix();
        }

        if ($privateCreation) {
            $priceToPay += 1;
        }

        $publicPrice = $priceToPay;
        $promotion = null;

        if ($promoCode) {
            if ($promoCode->getValeur() != null) {
                $priceToPay = $priceToPay - $promoCode->getValeur();
                $promotion = ' -' . $promoCode->getValeur() . ' €';
            } elseif ($promoCode->getPourcentage() != null) {
                $priceToPay = $priceToPay - ($priceToPay * $promoCode->getPourcentage() / 100);
                $priceToPay = round($priceToPay, 2);
                $promotion = ' -' . $promoCode->getPourcentage() . '%';
            }
        }

        if ($priceToPay < 0) {
            $priceToPay = 0;
        }

        if ($isNew) {
            $needCredit = true;
        }

        return array(
            'priceToPay' => $priceToPay,
            'publicPrice' => $publicPrice,
            'credit' => $needCredit,
            'promotion' => $promotion
        );
    }

    public function creationProjetTest($mode)
    {
        $ensoleillent = $this->em->getRepository(Ensoleillement::class)->findOneBy(['id' => 2]);
        if ($mode == 'balcon') {
            $projetType = $this->em->getRepository(CreationType::class)->findOneBy(['id' => 2]);
            $banquePhoto = $this->em->getRepository(BanquePhoto::class)->findOneBy(['id' => 30654]);
            $nom = 'Site web - Test balcon - ' . date('d/m/Y H:i:s');
        } else {
            $banquePhoto = $this->em->getRepository(BanquePhoto::class)->findOneBy(['id' => 30653]);
            $projetType = $this->em->getRepository(CreationType::class)->findOneBy(['id' => 1]);
            $nom = 'Site web - Test Jardin - ' . date('d/m/Y H:i:s');
        }

        $projet = new Projet();
        $projet->setNom($nom);
        $projet->setTest(true);
        $projet->setProjetType($projetType);

        $creation = new Creation();
        $creation->setProjet($projet);
        $creation->setBanquePhoto($banquePhoto);
        $creation->setEnsoleillement($ensoleillent);

        $this->em->persist($projet);
        $this->em->persist($creation);
        $this->em->flush();

        return $creation;
    }

}
