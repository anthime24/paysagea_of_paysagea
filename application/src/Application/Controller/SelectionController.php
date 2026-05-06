<?php

namespace App\Application\Controller;

use App\Core\Entity\CompositionVue;
use App\Core\Entity\Creation;
use App\Core\Entity\CreationType;
use App\Core\Entity\Entite;
use App\Core\Entity\EntitePhoto;
use App\Core\Entity\EntiteType;
use App\Core\Entity\Precipitation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SelectionController extends AbstractController
{

    public function rechercheFiltre(int $creationId): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $tab = null;

        // Enregistrement des filtres dans la session
        $session = $this->get("session");

        // Récupération des élément envoyés depuis le formulaire
        $typeEntiteGet = $request->query->get('type');
        $typeEntitePost = $request->request->get('mjmt_appbundle_filtre_entite_type');
        $entiteTypeSlug = (isset($typeEntiteGet) && $typeEntiteGet != '') ? $typeEntiteGet : $typeEntitePost;
        $entiteType = $em->getRepository(EntiteType::class)->findOneBySlug($entiteTypeSlug);

        if ($entiteType != null) {
            $filtresPost = $request->request->get('marque_blanche_appbundle_filtre_' . $entiteType->getSlug());

            if (isset($filtresPost)) {
                $recherche = $filtresPost;
                $session->set('marque_blanche_appbundle_filtre_' . $entiteType->getSlug(), $recherche);
            } else {
                $recherche = $session->get('marque_blanche_appbundle_filtre_' . $entiteType->getSlug());
            }

            $nbResultat = 16;
            $offsetGet = $request->query->get('offset');
            $depart = (isset($offsetGet)) ? $offsetGet : 0;
            if (isset($recherche['arrosage'])) {
                $precipitationMax = $this->appliquerRegleArrosage($creationId, true);
            } else {
                $precipitationMax = $this->appliquerRegleArrosage($creationId, false);
            }

            $creation = $em->getRepository(Creation::class)->findOneById($creationId);

            $nouvelleRechercheSurNom = false;

            if (!$creation->getCreationType() || $creation->getCreationType() == null) {
                $creation->setCreationType($em->getRepository(CreationType::class)->find(1));
            }

            // Recherche des entitées
            $entites = $em->getRepository(Entite::class)->recupDepuisFiltre(
                $entiteType,
                $recherche,
                $precipitationMax,
                $nbResultat,
                $depart,
                'result',
                $creation->getCreationType()->getId(),
                $creation->getSurface(),
                $request->getLocale()
            );
            if (count($entites) == 0 && $depart == 0 && array_key_exists('nom', $recherche)) {
                $precipitationMaxAvecArrosage = $this->appliquerRegleArrosage($creationId, true);
                $newRecherche['nom'] = $recherche['nom'];
                $nouvelleRechercheSurNom = true;

                $session->set('marque_blanche_appbundle_filtre_' . $entiteType->getSlug(), $newRecherche);
                $entites = $em->getRepository(Entite::class)->recupDepuisFiltre(
                    $entiteType,
                    $newRecherche,
                    $precipitationMaxAvecArrosage,
                    $nbResultat,
                    $depart,
                    'result',
                    $creation->getCreationType()->getId(),
                    $creation->getSurface(),
                    $request->getLocale()
                );

                //Recup du nombre total de résultats
                $nbTotalResult = $em->getRepository(Entite::class)->recupDepuisFiltre(
                    $entiteType,
                    $newRecherche,
                    $precipitationMaxAvecArrosage,
                    null,
                    null,
                    'nbresult',
                    $creation->getCreationType()->getId(),
                    $creation->getSurface(),
                    $request->getLocale()
                );

                if ($depart == 0) {
                    //Recup du nombre total de résultats
                    $nbTotalResult = $em->getRepository(Entite::class)->recupDepuisFiltre(
                        $entiteType,
                        $newRecherche,
                        $precipitationMaxAvecArrosage,
                        null,
                        null,
                        'nbresult',
                        $creation->getCreationType()->getId(),
                        $creation->getSurface(),
                        $request->getLocale()
                    );
                    $session->set('nbTotalResult' . $entiteType->getSlug(), $nbTotalResult);
                } else {
                    //Recup du nombre total de résultats
                    $nbTotalResult = $session->get('nbTotalResult' . $entiteType->getSlug());
                }
            } else {
                if ($depart == 0) {
                    //Recup du nombre total de résultats
                    $nbTotalResult = $em->getRepository(Entite::class)->recupDepuisFiltre(
                        $entiteType,
                        $recherche,
                        $precipitationMax,
                        null,
                        null,
                        'nbresult',
                        $creation->getCreationType()->getId(),
                        $creation->getSurface(),
                        $request->getLocale()
                    );
                    $session->set('nbTotalResult' . $entiteType->getSlug(), $nbTotalResult);
                } else {
                    //Recup du nombre total de résultats
                    $nbTotalResult = $session->get('nbTotalResult' . $entiteType->getSlug());
                }
            }

            if ($depart == 0) {
                //Nombre total de résultats affichés
                $nbEntitesAffichees = count($entites);
                $session->set('nbEntitesAffichees', $nbEntitesAffichees);
            } else {
                //Nombre total de résultats affichés
                $nbEntitesAffichees = $depart + count($entites);
                $session->set('nbEntitesAffichees', $nbEntitesAffichees);
            }

            if ($creation->getProjet()->getClient()) {
                if ($creation->getProjet()->getClient()->getAccesCompletPlantesObjets()) {
                    $accesCompletPlantesObjets = true;
                } else {
                    $accesCompletPlantesObjets = false;
                }
            } else {
                $accesCompletPlantesObjets = false;
            }

            $html = $this->render(
                'application/popin/partial_ajout/affichage_resultat_filtres.html.twig',
                array(
                    'entites' => $entites,
                    'entiteType' => $entiteType,
                    'nbEntitesAffichees' => $nbEntitesAffichees,
                    'nbTotalResult' => $nbTotalResult,
                    'accesCompletPlantesObjets' => $accesCompletPlantesObjets,
                    'nouvelleRechercheSurNom' => $nouvelleRechercheSurNom
                )
            );

            $tab[$entiteType->getSlug()] = $html->getContent();
            $tab["nbEntitesAffichees"] = $nbEntitesAffichees;
            $tab["nbTotalResult"] = $nbTotalResult;
            $tab["nbEntiteRecuperes"] = count($entites);
        }

        return new JsonResponse($tab);
    }

    /**
     *
     * @param type $creation
     * @return JsonResponse
     */
    public function rechercheLasso(int $creationId): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $tab = null;
        $session = $this->get("session");
        $nbResultat = 16;
        $offsetGet = $request->query->get('offset');
        $depart = (isset($offsetGet)) ? $offsetGet : 0;
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);
        $nouvelleRechercheSurNom = false;
        if (!$creation->getCreationType() || $creation->getCreationType() == null) {
            $creation->setCreationType($em->getRepository(CreationType::class)->find(1));
        }
        // Recherche des entitées
        $entites = $em->getRepository(Entite::class)->recupDepuisFiltreLasso(
            $nbResultat,
            $depart,
            'result',
            $creation->getCreationType()->getId(),
            $creation->getSurface()
        );

        if ($depart == 0) {
            //Recup du nombre total de résultats
            $nbTotalResult = $em->getRepository(Entite::class)->recupDepuisFiltreLasso(
                null,
                null,
                'nbresult',
                $creation->getCreationType()->getId(),
                $creation->getSurface()
            );
            $session->set('nbTotalResultLasso', $nbTotalResult);
        } else {
            //Recup du nombre total de résultats
            $nbTotalResult = $session->get('nbTotalResultLasso');
        }

        if ($depart == 0) {
            //Nombre total de résultats affichés
            $nbEntitesAffichees = count($entites);
            $session->set('nbEntitesAfficheesLasso', $nbEntitesAffichees);
        } else {
            //Nombre total de résultats affichés
            $nbEntitesAffichees = $depart + count($entites);
            $session->set('nbEntitesAfficheesLasso', $nbEntitesAffichees);
        }

        if ($creation->getProjet()->getClient()) {
            if ($creation->getProjet()->getClient()->getAccesCompletPlantesObjets()) {
                $accesCompletPlantesObjets = true;
            } else {
                $accesCompletPlantesObjets = false;
            }
        } else {
            $accesCompletPlantesObjets = false;
        }

        $html = $this->render(
            'application/popin/partial_ajout/affichage_resultat_filtres_lasso.html.twig',
            array(
                'entites' => $entites,
                'nbEntitesAffichees' => $nbEntitesAffichees,
                'nbTotalResult' => $nbTotalResult,
                'accesCompletPlantesObjets' => $accesCompletPlantesObjets
            )
        );
        $tab["nbEntitesAffichees"] = $nbEntitesAffichees;
        $tab["nbTotalResult"] = $nbTotalResult;
        $tab['entiteLasso'] = $html->getContent();
        return new JsonResponse($tab);
    }


    public function ajoutElement(int $entiteId, $bLasso = false): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $entite = $em->getRepository(Entite::class)->findOneBy(array('id' => $entiteId));
        $timestamp = round(microtime(true) * 1000);

        $sens = $request->get('sens');
        $compositionVueId = $request->get('composition-vue-id');

        if ($entite->getComposition()) {
            if ($compositionVueId && $sens) {
                $composition = $entite->getComposition();
                $compositionVue = $em->getRepository(CompositionVue::class)->findOneById($compositionVueId);

                if ($sens == "gauche") {
                    $newCompositionOrdre = $compositionVue->getOrdre() + 1;
                    if ($newCompositionOrdre > 4) {
                        $newCompositionOrdre = 1;
                    }
                    $newCompositionVue = $em->getRepository(CompositionVue::class)->findOneByOrdre(
                        $newCompositionOrdre
                    );
                } else {
                    $newCompositionOrdre = $compositionVue->getOrdre() - 1;
                    if ($newCompositionOrdre < 1) {
                        $newCompositionOrdre = 4;
                    }
                    $newCompositionVue = $em->getRepository(CompositionVue::class)->findOneByOrdre(
                        $newCompositionOrdre
                    );
                }
                $entitePhoto = $em->getRepository(EntitePhoto::class)->findOneBy(
                    array('entite' => $composition->getEntite(), 'compositionVue' => $newCompositionVue)
                );
                $newCompositionVueId = $newCompositionVue->getId();
            } else {
                $entitePhoto = $entite->getPhotoPrincipale();
                $newCompositionVueId = 1;
            }
        } else {
            $entitePhoto = $entite->getPhotoPrincipale();
            $newCompositionVueId = 0;
        }

        $maSelection = $this->render(
            'application/selection/ma_selection_html_ajoute.html.twig',
            array(
                'entite' => $entite,
                'entitePhoto' => $entitePhoto,
                'timestamp' => $timestamp
            )
        );

        $contenu = $this->render(
            'application/selection/entite_html_ajoute.html.twig',
            array(
                'entite' => $entite,
                'entitePhoto' => $entitePhoto,
                'compositionVueId' => $newCompositionVueId,
                'timestamp' => $timestamp,
                'lasso' => $bLasso
            )
        );

        $dernierAjout = $this->render(
            'application/selection/dernier_ajout_html_ajoute.html.twig',
            array(
                'entite' => $entite,
                'entitePhoto' => $entitePhoto
            )
        );

        $tab = array();
        $tab['maSelection'] = $maSelection->getContent();
        $tab['contenu'] = $contenu->getContent();
        $tab['dernierAjout'] = $dernierAjout->getContent();

        return new JsonResponse($tab);
    }

    private function appliquerRegleArrosage(int $creationId, bool $arrosage): float
    {
        $em = $this->getDoctrine()->getManager();

        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        $projet = $creation->getProjet();
        $precipitation = $projet->getPrecipitation();
        $precipitationMax = null;

        if ($precipitation != null) {
            $precipitationMaxDispo = $precipitation->getId();
            if (!$arrosage) {
                $precipitationMax = $precipitationMaxDispo;
            } else {
                $precipitationMaxPossible = $em->getRepository(Precipitation::class)->recupValeurMax();
                $precipitationMax = $precipitationMaxPossible[0]['id_max'];
            }
        } else {
            $precipitationMaxPossible = $em->getRepository(Precipitation::class)->recupValeurMax();
            $precipitationMax = $precipitationMaxPossible[0]['id_max'];
        }

        return $precipitationMax;
    }
}
