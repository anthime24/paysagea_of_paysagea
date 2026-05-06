<?php

namespace App\Core\Service;

use App\Core\Entity\Composition;
use App\Core\Entity\Entite;
use App\Core\Entity\EntitePhoto;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use ReflectionMethod;


class EntiteService
{
    //LA PHOTO LA PLUS ANCIENNE EST CELLE QUI RESSORT PAR DEFAULT DANS LE LISTING
    public function verifiePhotoPrinicpale(EntityManagerInterface $em, $refImport = null, $mode = 'OLDEST_PHOTO')
    {
        $photoToCheck = $em->getRepository(\App\Core\Entity\Entite::class)->getQueryEntitesAvecPlusieursPhotoPrincipales($refImport, $mode);

        $reqReset = $em->getConnection()->prepare("UPDATE entite_photo set principale = 0 where entite_id = :entite_id");
        $reqPromote = $em->getConnection()->prepare("UPDATE entite_photo set principale = 1 where id = :entite_photo_id");

        $nbrTraitesAvantFlush = 0;
        foreach($photoToCheck as $photoItem) {
            if(isset($photoItem['photoToPromote'])) {
                $reqReset->bindValue(':entite_id', $photoItem['id']);
                $reqReset->execute();

                $reqPromote->bindValue(':entite_photo_id', $photoItem['photoToPromote']);
                $reqPromote->execute();

                $nbrTraitesAvantFlush++;
                if($nbrTraitesAvantFlush > 350) {
                    $em->flush();
                    $nbrTraitesAvantFlush = 0;
                }
            }
        }
        $em->flush();
    }
}