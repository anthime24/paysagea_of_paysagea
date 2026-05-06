<?php

namespace App\Back\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EntiteAdminController extends CRUDController
{

    public function duplicate($id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('CREATE', $object)) {
            throw new AccessDeniedException();
        }

        $newObject = $this->get('mjmt_core.service.copy')->copyEntite($object);

        return $this->redirectTo($newObject);
    }
}
