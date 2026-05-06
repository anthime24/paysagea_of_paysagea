<?php

namespace App\Application\Service;

use Spipu\Html2Pdf\Html2Pdf as Html2Pdf;

class Html2PdfService
{
    public function create(
        $orientation = null,
        $format = null,
        $lang = null,
        $unicode = null,
        $encoding = null,
        $margin = null
    ) {
        return new Html2Pdf(
            $orientation ? $orientation : 'P',
            $format ? $format : 'A4',
            $lang ? $lang : 'fr',
            $unicode ? $unicode : true,
            $encoding ? $encoding : 'UTF-8',
            $margin ? $margin : [10, 15, 10, 15]
        );
    }
}