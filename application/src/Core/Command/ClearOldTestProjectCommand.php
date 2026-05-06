<?php

namespace App\Core\Command;

use App\Core\Entity\Entite;
use App\Core\Entity\Projet;
use App\Core\Import\ImportRusticiteEn;
use App\Core\Repository\ProjetRepository;
use App\Core\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ClearOldTestProjectCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:clear-old-test-project');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projets = $this->entityManager->getRepository(Projet::class)->findBy(['test' => true]);
        foreach ($projets as $projet) {
            if ($projet->getDateCreation()->add(new \DateInterval('P1D'))->format('U') <= time()) {
                foreach ($projet->getCreations() as $creation)
                    $this->entityManager->remove($creation);
                $this->entityManager->remove($projet);
            }
        }

        $this->entityManager->flush();
    }
}