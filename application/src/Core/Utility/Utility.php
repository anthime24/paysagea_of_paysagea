<?php

namespace App\Core\Utility;

class Utility
{
    public static function excerpt($orginalTxt, $maxLength) {
        if(mb_strlen($orginalTxt) <= $maxLength) {
            return $orginalTxt;
        } else {
            $beginning = mb_substr($orginalTxt, 0, $maxLength);
            $ending = mb_substr($orginalTxt, $maxLength);

            if(preg_match("/^ +/", $ending) === 1 || preg_match("/^\R/", $ending) === 1) {
                return $beginning . " ...";
            } else {
                $words = mb_split(' ', $beginning);
                if(count($words) <= 1) {
                    return $beginning . " ...";
                } else {
                    array_pop($words);

                    $excerpt = "";
                    for($i=0; $i<count($words); $i++) {
                        if($i == 0) {
                            $excerpt = $words[$i];
                        } else {
                            $excerpt = $excerpt . " " . $words[$i];
                        }
                    }

                    return $excerpt . " ...";
                }
            }
        }
    }

    public static function paginateDoctrineQuery($query, $fnCallback, $maxNbrResults = 200) {
        $nbrResults = 0;
        $rootAlias = $query->getRootAliases()[0];

        $nbrResultsQuery = clone $query;
        $nbrResultsQuery->resetDQLPart('select');
        $nbrResultsQuery->addSelect('COUNT(' . $rootAlias . ') as nbr_resultats');

        $nbrResults = $nbrResultsQuery->getQuery()->getArrayResult()[0]['nbr_resultats'];

        if($nbrResultsQuery->getMaxResults() !== null && is_int($nbrResultsQuery->getMaxResults()) && $nbrResultsQuery->getMaxResults() > 0) {
            $nbrResults = $nbrResultsQuery->getMaxResults();
        }

        $nbrIterrations = 1;

        if($nbrResults > $maxNbrResults) {
            $nbrIterrations = $nbrResults / $maxNbrResults;
            if($nbrResults % $maxNbrResults > 0) {
                $nbrIterrations++;
            }
        }

        for($currentIteration=0; $currentIteration<$nbrIterrations; $currentIteration++) {
            $beginning = $currentIteration * $maxNbrResults;

            $paginatedQuery = clone $query;
            $paginatedQuery->setFirstResult($beginning);
            $paginatedQuery->setMaxResults($maxNbrResults);

            $fnCallback($paginatedQuery);
        }
    }

    public static function nbrLinesInCsv($file, $length = 99999, $delimiter = ";")
    {
        $nbrLines = 0;
        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle, $length, $delimiter, '"')) !== false) {
                $nbrLines = $nbrLines + 1;
            }
            fclose($handle);
        }
        return $nbrLines;
    }
}