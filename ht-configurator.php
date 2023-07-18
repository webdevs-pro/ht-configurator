<?php
/**
 * Plugin Name: Hot Tube Configurator
 * Plugin URI: https://your-website.com/
 * Description: This is a plugin to provide a Hot Tube configuration shortcode.
 * Version: 0.0.1
 * Author: Alex Ishchenko
 * Author URI: https://website.cv.ua/
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require 'vendor/autoload.php';
require 'shortcode.php';

/**
 * Init plugin updater.
 *
 * @return void
 */
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$cpfeUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/webdevs-pro/ht-configurator',
	__FILE__,
	'ht-configurator'
);
//Set the branch that contains the stable release.
$cpfeUpdateChecker->setBranch( 'main' );