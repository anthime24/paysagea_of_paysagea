<?php

namespace App\Front\Controller;

use App\Core\Entity\Client;
use App\Core\Entity\CodePromo;
use App\Core\Entity\Creation;
use App\Core\Entity\Offre;
use App\Core\Entity\Projet;
use App\Core\Utility\LocalityDetail;
use App\Core\Utility\LocalityDetailBelgium;
use App\Core\Utility\LocalityDetailEurope;
use App\Front\Form\ClientConnectionType;
use App\Front\Form\InscriptionClientType;
use App\Front\Form\InscriptionCropType;
use App\Front\Form\InscriptionProjectType;
use App\Front\Form\PromoCodeType;
use App\Front\Service\InscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class InscriptionController extends AbstractController
{
    public function index(
        Request                $request,
        EntityManagerInterface $em,
        Session                $session,
        InscriptionService     $inscriptionService,
        FilterManager          $filterManager,
        TranslatorInterface    $translator,
        ParameterBagInterface  $parameterBag
    ): Response
    {
        $client = $this->getUser();
        $projectId = $request->get('projectId');
        $project = !empty($projectId) ? $em->getRepository(Projet::class)->findOneById($projectId) : new Projet();

        if (($client == null && !empty($projectId)) || ($client && !empty($projectId) && $project->getClient() != $client)) {
            throw $this->createNotFoundException('Project not found');
        }

        $offers = $em->getRepository(Offre::class)->findAllOrderedByPrice();
        $project->setClient($client);

        $form = $this->createForm(InscriptionProjectType::class, $project);
        $form->handleRequest($request);

        $extraFormErrors = array();
        $formIsValid = $form->isSubmitted() === true ? $form->isValid() : false;
        if ($form->isSubmitted()) {
            $photoBankUpload = $form['banquePhoto']->getData();
            $offer = $form['offre']->getData();
            $photoBank = $form['banquePhotos']->getData();

            $creditPhoto = $client !== null ? $client->getCreditPhotos() : 0;
            $priceCalculation = $inscriptionService->priceCalculation($offer);

            if ($photoBank === null && $photoBankUpload->getFile() === null) {
                $formIsValid = false;
                $extraFormErrors['banquePhotoFile'] = $translator->trans('Pour utiliser votre propre photo vous devez indiquer le fichier à télécharger');
            }

//            if($photoBankUpload->getFile() !== null && ($photoBankUpload->getEmail() === null || trim($photoBankUpload->getEmail()) == "" || filter_var($photoBankUpload->getEmail(), FILTER_VALIDATE_EMAIL) === false)) {
//                $formIsValid = false;
//                $extraFormErrors['banquePhotoEmail'] = $translator->trans('Vous devez indiquer une adresse email valide, pour pouvoir télécharger une photo');
//            }

            if ($priceCalculation['priceToPay'] == 0 && $creditPhoto < 1 && $photoBank === null && $photoBankUpload->getFile() !== null) {
                $formIsValid = false;
                $extraFormErrors['creditPhoto'] = $translator->trans('Credit photo insuffisant, pour utilisez votre propre photo veuillez sélectionner une offre');
            }
        }

        if ($form->isSubmitted() && $formIsValid) {
            $photoBankUpload = $form['banquePhoto']->getData();
            $offer = $form['offre']->getData();
            $photoBank = $form['banquePhotos']->getData();

            if ($photoBankUpload && $photoBankUpload->getFile()) {
                $photoBankUpload->setBanquePhotoType($project->getProjetType());
                $photoBankUpload->setUploadedDuringRegistration(true);
                $photoBankUpload->initialiserNom();
                $photoBankUpload->setClient($client);
                $photoBank = $photoBankUpload;

                $em->persist($photoBank);
            }

            $project->initialiserNom();


            if (($project->getLatitude() >= LocalityDetailBelgium::CARTE_LATITUDE_MIN && $project->getLatitude() <= LocalityDetailBelgium::CARTE_LATITUDE_MAX)
                && ($project->getLongitude() >= LocalityDetailBelgium::CARTE_LONGITUDE_MIN && $project->getLongitude() <= LocalityDetailBelgium::CARTE_LONGITUDE_MAX)) {
                $localityDetail = LocalityDetailBelgium::calcul($em, $project->getLatitude(), $project->getLongitude());
                $project->setOrigineCarte("BE");
            } else if (($project->getLatitude() >= LocalityDetail::CARTE_LATITUDE_MIN && $project->getLatitude() <= LocalityDetail::CARTE_LATITUDE_MAX)
                && ($project->getLongitude() >= LocalityDetail::CARTE_LONGITUDE_MIN && $project->getLongitude() <= LocalityDetail::CARTE_LONGITUDE_MAX)) {
                $localityDetail = LocalityDetail::calcul($em, $project->getLatitude(), $project->getLongitude());
                $project->setOrigineCarte("FR");
            } else {
                $localityDetail = LocalityDetailEurope::calcul($parameterBag, $em, $project->getLatitude(), $project->getLongitude());
                $project->setOrigineCarte("EU");
            }

            $project->setPrecipitation($localityDetail['precipitationsEstivales']);
            $project->setRusticite($localityDetail['rusticite']);
            $project->setPh($localityDetail['ph']);

            $creation = new Creation();
            $creation->setProjet($project);
            $creation->setBanquePhoto($photoBank);

            $em->persist($project);
            $em->persist($creation);
            $em->flush();

            $priceCalculation = $inscriptionService->priceCalculation($offer);

            $session->set('mjmt_front_inscription_creation_id', $creation->getId());
            $session->set('mjmt_front_inscription_offer_id', $offer->getId());
            $session->set('mjmt_front_inscription_private_creation', false);

            if ($photoBank !== null && $photoBank->getPublic() && $priceCalculation['priceToPay'] == 0) {
                $inscriptionService->validate($client, $creation);
                $session->clear();

                return $this->redirectToRoute(
                    'mjmt_application_homepage',
                    array(
                        'creationId' => $creation->getId(),
                        'hash' => $creation->getReferenceEcriture()
                    )
                );
            } else {
                if ($photoBank !== null && $photoBank->getPublic() && $priceCalculation['priceToPay'] > 0) {
                    return $this->redirectToRoute('mjmt_front_inscription_client');
                } else {
                    return $this->redirectToRoute('mjmt_front_inscription_crop');
                }
            }
        }

        return $this->render(
            'front/inscription/index.html.twig',
            array(
                'form' => $form->createView(),
                'extraFormErrors' => $extraFormErrors,
                'offers' => $offers,
                'client' => $client
            )
        );
    }

    public function crop(
        Request                $request,
        EntityManagerInterface $em,
        Session                $session,
        InscriptionService     $inscriptionService,
        KernelInterface        $kernel
    )
    {

        $creationId = $session->get('mjmt_front_inscription_creation_id');
        $creation = $creationId ? $em->getRepository(Creation::class)->find($creationId) : null;

        if (!$creation) {
            throw $this->createNotFoundException('Creation not found');
        }


        if ($creation->getBanquePhoto() == null || $creation->getBanquePhoto()->getPublic()) {
            return $this->redirectToRoute('mjmt_front_inscription');
        }

        $form = $this->createForm(InscriptionCropType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['rotation'] > 0) {
                //regénération de la photo pour cette création
                $creation->getBanquePhoto()->createRotation($data['rotation']);

                //regénération de la vignette pour la gallerie
                $creation->createRotation($data['rotation']);
                $em->flush();

                $application = new Application($kernel);
                $application->setAutoExit(false);
                $output = new BufferedOutput();
                $pathCommand = $creation->getBanquePhoto()->getWebPath();

                $input = new ArrayInput(array(
                    'command' => 'liip:imagine:cache:remove',
                    'paths' => array($pathCommand),
                    '--filter' => array('thumb_front_vignette_inscription')
                ));
                $application->doRun($input, $output);

                $input = new ArrayInput(array(
                    'command' => 'liip:imagine:cache:resolve',
                    'paths' => array($pathCommand),
                    '--filter' => array('thumb_front_vignette_inscription')
                ));
                $application->doRun($input, $output);
            }

            $newImage = $creation->createCropedImage(
                $data['x1'],
                $data['y1'],
                $data['width'],
                $data['height'],
                $data['originalWidth'],
                $data['originalHeight']
            );
            $creation->createRenderedImage();
            $creation->initialiserInformationsBanquePhoto();

            if ($newImage) {
                $em->persist($creation);
                $em->flush();

                $offerId = $session->get('mjmt_front_inscription_offer_id');
                $offer = $offerId ? $em->getRepository(Offre::class)->find($offerId) : null;
                $priceCalculation = $inscriptionService->priceCalculation($offer);

                if ($priceCalculation['priceToPay'] > 0) {
                    return $this->redirectToRoute('mjmt_front_inscription_client');
                } else {
                    $inscriptionService->validate($this->getUser(), $creation);
                    $session->clear();

                    return $this->redirectToRoute(
                        'mjmt_application_homepage',
                        array(
                            'creationId' => $creation->getId(),
                            'hash' => $creation->getReferenceEcriture()
                        )
                    );
                }
            }
        }

        return $this->render(
            'front/inscription/crop.html.twig',
            array(
                'form' => $form->createView(),
                'creation' => $creation
            )
        );
    }

    //TODO check that the promo code is substracted after each usage
    public function client(
        Request                 $request,
        EntityManagerInterface  $em,
        Session                 $session,
        AuthenticationUtils     $authenticationUtils,
        InscriptionService      $inscriptionService,
        TokenStorageInterface   $tokenStorage,
        EncoderFactoryInterface $encoderFactory
    )
    {
        $offerId = $session->get('mjmt_front_inscription_offer_id');
        $privateCreation = $session->get('mjmt_front_inscription_private_creation', false);
        $offer = $offerId ? $em->getRepository(Offre::class)->find($offerId) : null;

        if (!$offer) {
            throw $this->createNotFoundException('Offer not found');
        }

        $client = $this->getUser();
        $form = null;
        $formType = null;
        $loginForm = null;
        $error = null;
        $lastUsername = null;

        if ($client == null) {
            $client = new Client();
            $formType = InscriptionClientType::class;
            $form = $this->createForm($formType, $client);
        } else {
            $formType = PromoCodeType::class;
            $form = $this->createForm($formType);
        }

        $form->handleRequest($request);

        //check the promo code
        if ($form->isSubmitted()) {
            $promoCodeText = null;
            if ($formType == InscriptionClientType::class) {
                if ($form->has('codePromo') && isset($form->get('codePromo')->getViewData()['codePromo']) && trim($form->get('codePromo')->getViewData()['codePromo']) != '') {
                    $promoCodeText = $form->get('codePromo')->getViewData()['codePromo'];
                }
            } else if ($formType == PromoCodeType::class) {
                $data = $form->getData();
                $promoCodeText = $data['codePromo'];
            }

            if ($promoCodeText !== null && !empty($promoCodeText)) {
                $validityItem = $em->getRepository(CodePromo::class)->codePromoControleValidite($promoCodeText, $client, $offer);
                $promoCodeValide = $validityItem['valide'];
                $promoCodeMessage = $validityItem['message'];

                if ($promoCodeValide === false) {
                    if ($formType == InscriptionClientType::class) {
                        $form->get('codePromo')->get('codePromo')->addError(new FormError($promoCodeMessage));
                    } else if ($formType == PromoCodeType::class) {
                        $form->get('codePromo')->addError(new FormError($promoCodeMessage));
                    }
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($formType == InscriptionClientType::class) {
                $encoder = $encoderFactory->getEncoder($client);
                $password = $encoder->encodePassword($client->getPassword(), $client->getSalt());
                $client->setPassword($password);
                $client->setConfirmer(true);
                $em->persist($client);
                $em->flush();

                $token = new UsernamePasswordToken($client, null, 'main', $client->getRoles());
                $tokenStorage->setToken($token);
                $session->set('_security_main', serialize($token));
            }

            $data = $form->getData();

            if ($formType == InscriptionClientType::class) {
                $promoCodeText = null;

                if ($form->has('common') && $form->get('common')->has('codePromo') && isset($form->get('common')->get('codePromo')->getViewData()['codePromo']) && trim($form->get('common')->get('codePromo')->getViewData()['codePromo']) != '') {
                    $promoCodeText = $form->get('common')->get('codePromo')->getViewData()['codePromo'];
                }
            } else {
                $promoCodeText = $data['codePromo'];
            }

            $promoCode = null;
            if ($promoCodeText !== null && !empty($promoCodeText)) {
                $promoCode = $em->getRepository(CodePromo::class)->findOneByCode($promoCodeText);

                if ($promoCode)
                    $session->set('mjmt_front_inscription_promo_code_id', $promoCode->getId());
            }

            if ($promoCode !== null) {
                $priceCalculation = $inscriptionService->priceCalculation($offer, $privateCreation, $promoCode->getCode());
            } else {
                $priceCalculation = $inscriptionService->priceCalculation($offer, $privateCreation);
            }

            if ($priceCalculation['priceToPay'] > 0) {
                return $this->redirectToRoute('mjmt_front_payment_selection');
            } else {
                $creationId = $session->get('mjmt_front_inscription_creation_id');
                $creation = $creationId ? $em->getRepository(Creation::class)->find($creationId) : null;

                if ($creation !== null) {
                    $inscriptionService->validate($client, $creation, $offer, $privateCreation, $promoCode);
                    $session->clear();

                    return $this->redirectToRoute(
                        'mjmt_application_homepage',
                        array(
                            'creationId' => $creation->getId(),
                            'hash' => $creation->getReferenceEcriture()
                        )
                    );
                } else {
                    if (isset($priceCalculation['publicPrice']) && $priceCalculation['publicPrice'] > 0 && $promoCode !== null) {
                        $inscriptionService->validate($client, null, $offer, $privateCreation, $promoCode);
                    }
                    return $this->redirectToRoute('mjmt_front_client');
                }
            }
        } else {
            if ($this->getUser() == null) {
                $error = $authenticationUtils->getLastAuthenticationError();
                $lastUsername = $authenticationUtils->getLastUsername();

                $loginForm = $this->createForm(ClientConnectionType::class, null, array('show_label' => true));
            }
        }

        $priceCalculation = $inscriptionService->priceCalculation($offer, $privateCreation);

        return $this->render(
            'front/inscription/client.html.twig',
            array(
                'client' => $client,
                'form' => $form ? $form->createView() : null,
                'loginForm' => $loginForm ? $loginForm->createView() : null,
                'error' => $error,
                'lastUsername' => $lastUsername,
                'offer' => $offer,
                'priceToPay' => $priceCalculation['priceToPay']
            )
        );
    }
}
