<?php
/**
 * Plugin Name: Hot Tube Configurator
 * Plugin URI: https://your-website.com/
 * Description: This is a plugin to provide a Hot Tube configuration shortcode.
 * Version: 1.0.0
 * Author: Alex Ishchenko
 * Author URI: https://author-website.com/
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require 'vendor/autoload.php';


// The main function for your shortcode
function hot_tube_configurator_shortcode() {
	// This is where you should put your shortcode's functionality. 
	// For this example, we'll just return a simple string.
	return "Hot Tube Configurator is active!";
}
// This is where we register our shortcode in WordPress.
add_shortcode('ht-configurator', 'hot_tube_configurator_shortcode');



