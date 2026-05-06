<?php

namespace App\Back\Controller;

use App\Core\Entity\Creation;
use App\Core\Entity\Projet;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BanquePhotoAdminController extends CRUDController
{

    /**
     * On crée une création temporaire et on redirige vers l'application pour définir les proportions par défaut de la photo
     * @param Request $request [description]
     * @return [type]  [description]
     */
    public function proportions($id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('PROPORTIONS', $object)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        // On crée le projet temporaire
        $projet = new Projet();
        $projet->setNom('Projet temporaire pour initialiser les proportions de la photo gratuite');
        $projet->setLatitude(0.0);
        $projet->setLongitude(0.0);
        $projet->setProjetType($object->getBanquePhotoType());

        $em->persist($projet);
        $em->flush();

        // On crée la création temporaire
        $creation = new Creation();
        $creation->setBanquePhoto($object);
        $creation->setProjet($projet);
        $creation->setCreationType($object->getBanquePhotoType());
        $creation->setNom('Création temporaire pour initialiser les proportions de la photo gratuite');

        $creation->initialiserInformationsBanquePhoto();

        $em->persist($creation);
        $em->flush();

        $creation->moveUpload();

        // On redirige vers l'application
        return $this->redirect(
            $this->generateUrl(
                'mjmt_application_homepage',
                array(
                    'creationId' => $creation->getId(),
                    'hash' => $creation->getReferenceEcriture(),
                    'initialisation' => 'proportions'
                )
            )
        );
    }
}
