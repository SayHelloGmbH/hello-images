<?php

class GenerateImage
{
    private string $folder = '{{folder}}';
    private ?Imagick $imagick = null;
    private string $baseImagePath = '';
    private string $imagePath = '';
    private string $fileName = '';
    private int $width = 0;
    private int $height = 0;
    private int $quality = 100;
    private int $blur = 0;

    function __construct()
    {
        $imagePath      = $_SERVER['REQUEST_URI'];
        $imagePathParts = array_filter(explode('/', $imagePath), function ($e) {
            return ! ! $e;
        });

        $baseImagePathParts = [];

        foreach ($imagePathParts as $part) {
            if (substr($part, 0, 5) === 'size-') {
                $sizes        = explode('x', str_replace('size-', '', $part));
                $this->width  = intval($sizes[0]);
                $this->height = intval($sizes[1]);
            } elseif (substr($part, 0, 5) === 'blur-') {
                $this->blur = intval(str_replace('blur-', '', $part));
            } elseif (substr($part, 0, 8) === 'quality-') {
                $this->quality = intval(str_replace('quality-', '', $part));
            } elseif ($part !== $this->folder) {
                $baseImagePathParts[] = $part;
            }
        }

        $this->fileName = $imagePathParts[count($imagePathParts)];

        if ($this->width >= 4000) {
            $this->width = 4000;
        }
        if ($this->height >= 4000) {
            $this->height = 4000;
        }

        $this->baseImagePath = implode('/', $baseImagePathParts);
        $this->imagePath     = implode('/', $imagePathParts);
        $this->imagick       = new \imagick($this->getAbspath() . $this->baseImagePath);
    }

    private function getAbspath()
    {
        return str_replace("wp-content/uploads/{$this->folder}", '', str_replace('\\', '/', dirname(__FILE__)));
    }

    private static function debug($e)
    {
        echo '<pre>';
        if (is_array($e)) {
            foreach ($e as $i) {
                var_dump($i);
                echo '<br />';
            }
        } else {
            var_dump($e);
        }
        echo '</pre>';
        exit;
    }

    public function setSize()
    {
        $imagickFilter  = \Imagick::FILTER_LANCZOS;
        $imagickBlur    = 1;
        $imageOrgAspect = $this->imagick->getImageWidth() / $this->imagick->getImageHeight();

        if ($this->width && $this->height) {
            $this->imagick->cropThumbnailImage($this->width, $this->height);
        } elseif ($this->width) {
            $this->imagick->resizeImage($this->width, intval($this->width / $imageOrgAspect), $imagickFilter,
                $imagickBlur);
        } elseif ($this->height) {
            $this->imagick->resizeImage(intval($this->height * $imageOrgAspect), $this->height, $imagickFilter,
                $imagickBlur);
        }
    }

    public function setQuality()
    {
        if ($this->quality !== 0) {
            $this->imagick->setImageCompressionQuality($this->quality);
        }
    }

    public function setBlur()
    {
        if ($this->blur !== 0) {
            $this->imagick->blurImage($this->blur, 10);
        }
    }

    public function save()
    {
        $folder = str_replace($this->fileName, '', $this->getAbspath() . $this->imagePath);
        if ( ! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $this->imagick->writeImage($folder . $this->fileName);
    }

    public function echoImage()
    {
        $mime = $this->imagick->getImageMimeType();
        $mime = $mime === 'image/x-jpeg' ? 'image/jpeg' : $mime;
        $blob = $this->imagick->getImageBlob();
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . strlen($blob));
        echo $blob;
        exit;
    }
}

$image = new GenerateImage();
$image->setSize();
$image->setQuality();
$image->setBlur();
$image->save();
$image->echoImage();