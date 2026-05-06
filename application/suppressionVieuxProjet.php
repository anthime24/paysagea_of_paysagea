<?php

$dbHost = '127.0.0.1';
$dbUser = 'mjmt_app';
$dbPassword = 'T8aqw@97';
$dbName = 'mjmt_app';

$uploadPath = __DIR__ . '/public/uploads/';
$intervalMoisConservation = 24;
$intervalMoisConservation = $intervalMoisConservation + 1;
$simulation = 1;

$creationBasePath = __DIR__ . "/public/uploads/creation";
$banquePhotoBasePath = __DIR__ . "/public/uploads/banque_photo";


function creationBackgroundPath($creationBasePath, $creationId, $versionImage, $banquePhotoName) {
    $tabNomDossier = str_split($creationId);
    $creationPath = $creationBasePath . '/' . implode('/', $tabNomDossier) . '/images';
    $creationPath .= '/' . $versionImage . '-' . $banquePhotoName;

    return $creationPath;
}

if(isset($argv[1]) && $argv[1] == 0) {
    $simulation = 0;
}

$date = new \DateTime();

echo 'Date courante ' . $date->format('Y-m-d H:i:s') . PHP_EOL;

$date->sub(new DateInterval('P' . $intervalMoisConservation . 'M'));

echo 'Date suppression avant le : ' . $date->format('Y-m-d 00:00:00') . PHP_EOL;

$dbh = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName, $dbUser, $dbPassword);
$dbh->beginTransaction();

try {
    foreach ($dbh->query('SELECT COUNT(id) as total FROM creation') as $row) {
        echo 'Nb création : ' . $row['total'] . PHP_EOL;
    }
    foreach ($dbh->query('SELECT COUNT(id) as total FROM creation_entite') as $row) {
        echo 'Nb création entité : ' . $row['total'] . PHP_EOL;
    }

    $sth = $dbh->prepare('
        SELECT id 
        FROM creation 
        WHERE 
            (date_modification < "' . $date->format('Y-m-d 00:00:00') . '" OR (date_modification IS NULL AND date_creation < "' . $date->format('Y-m-d 00:00:00') . '"))
                AND
            id NOT IN (SELECT ce.creation_id FROM creation_entite ce WHERE ce.updated >= "' . $date->format('Y-m-d 00:00:00') . '")
        ORDER BY date_creation asc
		LIMIT 2000
        ');
    $sth->execute();
    $result = $sth->fetchAll();

    $creationToDeleteIds = array();
    foreach ($result as $row) {
        $creationToDeleteIds[] = $row['id'];
    }

    if($simulation == 1) {
        echo 'SELECT id 
        FROM creation 
        WHERE 
            (date_modification < "' . $date->format('Y-m-d 00:00:00') . '" OR (date_modification IS NULL AND date_creation < "' . $date->format('Y-m-d 00:00:00') . '"))
                AND
            id NOT IN (SELECT ce.creation_id FROM creation_entite ce WHERE ce.updated >= "' . $date->format('Y-m-d 00:00:00') . '")
            ORDER BY date_creation asc
			LIMIT 2000
            ' . PHP_EOL;
    }

    echo 'Nb création à supprimer : ' . count($creationToDeleteIds) . PHP_EOL;
    $sth = $dbh->prepare('
        SELECT id 
        FROM creation_entite 
        WHERE creation_id IN (' . implode(',', $creationToDeleteIds) . ')');

    $sth->execute();
    $result = $sth->fetchAll();
    $creationEntiteToDeleteIds = array();

    foreach ($result as $row) {
        $creationEntiteToDeleteIds[] = $row['id'];
    }

    if($simulation == 1) {
        echo 'SELECT id 
        FROM creation_entite 
        WHERE creation_id IN (' . implode(',', $creationToDeleteIds) . ')' . PHP_EOL;
    }

    echo 'Nb création entité à supprimer : ' . count($creationEntiteToDeleteIds) . PHP_EOL;
    $creationEntitePath = $uploadPath . 'creation_entite/';
    $creationPath = $uploadPath . 'creation/';

    if (count($creationToDeleteIds) > 0 && file_exists($creationPath) && file_exists($creationEntitePath)) {

        //suppression des banques photos
        /*$reqCountDefaultPicture = $dbh->prepare('
            SELECT * FROM banque_photo where default_picture = 1
        ');
        $reqCountDefaultPicture->execute();
        $defaultPicture = $reqCountDefaultPicture->fetchAll();
        if(count($defaultPicture) == 0) {
            echo "Suppression des images de la banque photo impossible, aucune photo n'est défini par défault" . PHP_EOL;
        }

        $reqMarqueBanquePhotoAsDeleted = $dbh->prepare('
            UPDATE banque_photo set deleted = 1 where id = :banquePhotoId
        ');

        $reqCreation = $dbh->prepare('
            select c.id, c.version_image
            from creation c
            where c.id in (:creationIds)
        ');

        $reqBanquePhoto = $dbh->prepare('
            select bp.id, bp.photo, bp.type,
            (select GROUP_CONCAT(c2.id SEPARATOR ",") FROM creation c2 where c2.banque_photo_id = bp.id) as "creation_liees"
            from banque_photo bp
            where bp.date_creation < "' . $date->format('Y-m-d 00:00:00') . '"
            and (bp.deleted is null or bp.deleted = 0)
            and (bp.default_picture is null or bp.default_picture = 0)
            group by bp.id
            order by bp.date_creation asc
            limit 500'
        );

        $reqBanquePhoto->execute();
        $banquePhotoToDelete = $reqBanquePhoto->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        echo 'Nb banque photo à supprimer : ' . count($banquePhotoToDelete) . PHP_EOL;*/

        /*
        foreach($banquePhotoToDelete as $banquePhotoKey => $banquePhotoItem) {
            $banquePhotoItem = $banquePhotoItem[0];
            $banquePhotoItem['banquePhotoId'] = $banquePhotoKey;

            $banquePhotoImagePath = $banquePhotoBasePath . '/' . $banquePhotoKey . '/' .$banquePhotoItem['photo'];

            $canDelete = false;
            if($banquePhotoItem['creation_liees'] === null || trim($banquePhotoItem['creation_liees']) == "") {
                $canDelete = true;
            } else {
                $reqCreation->bindValue(':creationIds', $banquePhotoItem['creation_liees']);
                $reqCreation->execute();

                $creationLieesIds = explode(",", $banquePhotoItem['creation_liees']);
                $creationLiees = $reqCreation->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

                $nbrCreationImageFound = 0;
                foreach($creationLiees as $creationKey => $creationItem) {
                    $creationItem = $creationItem[0];

                    $creationImage = creationBackgroundPath($creationBasePath, $creationKey, $creationItem['version_image'], $banquePhotoItem['photo']);
                    if(file_exists($creationImage)) {
                        $nbrCreationImageFound = $nbrCreationImageFound + 1;
                    }
                }
                if($nbrCreationImageFound == count($creationLieesIds)) {
                    $canDelete = true;
                }
            }

            //ATTENTION PROBLEME LA SUPPRESSION NE FONCTIONNE PAS
            if(count($defaultPicture) > 0 && $canDelete && file_exists($banquePhotoImagePath)) {
                if($simulation == 0) {
                    @unlink($banquePhotoImagePath);

                    //désactive la banque photo
                    $reqMarqueBanquePhotoAsDeleted->bindValue(':banquePhotoId', $banquePhotoKey);
                    $reqMarqueBanquePhotoAsDeleted->execute();
                } else {
                    //echo 'Suppression Banque Photo (chemin) : ' . $banquePhotoImagePath . PHP_EOL;
                }
            }
        }
        */

        // nettoyage image
        foreach ($creationEntiteToDeleteIds as $creationEntiteId) {
            $imagePath = $creationEntitePath . implode('/', str_split($creationEntiteId)) . '/images/';
            if (file_exists($imagePath)) {
                foreach (glob($imagePath . '/*.*') as $file) {
                    if (is_file($file)) {
                        //var_dump($file);
                        if ($simulation == 0) {
                            if(@unlink($file) === false) {
                                throw new \Exception("IMPOSSIBLE TO DELETE FILE " . $file);
                            }
                        }
                    }
                }
            }
        }

        foreach ($creationToDeleteIds as $creationId) {
            $imagePath = $creationPath . implode('/', str_split($creationId)) . '/images/';
            if (file_exists($imagePath)) {
                foreach (glob($imagePath . '/*.*') as $file) {
                    if (is_file($file)) {
                        //var_dump($file);
                        if ($simulation == 0) {
                            if(@unlink($file) === false) {
                                throw new \Exception("IMPOSSIBLE TO DELETE FILE " . $file);
                            }
                        }
                    }
                }
            }
        }

        if($simulation == 1) {
            var_dump('--------------');
            var_dump(implode(', ', $creationToDeleteIds));
            var_dump('------------');
            var_dump(implode(', ', $creationEntiteToDeleteIds));
        }

        //nettoyage projet
        if ($simulation == 0) {
            // nettoyage element en base de donnée
            $dbh->query('DELETE FROM creation_entite WHERE creation_id IN (' . implode(',', $creationToDeleteIds) . ')');
            $dbh->query('DELETE FROM creation WHERE id IN (' . implode(',', $creationToDeleteIds) . ')');

            // nettoyage des projets vide (projets sans création)
            //$dbh->query('DELETE FROM projet WHERE id NOT IN (SELECT c.projet_id FROM creation c) AND date_creation < "' . $date->format('Y-m-d 00:00:00') . '"');
        }

        $dbh->commit();
    } else {
        echo 'Chemin images inexistant' . PHP_EOL;
    }

    echo 'Fin script' . PHP_EOL;
    die();
} catch (\Exception $e) {
    $dbh->rollBack();
    echo 'Erreur !: ' . $e->getMessage();
    die();
}
