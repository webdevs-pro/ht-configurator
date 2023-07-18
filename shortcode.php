<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// The main function for your shortcode
function hot_tube_configurator_shortcode() {
	// This is where you should put your shortcode's functionality. 
	// For this example, we'll just return a simple string.
	return "Hot Tube Configurator is active!";
}
// This is where we register our shortcode in WordPress.
add_shortcode('ht-configurator', 'hot_tube_configurator_shortcode');



