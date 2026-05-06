<?php

namespace App\Core\Command;

use App\Core\Entity\Entite;
use App\Core\Import\ImportRusticiteEn;
use App\Core\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ImportRusticiteEnCommand extends Command {

    protected static $defaultName  = 'app:import-rusticite-en';

    private $importClass;
    private $containerBag;

    public function __construct(ImportRusticiteEn $importRusticiteEn, ContainerBagInterface $containerBag)
    {
        $this->importClass = $importRusticiteEn;
        $this->containerBag = $containerBag;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-rusticite-en');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernelProjectDir = $this->containerBag->get('kernel.project_dir');
        $importFile = $kernelProjectDir . '/public/import_rusticite.csv';

        $errors = $this->importClass->import($importFile, 200);
    }
}