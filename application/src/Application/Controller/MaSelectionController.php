<?php

namespace App\Application\Controller;

use App\Core\Entity\Creation;
use App\Core\Entity\Entite;
use App\Core\Entity\EntitePhoto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MaSelectionController extends AbstractController
{
    public function liste(int $creationId): Response
    {
        $em = $this->getDoctrine()->getManager();

        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        return $this->render(
            'application/selection/selection.html.twig',
            array(
                'creation' => $creation
            )
        );
    }

    public function description(int $entiteId): Response
    {
        $em = $this->getDoctrine()->getManager();

        $entite = $em->getRepository(Entite::class)->findOneById($entiteId);
        $entitePhoto = $em->getRepository(EntitePhoto::class)->getOrdoredEntitePhoto($entite);

        $tmpListEntites = array();
        if ($entite->getComposition()) {
            foreach ($entite->getComposition()->getCompositionEntites() as $compositionEntite) {
                if (array_key_exists($compositionEntite->getEntite()->getId(), $tmpListEntites)) {
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['entite'] = $compositionEntite->getEntite(
                    );
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] += 1;
                } else {
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['entite'] = $compositionEntite->getEntite(
                    );
                    $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] = 1;
                }
            }
        }

        return $this->render(
            'application/popin/informations_entite.html.twig',
            array(
                'entite' => $entite,
                'entitePhoto' => $entitePhoto,
                'entitesComposition' => $tmpListEntites
            )
        );
    }

}
