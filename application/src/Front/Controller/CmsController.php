<?php

namespace App\Front\Controller;

use App\Core\Entity\Cms;
use App\Front\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Swift_Mailer;
use Swift_Message;
use function Doctrine\ORM\QueryBuilder;


/**
 * Class CmsController
 * @package App\Front\Controller
 */
class CmsController extends AbstractController
{
    private $request;
    private $mailer;
    private $translator;
    private $session;
    private $em;

    private function _contact(\App\Core\Entity\Cms $cmsEntity, $locale) : Response
    {
        $request = $this->request;
        $translator = $this->translator;
        $mailer = $this->mailer;
        $session = $this->session;

        $translatedUrl = $this->em->getRepository(\App\Core\Entity\Cms::class)->findTranslatedUrl($cmsEntity, $locale);
        if($translatedUrl === null) {
            $translatedUrl = $cmsEntity->getUrl();
        }

        $form = $this->createForm(ContactType::class, null, array(
            'action' => $this->generateUrl('mjmt_front_page_cms', array('url' => $translatedUrl)),
            'method' => 'POST'
        ));

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

            return $this->redirectToRoute('mjmt_front_page_cms', array('url' => $translatedUrl));
        }

        return $this->render(
            'front/contact/index.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    public function index(Request $request,
                          Swift_Mailer $mailer,
                          TranslatorInterface $translator,
                          Session $session,
                          EntityManagerInterface $em,
                          $url): Response
    {
        $this->request = $request;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->session = $session;
        $this->em = $em;

        $locale = $this->request->getLocale();
        $cmsEntity = null;

        if($url === null || trim($url) == "") {
            return $this->redirectToRoute('mjmt_front_home');
        }

        $queryFr = $this->em->createQueryBuilder();
        $queryFr->select('c')
            ->from(\App\Core\Entity\Cms::class, 'c')
            ->where('c.url = :url')
            ->setParameter(':url', $url);

        $queryTranslated = $this->em->createQueryBuilder();
        $queryTranslated->select('c')
            ->from(\App\Core\Entity\Cms::class, 'c')
            ->leftJoin(
                \App\Core\Entity\Translation\Cms::class,
                'ct',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $queryTranslated->expr()->andX(
                    $queryTranslated->expr()->eq("IDENTITY(ct.object)", "c.id"),
                    $queryTranslated->expr()->eq("ct.field", "'url'")
                )
            )
            ->where('ct.content = :url')
            ->setParameters(
                array(
                    'url' => $url
                )
            );

        if($locale == 'fr') {
            $cmsEntity = $queryFr->getQuery()->getOneOrNullResult();

            //on recherche si il existe une page ayant cette url dans une autre langue
            if($cmsEntity == null) {
                $entities = $queryTranslated->getQuery()->getResult();
                if(count($entities) > 0) {
                    $cmsEntity = $entities[0];

                    $translatedUrl = $this->em->getRepository(\App\Core\Entity\Cms::class)->findTranslatedUrl($cmsEntity, 'fr');
                    if($translatedUrl !== null) {
                        return $this->redirectToRoute('mjmt_front_page_cms', array('url' => $translatedUrl));
                    }
                }
            }
        } else {
            $entities = $queryTranslated->getQuery()->getResult();
            if(count($entities) > 0) {
                $cmsEntity = $entities[0];
            }

            //on recherche si il existe une page ayant cette url en français
            if($cmsEntity == null) {
                $frCmsEntity = $queryFr->getQuery()->getOneOrNullResult();
                if($frCmsEntity !== null) {
                    $cmsEntity = $frCmsEntity;

                    $translatedUrl = $this->em->getRepository(\App\Core\Entity\Cms::class)->findTranslatedUrl($cmsEntity, $locale);
                    if($translatedUrl !== null) {
                        return $this->redirectToRoute('mjmt_front_page_cms', array('url' => $translatedUrl));
                    }
                }
            }
        }

        if($cmsEntity === null) {
            throw new NotFoundHttpException();
        }

        if($cmsEntity->getSlug() == 'contact') {
            return $this->_contact($cmsEntity, $locale);
        }

        return $this->render(
            'front/cms/index.html.twig',
            array(
                'page' => $cmsEntity
            )
        );
    }

}
