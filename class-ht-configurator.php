<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class HT_Configurator {

	public function __construct() {
		add_shortcode( 'ht-configurator', array( $this, 'hot_tube_configurator_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets() {
		wp_enqueue_script( 'ht-configurator-script', plugin_dir_url( __FILE__ ) . 'assets/ht-configurator.js', array( 'jquery' ), HTC_VERSION, true );
		wp_enqueue_style( 'ht-configurator-style', plugin_dir_url( __FILE__ ) . 'assets/ht-configurator.css', array(), HTC_VERSION );
	}

	public function hot_tube_configurator_shortcode() {
		return 'Hot Tube Configurator is active!';
	}
}

new HT_Configurator();