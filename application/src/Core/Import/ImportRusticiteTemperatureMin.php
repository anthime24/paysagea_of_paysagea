<?php

namespace App\Core\Import;

use Doctrine\ORM\EntityManagerInterface;

//TODO TESTER ET GENERALISER SUR L AUTRE IMPORT
class ImportRusticiteTemperatureMin extends BaseImport {

    protected $columns = array(
        'id' => array('propertyPath' => 'id', 'active' => false),
        'rusticite_valeur' => array('propertyPath' => 'rusticiteValeur', 'active' => false),
        'rusticite_id' => array('propertyPath' => 'rusticite', 'customFunction' => 'setRusticiteCustom')
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

    public function setRusticiteCustom(\App\Core\Entity\Entite $entity, $cellValue, $lineValue)
    {

        if(!isset($this->retrievedAssociations['rusticite'])) {
            $this->retrievedAssociations['rusticite'] = $this->em->getRepository(\App\Core\Entity\Rusticite::class)->findByIds('*', null, 'id');
        }

        $rusticiteDefines = array();
        if($lineValue['rusticite_id']['value'] !== null && trim($lineValue['rusticite_id']['value']) != "") {
            $rusticiteDefines =  explode(',', $lineValue['rusticite_id']['value']);
        }

        foreach($rusticiteDefines as $rustId) {
            $rustItem = isset($this->retrievedAssociations['rusticite'][$rustId]) ? $this->retrievedAssociations['rusticite'][$rustId] : null;
            if($rustItem !== null) {
                if(!$entity->getRusticiteMultiples()->contains($rustItem)) {
                    $entity->getRusticiteMultiples()->add($rustItem);
                }
            }
        }

        $rusticiteValeur = $entity->getRusticiteValeur();
        $rusticiteExistantes = $entity->getRusticiteMultiples();

        foreach($rusticiteExistantes as $rustItem) {
            if($rustItem->getMax() < $rusticiteValeur) {
                $entity->removeRusticiteMultiple($rustItem);
            }
        }
    }
}
