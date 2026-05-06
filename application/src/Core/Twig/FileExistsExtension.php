<?php

namespace App\Core\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class FileExistsExtension extends Twig_Extension
{
    public function getName()
    {
        return "file_exists";
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter("file_exists", array($this, "fileExists")),
        );
    }

    public function fileExists($file)
    {
        return file_exists($file);
    }
}