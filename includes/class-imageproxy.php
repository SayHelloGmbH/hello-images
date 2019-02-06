<?php

namespace SayHello\HelloImages;

class ImageProxy {

	public static function get_image() {
		if ( is_admin() || ! array_key_exists( 'hello-image', $_GET ) ) {
			return;
		}

		$max_width  = apply_filters( 'SayHello\HelloImages\MaxWidth', 3000 );
		$max_height = apply_filters( 'SayHello\HelloImages\MaxHeight', 3000 );

		$valid_extensions = [ 'jpg', 'png', 'gif' ];
		$content_dir      = trailingslashit( WP_CONTENT_DIR );
		$cache_dir        = $content_dir . 'cache/hello-image/';
		if ( ! is_dir( $cache_dir ) ) {
			mkdir( $cache_dir );
		}

		$image_org        = trailingslashit( $content_dir ) . $_GET['hello-image'];
		$image_org_size   = getimagesize( $image_org );
		$image_org_aspect = $image_org_size[0] / $image_org_size[1];
		$image_key        = md5( $_GET['hello-image'] );

		if ( ! extension_loaded( 'imagick' ) ) {
			self::exit_image( $image_org );
		}

		$width = 0;
		if ( array_key_exists( 'width', $_GET ) ) {
			$width = intval( $_GET['width'] );
		}
		if ( $width >= $max_width ) {
			$width = $max_width;
		}

		$height = 0;
		if ( array_key_exists( 'height', $_GET ) ) {
			$height = intval( $_GET['height'] );
		}
		if ( $height >= $max_height ) {
			$height = $max_height;
		}

		$quality = 100;
		if ( array_key_exists( 'quality', $_GET ) ) {
			$quality = intval( $_GET['quality'] );
		}
		if ( $quality >= 100 ) {
			$quality = 100;
		} elseif ( $quality <= 1 ) {
			$quality = 1;
		}

		$format = str_replace( 'image/', '', mime_content_type( $image_org ) );
		if ( 'jpeg' == $format ) {
			$format = 'jpg';
		}

		if ( array_key_exists( 'format', $_GET ) && in_array( $_GET['format'], $valid_extensions ) ) {
			$format = $_GET['format'];
		}

		$image_file = "{$image_key}-{$width}-{$height}-{$quality}.{$format}";
		if ( file_exists( $cache_dir . $image_file ) ) {
			self::exit_image( $cache_dir . $image_file );
		}

		$imagick        = new \imagick( $image_org );
		$imagick_filter = \Imagick::FILTER_LANCZOS;
		$imagick_blur   = 1;

		/**
		 * Set Size
		 */
		if ( $width && $height ) {
			$imagick->cropThumbnailImage( $width, $height );
		} elseif ( $width ) {
			$imagick->resizeImage( $width, intval( $width / $image_org_aspect ), $imagick_filter, $imagick_blur );
		} elseif ( $height ) {
			$imagick->resizeImage( intval( $height * $image_org_aspect ), $height, $imagick_filter, $imagick_blur );
		}

		/**
		 * Set Quality
		 */
		$imagick->setImageCompressionQuality( $quality );

		/**
		 * Set Format
		 */
		$imagick->setImageFormat( $format );

		/**
		 * Save
		 */

		$imagick->writeImage( $cache_dir . $image_file );
		self::exit_image( $cache_dir . $image_file );
	}

	private static function exit_image( $image ) {
		header( 'Content-Type: ' . mime_content_type( $image ) );
		header( 'Content-Length: ' . filesize( $image ) );
		echo file_get_contents( $image );
		exit;
	}
}