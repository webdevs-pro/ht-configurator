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
	 * Get default image URL by image ID.
	 *
	 * @param int $image_id The image ID.
	 *
	 * @return string Default image URL.
	 */
	private function get_image_url_by_id( $image_id ) {
		$image_url = '';

		if ( $image_id ) {
			$image_src = wp_get_attachment_image_src( $image_id, 'large' );
			$image_url = $image_src[0] ?? '';
		}

		return $image_url;
	}







	/**
	 * Get the image ID that matches the selected variation.
	 *
	 * @param array $selected_variation The selected variation data.
	 * @param array $variations         Array of available variations.
	 *
	 * @return int|false The image ID or false if no match is found.
	 */
	public function get_matching_variation_image_id( $selected_variation, $variations ) {
		foreach ( $variations as $variation_index => $variation ) {
			$selected_variation_keys = array_keys( $selected_variation );
			$variation_keys = array_keys( $variation );

			$missing_keys = array_diff( $selected_variation_keys, $variation_keys );
			if ( empty( $missing_keys ) ) {

				$match = $this->compare_variation_values( $selected_variation, $variation );

				if ( $match ) {
					return $variations[ $variation_index ]['variation_image'];
				}
			} else {
				continue;
			}
		}
	}







	/**
	 * Compare variation values to check if they match the selected variation.
	 *
	 * @param array $selected_variation The selected variation data.
	 * @param array $variation          The variation data to compare against.
	 *
	 * @return bool True if the variations match, false otherwise.
	 */
	private function compare_variation_values( $selected_variation, $variation ) {
		$matched_values = 0;

		foreach ( $selected_variation as $key => $values ) {
			foreach ( $values as $value ) {
				if ( in_array( $value, $variation[ $key ] ) ) {
					$matched_values++;
					break;
				}
			}
		}

		if ( $matched_values == count( $selected_variation ) ) {
			return true;
		} else {
			return false;
		}
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
												checked( $default_variation[ $options_group['id'] ][0], $option['id'], false )
											);

											$price_string = '';
											if ( ! empty( $option['option_price'] ) ) {
												$price_string = sprintf(
													'%s €%s',
													$option['option_price_prefix'],
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
		error_log( "_POST\n" . print_r( $_POST, true ) . "\n" );

		$form_fields = $_POST['form_fields'] ?? [];
		$variations = get_option( 'htc-variations' )['variation'] ?? [];
		$image_id = $this->get_matching_variation_image_id( $form_fields, $variations );
		$image_url = $this->get_image_url_by_id( $image_id );

		wp_send_json_success(
			array(
				'image_url' => $image_url,
			)
		);
	}
}

new HT_Configurator();