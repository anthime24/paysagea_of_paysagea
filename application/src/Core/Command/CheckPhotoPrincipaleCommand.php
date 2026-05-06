<?php


namespace App\Core\Command;

use App\Core\Entity\Entite;
use App\Core\Service\EntiteService;
use App\Core\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPhotoPrincipaleCommand extends Command
{

    protected static $defaultName = 'app:check-photo-principale';
    private $entityManager = null;
    private $entiteService = null;

    public function __construct(EntityManagerInterface $em, EntiteService $entiteService)
    {
        $this->entityManager = $em;
        $this->entiteService = $entiteService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:check-photo-principale')
            ->setHelp("Vérifie qu'il n'y a pas de doublon pour la photo principale");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $this->entiteService->verifiePhotoPrinicpale($this->em);
            $this->em->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->em->getConnection()->rollBack();
        }
    }
}