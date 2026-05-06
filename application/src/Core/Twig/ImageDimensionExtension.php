<?php

namespace App\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageDimensionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction("pdfStyleForMaximumHeight", array($this, "pdfStyleForMaximumHeight"))
        );
    }

    public function pdfStyleForMaximumHeight($file, $defaultHeightInMm, $maximumWidthInPx = 850, $maximumHeightInPx = 550)
    {
        $style = 'height: ' . $defaultHeightInMm . 'mm';
        if(file_exists($file)) {
            $imageSize = getimagesize($file);
            $imageWidth = $imageSize[0];
            $imageHeight = $imageSize[1];

            $ratio = $imageWidth / $imageHeight;

            if(($maximumHeightInPx * $ratio) > $imageWidth) {
                if(($maximumHeightInPx * $ratio) > $maximumWidthInPx) {
                    $style = 'max-height: ' . $defaultHeightInMm . 'mm';
                }
            }
        }

        return $style;
    }
}
