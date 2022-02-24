<?php

namespace SayHello\HelloImages;

class Plugin
{
    public static $folder = 'hello-images';

    public static function add()
    {
        $content = "<IfModule mod_rewrite.c>\n";
        $content .= "rewriteEngine on\n";
        $content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $content .= "RewriteRule ^.*$ ../../../index.php?hello-image [NC,L,QSA]\n";
        $content .= "</IfModule>";
        file_put_contents(self::getUploadsFolder() . '.htaccess', $content);
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
        // todo: unlink .htaccess
    }
}
