<?php

namespace App\Core\Import;

use Doctrine\ORM\EntityManagerInterface;

abstract class BaseImport
{
    protected $em;

    /**
     * doit être override
     *
     * ex
     * customFunction si définit spécifie la fonction à utiliser pour mettre à jour cette propriété
     * active si définit et vaut false skip cette propriété
     * array(
     *  'nom_fichier_excel' => array('propertyPath', 'customFunction' => null)
     * )
     */
    protected $columns = array();
    protected $errors = array();

    protected $classMetaData = null;
    protected $retrievedAssociations = array();

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function import($file, $nbrAvantFlush = 500)
    {
        $this->errors = array();
        $this->retrievedAssociations = array();
        $this->classMetaData = null;
        $this->em->getConnection()->beginTransaction();

        $nbrLignesParcourues = 0;
        $nbrLignesDepuisDernierFlush = 0;

        try {
            $this->checkFiles($file);

            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 99999, ';', '"')) !== false) {
                    if($nbrLignesParcourues == 0) {
                        $nbrLignesParcourues++;
                        continue;
                    }

                    $lineData = $this->getLineData($data);

                    try {
                        $newEntity = false;
                        $entity = $this->getEntity($lineData, $newEntity);

                        if($this->classMetaData === null) {
                            $this->classMetaData = $this->em->getClassMetadata(get_class($entity));
                        }

                        $this->importLine($lineData, $entity);
                        $nbrLignesDepuisDernierFlush++;

                        if($newEntity === true) {
                            $this->em->persist($newEntity);
                        }

                        if($nbrLignesDepuisDernierFlush >= $nbrAvantFlush) {
                            $this->em->flush();
                            $nbrLignesDepuisDernierFlush = 0;
                        }
                    } catch(\Exception $ex) {
                        if($ex instanceof \App\Core\Utility\CatchableTreatementException) {
                            $this->catchTreatementException($ex, $lineData, $nbrLignesParcourues + 1);
                        } else {
                            dump($lineData);
                            dump($ex->getMessage());
                            dump($ex->getFile());
                            dump($ex->getLine());

                            throw $ex;
                        }
                    }

                    $nbrLignesParcourues++;
                }

                $this->em->flush();
                $this->em->getConnection()->commit();
            } else {
                throw new \Exception("Impossible de lire le fichier vérifier qu'il est bien au format CSV séparateur point virgule");
            }
        } catch(\Exception $ex) {
            $this->errors[] = $ex->getMessage();
            $this->em->getConnection()->rollBack();
        }

        return $this->errors;
    }

    //vérifie que le fichier existe, et que les colonnes correspondent à celles définies
    protected function checkFiles($file)
    {
        $checked = true;
        $columnListKeys = array_keys($this->columns);
        $columnNameErrors = array();

        if(!file_exists($file)) {
            $checked = false;
        } else {
            if (($handle = fopen($file, "r")) !== false) {
                $data = fgetcsv($handle, 99999, ";", '"');
                foreach($data as $colIndex => $colValue) {
                    if($colIndex > (count($columnListKeys) - 1)) {
                        continue;
                    } else {
                        $colValue = preg_replace("/\xEF\xBB\xBF/", "", $colValue);
                        $colName = preg_replace("/\xEF\xBB\xBF/", "", $columnListKeys[$colIndex]);

                        if(trim($colName) != trim($colValue)) {
                            $checked = false;
                            $columnNameErrors[] = trim($colValue);
                        }
                    }
                }

                fclose($handle);
            } else {
                $checked = false;
            }
        }

        if($checked === false) {
            if(count($columnNameErrors) > 0) {
                throw new \Exception("Le nom des colonnes du fichier ne correspond pas aux nom attendus : " . implode(", ", $columnNameErrors));
            } else {
                throw new \Exception("Impossible de lire le fichier vérifier qu'il est bien au format CSV séparateur point virgule");
            }
        }

        return $checked;
    }

    protected function getLineData($rawData)
    {
        $lineData = $this->columns;
        $colmunKeys = array_keys($this->columns);

        for($i=0; $i<count($this->columns); $i++) {
            $cellValue = $rawData[$i];
            $cellValue = preg_replace("/\xEF\xBB\xBF/", "", $cellValue);
            $cellValue = trim($cellValue);

            $lineData[$colmunKeys[$i]]['value'] = $cellValue;
        }

        return $lineData;
    }

    /**
     *
     * @param $lineData
     * @return null
     */
    abstract protected function getEntity($lineData, &$newEntity);

    /**
     * TODO IL EST POSSIBLE DE DEVINER LE TYPE DE RELATION AVEC classMetadatadata->hasAssociation(fieldName)
     * classMetadata->getAssociationMapping('fieldName')['type'] en le comparant a \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY
     *
     * @param $lineData
     * @param $entity
     * @throws \Exception
     */
    protected function importLine($lineData, $entity)
    {
        foreach($lineData as $cellDetail) {
            $actif = true;
            if(isset($cellDetail['active']) && $cellDetail['active'] == false) {
                $actif = false;
            }

            if($actif) {
                if(isset($cellDetail['customFunction'])){
                    call_user_func(array($this, $cellDetail['customFunction']), $entity, $cellDetail['value'], $lineData);
                } else {
                    $camelCase = str_replace('_', '', ucwords($cellDetail['propertyPath'], '_'));
                    $setter = 'set' . $camelCase;

                    if(method_exists($entity, $setter)) {
                        $entity->$setter($cellDetail['value']);
                    } else {
                        throw new \Exception("Le setter n'existe pas pour la propriété " . $setter);
                    }
                }
            }
        }
    }

    protected function catchTreatementException(\App\Core\Utility\CatchableTreatementException $ex, $detailLigne, $numeroLigne) {
        throw $ex;
    }
}
