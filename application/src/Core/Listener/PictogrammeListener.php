<?php

namespace App\Core\Listener;

use App\Core\Entity\Entite;
use Doctrine\ORM\Event\OnFlushEventArgs;

class PictogrammeListener {
    public function onFlush(OnFlushEventArgs $args) {
        $em = $args->getEntityManager();

        $uow = $em->getUnitOfWork();
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()
        );

        $entitesToUpdates = array();

        foreach ($entities as $entity) {
            if (!($entity instanceof Entite)) {
                continue;
            }

            $pictoNouveauFlagChange = false;
            $pictoPromoFlagChange = false;
            $pictoCoupCoeurFlagChange = false;

            if($entity->hasPictoNouveau() != $entity->getPictoNouveauComputedFlag()) {
                $entity->setPictoNouveauComputedFlag($entity->hasPictoNouveau());
                $pictoNouveauFlagChange = true;
            }

            if($entity->hasPictoPromo() != $entity->getPictoPromoComputedFlag()) {
                $entity->setPictoPromoComputedFlag($entity->hasPictoPromo());
                $pictoPromoFlagChange = true;
            }

            if($entity->hasPictoCoupCoeur() != $entity->getPictoCoupCoeurComputedFlag()) {
                $entity->setPictoCoupCoeurComputedFlag($entity->hasPictoCoupCoeur());
                $pictoCoupCoeurFlagChangeChange = true;
            }

            if($pictoNouveauFlagChange === true || $pictoPromoFlagChange === true || $pictoCoupCoeurFlagChange === true) {
                $entitesToUpdates[] = $entity;
            }
        }

        if(count($entitesToUpdates) > 0) {
            $meta = $em->getClassMetadata(get_class($entitesToUpdates[0]));

            foreach($entitesToUpdates as $entity) {
                $uow->recomputeSingleEntityChangeSet($meta, $entity);
            }
        }
    }
}