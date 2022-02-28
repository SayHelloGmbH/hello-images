<?php

/*
Plugin Name: Hello Images
Plugin URI: https://github.com/SayHelloGmbH/Hello-Images
Description: Image resize proxy for WordPress
Author: Nico Martin
Author URI: https://nico.dev
Version: 0.1.9
Text Domain: hello-images
Domain Path: /languages
 */

include_once 'includes/class-plugin.php';
register_activation_hook(__FILE__, ['SayHello\HelloImages\Plugin', 'add']);
register_deactivation_hook(__FILE__, ['SayHello\HelloImages\Plugin', 'remove']);

