<?php

namespace App\Core\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class FileExistsRelativeExtension extends Twig_Extension
{
    private $kernelProjectDir = null;
    private $request = null;

    public function __construct($kernelProjectDir, $requestStack)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getName()
    {
        return "file_exists_relative";
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter("file_exists_relative", array($this, "fileExistsRelative")),
            new Twig_SimpleFilter("relative_file_path", array($this, "relativeFilePath")),
        );
    }

    public function fileExistsRelative($file)
    {
        $file = str_replace($this->request->getSchemeAndHttpHost(), '', $file);

        if (stristr($file, '/app_dev.php') !== false && stristr($file, '/app_dev.php') == 0) {
            $file = str_replace('/app_dev.php', '', $file);
        } else {
            if (stristr($file, '/app.php') !== false && stristr($file, '/app.php') == 0) {
                $file = str_replace('/app.php', '', $file);
            }
        }

        $rootDir = realpath($this->kernelProjectDir) . '/public';
        $path = $rootDir . $file;

        return file_exists($path);
    }

    public function relativeFilePath($file)
    {
        $file = str_replace($this->request->getSchemeAndHttpHost(), '', $file);

        if (stristr($file, '/app_dev.php') !== false && stristr($file, '/app_dev.php') == 0) {
            $file = str_replace('/app_dev.php', '', $file);
        } else {
            if (stristr($file, '/app.php') !== false && stristr($file, '/app.php') == 0) {
                $file = str_replace('/app.php', '', $file);
            }
        }

        return substr($file, 1);
    }
}