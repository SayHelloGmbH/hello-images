<?php

namespace SayHello\HelloImages;

class Plugin {
	private static $htaccess_key = 'HelloImages';

	public static function add() {
		$htaccess       = new \nicomartin\Htaccess( self::$htaccess_key );
		$content_folder = str_replace( ABSPATH, '', WP_CONTENT_DIR );

		$content = [
			'<IfModule mod_rewrite.c>',
			'RewriteEngine On',
			'RewriteRule ^' . $content_folder . '/([^\.]+)\.(png|jpg|gif) /index.php?hello-image=$1.$2 [QSA]',
			'</IfModule>',
		];

		$htaccess->set( implode( "\n", $content ) );
	}

	public static function remove() {
		$htaccess = new \nicomartin\Htaccess( self::$htaccess_key );
		$htaccess->delete();
	}
}
