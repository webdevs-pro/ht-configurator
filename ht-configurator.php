<?php
/**
 * Plugin Name: Hot Tube Configurator
 * Plugin URI: https://your-website.com/
 * Description: This is a plugin to provide a Hot Tube configuration shortcode.
 * Version: 0.2.0
 * Author: Alex Ishchenko
 * Author URI: https://website.cv.ua/
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
}
define( 'HTC_VERSION', get_plugin_data( __FILE__ )['Version'] );


require 'vendor/autoload.php';
require 'class-option-pages.php';
require 'class-metabox-fields.php';
require 'class-configurator-shortcode.php';


use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$cpfeUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/webdevs-pro/ht-configurator',
	__FILE__,
	'ht-configurator'
);
//Set the branch that contains the stable release.
$cpfeUpdateChecker->setBranch( 'main' );