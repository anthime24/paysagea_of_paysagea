<?php

namespace App\Core\Command;

use App\Core\Entity\Entite;
use App\Core\Utility\LocalityDetailEurope;
use App\Core\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Core\Utility\LocalityDetail;
use App\Core\Utility\LocalityDetailBelgium;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TestLocalityMap extends Command {

    protected static $defaultName  = 'app:test-locality-map';
    private $entityManager = null;
    private $parameterBag = null;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $em;
        $this->parameterBag = $parameterBag;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:test-locality-map')
            ->addArgument('latitude', InputArgument::REQUIRED)
            ->addArgument('longitude', InputArgument::REQUIRED)
            ->setHelp("Test les données retournées par localityDetail sur la carte, et la position indiquée");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->entityManager;
        $latitude = $input->getArgument('latitude');
        $longitude = $input->getArgument('longitude');

        if (($latitude >= LocalityDetailBelgium::CARTE_LATITUDE_MIN && $latitude <= LocalityDetailBelgium::CARTE_LATITUDE_MAX)
            && ($longitude >= LocalityDetailBelgium::CARTE_LONGITUDE_MIN && $longitude <= LocalityDetailBelgium::CARTE_LONGITUDE_MAX)) {
            $localityDetailClassName = LocalityDetailBelgium::class;
            $data = $localityDetailClassName::calcul($em, $latitude, $longitude);

            dump('BELGIUM MAP');
        } else if(($latitude >= LocalityDetail::CARTE_LATITUDE_MIN && $latitude <= LocalityDetail::CARTE_LATITUDE_MAX)
            && ($longitude >= LocalityDetail::CARTE_LONGITUDE_MIN && $longitude <= LocalityDetail::CARTE_LONGITUDE_MAX)) {
            $localityDetailClassName = LocalityDetail::class;
            $data = $localityDetailClassName::calcul($em, $latitude, $longitude);

            dump('FRANCE MAP');
        } else {
            $localityDetailClassName = LocalityDetailEurope::class;
            $data = $localityDetailClassName::calcul($this->parameterBag, $this->entityManager, $latitude, $longitude);
            dump($data);

            dump('GIS EUROPEAN DATA');
        }

        dump($data);
        die;


    }
}
