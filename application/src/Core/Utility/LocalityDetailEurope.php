<?php

namespace App\Core\Utility;

use Doctrine\ORM\EntityManagerInterface;
use PDO;
use App\Core\Entity\Rusticite;

class LocalityDetailEurope
{
    public static function calcul(EntityManagerInterface $em, $latitude_point, $longitude_point)
    {
        $db = null;

        $rusticiteFound = null;
        $precipitationEstivalleFound = null;
        $phFound = null;

        try {
            $dsn = $_ENV['GIS_DATABASE_URL'];
            $db = new PDO($dsn);

            if($latitude_point === null || trim($latitude_point) == "") {
                throw new \Exception("Lattiude manquante");
            }
            $latitude_point = trim($latitude_point);

            if($longitude_point === null || trim($longitude_point) == "") {
                throw new \Exception("Longitude manquante");
            }
            $longitude_point = trim($longitude_point);

            $polygonData = null;
            $reqPolygonZone =  $db->prepare("
                SELECT s.gid, s.rusticite
                FROM rusticite_gis s
                WHERE ST_Within('SRID=4326;POINT(" . $longitude_point . " " . $latitude_point . ")'::geometry, geom)");
            $reqPolygonZone->execute();
            $resPolygon = $reqPolygonZone->fetchAll(PDO::FETCH_ASSOC);

            if(count($resPolygon) > 0) {
                $polygonData = $resPolygon[0];
            } else {
                $reqNearestPolygon =  $db->prepare("
                SELECT s.gid, s.rusticite, ST_Distance('SRID=4326;POINT(" . $longitude_point . " " . $latitude_point. ")'::geometry, geom) as dist
                FROM rusticite_gis s
                ORDER BY ST_Distance('SRID=4326;POINT(" . $longitude_point . " " . $latitude_point . ")'::geometry, geom)");

                $reqNearestPolygon->execute();
                $resPolygon = $reqNearestPolygon->fetchAll(PDO::FETCH_ASSOC);

                if(count($resPolygon) > 0) {
                    $polygonData = $resPolygon[0];
                }
            }

            if($polygonData !== null && isset($polygonData['rusticite'])) {
                $rusticiteFound = $em->getRepository(Rusticite::class)->findOneBy(array(
                    'nom' => $polygonData['rusticite']
                ));
            }
        } catch (\Exception $e) {}

        return array('precipitationsEstivales' => $precipitationEstivalleFound, 'rusticite' => $rusticiteFound, 'ph' => $phFound);
    }
}
