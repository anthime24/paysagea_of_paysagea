<?php

namespace App\Front\Controller;

use App\Front\Form\ContactType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    public function index(
        Request $request,
        Swift_Mailer $mailer,
        TranslatorInterface $translator,
        Session $session
    ): Response {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new Swift_Message())
                ->setSubject($translator->trans('Prise de contact sur le site internet'))
                ->setFrom('contact@monjardin-materrasse.com')
                ->setTo('contact@monjardin-materrasse.com')
                ->setBcc('log.mjmt@tknoweb.com')
                ->setBody(
                    $this->renderView(
                        'front/email/contact.txt.twig',
                        array(
                            'data' => $form->getData()
                        )
                    )
                );

            $mail = $mailer->send($message);

            if ($mail) {
                $session->getFlashBag()->add(
                    'success',
                    $translator->trans('Votre message a bien été envoyé.')
                );
            } else {
                $session->getFlashBag()->add(
                    'danger',
                    $translator->trans('Le message n\'a pas pu être transmis. Merci de nous contacter directement.')
                );
            }

            return $this->redirectToRoute('mjmt_front_contact');
        }

        return $this->render(
            'front/contact/index.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

}
