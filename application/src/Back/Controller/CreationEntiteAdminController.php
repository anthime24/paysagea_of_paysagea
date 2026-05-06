<?php

namespace App\Back\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreationEntiteAdminController extends CRUDController
{
    /**
     * Redirection vers l'application avec cette création
     *
     * @param type $id
     */
    public function application($id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        return $this->redirect(
            $this->generateUrl(
                'mjmt_application_homepage',
                array(
                    'creationId' => $object->getCreation()->getId(),
                    'hash' => $object->getCreation()->getReferenceEcriture()
                )
            )
        );
    }

}
