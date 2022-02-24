<?php

namespace SayHello\HelloImages;

class ImageProxy
{
    public static function getImage()
    {
        if (is_admin() || ! array_key_exists('hello-image', $_GET)) {
            return;
        }

        $imagePath      = $_SERVER['REQUEST_URI'];
        $imagePathParts = array_filter(explode('/', $imagePath), function ($e) {
            return ! ! $e;
        });

        $fileName           = $imagePathParts[count($imagePathParts)];
        $baseImagePathParts = [];

        $maxWidth  = apply_filters('SayHello\HelloImages\MaxWidth', 3000);
        $maxHeight = apply_filters('SayHello\HelloImages\MaxHeight', 3000);
        $width     = 0;
        $height    = 0;
        $quality   = 100;
        $blur      = 0;

        foreach ($imagePathParts as $part) {
            // get quality from quality-90
            if (substr($part, 0, 5) === 'size-') {
                $sizes  = explode('x', str_replace('size-', '', $part));
                $width  = $sizes[0];
                $height = $sizes[1];
            } elseif (substr($part, 0, 5) === 'blur-') {
                $blur = str_replace('blur-', '', $part);
            } elseif (substr($part, 0, 8) === 'quality-') {
                $quality = str_replace('quality-', '', $part);
            } elseif ($part !== Plugin::$folder) {
                $baseImagePathParts[] = $part;
            }
        }

        if ($width >= $maxWidth) {
            $width = $maxWidth;
        }
        if ($height >= $maxHeight) {
            $height = $maxHeight;
        }

        $baseImagePath = implode('/', $baseImagePathParts);
        $baseImageUrl  = trailingslashit(get_home_url()) . $baseImagePath;

        if ( ! extension_loaded('imagick')) {
            exit;
        }

        $imageOrg               = ABSPATH . $baseImagePath;
        $contentFolder          = str_replace(get_home_url(), '', Plugin::getUploadsFolder('baseurl'));
        $imagePath              = str_replace(Plugin::getUploadsFolder(), '', $imagePath);
        $folderToCreate         = str_replace($fileName, '', str_replace($contentFolder, '', $imagePath));
        $absoluteFolderToCreate = Plugin::getUploadsFolder() . $folderToCreate;
        $imageOrgSize           = getimagesize($imageOrg);
        $imageOrgAspect         = $imageOrgSize[0] / $imageOrgSize[1];
        if ( ! is_dir($absoluteFolderToCreate)) {
            mkdir($absoluteFolderToCreate, 0755, true);
        }

        $imagick       = new \imagick(ABSPATH . $baseImagePath);
        $imagickFilter = \Imagick::FILTER_LANCZOS;
        $imagickBlur   = 1;

        /**
         * Set Size
         */
        if ($width && $height) {
            $imagick->cropThumbnailImage($width, $height);
        } elseif ($width) {
            $imagick->resizeImage($width, intval($width / $imageOrgAspect), $imagickFilter, $imagickBlur);
        } elseif ($height) {
            $imagick->resizeImage(intval($height * $imageOrgAspect), $height, $imagickFilter, $imagickBlur);
        }

        /**
         * Set Quality
         */
        if ($quality !== 0) {
            $imagick->setImageCompressionQuality($quality);
        }

        /**
         * Set blur
         */
        if ($blur !== 0) {
            $imagick->blurImage($blur, 10);
        }

        $imagick->writeImage($absoluteFolderToCreate . $fileName);

        self::exitImage($absoluteFolderToCreate . $fileName);

        exit;
    }

    private static function exitImage($image)
    {
        header('Content-Type: ' . mime_content_type($image));
        header('Content-Length: ' . filesize($image));
        echo file_get_contents($image);
        exit;
    }
}