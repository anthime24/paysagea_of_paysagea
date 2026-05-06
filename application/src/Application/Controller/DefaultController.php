<?php

namespace App\Application\Controller;

use App\Application\Form\ChoiceOfferType;
use App\Application\Form\CreationType;
use App\Application\Form\FiltreAmenagementType;
use App\Application\Form\FiltreDecorationType;
use App\Application\Form\FiltreDecorType;
use App\Application\Form\FiltrePlanteType;
use App\Application\Form\RenduType;
use App\Application\Service\Html2PdfService;
use App\Core\Entity\Annee;
use App\Core\Entity\BanquePhoto;
use App\Core\Entity\BesoinEau;
use App\Core\Entity\Client;
use App\Core\Entity\Composition;
use App\Core\Entity\CompositionVue;
use App\Core\Entity\Creation;
use App\Core\Entity\CreationEntite;
use App\Core\Entity\Entite;
use App\Core\Entity\EntiteType;
use App\Core\Entity\Mois;
use App\Core\Entity\Offre;
use App\Core\Entity\Projet;
use App\Core\Entity\Video;
use App\Front\Form\ClientConnectionType;
use App\Front\Form\InscriptionClientType;
use App\Front\Form\InscriptionProjectType;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class DefaultController extends AbstractController
{

    private $params;
    private $encoderFactory;
    private $tokenStorage;
    private $html2pdfService;
    private $mailer;

    private function verificationAcces(int $creationId, string $hash)
    {
        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        return $creation && ($creation->getReferenceLecture() == $hash || $creation->getReferenceEcriture() == $hash);
    }

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface   $tokenStorage,
        Html2PdfService         $html2PdfService,
        Swift_Mailer            $mailer,
        ParameterBagInterface   $params
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
        $this->html2pdfService = $html2PdfService;
        $this->mailer = $mailer;
        $this->params = $params;
    }

    public function authenticatedUserInfo(): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse(
            array(
                'id' => $user ? $user->getId() : '',
                'nom' => $user ? strtoupper($user->getNom()) : '',
                'prenom' => $user ? ucfirst(strtolower($user->getPrenom())) : ''
            )
        );
    }

    public function index(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $em = $this->getDoctrine()->getManager();

        if (is_numeric($creationId)) {
            $creation = $em->getRepository(Creation::class)->findOneById($creationId);
            $ecriture = $creation && $creation->getReferenceEcriture() == $hash;
        } else {
            $ecriture = false;
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();

        $session = $this->get("session");

        // Récupération des valeurs en session
        $sessionPlante = $session->get('mjmt_appbundle_filtre_plante');
        $sessionDecor = $session->get('mjmt_appbundle_filtre_decor');
        $sessionAmenagement = $session->get('mjmt_appbundle_filtre_amenagement');
        $sessionDecoration = $session->get('mjmt_appbundle_filtre_decoration');

        $formPlante = $this->createForm(FiltrePlanteType::class)->submit($sessionPlante)->createView();
        $formDecor = $this->createForm(FiltreDecorType::class)->submit($sessionDecor)->createView();
        $formAmenagement = $this->createForm(FiltreAmenagementType::class)->submit($sessionAmenagement)->createView();
        $formDecoration = $this->createForm(FiltreDecorationType::class)->submit($sessionDecoration)->createView();

        //Liste ordonnée des types d'entité
        $listTypes = $em->getRepository(EntiteType::class)->findBy(array(), array('ordre' => 'asc'));

        //Liste ordonéée des vidéos de l'onglet aide
        $listVideos = $em->getRepository(Video::class)->findBy(array(), array('ordre' => 'asc'));

        $backgroundSize = getimagesize($creation->getAbsolutePathResize());

        $total = $em->getRepository(Creation::class)->getTotal($creation->getId());
        $total = is_null($total) ? 0 : $total;

        // Recherche du besoin en eau moyen
        $idBesoinEauGroupe = '';
        /*if ($creation->getProjet() != null && $creation->getProjet()->getPrecipitation() != null && count(
                $creation->getProjet()->getPrecipitation()->getBesoinEaux()
            ) > 0) {
            $eauMin = null;
            $eauMax = null;
            foreach ($creation->getProjet()->getPrecipitation()->getBesoinEaux() as $besoinEau) {
                if (is_null($eauMin) || $besoinEau->getValeur() < $eauMin) {
                    $eauMin = $besoinEau->getValeur();
                }
                if (is_null($eauMax) || $besoinEau->getValeur() > $eauMax) {
                    $eauMax = $besoinEau->getValeur();
                }
            }

            if (!is_null($eauMin) && !is_null($eauMax)) {
                $eauMoy = round(($eauMax - $eauMin) / 2);
                $idBesoinEauGroupe = $em->getRepository(BesoinEau::class)->findOneByValeur($eauMoy)->getBesoinEauGroupe()->getId();
            }
        }
        */

        $offers = $em->getRepository(Offre::class)->findAllOrderedByPrice();

        $form = $this->createForm(ChoiceOfferType::class);
        $form->handleRequest($request);
        $formIsValid = $form->isSubmitted() === true ? $form->isValid() : false;
        if ($form->isSubmitted() && $formIsValid) {
            $offer = $form['offre']->getData();+

            $session->set('mjmt_front_inscription_creation_id', $creation->getId());
            $session->set('mjmt_front_inscription_offer_id', $offer->getId());
            $session->set('mjmt_front_inscription_private_creation', false);

            return $this->redirectToRoute('mjmt_front_inscription_client');
        }

        return $this->render(
            'application/content/index.html.twig',
            array(
                'html2CanvasScreenshot' => $this->params->get('html2CanvasScreenshot'),
                'sessionPlante' => $sessionPlante,
                'sessionDecor' => $sessionDecor,
                'sessionAmenagement' => $sessionAmenagement,
                'sessionDecoration' => $sessionDecoration,
                'formPlante' => $formPlante,
                'formDecor' => $formDecor,
                'formAmenagement' => $formAmenagement,
                'formDecoration' => $formDecoration,
                'listTypes' => $listTypes,
                'creation' => $creation,
                'ecriture' => $ecriture,
                'listVideos' => $listVideos,
                'initialisationProportionsAdmin' => ($request->query->has('initialisation') && $request->get(
                        'initialisation'
                    ) == 'proportions'),
                'backgroundWidth' => $backgroundSize[0],
                'backgroundHeight' => $backgroundSize[1],
                'total' => $total,
                'idBesoinEauGroupe' => $idBesoinEauGroupe,
                'form' => $form->createView(),
                'offers' => $offers
            )
        );
    }

    public function sauvegarderProportion(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();

        $result = '0';

        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        if ($creation != null) {
            $coordonnees = $this->container->get('request_stack')->getCurrentRequest()->get('coordonnees');
            $banquePhoto = $creation->getBanquePhoto();
            if ($coordonnees) {
                foreach ($coordonnees as $key => $val) {
                    $creation->{'setRepere' . $key . 'X'}($val['left']);
                    $creation->{'setRepere' . $key . 'Y'}($val['top']);
                    $creation->{'setRepere' . $key . 'Largeur'}($val['width']);
                    $creation->{'setRepere' . $key . 'Hauteur'}($val['height']);

                    if ($banquePhoto != null && ($banquePhoto->getPublic() == 0 || $request->get(
                                'initialisation'
                            ) == 'proportions')) {
                        $banquePhoto->{'setRepere' . $key . 'X'}($val['left']);
                        $banquePhoto->{'setRepere' . $key . 'Y'}($val['top']);
                        $banquePhoto->{'setRepere' . $key . 'Largeur'}($val['width']);
                        $banquePhoto->{'setRepere' . $key . 'Hauteur'}($val['height']);
                    }
                }

                $creation->setEnregistree(1);
                $em->persist($creation);
                $em->flush();

                if ($banquePhoto != null && ($banquePhoto->getPublic() == 0 || $request->get(
                            'initialisation'
                        ) == 'proportions')) {
                    $em->persist($banquePhoto);
                    $em->flush();
                }

                $result = '1';
            }
        }

        return new Response($result);
    }

    public function sauvegarderTampon(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        if ($creation != null) {
            $dataUrl = $this->container->get('request_stack')->getCurrentRequest()->request->get('imgDataUrl');
            $creation->setEnregistree(1);
            $creation->saveNewVersionImage($dataUrl);

            //on sauvegarde le rendu en image
            $creation->createRenderedImage();

            $em->persist($creation);
            $em->flush();
        }

        return new Response('');
    }

    private function _raffraichirCachePhotoEntite(KernelInterface $kernel, \App\Core\Entity\Creation $creation)
    {
        $entites = $creation->getCreationEntites();

        $listEntites = array();
        $listCompositions = array();
        $numero = 1;
        foreach ($entites as $creationEntite) {
            if ($creationEntite->getVisibilite() == 1) {
                if (array_key_exists($creationEntite->getEntite()->getId(), $listEntites)) {
                    $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite();
                    $listEntites[$creationEntite->getEntite()->getId()]['quantite'] += 1;
                    $listEntites[$creationEntite->getEntite()->getId()]['numeros'] = $listEntites[$creationEntite->getEntite()->getId()]['numeros'];
                } else {
                    $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite();
                    $listEntites[$creationEntite->getEntite()->getId()]['quantite'] = 1;
                    $listEntites[$creationEntite->getEntite()->getId()]['numeros'] = "" . $numero;
                    $numero++;
                }
                if ($creationEntite->getComposition()) {
                    $listCompositions[] = $creationEntite->getComposition();
                }
            }
        }
        $listCompositionEntites = array();
        foreach ($listCompositions as $composition) {
            $entites = $composition->getCompositionEntites();
            $tmpListEntites = array();
            foreach ($entites as $compositionEntite) {
                if (array_key_exists($compositionEntite->getEntite()->getId(), $tmpListEntites)) {
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['entite'] = $compositionEntite->getEntite();
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] += 1;
                } else {
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['entite'] = $compositionEntite->getEntite();
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] = 1;
                }
            }
            $listCompositionEntites[$composition->getId()] = $tmpListEntites;
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $output = new BufferedOutput();
        $pathCommand = $creation->getWebPathRenderedImage();

        foreach ($listEntites as $entiteKey => $entiteValue) {
            foreach ($entiteValue['entite']->getEntitePhotos() as $entitePhotoValue) {
                if ($entitePhotoValue->getWebPath() !== null) {
                    $input = new ArrayInput(
                        array(
                            'command' => 'liip:imagine:cache:resolve',
                            'paths' => array($entitePhotoValue->getWebPath()),
                            '--filter' => array('thumb_app_resultat_pdf')
                        )
                    );
                    $application->doRun($input, $output);
                }
            }
        }

        foreach ($listCompositionEntites as $outerCompositionKey => $outerCompositionValue) {
            foreach ($outerCompositionValue as $compositionValueKey => $compositionValue) {
                foreach ($compositionValue['entite']->getEntitePhotos() as $entitePhotoValue) {
                    if ($entitePhotoValue->getWebPath() !== null) {
                        $input = new ArrayInput(
                            array(
                                'command' => 'liip:imagine:cache:resolve',
                                'paths' => array($entitePhotoValue->getWebPath()),
                                '--filter' => array('thumb_app_resultat_pdf')
                            )
                        );
                        $application->doRun($input, $output);
                    }
                }
            }
        }
    }

    public function sauvegarderCreation(KernelInterface $kernel, Request $request, int $creationId, string $hash): JsonResponse
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $results = array();

        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->find($creationId);

        if ($creation != null) {
            $imageRendu = $this->container->get('request_stack')->getCurrentRequest()->get('image');
            $screenshotHtmlContent = $this->container->get('request_stack')->getCurrentRequest()->get('screenshotHtmlContent');
            $entites = $this->container->get('request_stack')->getCurrentRequest()->get('entites');

            $creation->setDateModification(new \DateTime());
            $creation->setEnregistree(1);
            $entiteOk = array();
            if ($entites) {
                foreach ($entites as $entite) {
                    if (!empty($entite['creation_entite_id'])) {
                        $creationEntite = $em->getRepository(CreationEntite::class)->findOneById(
                            $entite['creation_entite_id']
                        );
                    } else {
                        $creationEntite = new CreationEntite();
                        $entiteBdd = $em->getRepository(Entite::class)->findOneById($entite['entite_id']);
                        $compositionBdd = $em->getRepository(Composition::class)->findOneById(
                            $entite['composition_id']
                        );
                        $compositionVueBdd = $em->getRepository(CompositionVue::class)->findOneById(
                            $entite['composition_vue_id']
                        );
                        if ($entiteBdd != null) {
                            $creationEntite->setEntite($entiteBdd);
                        }
                        if ($compositionBdd != null) {
                            $creationEntite->setComposition($compositionBdd);
                        }
                        if ($compositionVueBdd != null) {
                            $creationEntite->setCompositionVue($compositionVueBdd);
                        }
                        $creationEntite->setSymetrie(0);
                        $creationEntite->setCreation($creation);
                    }

                    $creationEntite->setCoordonneeX($entite['coordonnee_x']);
                    $creationEntite->setCoordonneeY($entite['coordonnee_y']);
                    $creationEntite->setLargeur($entite['largeur']);
                    $creationEntite->setHauteur($entite['hauteur']);
                    $creationEntite->setRotation($entite['rotation']);
                    $creationEntite->setTransformation($entite['transformation']);
                    $creationEntite->setTailleFixe($entite['taille_fixe']);
                    $creationEntite->setVisibilite($entite['visibilite']);
                    $creationEntite->setZindex($entite['zindex']);
                    $creationEntite->setLasso($entite['lasso']);

                    $em->persist($creationEntite);
                    $em->flush();

                    $results[$entite['div_id']] = $creationEntite->getId();

                    //si symetrie <> alors on applique en php
                    if ($entite['symetrie'] != $creationEntite->getSymetrie() && $entite['envoyer_image'] == 0) {
                        $creationEntite->setSymetrie($entite['symetrie']);
                        $creationEntite->appliquerSymetrieVerticale();
                        $em->persist($creationEntite);
                        $em->flush();
                    } //si gomme alors on enregistre l'image
                    else {
                        if ($entite['envoyer_image'] == 1) {
                            $creationEntite->saveNewVersionImage($entite['data_url']);
                            $em->persist($creationEntite);
                            $em->flush();
                        }
                    }

                    array_push($entiteOk, $creationEntite->getId());
                }
            }

            //supprimer entite qui ne sont pas dans la liste
            $em->getRepository(CreationEntite::class)->deleteOldEntites($creation->getId(), $entiteOk);
            $this->_raffraichirCachePhotoEntite($kernel, $creation);

            //on sauvegarde le rendu en image (on prend l'image utilisée sur la page de rendu du site)
            if ($screenshotHtmlContent !== null && $screenshotHtmlContent == "true") {
                $projectDir = $this->params->get('projectDir');
                $backgroundSize = @getimagesize($creation->getAbsolutePathResize());
                if (!is_array($backgroundSize) || count($backgroundSize) < 2) {
                    $creation->createRenderedImage();
                } else {
                    $command = "node screenshot.js " . $creation->getId() . " " . $creation->getReferenceEcriture() . " " . $backgroundSize[0] . " " . $backgroundSize[1] . " " . $_SERVER["HTTP_HOST"];
                    $scheme = $request->getScheme();

                    if ($scheme != "https") {
                        $command = "node screenshot.js " . $creation->getId() . " " . $creation->getReferenceEcriture() . " " . $backgroundSize[0] . " " . $backgroundSize[1] . " " . $_SERVER["HTTP_HOST"] . " " . $scheme;
                    }

                    if (!file_exists($projectDir . '/tmp')) {
                        mkdir($projectDir . '/tmp');
                    }

                    try {
                        $execValue = exec("cd " . $projectDir . " && " . $command);
                        $screenshotFileName = $creation->getId() . "_" . $creation->getReferenceEcriture() . ".jpg";
                        $creation->generateRenderedImageFromScreenshot($screenshotFileName);
                    } catch (\Exception $ex) {
                    }
                }
            } else if ($imageRendu !== null) {
                $creation->generateRenderedImageFromDataURL($imageRendu);
            } else {
                $creation->createRenderedImage();
            }

            $creation->createPlanMasseImage();

            $application = new Application($kernel);
            $application->setAutoExit(false);
            $output = new BufferedOutput();
            $pathCommand = $creation->getWebPathRenderedImage();

            $input = new ArrayInput(
                array(
                    'command' => 'liip:imagine:cache:remove',
                    'paths' => array($pathCommand)
                )
            );
            $application->doRun($input, $output);

            $input = new ArrayInput(
                array(
                    'command' => 'liip:imagine:cache:resolve',
                    'paths' => array($pathCommand),
                )
            );
            $application->doRun($input, $output);
        }

        return new JsonResponse($results);
    }


    public function finaliser(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $c = $em->getRepository(Creation::class)->findOneById($creationId);
        $client = $c->getProjet()->getClient();
        $enregistrer = $client != null;
        $email = $client != null ? $client->getEmail() : '';
        $redirectToOfferSelection = $request->get('redirectToOfferSelection');

        $formUrlParams = array();
        $formUrlParams['creationId'] = $creationId;
        $formUrlParams['hash'] = $hash;
        if ($redirectToOfferSelection !== null) {
            $formUrlParams['redirectToOfferSelection'] = $redirectToOfferSelection;
        }

        $form = $this->createForm(InscriptionClientType::class, $client, array(
            'action' => $this->generateUrl('mjmt_application_sauvegarder_client', $formUrlParams)
        ));
        $error = '';

        $formLogin = $this->createForm(ClientConnectionType::class, null, array(
            'show_label' => true
        ));

        if ($redirectToOfferSelection !== null) {
            if ($enregistrer) {
                return $this->render(
                    'application/popin/partial_finaliser/pdf.html.twig',
                    array(
                        'form' => $form->createView(),
                        //'error' => $error,
                        'form_login' => $formLogin->createView(),
                        'type' => 'finaliser',
                        'enregistrer' => $enregistrer,
                        'email' => $email,
                        'creation' => $c,
                        'redirectHtmlToOfferSelection' => true
                    )
                );
            } else {
                return $this->render(
                    'application/popin/partial_finaliser/formulaire.html.twig',
                    array(
                        'form' => $form->createView(),
                        //'error' => $error,
                        'form_login' => $formLogin->createView(),
                        'type' => 'finaliser',
                        'enregistrer' => $enregistrer,
                        'email' => $email,
                        'creation' => $c,
                        'redirectHtmlToOfferSelection' => true
                    )
                );
            }
        } else {
            if ($enregistrer) {
                return $this->render(
                    'application/popin/partial_finaliser/pdf.html.twig',
                    array(
                        'form' => $form->createView(),
                        //'error' => $error,
                        'form_login' => $formLogin->createView(),
                        'type' => 'finaliser',
                        'enregistrer' => $enregistrer,
                        'email' => $email,
                        'creation' => $c,
                    )
                );
            } else {
                return $this->render(
                    'application/popin/partial_finaliser/formulaire.html.twig',
                    array(
                        'form' => $form->createView(),
                        //'error' => $error,
                        'form_login' => $formLogin->createView(),
                        'type' => 'finaliser',
                        'enregistrer' => $enregistrer,
                        'email' => $email,
                        'creation' => $c
                    )
                );
            }
        }
    }

    public function lierCreationClient(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $client = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        if ($client != null && $creation->getProjet()->getClient() == null && ($client->getCreditPhotos() > 0 || !is_null($creation->getBanquePhoto()->getBanquePhotoType()))) {


            if ($creation->getBanquePhoto()->isUploadedDuringRegistration() === true) {
                if ($client->getCreditPhotos() < 1) {
                    return new Response('Vous n\'avez pas assez de crédits photos pour associer ce projet à ce compte.');
                } else {
                    $client->setCreditPhotos($client->getCreditPhotos() - 1);
                    $creation->getBanquePhoto()->setUploadedDuringRegistration(false);
                }
            }

            $projet = $em->getRepository(Projet::class)->findOneById($creation->getProjet()->getId());
            $projet->setClient($client);

            $em->persist($projet);
            $em->flush();

            return $this->forward(
                'App\Application\Controller\DefaultController::finaliser',
                array('creationId' => $creation->getId(), 'hash' => $creation->getReferenceEcriture())
            );
        } elseif ($client->getCreditPhotos() <= 0 && is_null($creation->getBanquePhoto()->getBanquePhotoType())) {
            return new Response('Vous n\'avez pas assez de crédits photos pour associer ce projet à ce compte.');
        }

        return new Response('Un problème est survenue pendant liaison.');
    }

    public function sauvegarderClient(int $creationId, string $hash)
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $redirectToOfferSelection = $request->get('redirectToOfferSelection');

        $em = $this->getDoctrine()->getManager();


        $c = $em->getRepository(Creation::class)->findOneById($creationId);
        $client = $c->getProjet()->getClient();
        if ($client == null) {
            $client = new Client();
            $client->setConfirmer(1);
        } else {
            if ($redirectToOfferSelection !== null) {
                return $this->forward(
                    'App\Application\Controller\DefaultController::finaliser',
                    array('creationId' => $c->getId(), 'hash' => $c->getReferenceEcriture(), 'redirectToOfferSelection' => $redirectToOfferSelection)
                );
            } else {
                return $this->forward(
                    'App\Application\Controller\DefaultController::finaliser',
                    array('creationId' => $c->getId(), 'hash' => $c->getReferenceEcriture())
                );
            }
        }

        $formUrlParams = array();
        $formUrlParams['creationId'] = $creationId;
        $formUrlParams['hash'] = $hash;
        if ($redirectToOfferSelection !== null) {
            $formUrlParams['redirectToOfferSelection'] = $redirectToOfferSelection;
        }

        $form = $this->createForm(InscriptionClientType::class, $client, array(
            'action' => $this->generateUrl('mjmt_application_sauvegarder_client', $formUrlParams),
        ));
        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->encoderFactory->getEncoder($client);
            $password = $encoder->encodePassword($client->getPassword(), $client->getSalt());
            $client->setPassword($password);
            $client->setConfirmer(1);

            $em->persist($client);
            $em->flush();

            $token = new UsernamePasswordToken($client, null, 'main', $client->getRoles());
            $this->tokenStorage->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            //On relie le projet au client
            if (!empty($creationId)) {
                $c = $em->getRepository(Creation::class)->findOneById($creationId);
                if ($c && $c->getProjet()->getClient() == null) {
                    $projet = $em->getRepository(Projet::class)->findOneById($c->getProjet()->getId());
                    if ($projet != null) {
                        $projet->setClient($client);
                        $em->persist($projet);
                        $em->flush();
                    }
                }

                if ($c && $c->getBanquePhoto()->getClient() == null && $c->getBanquePhoto()->getPublic() == 0) {
                    $banquePhoto = $em->getRepository(BanquePhoto::class)->findOneById($c->getId());
                    if ($banquePhoto != null) {
                        $banquePhoto->setClient($client);
                        $em->persist($banquePhoto);
                        $em->flush();
                    }
                }
            }

            if ($redirectToOfferSelection !== null) {
                return $this->forward(
                    'App\Application\Controller\DefaultController::finaliser',
                    array('creationId' => $creationId, 'hash' => $c->getReferenceEcriture(), 'redirectToOfferSelection' => $redirectToOfferSelection)
                );
            } else {
                return $this->forward(
                    'App\Application\Controller\DefaultController::finaliser',
                    array('creationId' => $creationId, 'hash' => $c->getReferenceEcriture())
                );
            }
        }

        $formLogin = $this->createForm(ClientConnectionType::class, null, array('show_label' => true));

        return $this->render(
            'application/popin/partial_finaliser/formulaire.html.twig',
            array(
                'form' => $form->createView(),
                'form_login' => $formLogin->createView(),
                'enregistrer' => false,
                'type' => 'finaliser',
                'email' => '',
                'creation' => $c
            )
        );
    }

    public function pdf(KernelInterface $kernel, int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);
        $total = $em->getRepository(Creation::class)->getTotal($creation->getId());

        if ($request->get('refresh_image') == '1') {
            $projectDir = $this->params->get('projectDir');
            $backgroundSize = getimagesize($creation->getAbsolutePathResize());
            $command = "node screenshot.js " . $creation->getId() . " " . $creation->getReferenceEcriture() . " " . $backgroundSize[0] . " " . $backgroundSize[1] . " " . $_SERVER["HTTP_HOST"];
            $scheme = $request->getScheme();

            if ($scheme != "https") {
                $command = "node screenshot.js " . $creation->getId() . " " . $creation->getReferenceEcriture() . " " . $backgroundSize[0] . " " . $backgroundSize[1] . " " . $_SERVER["HTTP_HOST"] . " " . $scheme;
            }

            if (!file_exists($projectDir . '/tmp')) {
                mkdir($projectDir . '/tmp');
            }

            try {
                $execValue = exec("cd " . $projectDir . " && " . $command);
                $screenshotFileName = $creation->getId() . "_" . $creation->getReferenceEcriture() . ".jpg";
                $creation->generateRenderedImageFromScreenshot($screenshotFileName);
            } catch (\Exception $ex) {
            }
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $output = new BufferedOutput();

        $entites = $creation->getCreationEntites();

        $listEntites = array();
        $listCompositions = array();
        $numero = 1;
        foreach ($entites as $creationEntite) {
            if ($creationEntite->getVisibilite() == 1) {
                if (array_key_exists($creationEntite->getEntite()->getId(), $listEntites)) {
                    $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite();
                    $listEntites[$creationEntite->getEntite()->getId()]['quantite'] += 1;
                    $listEntites[$creationEntite->getEntite()->getId()]['numeros'] = $listEntites[$creationEntite->getEntite()->getId()]['numeros'];
                } else {
                    $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite();
                    $listEntites[$creationEntite->getEntite()->getId()]['quantite'] = 1;
                    $listEntites[$creationEntite->getEntite()->getId()]['numeros'] = "" . $numero;
                    $numero++;
                }
                if ($creationEntite->getComposition()) {
                    $listCompositions[] = $creationEntite->getComposition();
                }
            }
        }
        $listCompositionEntites = array();
        foreach ($listCompositions as $composition) {
            $entites = $composition->getCompositionEntites();
            $tmpListEntites = array();
            foreach ($entites as $compositionEntite) {
                if (array_key_exists($compositionEntite->getEntite()->getId(), $tmpListEntites)) {
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['entite'] = $compositionEntite->getEntite();
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] += 1;
                } else {
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['entite'] = $compositionEntite->getEntite();
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] = 1;
                }
            }
            $listCompositionEntites[$composition->getId()] = $tmpListEntites;
        }

        foreach ($listEntites as $entiteKey => $entiteValue) {
            foreach ($entiteValue['entite']->getEntitePhotos() as $entitePhotoValue) {
                if ($entitePhotoValue->getWebPath() !== null) {
                    $input = new ArrayInput(
                        array(
                            'command' => 'liip:imagine:cache:resolve',
                            'paths' => array($entitePhotoValue->getWebPath()),
                            '--filter' => array('thumb_app_resultat_pdf')
                        )
                    );
                    $application->doRun($input, $output);
                }
            }
        }

        foreach ($listCompositionEntites as $outerCompositionKey => $outerCompositionValue) {
            foreach ($outerCompositionValue as $compositionValueKey => $compositionValue) {
                foreach ($compositionValue['entite']->getEntitePhotos() as $entitePhotoValue) {
                    if ($entitePhotoValue->getWebPath() !== null) {
                        $input = new ArrayInput(
                            array(
                                'command' => 'liip:imagine:cache:resolve',
                                'paths' => array($entitePhotoValue->getWebPath()),
                                '--filter' => array('thumb_app_resultat_pdf')
                            )
                        );
                        $application->doRun($input, $output);
                    }
                }
            }
        }

        $html = $this->renderView(
            'application/pdf/pdf.html.twig',
            array(
                'creation' => $creation,
                'projet' => $creation->getProjet(),
                'listEntites' => $listEntites,
                'listCompositionEntites' => $listCompositionEntites,
                'total' => $total
            )
        );

        $html2pdf = $this->html2pdfService->create();
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($html);
        $fichier = $html2pdf->Output($creation->getAbsolutePathPdf(), 'F');

        $creation->setPdfGenere(true);

        $em->persist($creation);
        $em->flush();

        if ($request->get('methode') && $request->get('methode') == 'email') {
            $message = (new Swift_Message())
                ->setSubject('Mon Jardin Ma Terrasse - Finalisation création')
                ->setFrom('contact@monjardin-materrasse.com')
                ->setTo($request->get('email'))
                ->attach(Swift_Attachment::fromPath($creation->getAbsolutePathPdf()))
                ->setBody(
                    $this->renderView('application/email/email.html.twig', array('creation' => $creation)),
                    'text/html'
                );

            $this->mailer->send($message);

            return new Response('ok');
        } else {
            $response = new Response();
            //$response->clearHttpHeaders();
            $response->headers->addCacheControlDirective('no-cache', true);
            $response->headers->addCacheControlDirective('max-age', 0);
            $response->headers->addCacheControlDirective('must-revalidate', true);
            $response->headers->addCacheControlDirective('no-store', true);
            $response->setContent(file_get_contents($creation->getAbsolutePathPdf()));
            $response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set(
                'Content-disposition',
                'filename=finalisation-creation-' . date('Y-m-d-h-i-s') . '.pdf'
            );

            return $response;
        }
    }

    public function image(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $em = $this->getDoctrine()->getManager();

        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        if (!$creation) {
            throw $this->createNotFoundException('Creation non trouvée');
        }

        $filepath = $creation->getAbsolutePathRenderedImage();

        if (!file_exists($filepath)) {
            // Si on demande l'image et qu'elle est plus ancienne que la dernière date de modification de la création alors on la regénère
            $creation->createRenderedImage();
        }


        // Generate response
        $response = new Response();
        $filename = time() . '-' . basename($filepath);
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filepath));
        $response->headers->set('Content-Disposition', 'inline; filename="' . $filename . '"');
        $response->headers->set('Content-length', filesize($filepath));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(readfile($filepath));

        return $response;
    }

    public function parametrageCreation(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();

        $creation = $em->getRepository(Creation::class)->findOneById($creationId);
        $oldCreationTypeId = $creation->getCreationType()->getId();

        /*
        $arrosageDeuxFoisParSemaine = null;

        //on enregistre le reglage de arrosage deux fois par semaine pour le type original du projet
        if ($oldCreationTypeId == $creation->getProjet()->getProjetType()->getId()) {
            $arrosageDeuxFoisParSemaine = $creation->getProjet()->getArrosageDeuxFoisParSemaine();
        }
        */

        $form = $this->createForm(CreationType::class, $creation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrosageDeuxFoisParSemaine = $form->get('arrosageDeuxFoisSemaine')->getData();
            $creation->getProjet()->setArrosageDeuxFoisParSemaine($arrosageDeuxFoisParSemaine);

            /*
            if ($oldCreationTypeId == 1) {
                $creation->getProjet()->setArrosageDeuxFoisParSemaineJardin($arrosageDeuxFoisParSemaine);
            } else {
                $creation->getProjet()->setArrosageDeuxFoisParSemaineTerrasse($arrosageDeuxFoisParSemaine);
            }
            */

            $em->persist($creation);
            $em->flush();

            return new Response('ok');
        }

        return $this->render(
            'application/popin/parametrage.html.twig',
            array(
                'type' => 'parametrage',
                'creation' => $creation,
                'form' => $form->createView()
            )
        );
    }

    public function optionsRendu(int $creationId, string $hash): Response
    {
        if (!$this->verificationAcces($creationId, $hash)) {
            return $this->redirect($this->generateUrl('mjmt_front_home'));
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $em = $this->getDoctrine()->getManager();

        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        $session = $this->get("session");
        $donnees = $session->get('mjmt_appbundle_option_rendu');
        $form = $this->createForm(RenduType::class);
        $form->get('annee')->setData($em->getRepository(Annee::class)->findOneById($donnees['annee']));
        $form->get('mois')->setData($em->getRepository(Mois::class)->findOneById($donnees['mois']));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('mjmt_appbundle_option_rendu', $request->request->get($form->getName()));

            return new Response('ok');
        }

        return $this->render(
            'application/popin/rendu.html.twig',
            array(
                'type' => 'option',
                'creation' => $creation,
                'options' => $donnees,
                'form' => $form->createView()
            )
        );
    }

    public function displayScreenshot($identifiantCreation, $identifiantEcriture, $key)
    {
        if ($key != $this->params->get('snapshotKey')) {
            throw new \Exception("not allowed");
        }

        $em = $this->getDoctrine()->getManager();
        $creation = $em->getRepository(Creation::class)->findOneById($identifiantCreation);
        $ecriture = true;

        $backgroundSize = getimagesize($creation->getAbsolutePathResize());

        return $this->render('application/displayScreenshot.html.twig', array(
            'creation' => $creation,
            'ecriture' => $ecriture,
            'backgroundWidth' => $backgroundSize[0],
            'backgroundHeight' => $backgroundSize[1],
            'initialisationProportionsAdmin' => false
        ));
    }

    public function quitterApplication(): RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('mjmt_front_client'));
        } else {
            return $this->redirect('https://www.monjardin-materrasse.com/');
        }
    }
}
