<?php

namespace App\Core\Utility;

use Exception;

class CatchableTreatementException extends Exception
{
    protected $treatmentResult = null;

    /**
     * @return null
     */
    public function getTreatmentResult()
    {
        return $this->treatmentResult;
    }

    /**
     * @param null $treatmentResult
     */
    public function setTreatmentResult($treatmentResult): void
    {
        $this->treatmentResult = $treatmentResult;
    }
}
