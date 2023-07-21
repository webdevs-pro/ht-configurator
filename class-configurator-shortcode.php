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
	 *
	 * Enqueues the required scripts and styles for the configurator.
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'ht-configurator-script', plugin_dir_url( __FILE__ ) . 'assets/ht-configurator.js', array( 'jquery' ), HTC_VERSION, true );
		wp_localize_script( 'ht-configurator-script', 'ht_configurator', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_style( 'ht-configurator-style', plugin_dir_url( __FILE__ ) . 'assets/ht-configurator.css', array(), HTC_VERSION );
	}














	/**
	 * Gets the URL of the image that matches the selected variation.
	 *
	 * This function first finds the best match for the selected variation from a list of variations.
	 * Then, it retrieves the image URL associated with that matched variation.
	 *
	 * @param array $selected_variation The selected variation.
	 * @param array $variations         The list of variations to compare with the selected variation.
	 *
	 * @return string The URL of the matching image or an empty string if no match is found.
	 */
	public function get_matching_variation_image_url( $selected_variation, $variations ) {
		$matched_variation = $this->find_best_match( $selected_variation, $variations );
		$image_url = '';

		$image_id = $matched_variation['variation_image'];
		if ( $image_id ) {
			$image_src = wp_get_attachment_image_src( $image_id, 'large' );
			$image_url = $image_src[0] ?? '';
		}

		return $image_url;
	}









/**
 * Finds the best match of a selected variation from a list of variations.
 *
 * This function compares the selected variation's properties with each of the variations 
 * in the provided list. The best match is the variation with the highest number of matching 
 * properties.
 *
 * @param array $selected   The selected variation.
 * @param array $variations The list of variations to compare with the selected variation.
 *
 * @return array|null The best match variation or null if no match is found.
 */
private function find_best_match( $selected, $variations ) {
	$best_match = null;
	$best_match_count = 0;

	foreach ( $variations as $variation ) {
		$match_count = 0;

		foreach ( $selected as $property => $selected_values ) {
			if ( isset( $variation[ $property ] ) ) {
				foreach ( $selected_values as $selected_value ) {
					if ( in_array( $selected_value, $variation[ $property ], true ) ) {
						$match_count++;
					}
				}
			}
		}

		if ( $match_count > $best_match_count ) {
			$best_match = $variation;
			$best_match_count = $match_count;
		}
	}

	return $best_match;
}







	/**
	 * Shortcode callback for the hot tube configurator.
	 *
	 * @return string Configurator HTML.
	 */
	public function hot_tube_configurator_shortcode() {
		ob_start();


			$option_groups = get_option( 'htc-options' )['options_group'] ?? [];
			$default_variation = get_option( 'htc-variations' )['default_variation'] ?? [];

			// $variations = get_option( 'htc-variations' )['variation'] ?? [];
			// $default_image_id = $this->get_matching_variation_image_id( $default_variation, $variations );
			// $default_image_url = $this->get_image_url_by_id( $default_image_id );



			echo '<div class="dtc-wrapper">';
			
				echo '<div class="dtc-image-column">';	
					echo '<div class="dtc-image-wrapper">';	
						echo '<img class="dtc-image" src="/wp-admin/images/loading.gif"/>';
					echo '</div>';
				echo '</div>';

				echo '<div class="dtc-options-column">';
					echo '<div class="dtc-options-wrapper">';

						echo '<div class="dtc-total-price">';

						echo '</div>';

						echo '<form class="ht-configurator" autocomplete="off">';
							foreach ( $option_groups as $options_group ) {
								echo '<fieldset>';
									echo '<legend>' . $options_group['label'] . '</legend>';
									foreach ( $options_group['options'] as $option ) {
										echo '<label>';
											echo sprintf(
												'<input type="%s" name="%s" value="%s" %s>',
												$options_group['type'],
												$options_group['id'],
												$option['id'],
												checked( 
													$default_variation[ $options_group['id'] ][0] ?? '',
													$option['id'] ?? '',
													false 
												)
											);

											$price_string = '';
											if ( ! empty( $option['option_price'] ) ) {
												$price_string = sprintf(
													'%s €%s',
													$option['option_price_prefix'] ?? '',
													$option['option_price']
												);
											}
											printf(
												'<span>%s %s</span>',
												esc_html( $option['label'] ),
												esc_html( $price_string )
											);
										echo '</label>';
									}
								echo '</fieldset>';
							}
						echo '</form>';

						echo '<div class="dtc-total-price">';

						echo '</div>';

					echo '</div>';
				echo '</div>';
			echo '</div>';

		return ob_get_clean();
	}







	/**
	 * AJAX callback for form change.
	 *
	 * Handles the AJAX request for form changes and returns the updated image URL.
	 */
	public function ajax_htc_form_change() {
		$form_fields = $_POST['form_fields'] ?? [];

		$variations = get_option( 'htc-variations' )['variation'] ?? [];

		$image_url = $this->get_matching_variation_image_url( $form_fields, $variations );

		$option_groups = get_option( 'htc-options' )['options_group'] ?? [];
		$price = 0;
		foreach ( $form_fields as $section_id => $values ) {
			foreach ( $option_groups as $options_group ) {
				if ( $section_id == $options_group['id'] ) {
					foreach ( $options_group['options'] as $option ) {
						if ( in_array( $option['id'], $values ) ) {
							$price = $price + ( $option['option_price'] ?? 0 );
						}
					}
				}
			}
		}

		wp_send_json_success(
			array(
				'image_url' => $image_url,
				'price'     => number_format( $price, 2, '.', ',') . '€',
			)
		);
	}
}

new HT_Configurator();