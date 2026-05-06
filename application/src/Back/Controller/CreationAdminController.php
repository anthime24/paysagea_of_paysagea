<?php

namespace App\Back\Controller;

use App\Core\Entity\Creation;
use App\Core\Entity\CreationEntite;
use DateTime;
use Doctrine\ORM\PersistentCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreationAdminController extends CRUDController
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
                array('creationId' => $id, 'hash' => $object->getReferenceEcriture())
            )
        );
    }

    public function duplicate($id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        $object->setInDuplication(true);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $newCrea = new Creation();
        $newCrea->setInDuplication(true);

        $em->persist($newCrea);

        $newCrea = $this->duplicateItem($newCrea, $object, [new CreationEntite()]);
        $newCrea->setDateCreation(new DateTime());
        $newCrea->setPdfGenere(false);
        $newCrea->setDuplicateAdmin(true);

        $em->flush();
        $object->setInDuplication(false);
        $newCrea->setInDuplication(false);

        // Copie des images
        if (file_exists($object->getAbsolutePathResize())) {
            if (!file_exists(dirname($newCrea->getAbsolutePath()))) {
                mkdir(dirname($newCrea->getAbsolutePath()), 0777, true);
            }
            copy($object->getAbsolutePathResize(), $newCrea->getAbsolutePath());
        }

        if (file_exists($object->getAbsolutePathRenderedImage())) {
            if (!file_exists(dirname($newCrea->getAbsolutePathRenderedImage()))) {
                mkdir(dirname($newCrea->getAbsolutePathRenderedImage()), 0777, true);
            }
            copy($object->getAbsolutePathRenderedImage(), $newCrea->getAbsolutePathRenderedImage());
        }

        if (!file_exists($newCrea->getAbsolutePathPlanMasseImage())) {
            if (!file_exists(dirname($newCrea->getAbsolutePathPlanMasseImage()))) {
                mkdir(dirname($newCrea->getAbsolutePathPlanMasseImage()), 0777, true);
            }
            copy($object->getAbsolutePathPlanMasseImage(), $newCrea->getAbsolutePathPlanMasseImage());
        }

        return $this->redirect($this->generateUrl('admin_app_core_creation_list', array()));
    }

    public function duplicateItem($newObject, $object, $manytoones)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ((array)$object as $k => $v) {
            $vars = explode('_', str_replace(get_class($object), '', $k));
            $item = '';

            foreach ($vars as $s) {
                $item .= ucfirst(trim(strtolower($s)));
            }

            $getter = 'get' . $item;
            $setter = 'set' . $item;
            $adder = 'add' . $item;
            $remover = 'remove' . $item;

            if (method_exists($newObject, $setter)) {
                $newObject->$setter($object->$getter());
            } else {
                if (method_exists($newObject, $getter) && $object->$getter() instanceof PersistentCollection) {
                    if (!method_exists($newObject, $adder) && substr($adder, -1) == 's') {
                        $adder = substr($adder, 0, -1);
                    }

                    if (method_exists($newObject, $adder)) {
                        foreach ($newObject->$getter() as $i) {
                            $newObject->$remover($i);
                        }

                        foreach ($object->$getter() as $i) {
                            $class = get_class($i);
                            $linker = 'set' . str_replace('App\\Core\\Entity\\', '', get_class($newObject));

                            $isInstance = false;
                            foreach ($manytoones as $instance) {
                                if ($i instanceof $instance) {
                                    $isInstance = true;
                                }
                            }
                            if ($isInstance) {
                                $newI = $this->duplicateItem(new $class(), $i, []);

                                $em->persist($newI);

                                $newI->$linker($newObject);
                                $newObject->$adder($newI);

                                if ($newI instanceof CreationEntite) {
                                    $em->flush();

                                    $photo = $i->getPhoto();
                                    $versionPhoto = $i->getVersionPhoto();
                                    $newI->setPhoto($photo);
                                    $newI->setVersionPhoto($versionPhoto);

                                    $photoName = $versionPhoto . '-' . $photo;
                                    $photoFile = $i->getAbsolutePath();
                                    $newPath = $newI->getUploadRootDir();

                                    if (!is_dir($newPath)) {
                                        mkdir($newPath, 0755, true);
                                    }

                                    copy($photoFile, $newPath . '/' . $photoName);
                                }
                            } else {
                                $newObject->$adder($i);
                            }
                        }
                    }
                }
            }
        }

        return $newObject;
    }

    public function pdf($id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        return $this->redirectToRoute('mjmt_application_pdf', array('creationId' => $object->getId(), 'hash' => $object->getReferenceEcriture(), 'refresh_image' => 1));
    }

    public function exportEntite($id = null)
    {
        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $container = $this->container;

        $response = new StreamedResponse(
            function () use ($container, $object) {
                $handle = fopen('php://output', 'r+');

                $i = 0;

                $values = array(
                    utf8_decode(
                        'Export entités création ' . $object->getNom() . ' - ' . $object->getProjet()->getClient()
                    ),
                );
                fputcsv($handle, $values, ',');
                $i++;

                $values = array(
                    '',
                );
                fputcsv($handle, $values, ',');
                $i++;

                $values = array(
                    'Nom',
                    'Nom vernaculaire',
                    'Type',
                    utf8_decode('Quantité'),
                );
                fputcsv($handle, $values, ',');
                $i++;


                $entites = $object->getCreationEntites();

                $listEntites = array();
                $listCompositions = array();
                $numero = 1;
                foreach ($entites as $creationEntite) {
                    if ($creationEntite->getVisibilite() == 1) {
                        if (array_key_exists($creationEntite->getEntite()->getId(), $listEntites)) {
                            $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite(
                            );
                            $listEntites[$creationEntite->getEntite()->getId()]['quantite'] += 1;
                            $listEntites[$creationEntite->getEntite()->getId(
                            )]['numeros'] = $listEntites[$creationEntite->getEntite()->getId()]['numeros'];
                        } else {
                            $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite(
                            );
                            $listEntites[$creationEntite->getEntite()->getId()]['quantite'] = 1;
                            $listEntites[$creationEntite->getEntite()->getId()]['numeros'] = "" . $numero;
                            $numero++;
                        }
                        if ($creationEntite->getComposition()) {
                            $listCompositions[] = $creationEntite->getComposition();
                        }
                    }
                }
                $listCompositionEntites = array();
                foreach ($listCompositions as $composition) {
                    $entites = $composition->getCompositionEntites();
                    $tmpListEntites = array();
                    foreach ($entites as $compositionEntite) {
                        if (array_key_exists($compositionEntite->getEntite()->getId(), $tmpListEntites)) {
                            $tmpListEntites[$compositionEntite->getEntite()->getId(
                            )]['entite'] = $compositionEntite->getEntite();
                            $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] += 1;
                        } else {
                            $tmpListEntites[$compositionEntite->getEntite()->getId(
                            )]['entite'] = $compositionEntite->getEntite();
                            $tmpListEntites[$compositionEntite->getEntite()->getId()]['quantite'] = 1;
                        }
                    }
                    $listCompositionEntites[$composition->getId()] = $tmpListEntites;
                }

                foreach ($listEntites as $r) {
                    $entite = $r['entite'];
                    $composition = $entite->getComposition();

                    $values = array(
                        ($composition ? 'COMPOSITION ' : '') . utf8_decode($entite->getNom()),
                        utf8_decode($entite->getNomVernaculaire()),
                        utf8_decode($entite->getEntiteSousType()),
                        $r['quantite'],
                    );
                    fputcsv($handle, $values, ',');
                    $i++;

                    if ($composition) {
                        foreach ($listCompositionEntites[$composition->getId()] as $e) {
                            $entite = $e['entite'];
                            $values = array(
                                ' - ' . utf8_decode($entite->getNom()),
                                utf8_decode($entite->getNomVernaculaire()),
                                utf8_decode($entite->getEntiteSousType()),
                                $e['quantite'],
                            );
                            fputcsv($handle, $values, ',');
                            $i++;
                        }
                    }
                }

                fclose($handle);
            }
        );

        $response->headers->set('Content-Type', 'text/csv; charset=iso-8859-1');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="export_mjmt_entite_' . date('Y-m-d-H-i-s') . '.csv"'
        );
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function exportDevis(\Symfony\Component\HttpKernel\KernelInterface $kernel, $id = null)
    {
        $totalHt = 0;
        $totalTtc = 0;

        $styles = array(
            'cellWithLeftBoldBorders' => array(
                'borders' => array(
                    'left' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000']
                    )
                )
            ),
            'cellWithRightBoldBorders' => array(
                'borders' => array(
                    'right' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000']
                    )
                )
            ),
            'cellWithBottomBoldBorders' => array(
                'borders' => array(
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000']
                    )
                ),
            ),
            'cellWithBorders' => array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    )
                ),
            ),
            'cellWithBoldTextAndBorders' => array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    )
                ),
                'font' => array(
                    'bold' => true
                )
            ),
            'cellWithBoldText' => array(
                'font' => array(
                    'bold' => true,
                    'color' => ['rgb' => '000000']
                )
            )
        );

        $id = $this->getRequest()->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $container = $this->container;

        $entites = $object->getCreationEntites();

        $listEntites = array();
        $listCompositions = array();
        $numero = 18;
        $spreadsheet = IOFactory::load(
            $kernel->getProjectDir() . '/var/modele/modele_devis_type.xlsx'
        );


        $spreadsheet->getActiveSheet()->setCellValue('A11', 'Date : ' . date('d/m/Y'));
        $spreadsheet->getActiveSheet()->setCellValue('D11', 'Nom : ' . $object->getNom() . ' - Identifiant création : ' . $object->getId());
        $spreadsheet->getActiveSheet()->getCell('D11')->getStyle()->getAlignment()->setWrapText(true);

        foreach ($entites as $creationEntite) {
            if ($creationEntite->getVisibilite() == 1) {
                if (array_key_exists($creationEntite->getEntite()->getId(), $listEntites)) {
                    $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite();
                    $listEntites[$creationEntite->getEntite()->getId()]['quantite'] += 1;
                    $listEntites[$creationEntite->getEntite()->getId(
                    )]['numeros'] = $listEntites[$creationEntite->getEntite()->getId()]['numeros'];
                } else {
                    $listEntites[$creationEntite->getEntite()->getId()]['entite'] = $creationEntite->getEntite();
                    $listEntites[$creationEntite->getEntite()->getId()]['quantite'] = 1;
                    $listEntites[$creationEntite->getEntite()->getId()]['numeros'] = "" . $numero;
                    $numero++;
                }
                if ($creationEntite->getComposition()) {
                    $listCompositions[] = $creationEntite->getComposition();
                }
            }
        }

        $listCompositionEntites = array();
        foreach ($listCompositions as $composition) {
            $entites = $composition->getCompositionEntites();
            $tmpListEntites = array();
            foreach ($entites as $compositionEntite) {
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
            $listCompositionEntites[$composition->getId()] = $tmpListEntites;
        }

        $numero = 16;
        foreach ($listEntites as $r) {
            $entite = $r['entite'];

            $composition = $entite->getComposition();
            if($entite->getLasso()) {
                $composition = false;
            }

            $spreadsheet->getActiveSheet()->setCellValue(
                'A' . $numero,
                ($composition ? 'COMPOSITION ' : '') . $entite->getNom()
            );
            $spreadsheet->getActiveSheet()->getStyle('A' . $numero)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getCell('A' . $numero)->getStyle()->applyFromArray($styles['cellWithBoldText']);

            $spreadsheet->getActiveSheet()->setCellValue(
                'B' . $numero,
                $entite->getEntiteSousType() . ($entite->getNomVernaculaire() ? ' - ' . $entite->getNomVernaculaire(
                    ) : '') . $entite->getNomVernaculaire()
            );
            $spreadsheet->getActiveSheet()->getStyle('B' . $numero)->getAlignment()->setWrapText(true);

            $hauteur = "";
            if($entite->getHauteurPot() !== null && $entite->getHauteurPot() > 0) {
                $hauteur = $entite->getHauteurPotAvecUnite();
            }
            $spreadsheet->getActiveSheet()->setCellValue('C' . $numero, $hauteur);

            $spreadsheet->getActiveSheet()->setCellValue('D' . $numero, $r['quantite']);

            $pourcentageTva = 20;
            if(in_array($entite->getEntiteType()->getId(), array(1, 2))) {
                $pourcentageTva = 10;
                $spreadsheet->getActiveSheet()->setCellValue('E' . $numero, '10' . ' %');
            } else {
                $spreadsheet->getActiveSheet()->setCellValue('E' . $numero, '20' . ' %');
            }

            if($entite->getPrixMini() !== null) {
                $prixMiniAvecTva = (string)$entite->getPrixMini();
                if(strstr($prixMiniAvecTva, '€') !== false) {
                    $prixMiniAvecTva = str_replace('€', '', $prixMiniAvecTva);
                }
                if(strstr($prixMiniAvecTva, ',') !== false) {
                    $prixMiniAvecTva = str_replace('.', '', $prixMiniAvecTva);
                }
                $prixMiniAvecTva = trim($prixMiniAvecTva);

                $prixMiniAvecTvaFloat = floatval($prixMiniAvecTva);
                if(is_float($prixMiniAvecTvaFloat) && $prixMiniAvecTvaFloat > 0) {

                    if($pourcentageTva == 20) {
                        $prixMiniHt = $prixMiniAvecTvaFloat / 1.2;
                    } else {
                        $prixMiniHt = $prixMiniAvecTvaFloat / 1.1;
                    }
                    $prixMiniHt = round($prixMiniHt, 2);

                    $spreadsheet->getActiveSheet()->setCellValue('F' . $numero, $prixMiniHt . ' €');
                    $spreadsheet->getActiveSheet()->setCellValue('G' . $numero, $prixMiniAvecTvaFloat . ' €');

                    $prixTotalHt = $prixMiniHt * $r['quantite'];
                    $totalHt = $totalHt + $prixTotalHt;
                    $spreadsheet->getActiveSheet()->setCellValue('H' . $numero, $prixTotalHt . ' €');

                    $prixTotalTTC = $prixMiniAvecTvaFloat * $r['quantite'];
                    $totalTtc = $totalTtc + $prixTotalTTC;
                    $spreadsheet->getActiveSheet()->setCellValue('I' . $numero, $prixTotalTTC . ' €');
                }  else {
                    $spreadsheet->getActiveSheet()->setCellValue('F' . $numero, '');
                    $spreadsheet->getActiveSheet()->setCellValue('G' . $numero, $prixMiniAvecTva . ' €');
                    $spreadsheet->getActiveSheet()->setCellValue('H' . $numero, '');
                    $spreadsheet->getActiveSheet()->setCellValue('I' . $numero, '');
                }
            }

            $numero++;

            if ($composition) {
                foreach ($listCompositionEntites[$composition->getId()] as $e) {
                    $entite = $e['entite'];

                    $spreadsheet->getActiveSheet()->setCellValue('A' . $numero, '          ' . ' - '   . $entite->getNom());
                    $spreadsheet->getActiveSheet()->setCellValue(
                        'B' . $numero,
                        $entite->getEntiteSousType() . ($entite->getNomVernaculaire(
                        ) ? ' ' . $entite->getNomVernaculaire() : '') . $entite->getNomVernaculaire()
                    );
                    $spreadsheet->getActiveSheet()->setCellValue('D' . $numero, '');
                    $numero++;
                    $numero++;
                }
            }
        }

        //FOOTER
        $indexDerniereColonne = 9;

        $indexAvantDerniereColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(9 - 1);
        $indexDerniereColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(9);
        $indexPremiereColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1);
        $indexDerniereLigne = $numero;

        $spreadsheet->getActiveSheet()->setCellValue($indexAvantDerniereColonne . $indexDerniereLigne, 'Total HT');
        $spreadsheet->getActiveSheet()->getCell($indexAvantDerniereColonne . $indexDerniereLigne)->getStyle()->applyFromArray($styles['cellWithBoldTextAndBorders']);
        $spreadsheet->getActiveSheet()->setCellValue($indexDerniereColonne . $indexDerniereLigne, $totalHt . ' €');
        $spreadsheet->getActiveSheet()->getCell($indexDerniereColonne . $indexDerniereLigne)->getStyle()->applyFromArray($styles['cellWithBorders']);

        $totalTva = $totalTtc - $totalHt;
        $spreadsheet->getActiveSheet()->setCellValue($indexAvantDerniereColonne . ($indexDerniereLigne + 1), 'TVA');
        $spreadsheet->getActiveSheet()->getCell($indexAvantDerniereColonne . ($indexDerniereLigne + 1))->getStyle()->applyFromArray($styles['cellWithBoldTextAndBorders']);
        $spreadsheet->getActiveSheet()->setCellValue($indexDerniereColonne . ($indexDerniereLigne + 1), $totalTva . ' €');
        $spreadsheet->getActiveSheet()->getCell($indexDerniereColonne . ($indexDerniereLigne + 1))->getStyle()->applyFromArray($styles['cellWithBorders']);

        $spreadsheet->getActiveSheet()->setCellValue($indexAvantDerniereColonne . ($indexDerniereLigne + 2), 'Total TTC');
        $spreadsheet->getActiveSheet()->getCell($indexAvantDerniereColonne . ($indexDerniereLigne + 2))->getStyle()->applyFromArray($styles['cellWithBoldTextAndBorders']);
        $spreadsheet->getActiveSheet()->setCellValue($indexDerniereColonne . ($indexDerniereLigne + 2), $totalTtc . ' €');
        $spreadsheet->getActiveSheet()->getCell($indexDerniereColonne . ($indexDerniereLigne + 2))->getStyle()->applyFromArray($styles['cellWithBorders']);

        for($i=1; $i<=9; $i++) {
            $colonneCourante = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $spreadsheet->getActiveSheet()->getCell($colonneCourante . ($indexDerniereLigne - 1))->getStyle()->applyFromArray($styles['cellWithBottomBoldBorders']);
        }

        for($i=14; $i<=$numero; $i++) {
            $spreadsheet->getActiveSheet()->getCell($indexDerniereColonne . $i)->getStyle()->applyFromArray($styles['cellWithRightBoldBorders']);
        }

        for($i=1; $i<=$numero; $i++) {
            $spreadsheet->getActiveSheet()->getCell($indexPremiereColonne . $i)->getStyle()->applyFromArray($styles['cellWithLeftBoldBorders']);
        }

        $response = new Response();

        // Redirect output to a client’s web browser (Excel5)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set(
            'Content-Disposition',
            'attachment;filename="export_devis_creation_ ' . $object->getId() . '.xlsx"'
        );
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->sendHeaders();
        // Create your Office 2007 Excel (XLSX Format)
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        return $response;
    }
}
