<?php

namespace App\Core\Utility;

use App\Core\Entity\Ph;
use App\Core\Entity\Precipitation;
use App\Core\Entity\Rusticite;

class LocalityDetail
{
    /**
     * Largeur des cartes PNG
     */
    public const CARTE_LARGEUR_IMG = 868;

    /**
     * Hauteur des cartes PNG
     */
    public const CARTE_HAUTEUR_IMG = 798;


    public const CARTE_LATITUDE_MIN = 41.37;

    public const CARTE_LATITUDE_MAX = 51.22;

    public const CARTE_LONGITUDE_MIN = -5.53;

    public const CARTE_LONGITUDE_MAX = 10.15;

    /**
     * Retourne les informations liées au terrain (rusticité, ph, précipitation) depuis la latitude et la longitude
     * @param  [type] $em              [description]
     * @param  [type] $latitude_point  [description]
     * @param  [type] $longitude_point [description]
     * @return [type] [description]
     */
    public static function calcul($em, $latitude_point, $longitude_point)
    {

        $ratioLngPixel = self::CARTE_LARGEUR_IMG / (self::CARTE_LONGITUDE_MAX - self::CARTE_LONGITUDE_MIN);
        $x_carte = ($longitude_point - self::CARTE_LONGITUDE_MIN) * $ratioLngPixel;

        $ratioLatPixel = self::CARTE_HAUTEUR_IMG / (self::CARTE_LATITUDE_MAX - self::CARTE_LATITUDE_MIN);
        $y_carte = ($latitude_point - self::CARTE_LATITUDE_MIN) * $ratioLatPixel;
        $y_carte = self::CARTE_HAUTEUR_IMG - $y_carte;

        /////////////////////////////////////////////
        // Rusticité
        $im = imagecreatefrompng(__DIR__ . '/carte-rusticite.png');
        $codecouleurhex = LocalityDetail::codeCouleur($im, $x_carte, $y_carte);
        $rusticite = $em->getRepository(Rusticite::class)->findOneBy(array('codeCouleur' => $codecouleurhex));

        /////////////////////////////////////////////
        // Précipitations estivales
        $im = imagecreatefrompng(__DIR__ . '/carte-precipitation-estivales.png');
        $codecouleurhex = LocalityDetail::codeCouleur($im, $x_carte, $y_carte);
        $precipitations_estivales = $em->getRepository(Precipitation::class)->findOneBy(
            array('codeCouleur' => $codecouleurhex)
        );

        /////////////////////////////////////////////
        // pH sol
        $im = imagecreatefrompng(__DIR__ . '/carte-ph.png');
        $codecouleurhex = LocalityDetail::codeCouleur($im, $x_carte, $y_carte);
        $ph = $em->getRepository(Ph::class)->findOneBy(array('codeCouleur' => $codecouleurhex));

        return array('precipitationsEstivales' => $precipitations_estivales, 'rusticite' => $rusticite, 'ph' => $ph);
    }

    /**
     * Retourne le code couleur selon les coordonnées X Y sur la carte PNG
     * @param  [type] $image   [description]
     * @param  [type] $x_carte [description]
     * @param  [type] $y_carte [description]
     * @return [type] [description]
     */
    public static function codeCouleur($image, $x_carte, $y_carte)
    {
        $codecouleurhex = null;

        // On vérifie que le x et le y sont dans la carte
        if ($x_carte >= 0 && $x_carte <= imagesx($image) && $y_carte >= 0 && $y_carte <= imagesy($image)) {
            $rgb = imagecolorat($image, $x_carte, $y_carte);
            $colors = imagecolorsforindex($image, $rgb);
            $hexred = dechex($colors['red']);
            if (strlen($hexred) == 1) {
                $hexred = "0" . $hexred;
            }
            $hexgreen = dechex($colors['green']);
            if (strlen($hexgreen) == 1) {
                $hexgreen = "0" . $hexgreen;
            }
            $hexblue = dechex($colors['blue']);
            if (strlen($hexblue) == 1) {
                $hexblue = "0" . $hexblue;
            }
            $codecouleurhex = '#' . $hexred . $hexgreen . $hexblue;
        }

        return $codecouleurhex;
    }

}
