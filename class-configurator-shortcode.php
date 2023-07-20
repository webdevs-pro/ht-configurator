<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class HT_Configurator {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'ht-configurator', array( $this, 'hot_tube_configurator_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_htc_form_change',  array( $this, 'ajax_htc_form_change' ) );
      add_action( 'wp_ajax_nopriv_htc_form_change',  array( $this, 'ajax_htc_form_change' ) );
	}









	/**
	 * Enqueue assets.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'ht-configurator-script', plugin_dir_url( __FILE__ ) . 'assets/ht-configurator.js', array( 'jquery' ), HTC_VERSION, true );
		wp_localize_script( 'ht-configurator-script', 'ht_configurator', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_style( 'ht-configurator-style', plugin_dir_url( __FILE__ ) . 'assets/ht-configurator.css', array(), HTC_VERSION );
	}









	/**
	 * Get options.
	 *
	 * @return array Options data.
	 */
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









	/**
	 * Get variations.
	 *
	 * @return array Variations data.
	 */
	private function get_variations() {
		$variations = array(
			// BATH HB
			[
				'image_id' => 27,
				'conditions' => [
					'bath_color' => ['hb'],
					'external_color' => ['th'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 36,
				'conditions' => [
					'bath_color' => ['hb'],
					'external_color' => ['gr'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 35,
				'conditions' => [
					'bath_color' => ['hb'],
					'external_color' => ['br'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 65,
				'conditions' => [
					'bath_color' => ['hb'],
					'external_color' => ['fh'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			// BATH OB
			[
				'image_id' => 39,
				'conditions' => [
					'bath_color' => ['ob'],
					'external_color' => ['th'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 38,
				'conditions' => [
					'bath_color' => ['ob'],
					'external_color' => ['gr'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 37,
				'conditions' => [
					'bath_color' => ['ob'],
					'external_color' => ['br'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 66,
				'conditions' => [
					'bath_color' => ['ob'],
					'external_color' => ['fh'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			// BATH CR
			[
				'image_id' => 31,
				'conditions' => [
					'bath_color' => ['cr'],
					'external_color' => ['th'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 29,
				'conditions' => [
					'bath_color' => ['cr'],
					'external_color' => ['br'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 30,
				'conditions' => [
					'bath_color' => ['cr'],
					'external_color' => ['gr'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 63,
				'conditions' => [
					'bath_color' => ['cr'],
					'external_color' => ['fh'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			// BATH GR
			[
				'image_id' => 34,
				'conditions' => [
					'bath_color' => ['gr'],
					'external_color' => ['th'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 32,
				'conditions' => [
					'bath_color' => ['gr'],
					'external_color' => ['br'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 33,
				'conditions' => [
					'bath_color' => ['gr'],
					'external_color' => ['gr'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 64,
				'conditions' => [
					'bath_color' => ['gr'],
					'external_color' => ['fh'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			// WEIS GR
			[
				'image_id' => 42,
				'conditions' => [
					'bath_color' => ['ws'],
					'external_color' => ['th'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 40,
				'conditions' => [
					'bath_color' => ['ws'],
					'external_color' => ['br'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 41,
				'conditions' => [
					'bath_color' => ['ws'],
					'external_color' => ['gr'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
			[
				'image_id' => 62,
				'conditions' => [
					'bath_color' => ['ws'],
					'external_color' => ['fh'],
					'oven_type' => ['e430s', 'e304s', 'e316sgc'],
				]
			],
		);

		return $variations;
	}









	/**
	 * Get matching variation image ID.
	 *
	 * @param array $variations       Variations data.
	 * @param array $default_options  Default options.
	 *
	 * @return int|null Matching image ID or null if no match found.
	 */
	private function get_matching_variation_image_id( $variations, $default_options ) {
		foreach ( $variations as $variation ) {
			$conditions = $variation['conditions'];
			$matched = true;

			foreach ( $conditions as $option => $values ) {
				if ( ! in_array( $default_options[$option], $values ) ) {
					$matched = false;
					break;
				}
			}

			if ( $matched ) {
				return $variation['image_id'];
			}
		}

		return null; // Return null if no match found
	}









	/**
	 * Get default image URL.
	 *
	 * @param array $options Options data.
	 *
	 * @return string Default image URL.
	 */
	private function get_default_image_url( $options ) {
		$default_options = array();
		foreach ( $options as $option_key => $option_data ) {
			$default_options[$option_key] = $option_data['default'];
		}

		error_log( "default_options\n" . print_r( $default_options, true ) . "\n" );



		$default_image_id = $this->get_matching_variation_image_id( $this->get_variations(), $default_options );
		$default_image_url = '';
		if ( $default_image_id ) {
			$default_image_src = wp_get_attachment_image_src( $default_image_id, 'large' );
			$default_image_url = $default_image_src[0] ?? '';
		}

		return $default_image_url;
	}









	/**
	 * Get default image URL.
	 *
	 * @param array $options Options data.
	 *
	 * @return string Default image URL.
	 */
	private function get_matching_image_url( $options, $variation = [] ) {
		if ( ! $variation ) {
			$variation = array();
			foreach ( $options as $option_key => $option_data ) {
				$variation[$option_key] = $option_data['default'];
			}
		}


		$default_image_id = $this->get_matching_variation_image_id( $this->get_variations(), $variation );
		$default_image_url = '';
		if ( $default_image_id ) {
			$default_image_src = wp_get_attachment_image_src( $default_image_id, 'large' );
			$default_image_url = $default_image_src[0] ?? '';
		}

		return $default_image_url;
	}









	/**
	 * Shortcode callback for hot tube configurator.
	 *
	 * @return string Configurator HTML.
	 */
	public function hot_tube_configurator_shortcode() {
		ob_start();

			$post_id = get_the_ID();
			if ( ! $post_id ) {
				return '';
			}

			// $options = get_post_meta( $post_id, 'options_group', true );
			// $options = $this->get_options();
			$options = get_option( 'htc-options' )['options_group'] ?? [];
			$default_variant = get_option( 'htc-variations' )['default_variation'] ?? [];
			
			$default_image_url = $this->get_default_image_url( $options );


			echo '<div class="dtc-wrapper">';
			
				echo '<div class="dtc-image-column">';	
					echo '<div class="dtc-image-wrapper">';	
						echo '<img class="dtc-image" src="' . $default_image_url . '"/>';
					echo '</div>';
				echo '</div>';

				echo '<div class="dtc-options-column">';
					echo '<div class="dtc-options-wrapper">';

						echo '<form class="ht-configurator" autocomplete="off">';
							foreach ( $options as $options_group_name => $options_group_data ) {
								echo '<fieldset>';
									echo '<legend>' . $options_group_data['label'] . '</legend>';
									foreach ( $options_group_data['options'] as $option_name => $option_data ) {
										echo '<label>';
											echo sprintf(
												'<input type="%s" name="%s" value="%s" %s>',
												$options_group_data['type'],
												$options_group_name,
												$option_name,
												checked( $options_group_data['default'] ?? '', $option_name, false )
											);
											echo '<span>' . $option_data['label'] . '</span>';
										echo '</label>';
									}
								echo '</fieldset>';
							}
						echo '</form>';

					echo '</div>';
				echo '</div>';
			echo '</div>';

		return ob_get_clean();
	}








	public function ajax_htc_form_change() {
		error_log( "_POST\n" . print_r( $_POST, true ) . "\n" );

		$form_fields = $_POST['form_fields'] ?? [];


		$image_url = $this->get_matching_image_url( $this->get_options(), $form_fields );

		error_log( "image_url\n" . print_r( $image_url, true ) . "\n" );

		wp_send_json_success(
			array(
				'image_url' => $image_url,
			)
		);
	}
}

new HT_Configurator();