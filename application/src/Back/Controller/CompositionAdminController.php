<?php

namespace App\Back\Controller;

use App\Core\Entity\Annee;
use App\Core\Entity\CompositionEntite;
use App\Core\Entity\CompositionVue;
use App\Core\Entity\Entite;
use App\Core\Entity\EntitePhoto;
use App\Core\Entity\EntitePhotoEtat;
use App\Core\Entity\EntiteType;
use App\Core\Service\CopyService;
use ReflectionClass;
use RuntimeException;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use function count;
use function is_array;

class CompositionAdminController extends CRUDController
{

    /**
     * Retourne une liste de entités en recherchant par le nom
     *
     * @return JsonResponse
     * @throws AccessDeniedException
     */
    public function rechercheEntites()
    {
        $request = $this->getRequest();

        if ($request->get('nom') == null) {
            throw $this->createNotFoundException('Erreur de nom');
        }

        $nom = $request->get('nom');

        $entites = $this->getDoctrine()
            ->getManager()
            ->getRepository(Entite::class)
            ->findAllByNom($nom);

        return new JsonResponse($entites);
    }

    public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new ReflectionClass(
            $this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass()
        );

        if ($class->isAbstract()) {
            return $this->renderWithExtraParams(
                '@SonataAdmin/CRUD/select_subclass.html.twig',
                [
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ],
                null
            );
        }

        $newObject = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $newObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($newObject);

        $form = $this->admin->getForm();

        if (!is_array($fields = $form->all()) || 0 === count($fields)) {
            throw new RuntimeException(
                'No editable field defined. Did you forget to implement the "configureFormFields" method?'
            );
        }

        $form->setData($newObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);
                $this->admin->checkAccess('create', $submittedObject);

                try {
                    $newObject = $this->admin->create($submittedObject);

                    $this->saveCompositionEntite($newObject, $this->getRequest()->get('composition'));

                    if ($this->isXmlHttpRequest()) {
                        return $this->handleXmlHttpRequestSuccessResponse($request, $newObject);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_create_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($newObject);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if ($this->isXmlHttpRequest() && null !== ($response = $this->handleXmlHttpRequestErrorResponse(
                        $request,
                        $form
                    ))) {
                    return $response;
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_create_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                        'SonataAdminBundle'
                    )
                );
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        //$this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams(
            $template,
            [
                'action' => 'create',
                'form' => $formView,
                'object' => $newObject,
                'objectId' => null,
            ],
            null
        );
    }

    public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        // $this->checkParentChildAssociation($request, $existingObject);

        $this->admin->checkAccess('edit', $existingObject);

        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->admin->getForm();

        if (!is_array($fields = $form->all()) || 0 === count($fields)) {
            throw new RuntimeException(
                'No editable field defined. Did you forget to implement the "configureFormFields" method?'
            );
        }

        $form->setData($existingObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);

                try {
                    $existingObject = $this->admin->update($submittedObject);

                    $this->saveCompositionEntite($existingObject, $this->getRequest()->get('composition'));

                    if ($this->isXmlHttpRequest()) {
                        return $this->handleXmlHttpRequestSuccessResponse($request, $existingObject);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_edit_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($existingObject);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_lock_error',
                            [
                                '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
                                '%link_start%' => '<a href="' . $this->admin->generateObjectUrl(
                                        'edit',
                                        $existingObject
                                    ) . '">',
                                '%link_end%' => '</a>',
                            ],
                            'SonataAdminBundle'
                        )
                    );
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if ($this->isXmlHttpRequest() && null !== ($response = $this->handleXmlHttpRequestErrorResponse(
                        $request,
                        $form
                    ))) {
                    return $response;
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_edit_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                        'SonataAdminBundle'
                    )
                );
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        // $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams(
            $template,
            [
                'action' => 'edit',
                'form' => $formView,
                'object' => $existingObject,
                'objectId' => $objectId,
            ],
            null
        );
    }

    private function saveCompositionEntite($composition, $compositionGrille)
    {
        set_time_limit(10000);
        $dm = $this->getDoctrine()->getManager();
        $dm->persist($composition);

        // On supprime les entites de la composition (surtout dans le cas ou la taille de grille change)
        if ($composition->getCompositionEntites()) {
            foreach ($composition->getCompositionEntites() as $compositionEntite) {
                $dm->remove($compositionEntite);
            }
        }
        $dm->flush();

        $compositionContientEntite = false;
        foreach ($compositionGrille as $ligneKey => $ligneValue) {
            foreach ($ligneValue as $colonneKey => $colonneValue) {
                if (!empty($colonneValue)) {
                    $entite = $dm->getRepository(Entite::class)->findOneById($colonneValue);
                    if ($entite != null) {
                        $compositionEntite = new CompositionEntite();
                        $compositionEntite->setPositionX($colonneKey);
                        $compositionEntite->setPositionY($ligneKey);
                        $compositionEntite->setEntite($entite);
                        $compositionEntite->setComposition($composition);
                        $dm->persist($compositionEntite);
                        $compositionContientEntite = true;
                    }
                }
            }
        }
        $dm->flush();
        $dm->refresh($composition);

        // Génération des vues
        $composition->generateImages();
        $dm->flush();

        // Génération de l'entite
        $entite = $dm->getRepository(Entite::class)->findOneBy(array('composition' => $composition));

        if ($entite == null) {
            $entite = new Entite();
        }

        $entite->setNouveau(false);
        $entite->setLasso(false);
        $entite->setAcronyme('Composition ' . $composition->getId());
        $entite->setNom($composition->getNom());
        $entite->setGratuit($composition->getGratuit());
        $entite->setEntretien($composition->getEntretien());

        $entite->getRusticiteMultiples()->clear();
        foreach ($composition->getRusticiteMultiples() as $item) {
            $entite->addRusticiteMultiple($item);
        }

        $entite->getBesoinEauMultiples()->clear();
        foreach ($composition->getBesoinEauMultiples() as $item) {
            $entite->addBesoinEauMultiple($item);
        }

        $entite->setEntiteType($dm->getRepository(EntiteType::class)->findOneById(2)); // Type : Composition plante
        $entite->setComposition($composition);

        // On supprime les informations d'ensoleillement
        if ($entite->getEnsoleillements()) {
            foreach ($entite->getEnsoleillements() as $e) {
                $entite->getEnsoleillements()->removeElement($e);
            }
        }

        foreach ($composition->getEnsoleillements() as $e) {
            $entite->addEnsoleillement($e);
        }

        // On supprime les informations de type de sol
        if ($entite->getTypeSols()) {
            foreach ($entite->getTypeSols() as $ts) {
                $entite->getTypeSols()->removeElement($ts);
            }
        }

        foreach ($composition->getTypeSols() as $ts) {
            $entite->addTypeSol($ts);
        }

        // On supprime les informations de catégorie
        if ($entite->getCategories()) {
            foreach ($entite->getCategories() as $c) {
                $entite->getCategories()->removeElement($c);
            }
        }

        foreach ($composition->getCategories() as $c) {
            $entite->addCategory($c);
        }

        // On supprime les informations de style
        if ($entite->getStyles()) {
            foreach ($entite->getStyles() as $s) {
                $entite->getStyles()->removeElement($s);
            }
        }

        foreach ($composition->getStyles() as $s) {
            $entite->addStyle($s);
        }

        // On supprime les types de création
        if ($entite->getCreationTypes()) {
            foreach ($entite->getCreationTypes() as $ct) {
                $entite->getCreationTypes()->removeElement($ct);
            }
        }

        //On set les types créations
        foreach ($composition->getCreationTypes() as $ct) {
            $entite->addCreationType($ct);
        }

        foreach ($entite->getCreationEntites() as $ce) {
            $ce->setEntitePhoto(null);
            $dm->persist($ce);
            $dm->flush();
        }

        if ($entite->getEntitePhotos()) {
            foreach ($entite->getEntitePhotos() as $ep) {
                $dm->remove($ep);
                $dm->flush();
            }
        }

        $dm->persist($entite);
        $dm->flush();

        $compositionVues = $dm->getRepository(CompositionVue::class)->findAll();

        if ($compositionContientEntite) {
            foreach ($compositionVues as $compositionVue) {
                $entitePhoto = new EntitePhoto();

                $entitePhoto->setEntite($entite);
                $entitePhoto->setNom($composition->getNom() . ' ' . $compositionVue->getNom());
                $entitePhoto->setPhoto($composition->getPhoto($compositionVue->getSlug()));
                $entitePhoto->setHauteurEntite($composition->getHauteurComposition($compositionVue->getSlug()));
                $entitePhoto->setAnnee($dm->getRepository(Annee::class)->findOneById(1)); // Année 0
                $entitePhoto->setEntitePhotoEtat(
                    $dm->getRepository(EntitePhotoEtat::class)->findOneById(1)
                ); // Fleurie par défaut
                $entitePhoto->setCompositionVue($compositionVue);
                $dm->persist($entitePhoto);
                $dm->flush();

                $entitePhoto->testMkdirUpload();
                $photo = $composition->getAbsolutePath($compositionVue->getSlug());
                copy($photo, $entitePhoto->getAbsolutePath());
                $imageSize = getimagesize($photo);
                $entitePhoto->setLargeur($imageSize[0]);
                $entitePhoto->setHauteur($imageSize[1]);
                $entitePhoto->setPoids(filesize($photo));
                $pathParts = pathinfo($photo);
                $entitePhoto->setType($pathParts['extension']);
                $dm->flush();
            }

            foreach ($entite->getCreationEntites() as $ce) {
                $entitePhoto = $dm->getRepository(EntitePhoto::class)->findOneBy(
                    array('entite' => $entite, 'compositionVue' => $ce->getCompositionVue())
                );
                $ce->setEntitePhoto($entitePhoto);
                $dm->persist($ce);
                $dm->flush();
            }
        }
    }

    /**
     * Action de visualisation des vues de la composition
     * @param type $id
     */
    public function visualiser($id = null)
    {
        // the key used to lookup the template
        $templateKey = 'visualiser';

        $this->admin->setTemplate($templateKey, 'back/composition/visualiser.html.twig');

        $id = $this->getRequest()->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        return $this->render(
            $this->admin->getTemplate('visualiser'),
            array(
                'action' => $templateKey,
                'object' => $object,
            )
        );
    }


    public function duplicate(CopyService $copyService, $id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('CREATE', $object)) {
            throw new AccessDeniedException();
        }

        $newObject = $copyService->copyComposition($object);

        return $this->redirectTo($newObject);
    }
}
