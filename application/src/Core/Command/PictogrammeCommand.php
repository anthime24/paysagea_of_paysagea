<?php

namespace App\Core\Command;

use App\Core\Entity\Entite;
use App\Core\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PictogrammeCommand extends Command {

    protected static $defaultName  = 'app:pictogramme-command';
    private $entityManager = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:pictogramme-command')
            ->setHelp("Met à jour la colonne pictogramme_computed_flag pour les entités");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->entityManager;
        $query = $this->entityManager->getRepository(Entite::class)->getQueryEntitesWithPictogrammeToUpdate();

        Utility::paginateDoctrineQuery($query, function($paginatedQuery) use ($em){
            $results = $paginatedQuery->getQuery()->getResult();

            foreach($results as $entity) {
                if($entity->hasPictoNouveau() != $entity->getPictoNouveauComputedFlag()) {
                    $entity->setPictoNouveauComputedFlag($entity->hasPictoNouveau());
                }

                if($entity->hasPictoPromo() != $entity->getPictoPromoComputedFlag()) {
                    $entity->setPictoPromoComputedFlag($entity->hasPictoPromo());
                }

                if($entity->hasPictoCoupCoeur() != $entity->getPictoCoupCoeurComputedFlag()) {
                    $entity->setPictoCoupCoeurComputedFlag($entity->hasPictoCoupCoeur());
                }
            }

            $em->flush();
        });
    }
}