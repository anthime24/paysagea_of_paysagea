<?php

namespace App\Front\Controller;

use App\Core\Entity\BanquePhoto;
use App\Core\Entity\Creation;
use App\Core\Entity\CreationType;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Projet;
use App\Front\Service\InscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TesterController extends AbstractController
{
    public function jardin(InscriptionService $inscriptionService): Response
    {
    $creation = $inscriptionService->creationProjetTest('jardin');

        return $this->redirectToRoute(
            'mjmt_application_homepage',
            array(
                'creationId' => $creation->getId(),
                'hash' => $creation->getReferenceEcriture()
            )
        );
    }

    public function balcon(InscriptionService $inscriptionService): Response
    {
    $creation = $inscriptionService->creationProjetTest('balcon');

        return $this->redirectToRoute(
            'mjmt_application_homepage',
            array(
                'creationId' => $creation->getId(),
                'hash' => $creation->getReferenceEcriture()
            )
        );
    }

}
