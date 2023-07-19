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

	private function get_options() {
		$options = array(
			'bath_color' => [
				'label' => 'Bath color',
				'type' => 'radio',
				'default' => 'hb',
				'options' => [
					'hb' => [
						'label' => 'Himmel Blau (Standard)',
						'amount' => 0,
					],
					'ob' => [
						'label' => 'Ozeanblau',
						'amount' => 0,
					],
					'cr' => [
						'label' => 'Creme',
						'amount' => 0,
					],
					'gr' => [
						'label' => 'Grau',
						'amount' => 0,
					],
					'ws' => [
						'label' => 'Weis',
						'amount' => 0,
					],
				],
			],
			'external_color' => [
				'label' => 'External color',
				'type' => 'radio',
				'default' => 'th',
				'options' => [
					'th' => [
						'label' => 'Thermo Holz (Hell)',
						'amount' => 0,
					],
					'gr' => [
						'label' => 'Grau WPC',
						'amount' => 0,
					],
					'br' => [
						'label' => 'Braun WPC',
						'amount' => 0,
					],
					'fh' => [
						'label' => 'Fichtenholz (dunkel)',
						'amount' => 0,
					],
				],
			],
			'oven_type' => [
				'label' => 'Ofen Art',
				'type' => 'radio',
				'default' => 'e430s',
				'options' => [
					'oo' => [
						'label' => 'Ohne Ofen (Nur Tub)',
						'amount' => 0,
					],
					'e430s' => [
						'label' => 'Externer Ofen Model: AISI430 mit Schornsteinsystem',
						'amount' => 0,
					],
					'e304s' => [
						'label' => 'Externer Ofen Model: AISI304 mit Schornsteinsystem',
						'amount' => 0,
					],
					'e316sgc' => [
						'label' => 'Externer Ofen Model: AISI316 mit Schornsteinsystem Geeignet für Chemikalien (chlor)',
						'amount' => 0,
					],
				],
			],
		);

		return $options;
	}

	public function hot_tube_configurator_shortcode() {
		ob_start();

			$post_id = get_the_ID();
			if ( ! $post_id ) {
				return '';
			}

			// $options = get_post_meta( $post_id, 'options_group', true );
			$options = $this->get_options();
			$variation_images = get_post_meta( $post_id, 'variation', true );
			$initial_image_id = get_post_meta( $post_id, 'initial_image', true );
			$initial_image_src = wp_get_attachment_image_src( $initial_image_id, 'large' );


			echo '<div class="dtc-wrapper">';
			
				echo '<div class="dtc-image-column">';	
					echo '<div class="dtc-image-wrapper">';	
						echo '<img class="dtc-image" src="' . $initial_image_src[0] . '"/>';
					echo '</div>';
				echo '</div>';

				echo '<div class="dtc-options-column">';
					echo '<div class="dtc-options-wrapper">';

						echo '<form autocomplete="off">';
							foreach ( $options as $options_group_name => $options_group_settings ) {
								echo '<fieldset>';
									echo '<legend>' . $options_group_settings['label'] . '</legend>';
									foreach ( $options_group_settings['options'] as $option_name => $option_settings ) {
										echo '<label>';
											echo sprintf(
												'<input type="%s" name="%s" value="%s" %s>',
												$options_group_settings['type'],
												$options_group_name,
												$option_name,
												checked( $options_group_settings['default'] ?? '', $option_name, false )
											);
											echo '<span>' . $option_settings['label'] . '</span>';
										echo '</label>';
									}
								echo '</fieldset>';
							}
						echo '</form>';

						// echo '<pre>variation_images ' . print_r($variation_images, true) . '</pre><br>';

					echo '</div>';
				echo '</div>';
			echo '</div>';

		return ob_get_clean();
	}
}

new HT_Configurator();