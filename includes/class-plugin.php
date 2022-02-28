<?php

namespace SayHello\HelloImages;

class Plugin
{
    private static $folder = 'hello-images';

    public static function add()
    {
        $htaccessContent = file_get_contents(dirname(dirname(__FILE__)) . '/templates/.htaccess');
        file_put_contents(self::getUploadsFolder() . '.htaccess', $htaccessContent);
        $indexContent = file_get_contents(dirname(dirname(__FILE__)) . '/templates/index.php');
        $indexContent = str_replace('{{folder}}', self::$folder, $indexContent);
        file_put_contents(self::getUploadsFolder() . 'index.php', $indexContent);
    }

    public static function getUploadsFolder($type = 'basedir')
    {
        $upload = wp_get_upload_dir();
        $dir    = trailingslashit($upload[$type]) . trailingslashit(self::$folder);
        if ( ! is_dir($dir) && $type === 'basedir') {
            mkdir($dir);
        }

        return $dir;
    }

    public static function remove()
    {
        unlink(self::getUploadsFolder() . 'index.php');
        unlink(self::getUploadsFolder() . '.htaccess');
    }
}
