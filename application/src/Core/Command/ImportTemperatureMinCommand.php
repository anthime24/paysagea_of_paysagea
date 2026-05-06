<?php

namespace App\Core\Command;

use App\Core\Entity\Entite;
use App\Core\Import\ImportRusticiteEn;
use App\Core\Import\ImportRusticiteTemperatureMin;
use App\Core\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ImportTemperatureMinCommand extends Command {

    protected static $defaultName  = 'app:import-temperature-min';

    private $importClass;
    private $containerBag;

    public function __construct(ImportRusticiteTemperatureMin $importRusticiteTemperatureMin, ContainerBagInterface $containerBag)
    {
        $this->importClass = $importRusticiteTemperatureMin;
        $this->containerBag = $containerBag;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-temperature-min');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernelProjectDir = $this->containerBag->get('kernel.project_dir');
        $importFile = $kernelProjectDir . '/public/rusticite_temperature_min.csv';

        $errors = $this->importClass->import($importFile, 100);
        dump($errors);
    }
}
