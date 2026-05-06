<?php

namespace App\Front\Controller;

use App\Core\Entity\Client;
use App\Core\Entity\Offre;
use App\Core\Entity\Projet;
use App\Front\Form\ClientConnectionType;
use App\Front\Form\ClientEditType;
use App\Front\Form\ClientLostPasswordType;
use App\Front\Form\ClientOfferType;
use App\Front\Form\InscriptionClientType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClientController extends AbstractController
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $client = $this->getUser();
        $projects = $em->getRepository(Projet::class)->recupOrdreDateDescParClient($client->getId());
        $clientForm = $this->createForm(ClientEditType::class, $client);
        $clientForm->handleRequest($request);

        if ($clientForm->isSubmitted() && $clientForm->isValid()) {
            $password = $clientForm->get('passwordRepeat')->get('first')->getData();
            if (!empty($password)) {
                $encoder = $this->encoderFactory->getEncoder($client);
                $newPassword = $encoder->encodePassword($password, $client->getSalt());
                $client->setPassword($newPassword);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('mjmt_front_client'));
        }

        return $this->render(
            'front/client/index.html.twig',
            array(
                'client' => $client,
                'projects' => $projects,
                'client_form' => $clientForm->createView()
            )
        );
    }

    public function login(
        AuthenticationUtils     $authenticationUtils
    ): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $loginForm = $this->createForm(ClientConnectionType::class, null, array('show_label' => true));

        return $this->render(
            'front/client/login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
                'loginForm' => $loginForm->createView()
            )
        );
    }

    public function inscription(
        Request                 $request,
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface  $em,
        TokenStorageInterface   $tokenStorage,
        Session                 $session
    ): Response
    {
        $client = new Client();
        $form = $this->createForm(InscriptionClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $encoderFactory->getEncoder($client);
            $password = $encoder->encodePassword($client->getPassword(), $client->getSalt());
            $client->setPassword($password);
            $client->setConfirmer(true);
            $em->persist($client);
            $em->flush();

            $token = new UsernamePasswordToken($client, null, 'main', $client->getRoles());
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            return $this->redirectToRoute('mjmt_front_client');
        }

        return $this->render(
            'front/client/inscription.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    public function lostPassword(
        Request                $request,
        EntityManagerInterface $em,
        Swift_Mailer           $mailer,
        TranslatorInterface    $translator,
        Session                $session
    ): Response
    {
        $form = $this->createForm(ClientLostPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $client = $em->getRepository(Client::class)->findOneByEmail($data['email']);
            if ($client) {
                $newPassword = $client->generationMotDePasse($this->encoderFactory);

                $message = (new Swift_Message())
                    ->setSubject($translator->trans('Génération d\'un nouveau mot de passe'))
                    ->setFrom('contact@monjardin-materrasse.com')
                    ->setTo($client->getEmail())
                    ->setBcc('log@tknoweb.com')
                    ->setBody(
                        $this->renderView(
                            'front/email/password_generation.html.twig',
                            array(
                                'client' => $client,
                                'newPassword' => $newPassword,
                            )
                        ),
                        'text/html'
                    );
                $mailer->send($message);

                $em->persist($client);
                $em->flush();

                $session->getFlashBag()->add(
                    'success',
                    $translator->trans(
                        'Un nouveau mot de passe vient de vous être envoyé sur votre email.'
                    )
                );

                return $this->redirectToRoute('mjmt_front_client_login');
            } else {
                $session->getFlashBag()->add(
                    'notice',
                    $translator->trans(
                        'Aucun client trouvé pour ce compte'
                    )
                );
                return $this->redirectToRoute('mjmt_front_client_login');
            }
        }

        return $this->render(
            'front/client/lost_password.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    public function offerSelection(Request $request, EntityManagerInterface $em, Session $session): Response
    {
        $client = $this->getUser();
        $offers = $em->getRepository(Offre::class)->findAllOrderedByPrice();

        $form = $this->createForm(ClientOfferType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $session->set('mjmt_front_inscription_offer_id', $data['offre']->getId());
            $session->set(
                'mjmt_front_inscription_private_creation',
                !empty($data['creationNonPublique']) ? $data['creationNonPublique'] : false
            );

            return $this->redirectToRoute('mjmt_front_inscription_client');
        }

        return $this->render(
            'front/client/offer_selection.html.twig',
            array(
                'form' => $form->createView(),
                'client' => $client,
                'offers' => $offers
            )
        );
    }
}
