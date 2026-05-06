<?php

namespace App\Core\Import;

use Doctrine\ORM\EntityManagerInterface;

class ImportRusticiteEn extends BaseImport {

    protected $columns = array(
        'id' => array('propertyPath' => 'id', 'active' => false),
        'type' => array('propertyPath' => 'type', 'active' => false),
        'acronyme' => array('propertyPath' => 'acronyme', 'active' => false),
        'nom' => array('propertyPath' => 'nom', 'active' => false),
        'rusticite_id' => array('propertyPath' => 'rusticite', 'customFunction' => 'setRusticite'),
        'nom_anglais' => array('propertyPath' => 'NomAnglais', 'customFunction' => 'setNomAnglais')
    );

    //on ignore volontairement les lignes ou l'id n'est pas trouvée
    protected function getEntity($lineData, &$newEntity)
    {
        $found = false;
        $entity = null;

        if(isset($lineData['id']['value']) && $lineData['id']['value'] != "") {
            $entity = $this->em->getRepository(\App\Core\Entity\Entite::class)->find($lineData['id']['value']);
            if($entity !== null) {
                $found = true;
            }
        }

        if($found === false) {
            $exceptionToThrow = new \App\Core\Utility\CatchableTreatementException("LINE_NOT_FOUND");
            throw $exceptionToThrow;
        }

        return $entity;
    }

    protected function catchTreatementException(\App\Core\Utility\CatchableTreatementException $ex, $detailLigne, $numeroLigne) {
        $this->errors[] = "LIGNE " . $numeroLigne . " ID NON TROUVE " . $detailLigne['id']['value'];
    }

    //il y'a un déclage entre le contenu du fichier et les rusticites definis
    //attention ce n'est valable que pour ce fichier
    public function setRusticite($entity, $value, $lineValues)
    {
        $entity->getRusticiteMultiples()->clear();

        $correspondanceIdRusticiteImportUsda = array(
            '13' => '6b',
            '14' => '7a',
            '15' => '7b',
            '16' => '8a',
            '17' => '8b',
            '18' => '9a',
            '19' => '9b',
            '20' => '10a',
            '21' => '10b',
            '22' => '11a',
            '23' => '11b',
            '24' => '12a',
            '25' => '12b',
            '26' => '13a',
            '27' => '13b',
            '28' => '10b'
        );
        if(!isset($this->retrievedAssociations['correspondanceRusticiteUsda'])){
            $this->retrievedAssociations['correspondanceRusticiteUsda'] = $this->em->getRepository(\App\Core\Entity\Rusticite::class)->findByIds(array_keys($correspondanceIdRusticiteImportUsda), null, 'nom');
        }
        $correspondanceRusticiteUsda = $this->retrievedAssociations['correspondanceRusticiteUsda'];

        if(!isset($this->retrievedAssociations['correspondanceRusticiteIds'])){
            $this->retrievedAssociations['correspondanceRusticiteIds'] = $this->em->getRepository(\App\Core\Entity\Rusticite::class)->findByIds('*', null, 'id');
        }
        $correspondanceRusticiteIds = $this->retrievedAssociations['correspondanceRusticiteIds'];

        if($value != "") {
            $cellRusticiteIds = explode(',', $value);
            $cellRusticiteList = array();

            foreach($cellRusticiteIds as $itemId) {
                if(trim($itemId) != "") {
                    $entityToAdd = null;
                    if(!isset($correspondanceIdRusticiteImportUsda[$itemId])){
                        if(isset($correspondanceRusticiteIds[$itemId])) {
                            $entityToAdd = $correspondanceRusticiteIds[$itemId];
                        }
                    } else {
                        $zoneUsda = $correspondanceIdRusticiteImportUsda[$itemId];
                        if(isset($correspondanceRusticiteUsda[$zoneUsda])){
                            $entityToAdd = $correspondanceRusticiteUsda[$zoneUsda];
                        }
                    }

                    if($entityToAdd !== null) {
                        $entity->addRusticiteMultiple($entityToAdd);
                    }
                }
            }
        }
    }

    public function setNomAnglais($entity, $value)
    {
        if($value != "") {
            $entity->translate("nomVernaculaire", "en", $value);
        }
    }
}
