<?php

namespace App\Back\Controller;

use App\Back\Form\Type\ImportType;
use App\Core\Entity\Annee;
use App\Core\Entity\BesoinEau;
use App\Core\Entity\Categorie;
use App\Core\Entity\Couleur;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Entite;
use App\Core\Entity\EntitePhoto;
use App\Core\Entity\EntitePhotoEtat;
use App\Core\Entity\EntiteSousType;
use App\Core\Entity\EntiteType;
use App\Core\Entity\Entretien;
use App\Core\Entity\Marque;
use App\Core\Entity\Matiere;
use App\Core\Entity\Mois;
use App\Core\Entity\Rusticite;
use App\Core\Entity\Style;
use App\Core\Entity\TypeSol;
use App\Core\Service\EntiteService;
use App\Core\Utility\Slug;
use App\Core\Utility\Utility;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImportAdminController extends CRUDController
{
    /**
     * Page d'informations pour l'import d'entités (affichage des identifiants des tables liées à une entité => couleur, ...)
     */
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ImportType::class);

        $em = $this->getDoctrine()->getManager();
        $entiteTypes = $em->getRepository(EntiteType::class)->findAll();
        $entiteSousTypes = $em->getRepository(EntiteSousType::class)->findAll();
        $couleurs = $em->getRepository(Couleur::class)->findAll();
        $ensoleillements = $em->getRepository(Ensoleillement::class)->findAll();
        $typesSols = $em->getRepository(TypeSol::class)->findAll();
        $styles = $em->getRepository(Style::class)->findAll();
        $categories = $em->getRepository(Categorie::class)->findAll();
        $entretiens = $em->getRepository(Entretien::class)->findAll();
        $matieres = $em->getRepository(Matiere::class)->findAll();
        $marques = $em->getRepository(Marque::class)->findAll();
        $rusticite = $em->getRepository(Rusticite::class)->findAll();

        $importingUser = null;

        return $this->render(
            'back/import/list.html.twig',
            array(
                'form' => $form->createView(),
                'entiteTypes' => $entiteTypes,
                'entiteSousTypes' => $entiteSousTypes,
                'couleurs' => $couleurs,
                'ensoleillements' => $ensoleillements,
                'typesSols' => $typesSols,
                'styles' => $styles,
                'categories' => $categories,
                'entretiens' => $entretiens,
                'matieres' => $matieres,
                'marques' => $marques,
                'rusticite' => $rusticite,
                'importingUser' => $importingUser
            )
        );
    }

    /**
     * Fichier d'export des entités (ce même fichier est utilisé pour l'import)
     */
    public function exportAction(Request $request)
    {
        if (false === $this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        $container = $this->container;

        $locale = "fr";
        if($request->getSession()->has('_locale')){
            $locale = $request->getSession()->get('_locale');
        } else {
            $locale = $request->getLocale();
        }

        $em = $container->get('doctrine')->getManager();

        $response = new StreamedResponse(
            function () use ($container, $locale) {
                $em = $container->get('doctrine')->getManager();

                // On récupère les entités pour l'export csv
                $results = $em->getRepository(Entite::class)->getEntitesForCsvExport($locale);

                $handle = fopen('php://output', 'r+');

                $i = 0;
                foreach ($results as $r) {
                    $values = array();
                    foreach ($r as $key => $value) {
                        $values[] = utf8_decode($i == 0 ? $key : $value);
                    } // Pour la première ligne on affiche le nom de la colonne
                    fputcsv($handle, $values, ';');
                    $i++;
                }

                fclose($handle);
            }
        );

        $response->headers->set('Content-Type', 'text/csv;');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="export_mjmt_entite_' . date('Y-m-d-H-i-s') . '.csv"'
        );
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function exportExterneAction(Request $request)
    {
        if (false === $this->admin->isGranted('EXPORT')) {
            throw new AccessDeniedException();
        }

        $container = $this->container;

        $locale = "fr";
        if($request->getSession()->has('_locale')){
            $locale = $request->getSession()->get('_locale');
        } else {
            $locale = $request->getLocale();
        }

        $em = $container->get('doctrine')->getManager();

        $response = new StreamedResponse(
            function () use ($container, $locale) {
                $em = $container->get('doctrine')->getManager();

                // On récupère les entités pour l'export csv
                $results = $em->getRepository(Entite::class)->getEntitesForCsvExport(null);

                $handle = fopen('php://output', 'r+');

                $i = 0;
                foreach ($results as $r) {
                    $values = array();
                    foreach ($r as $key => $value) {
                        $values[] = utf8_decode($i == 0 ? $key : $value);
                    } // Pour la première ligne on affiche le nom de la colonne
                    fputcsv($handle, $values, ';');
                    $i++;
                }

                fclose($handle);
            }
        );

        $response->headers->set('Content-Type', 'text/csv;');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="export_externe_mjmt_entite_' . date('Y-m-d-H-i-s') . '.csv"'
        );
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function checkFileHeaders($file, $headerKeys)
    {
        $checked = true;
        $columnList = $headerKeys;
        $errors = array();

        if (($handle = fopen($file, "r")) !== false) {
            $data = fgetcsv($handle, 99999, ";", '"');
            if($data !== false) {
                foreach($data as $colIndex => $colValue) {
                    if(trim($colValue) != "" && isset($columnList[$colIndex])) {
                        $colValue = preg_replace("/\xEF\xBB\xBF/", "", $colValue);
                        $colName = preg_replace("/\xEF\xBB\xBF/", "", $columnList[$colIndex]);

                        if(trim($colName) != trim($colValue)) {
                            $errors[] = trim($colValue);
                        }
                    }
                }
            }
            fclose($handle);
        }

        if(count($errors) > 0) {
            $checked = false;
            throw new \Exception("Le nom des colonnes du fichier ne correspond pas aux nom attendus : " . implode(", ", $errors));
        }
    }

    /*
    public function importTraitement(KernelInterface $kernel, Request $request)
    {
        throw new AccessDeniedException();
    }
    */

    public function myErrorHandler($errno, $errstr, $errfile, $errline) {
        if($errno == E_USER_ERROR || $errno == E_USER_WARNING) {
            dump('errno: ' . $errno);
            dump('errstr: ' . $errstr);
            dump('errfile: ' . $errfile);
            dump('errline: ' . $errline);
        }
    }

    /**
     * Traitement de l'import du fichier csv d'entités (plantes et objets)
     * Attention - si il y a un problème pendant le traitement des lignes du fichier, l'import est annulé
     */
    public function importTraitement(KernelInterface $kernel, EntiteService $entiteService, Request $request)
    {
        //set_error_handler(array($this, "myErrorHandler"));

        if (false === $this->admin->isGranted('IMPORT_TRAITEMENT')) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return new Response('Formulaire invalide');
        }

        if ($form->get('fichier')->getData()->getClientOriginalExtension() != 'csv') {
            return new Response(
                'L\'extension du fichier importé n\'est pas valide (fichier attendu de type CSV séparateur point-virugle)'
            );
        }

        set_time_limit(0);
        ini_set("memory_limit", "2048M");

        $identifiantImport = 'IMPORT_CLASSIQUE_' . date('Y-m-d:H:i:s', strtotime('now'));
        $csvFile = $form->get('fichier')->getData()->getPathName();
        $desactiverEntiteNonPresente = false;
        $currentUser = $this->getUser();
        $importingUser = null;

        $response = new StreamedResponse(
            function () use ($kernel, $csvFile, $desactiverEntiteNonPresente, $currentUser, $importingUser, $entiteService, $identifiantImport) {
                $em = $this->getDoctrine()->getManager();
                $entityTranslationRepository = $em->getRepository(\App\Core\Entity\Translation\Entite::class);

                $lignesAvecWarning = array();
                $lignesAmbigues = array();

                // Les photos sont envoyées par FTP par le client
                $pathToPhotoDir = $kernel->getProjectDir() . '/public/import/photos/';
                echo 'Début du traitement d\'import - ' . date('d-m-Y H:i:s') . "\r\n";
                flush();

                // Liste des colonnes du fichiers
                $key = array(
                    'id',
                    'type',
                    'acronyme',
                    'nom',
                    'nom_vernaculaire',
                    'sous_type',
                    'prix_mini',
                    'gratuit',
                    'rusticite_valeur',
                    'besoin_eau',
                    'janvier',
                    'fevrier',
                    'mars',
                    'avril',
                    'mai',
                    'juin',
                    'juillet',
                    'aout',
                    'septembre',
                    'octobre',
                    'novembre',
                    'decembre',
                    'couleurs',
                    'couleurs_fleurs',
                    'ensoleillements',
                    'types_sols',
                    'styles',
                    'categories',
                    'matieres',
                    'marque',
                    'pot',
                    'diametre_pot',
                    'hauteur_pot',
                    'diametre_final',
                    'hauteur_finale',
                    'caduc_persistant',
                    'toxique_alimentaire',
                    'toxique_alimentaire_information',
                    'divers',
                    'conseil_printemps',
                    'conseil_ete',
                    'conseil_automne',
                    'conseil_hiver',
                    'entretien',
                    'annuelle',
                    'photo_fleurie_0an',
                    'hauteur_fleurie_0an',
                    'photo_non_fleurie_0an',
                    'hauteur_non_fleurie_0an',
                    'photo_feuille_automne_0an',
                    'hauteur_feuille_automne_0an',
                    'photo_sans_feuille_0an',
                    'hauteur_sans_feuille_0an',
                    'photo_fleurie_2an',
                    'hauteur_fleurie_2an',
                    'photo_non_fleurie_2an',
                    'hauteur_non_fleurie_2an',
                    'photo_feuille_automne_2an',
                    'hauteur_feuille_automne_2an',
                    'photo_sans_feuille_2an',
                    'hauteur_sans_feuille_2an',
                    'lasso',
                    'lasso_photo',
                    'rusticite_id'
                );

                $translatedMandatoryKeys = array(
                    "nom" => "nom",
                    "nom_vernaculaire" => "nomVernaculaire"
                );

                $translatedKeys = array(
                    "nom" => "nom",
                    "nom_vernaculaire" => "nomVernaculaire",
                    "conseil_automne" => "conseilAutomne",
                    "conseil_hiver" => "conseilHiver",
                    "conseil_printemps" => "conseilPrintemps",
                    "conseil_ete" => "conseilEte",
                    "divers" => "divers"
                );

                $optionnalKeys = array(
                    'rusticite_id'
                );

                // Liste des colonnes qui sont en multi-valeurs (séparation par une virgule)
                $explodeString = array(
                    'sous_type',
                    'couleurs',
                    'couleurs_fleurs',
                    'ensoleillements',
                    'types_sols',
                    'styles',
                    'categories',
                    'matieres',
                    'besoin_eau',
                    'rusticite_id'
                );

                // Liste des colonnes à nettoyer et formater
                $mbFormatString = array(
                    'nom',
                    'nom_vernaculaire',
                    'divers',
                    'conseil_printemps',
                    'conseil_ete',
                    'conseil_automne',
                    'conseil_hiver',
                    'toxique_alimentaire_information'
                );

                // Liste des colonnes qui contiennent une photo
                $photos = array(
                    'photo_fleurie_0an',
                    'photo_non_fleurie_0an',
                    'photo_feuille_automne_0an',
                    'photo_sans_feuille_0an',
                    'photo_fleurie_2an',
                    'photo_non_fleurie_2an',
                    'photo_feuille_automne_2an',
                    'photo_sans_feuille_2an'
                );

                $index = 1;

                // On convertit le fichier en UTF-8 si il ne l'est pas
                if (file_exists($csvFile)) {
                    $contents = file_get_contents($csvFile);
                    if (!mb_check_encoding($contents, 'UTF-8')) {
                        echo 'Conversion du fichier en UTF-8' . "\r\n";
                        flush();
                        if (($fileToUtf8 = fopen($csvFile, 'w+')) !== false) {
                            fputs($fileToUtf8, utf8_encode($contents));
                            fclose($fileToUtf8);
                        }
                    }
                }

                $processedRows = array();

                $fileTooBigException = false;
                $nbrLignes = Utility::nbrLinesInCsv($csvFile);
                if($nbrLignes > 500) {
                    $fileTooBigException = true;
                }

                try {
                    $this->checkFileHeaders($csvFile, $key);
                } catch(\Exception $ex) {
                    echo $ex->getMessage();
                    return;
                }

                if($importingUser !== null && ($importingUser->getId() != $currentUser->getId())) {
                    echo "L'utilisateur " . $currentUser->getUsername() . " est déja en train d'importer un fichier";
                    return;
                } else {
                    //$currentUser->setIsImporting(true);
                    $em->flush();
                }

                // Ouverture du fichier si possible
                if (($handle = fopen($csvFile, "r")) !== false) {
                    // On démarre la transaction
                    $em->getConnection()->beginTransaction();
                    try {
                        if($fileTooBigException === true) {
                            throw new \Exception("Le fichier ne doit pas comporter plus de 250 lignes");
                        }

                        $idsTraites = array();
                        $idsAvecPhotoManquante = array();

                        // Parcours les lignes du fichier csv
                        while (($data = fgetcsv($handle, 0, ";")) !== false) {

                            echo 'Taitement de la ligne ' . $index . ($index == 1 ? ' (entêtes)' : '') . "\r\n";
                            flush();

                            // On saute le traitement des entêtes du fichier
                            if ($index == 1) {
                                $indexEntete = 0;
                                $indexValide = true;

                                foreach($data as $k => $v) {
                                    $colValue = preg_replace("/\xEF\xBB\xBF/", "", $v);
                                    $expectedColName = preg_replace("/\xEF\xBB\xBF/", "", $key[$indexEntete]);

                                    if(trim($colValue) != "" && trim($expectedColName) != trim($colValue) && !in_array($expectedColName, $optionnalKeys)) {
                                        $indexValide = false;
                                    }
                                    $indexEntete++;
                                }

                                if($indexValide === false) {
                                    throw new \Exception("Le fichier comporte des colonnes diférentes de celles définies dans l'export, veuillez comparer les 2 fichiers et inclure l'ensemble des colonnes");
                                }

                                $index++;
                                continue;
                            }

                            // On néttoie les champs
                            $data = array_map('trim', $data);

                            $row = array();
                            foreach ($data as $k => $v) {
                                if (array_key_exists(
                                    $k,
                                    $key
                                )) // Si le fichier à trop de colonne on ne récupère pas les valeurs
                                {
                                    $row[$key[$k]] = trim($v);
                                }
                            }

                            //on récupère le contenu des champs traduits

                            foreach($translatedKeys as $k => $v) {
                                if(isset($row[$k])){
                                    $stringParts = explode("___locale", trim($row[$k]));
                                    foreach($stringParts as $item) {
                                        if($item != "") {
                                            $localIdentifier = substr($item, 0, 4);
                                            if(preg_match('/:[a-z]{2} /', $localIdentifier) == 1) {
                                                if(!isset($row[$k]["translation"])){
                                                    $row[$k] = array("translation" => array());
                                                }

                                                $locale = substr($item, 1, 2);
                                                $locale = strtolower($locale);
                                                $translatedString = substr($item, 4);
                                                $translatedString = trim($translatedString);

                                                $translatedString = mb_strtolower($translatedString, 'UTF-8');
                                                $translatedString = mb_strtoupper(mb_substr($translatedString, 0, 1), 'UTF-8') . mb_substr(
                                                        $translatedString,
                                                        1
                                                    );


                                                $row[$k]["translation"][$locale] = array(
                                                    "locale" => $locale,
                                                    "translation" => $translatedString
                                                );

                                                if(in_array($k, $translatedMandatoryKeys) && !isset($row[$k]["translation"]["fr"])){
                                                    throw new \Exception("La valeur fr doit être renseignée pour la ligne " . $index . " et le champ " . $k);
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // On sépare les champs à saisie multiple
                            foreach ($explodeString as $k) {
                                if(isset($row[$k])){
                                    $tmp = explode(',', $row[$k]);
                                    if(count($tmp) == 1 && trim($tmp[0]) == "") {
                                        $tmp = array();
                                    }
                                    $row[$k] = $tmp;
                                }
                            }

                            // On formate les chaines
                            foreach ($mbFormatString as $k) {
                                if (isset($row[$k]) && !is_array($row[$k]) && !empty($row[$k])) {
                                    $row[$k] = mb_strtolower($row[$k], 'UTF-8');
                                    $row[$k] = mb_strtoupper(mb_substr($row[$k], 0, 1), 'UTF-8') . mb_substr(
                                            $row[$k],
                                            1
                                        );
                                }
                            }


                            // On néttoie les champs à saisie multiple
                            foreach ($row as $subRowKey => $subRowValue) {
                                if (is_array($subRowValue)) {
                                    $subRowValueKeys = array_keys($subRowValue);
                                    if(!is_array($subRowValueKeys)){
                                        $row[$subRowKey] = array_map('trim', $subRowValue);
                                    }
                                }
                            }

                            // On regroupe les colonnes des mois dans un tableau
                            $row['mois'] = array(
                                $row['janvier'],
                                $row['fevrier'],
                                $row['mars'],
                                $row['avril'],
                                $row['mai'],
                                $row['juin'],
                                $row['juillet'],
                                $row['aout'],
                                $row['septembre'],
                                $row['octobre'],
                                $row['novembre'],
                                $row['decembre']
                            );

                            // Si la colonne id est vide alors c'est une nouvelle entité
                            try {
                                $entite = $em->getRepository(Entite::class)->findForExportClassique($row, $index);
                            } catch(\Exception $ex) {
                                $lignesAmbigues[] = "La ligne " . $index . " est ambigue et n'a pas été traitée : " . $ex->getMessage();
                                $index++;
                                continue;
                            }

                            // Si la colonne id est vide alors c'est une nouvelle entité
                            if($entite === null) {
                                if(isset($row['id']) && trim($row['id']) != "") {
                                    throw new \Exception("L'id " . $row['id'] . " n'existe pas dans cette base de donnée, veuillez vérifier la provenance du fichier d'import");
                                }

                                $entite = new Entite();
                                $entite->setNouveau(true);
                                $new = true;
                            } else {
                                $entite->setNouveau(false);
                                $new = false;
                            }

                            $entite->setDernierePhotoPrincipaleImporte(null);
                            $entite->setIdentifiantDernierImport($identifiantImport);
                            $entite->setAcronyme($row['acronyme']);

                            //champ traductible
                            $this->_translateField($translatedKeys, $entite, $row, 'nom', 'setNom');

                            $entite->setPot($row['pot']);

                            $this->_translateField($translatedKeys, $entite, $row, 'nom_vernaculaire', 'setNomVernaculaire');

                            $entite->setCaducPersistant($row['caduc_persistant']);
                            $entite->setToxiqueAlimentaire($row['toxique_alimentaire']);
                            $entite->setToxiqueAlimentaireInformation($row['toxique_alimentaire_information']);
                            $entite->setGratuit($row['gratuit']);
                            $entite->setDiametrePot($row['diametre_pot']);
                            $entite->setHauteurPot($row['hauteur_pot']);
                            $entite->setDiametreFinal($row['diametre_final']);
                            $entite->setHauteurFinale($row['hauteur_finale']);

                            $this->_translateField($translatedKeys, $entite, $row, 'divers', 'setDivers');
                            $this->_translateField($translatedKeys, $entite, $row, 'conseil_printemps', 'setConseilPrintemps');
                            $this->_translateField($translatedKeys, $entite, $row, 'conseil_ete', 'setConseilEte');
                            $this->_translateField($translatedKeys, $entite, $row, 'conseil_automne', 'setConseilAutomne');
                            $this->_translateField($translatedKeys, $entite, $row, 'conseil_hiver', 'setConseilHiver');

                            $entite->setPrixMini($row['prix_mini']);
                            $entite->setNouveau(false);


                            $rusticite = $em->getRepository(Rusticite::class)->recupOneParValeur(
                                $row['rusticite_valeur']
                            );
                            $entite->setRusticiteValeur($row['rusticite_valeur']);

                            $entite->getRusticiteMultiples()->clear();
                            if (isset($row['rusticite_id']) && is_array($row['rusticite_id']) && count($row['rusticite_id']) > 0) {
                                $rusticitesFound = $em->getRepository(Rusticite::class)->findByIds(
                                    $row['rusticite_id']
                                );
                                foreach ($rusticitesFound as $item) {
                                    $entite->addRusticiteMultiple($item);
                                }
                            } else if($rusticite !== null){
                                $entite->addRusticiteMultiple($rusticite);
                            }

                            $entite->getBesoinEauMultiples()->clear();
                            if (is_array($row['besoin_eau']) && count($row['besoin_eau']) > 0) {
                                $besoinEauFound = $em->getRepository(BesoinEau::class)->findByValeurs(
                                    $row['besoin_eau']
                                );
                                foreach ($besoinEauFound as $item) {
                                    $entite->addBesoinEauMultiple($item);
                                }
                            }

                            $entite->getEntiteSousTypeMultiples()->clear();
                            if (is_array($row['sous_type']) && count($row['sous_type']) > 0) {
                                $sousTypeFound = $em->getRepository(EntiteSousType::class)->findByIds(
                                    $row['sous_type']
                                );
                                foreach ($sousTypeFound as $item) {
                                    $entite->addEntiteSousTypeMultiple($item);
                                }
                            }

                            $entretien = $em->getRepository(Entretien::class)->findOneById($row['entretien']);
                            $entite->setEntretien($entretien);

                            $entite->setAnnuelle(!empty($row['annuelle']) ? 1 : 0);

                            $entiteType = $em->getRepository(EntiteType::class)->findOneByNom($row['type']);
                            $entite->setEntiteType($entiteType);

                            $marque = $em->getRepository(Marque::class)->findOneByNom($row['marque']);
                            $entite->setMarque($marque);

                            if (isset($row['lasso']) && $row['lasso'] == 1) {
                                $entite->setLasso(true);
                            } else {
                                $entite->setLasso(false);
                            }

                            if (isset($row['lasso_photo']) && trim($row['lasso_photo']) != '') {
                                // Chemin vers la photo
                                $pathToFile = $pathToPhotoDir . iconv('utf-8', 'iso-8859-1', $row['lasso_photo']);

                                if (file_exists($pathToFile)) {
                                    if ($entite->getId() === null) {
                                        $em->persist($entite);
                                        $em->flush();
                                    }

                                    $fileName = Slug::slug(pathinfo($pathToFile, PATHINFO_FILENAME));
                                    $fileExtension = strtolower(pathinfo($pathToFile, PATHINFO_EXTENSION));

                                    $imageSize = getimagesize($pathToFile);
                                    $entite->setLassoPhoto($fileName);
                                    $entite->setLassoPhotoHauteur($imageSize[1]);
                                    $entite->setLassoPhotoLargeur($imageSize[0]);

                                    // Création du répertoire de destination si il n'existe pas
                                    $entite->testMkdirUpload();
                                    copy($pathToFile, $entite->getAbsolutePath());
                                }
                            }

                            // On supprime les informations des mois
                            if ($new === false && $entite->getMois()) {
                                foreach ($entite->getMois() as $mois) {
                                    $entite->getMois()->removeElement($mois);
                                }
                            }

                            // On importe les nouvelles informations des mois
                            foreach ($row['mois'] as $k => $v) {
                                if (!empty($v)) {
                                    $mois = $em->getRepository(Mois::class)->findOneById(
                                        ($k + 1)
                                    ); // car index départ 0
                                    if ($mois != null) {
                                        $entite->addMois($mois);
                                    }
                                }
                            }

                            // On supprime les informations des couleurs
                            if ($new === false && $entite->getCouleurs()) {
                                foreach ($entite->getCouleurs() as $couleur) {
                                    $entite->getCouleurs()->removeElement($couleur);
                                }
                            }

                            // On importe les nouvelles informations des couleurs
                            foreach ($row['couleurs'] as $value) {
                                $couleur = $em->getRepository(Couleur::class)->findOneById($value);
                                if ($couleur != null) {
                                    $entite->addCouleur($couleur);
                                }
                            }

                            // On supprime les informations des couleurs fleurs
                            if ($new === false && $entite->getCouleurFleurs()) {
                                foreach ($entite->getCouleurFleurs() as $couleur) {
                                    $entite->getCouleurFleurs()->removeElement($couleur);
                                }
                            }

                            // On importe les nouvelles informations des couleurs fleurs
                            foreach ($row['couleurs_fleurs'] as $value) {
                                $couleur = $em->getRepository(Couleur::class)->findOneById($value);
                                if ($couleur != null) {
                                    $entite->addCouleurFleur($couleur);
                                }
                            }

                            // On supprime les informations d'ensoleillement
                            if ($new === false && $entite->getEnsoleillements()) {
                                foreach ($entite->getEnsoleillements() as $ensoleillement) {
                                    $entite->getEnsoleillements()->removeElement($ensoleillement);
                                }
                            }

                            // On importe les nouvelles informations d'ensoleillement
                            foreach ($row['ensoleillements'] as $value) {
                                $ensoleillement = $em->getRepository(Ensoleillement::class)->findOneById($value);
                                if ($ensoleillement != null) {
                                    $entite->addEnsoleillement($ensoleillement);
                                }
                            }

                            // On supprime les informations des types sols
                            if ($new === false && $entite->getTypeSols()) {
                                foreach ($entite->getTypeSols() as $typeSol) {
                                    $entite->getTypeSols()->removeElement($typeSol);
                                }
                            }

                            // On importe les nouvelles informations des types sols
                            foreach ($row['types_sols'] as $value) {
                                $typeSol = $em->getRepository(TypeSol::class)->findOneById($value);
                                if ($typeSol != null) {
                                    $entite->addTypeSol($typeSol);
                                }
                            }

                            // On supprime les informations des styles
                            if ($new === false && $entite->getStyles()) {
                                foreach ($entite->getStyles() as $style) {
                                    $entite->getStyles()->removeElement($style);
                                }
                            }

                            // On importe les nouvelles informations des styles
                            foreach ($row['styles'] as $value) {
                                $style = $em->getRepository(Style::class)->findOneById($value);
                                if ($style != null) {
                                    $entite->addStyle($style);
                                }
                            }

                            // On supprime les informations des catégories
                            if ($new === false && $entite->getCategories()) {
                                foreach ($entite->getCategories() as $categorie) {
                                    $entite->getCategories()->removeElement($categorie);
                                }
                            }

                            // On importe les nouvelles informations des catégories
                            foreach ($row['categories'] as $value) {
                                $categorie = $em->getRepository(Categorie::class)->findOneById($value);
                                if ($categorie != null) {
                                    $entite->addCategory($categorie);
                                }
                            }

                            // On supprime les informations des matières
                            if ($new === false && $entite->getMatieres()) {
                                foreach ($entite->getMatieres() as $matiere) {
                                    $entite->getMatieres()->removeElement($matiere);
                                }
                            }

                            // On importe les nouvelles informations des matières
                            foreach ($row['matieres'] as $value) {
                                $matiere = $em->getRepository(Matiere::class)->findOneById($value);
                                if ($matiere != null) {
                                    $entite->addMatiere($matiere);
                                }
                            }

                            // On sauvegarde l'entite
                            $em->persist($entite);
                            $em->flush();

                            // Si la nouvelle entité est bien enregistrée alors on ajoute la photo
                            if (!is_null($entite->getId())) {
                                $idsTraites[] = $entite->getId();

                                $photoAjoutee = false;
                                foreach ($photos as $photo) {
                                    // Récupération de la colonne qui contient la hauteur réelle de l'entité en cm
                                    $hauteurEntite = $row[str_replace('photo_', 'hauteur_', $photo)];
                                    if(trim($hauteurEntite) == "" || filter_var($hauteurEntite, FILTER_VALIDATE_FLOAT) === false) {
                                        $hauteurEntite = 0;
                                    }

                                    // Si c'est une photo à 2 an alors on prendre le diamétre final de l'entité et on sélectionne l'identifiant de l'année (cf BDD : annee)
                                    if (stripos($photo, '_2an') !== false) {
                                        $diametreEntite = $row['diametre_final'];
                                        $anneeId = 2;
                                    } else {
                                        $diametreEntite = $row['diametre_pot'];
                                        $anneeId = 1;
                                    }

                                    // Suivant le mode le nom de la colonne photo, on sélectionne l'état de la photo correspondant (cf BDD : entite_photo_etat)
                                    if (stripos($photo, '_non_fleurie_') !== false) {
                                        $entitePhotoEtatId = 2;
                                    } elseif (stripos($photo, '_feuille_automne_') !== false) {
                                        $entitePhotoEtatId = 3;
                                    } elseif (stripos($photo, '_sans_feuille_') !== false) {
                                        $entitePhotoEtatId = 4;
                                    } else {
                                        $entitePhotoEtatId = 1; // fleurie
                                    }

                                    // On regarde si la photo existe déjà pour cet état et cette entité
                                    $entitePhoto = $em->getRepository(EntitePhoto::class)->findOneBy(
                                        array(
                                            'entite' => $entite->getId(),
                                            'entitePhotoEtat' => $entitePhotoEtatId,
                                            'annee' => $anneeId
                                        )
                                    );

                                    if (!empty($row[$photo])) {
                                        $existe = true;
                                        if ($entitePhoto == null) {
                                            $entitePhoto = new EntitePhoto();
                                            $existe = false;
                                        }

                                        if(trim($hauteurEntite) == "" || filter_var($hauteurEntite, FILTER_VALIDATE_FLOAT) === false) {
                                            $nomColonne = str_replace('photo_', 'hauteur_', $photo);
                                            $lignesAvecWarning[] = "La colonne " . $nomColonne . " à la ligne " . $index . " doit être rempli";
                                        }

                                        $mightSetPrincipale = false;
                                        $entitePhoto->setAnnee($em->getRepository(Annee::class)->findOneById($anneeId));
                                        if ($anneeId == 1 && ($entitePhotoEtatId == 1 || ($entitePhotoEtatId == 2 && $photoAjoutee == false))) {

                                            $canSetPrincipale = false;
                                            if($entitePhoto->getId() !== null) {
                                                $pathToExistingPhoto = $entitePhoto->getAbsolutePath();
                                                if(file_exists($pathToExistingPhoto)) {
                                                    $canSetPrincipale = true;
                                                } else {
                                                    $pathToNewEntitePhoto = $pathToPhotoDir . iconv('utf-8', 'iso-8859-1', $row[$photo]);
                                                    if(file_exists($pathToPhotoDir) . iconv('utf-8', 'iso-8859-1', $row[$photo])) {
                                                        $mightSetPrincipale = true;
                                                    }
                                                }
                                            } else {
                                                $pathToNewEntitePhoto = $pathToPhotoDir . iconv('utf-8', 'iso-8859-1', $row[$photo]);
                                                if(file_exists($pathToPhotoDir) . iconv('utf-8', 'iso-8859-1', $row[$photo])) {
                                                    $mightSetPrincipale = true;
                                                }
                                            }

                                            if($canSetPrincipale) {
                                                $entite->setDernierePhotoPrincipaleImporte($entitePhoto);
                                                $entitePhoto->setPrincipale(1);
                                            }
                                        }
                                        $entitePhoto->setHauteurEntite($hauteurEntite);
                                        $entitePhoto->setDiametreEntite($diametreEntite);
                                        $entitePhoto->setEntite($entite);

                                        $entitePhotoEtat = $em->getRepository(EntitePhotoEtat::class)->findOneById(
                                            $entitePhotoEtatId
                                        );
                                        $entitePhoto->setEntitePhotoEtat($entitePhotoEtat);

                                        // Si la photo existe déjà dans la base de données alors on enregistre les informations
                                        if ($existe) {
                                            $em->persist($entitePhoto);
                                            $em->flush();
                                        }

                                        // Chemin vers la photo
                                        $pathToFile = $pathToPhotoDir . iconv('utf-8', 'iso-8859-1', $row[$photo]);

                                        // Récupération du md5 de la photo si elle existe pour le comparer à la nouvelle photo
                                        $md5CurrentFile = $entitePhoto->getPhoto() && file_exists(
                                            $entitePhoto->getAbsolutePath()
                                        ) ? md5(file_get_contents($entitePhoto->getAbsolutePath())) : null;

                                        // Récupération du md5 de la nouvelle photo si elle existe pour le comparer à l'ancienne photo
                                        $md5NewFile = file_exists($pathToFile) ? md5(
                                            file_get_contents($pathToFile)
                                        ) : null;


                                        // Si il y a une image et qu'elle différente de celle qui existe alors on l'enregistre
                                        if ($md5NewFile != null && $md5CurrentFile != $md5NewFile) {
                                            // On néttoie le nom du fichier
                                            $fileName = Slug::slug(pathinfo($pathToFile, PATHINFO_FILENAME));
                                            $fileExtension = strtolower(pathinfo($pathToFile, PATHINFO_EXTENSION));

                                            $entitePhoto->setNom($fileName);

                                            if($mightSetPrincipale === true) {
                                                $entitePhoto->setPrincipale(1);
                                                $entite->setDernierePhotoPrincipaleImporte($entitePhoto);
                                            }

                                            $entitePhoto->setPhoto($fileName . '.' . $fileExtension);
                                            $entitePhoto->setType(filetype($pathToFile));
                                            $entitePhoto->setPoids(filesize($pathToFile));

                                            // Récupération de la taille de la photo
                                            $imageSize = getimagesize($pathToFile);

                                            if (!empty($imageSize)) {
                                                $entitePhoto->setLargeur($imageSize[0]);
                                                $entitePhoto->setHauteur($imageSize[1]);
                                            }

                                            // On sauvegarde la photo de l'entité
                                            $em->persist($entitePhoto);
                                            $em->flush();

                                            // Création du répertoire de destination si il n'existe pas
                                            $entitePhoto->testMkdirUpload();

                                            // On supprime l'ancienne photo
                                            if (file_exists($entitePhoto->getAbsolutePath()) && is_file(
                                                    $entitePhoto->getAbsolutePath()
                                                )) {
                                                unlink($entitePhoto->getAbsolutePath());
                                            }

                                            // On copie l'image
                                            copy($pathToFile, $entitePhoto->getAbsolutePath());
                                            $photoAjoutee = true;
                                        } elseif (!file_exists($pathToFile) && $entitePhoto->getPhoto(
                                            ) != $row[$photo]) {
                                            $idsAvecPhotoManquante[$entite->getId()] = array(
                                                'photo' => $row[$photo],
                                                'index' => $index
                                            );

                                            echo 'La photo "' . $photo . '" avec le nom "' . $row[$photo] . '" n\'a pas été trouvée à la ligne ' . $index . ' du fichier csv.' . "\r\n";
                                            flush();
                                        }
                                    } /*elseif (empty($row[$photo]) && $entitePhoto != null) {
                                    $em->remove($entitePhoto);
                                    $em->flush();
                                }*/
                                }
                            }
                            $index++;
                        }

                        $entiteService->verifiePhotoPrinicpale($em, $identifiantImport, "LAST_IMPORTED_PHOTO");

                        // On envoie la transcation
                        $em->getConnection()->commit();

                        // On désactive les références qui ne sont pas dans le fichiers
                        if ($desactiverEntiteNonPresente && $idsTraites != null && count($idsTraites) > 0) {
                            $qb = $em->createQueryBuilder();
                            $q = $qb->update('App\Core\Entity\Entite', 'e')
                                ->set('e.actif', '?1')
                                ->where('e.id NOT IN (?2)')
                                ->setParameter(1, 0)
                                ->setParameter(2, $idsTraites)
                                ->getQuery();
                            $q->execute();
                        }

                        if(count($idsAvecPhotoManquante) > 0) {
                            $qb = $em->createQueryBuilder();
                            $q = $qb->update('App\Core\Entity\Entite', 'e')
                                ->set('e.actif', '?1')
                                ->where('e.id IN (?2)')
                                ->setParameter(1, 0)
                                ->setParameter(2, array_keys($idsAvecPhotoManquante))
                                ->getQuery();
                            $q->execute();
                        }

                        if(count($idsAvecPhotoManquante) > 0) {
                            echo "Les lignes suivantes ont posé un problème et ont été ignorées" . "\r\n";
                            foreach($idsAvecPhotoManquante as $entiteId => $entiteDetail) {
                                echo "Ligne  : " .  $entiteDetail['index'] . " la photo " . $entiteDetail['photo'] . "n'a pas été trouvée " . "\r\n";
                            }
                        }

                        if(count($lignesAmbigues) > 0) {
                            echo "Les lignes suivantes ont posé un problème et ont été ignorées" . "\r\n";
                            foreach($lignesAmbigues as $ambiguiteDetail) {
                                echo $ambiguiteDetail . "\r\n";
                            }
                        }

                        echo 'Le fichier a bien été importé.' . "\r\n";

                        if(count($lignesAvecWarning) > 0) {
                            echo "Cependant les lignes suivantes ont des colonnes mal remplies" . "\r\n";
                            echo implode("\r\n", $lignesAvecWarning);
                        }

                        flush();
                    } catch (\Exception $e) {
                        // Si il y a une erreure on revient annule la transaction
                        $em->getConnection()->rollback();
                        $em->close();
                        echo 'Un problème est survenu pendant l\'import. Aucune information importée.' . "\r\n";
                        echo $e->getMessage() . "\r\n";

                        flush();
                    }
                    fclose($handle);
                }
                echo 'Fin du traitement d\'import - ' . date('d-m-Y H:i:s') . "\r\n";
                flush();
            }, 200, array('Content-Type' => 'text/plain')
        );

        return $response;
    }

    private function _translateField($translatedKeys, $entite, $row, $fieldName, $setterName) {
        if(!is_array($row[$fieldName])){
            $entite->$setterName($row[$fieldName]);
        } else {
            foreach($row[$fieldName]['translation'] as $locale => $translatedContent) {
                if($locale != "fr") {
                    $propertyName = $translatedKeys[$fieldName];
                    $entite->translate($propertyName, $locale, $translatedContent['translation']);
                } else {
                    $entite->$setterName($translatedContent['translation']);
                }
            }
        }
    }

}
