<?php
/**
 * Plugin Name: Hot Tube Configurator
 * Plugin URI: https://github.com/webdevs-pro/ht-configurator
 * Description: This is a plugin to provide a Hot Tube configuration shortcode.
 * Version: 0.7.0
 * Author: Alex Ishchenko
 * Author URI: https://website.cv.ua/
 * License: GPL2
 */

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class HT_Configurator_Plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialize_plugin' ) );
	}

	/**
	 * Initialize the plugin.
	 */
	public function initialize_plugin() {
		// Initialize update checker.
		require 'vendor/autoload.php';
		$this->initialize_update_checker();

		if ( ! function_exists( 'get_plugin_data' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Check if the required plugins are installed and activated.
		$required_plugins = array(
			'meta-box/meta-box.php',
			'meta-box-group/meta-box-group.php',
			'mb-settings-page/mb-settings-page.php',
		);

		$missing_plugins = array();

		foreach ( $required_plugins as $plugin ) {
			if ( ! is_plugin_active( $plugin ) ) {
				$missing_plugins[] = $plugin;
			}
		}

		// If any of the required plugins are missing, show an admin notice and return.
		if ( ! empty( $missing_plugins ) ) {
			add_action( 'admin_notices', array( $this, 'missing_plugins_notice' ) );
			return;
		}

		// Define HTC_VERSION after confirming that the 'get_plugin_data' function is available.
		define( 'HTC_VERSION', get_plugin_data( __FILE__ )['Version'] );

		// Load required files and initialize plugin components.
		require 'class-option-pages.php';
		require 'class-metabox-fields.php';
		require 'class-configurator-shortcode.php';

	}

	/**
	 * Show an admin notice for missing plugins.
	 */
	public function missing_plugins_notice() {
		$missing_plugins = array(
			'<b>Meta Box</b>',
			'<b>Meta Box Group</b>',
			'<b>MB Settings Page</b>',
		);

		$message = sprintf(
			'The Hot Tube Configurator plugin requires the following plugins to be installed and activated: %s.',
			implode( ', ', $missing_plugins )
		);

		echo '<div class="error"><p>' . $message . '</p></div>';
	}

	/**
	 * Initialize the update checker.
	 */
	private function initialize_update_checker() {
		$cpfeUpdateChecker = PucFactory::buildUpdateChecker(
			'https://github.com/webdevs-pro/ht-configurator',
			__FILE__,
			'ht-configurator'
		);
		//Set the branch that contains the stable release.
		$cpfeUpdateChecker->setBranch( 'main' );
	}
}
new HT_Configurator_Plugin();
