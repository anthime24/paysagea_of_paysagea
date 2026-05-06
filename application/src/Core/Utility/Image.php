<?php

namespace App\Core\Utility;

class Image
{
    public static function getImageType($filepath)
    {
        if (!file_exists($filepath) || !is_file($filepath)) {
            return false;
        }

        $imageSize = getimagesize($filepath); // [] if you don't have exif you could use getImageSize()
        $type = $imageSize[2];
        $allowedTypes = array(
            1 => 'gif', // [] gif
            2 => 'jpg', // [] jpg
            3 => 'png', // [] png
            6 => 'bmp'   // [] bmp
        );

        if (!array_key_exists($type, $allowedTypes)) {
            return false;
        }

        return $allowedTypes[$type];
    }

    public static function imageCreateFromAny($filepath)
    {
        $im = null;
        $type = Image::getImageType($filepath);

        try {
            switch ($type) {
                case 'gif' :
                    $im = @imagecreatefromgif($filepath);
                    break;
                case 'jpg' :
                    $im = @imagecreatefromjpeg($filepath);
                    break;
                case 'png' :
                    $im = @imagecreatefrompng($filepath);
                    break;
                case 'bmp' :
                    $im = @imagecreatefrombmp($filepath);
                    break;
            }
        } catch (Exception $e) {
            $im = null;
        }

        if (!$im) {
            $im = imagecreatefromstring(file_get_contents($filepath));
        }

        return $im;
    }

    public static function saveImageFromAny($resource, $previousPath, $filepath)
    {
        $status = null;
        $type = Image::getImageType($previousPath);

        switch ($type) {
            case 'gif' :
                $status = imagegif($resource, $filepath);
                break;
            case 'jpg' :
                $status = imagejpeg($resource, $filepath);
                break;
            case 'png' :
                $status = imagepng($resource, $filepath);
                break;
            case 'bmp' :
                $status = imagebmp($resource, $filepath);
                break;
        }

        return $status;
    }
}
