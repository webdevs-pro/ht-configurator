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
		add_action( 'wp_ajax_htc_form_submit',  array( $this, 'ajax_htc_form_submit' ) );
      add_action( 'wp_ajax_nopriv_htc_form_submit',  array( $this, 'ajax_htc_form_submit' ) );
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
		$image_url         = '';

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
		$best_match_count = 0;
		$best_match       = null;

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

			$settings          = get_option( 'ht-configurator' );
			$option_groups     = get_option( 'htc-options' )['options_group'] ?? [];
			$default_variation = get_option( 'htc-variations' )['default_variation'] ?? [];

			echo '<div class="dtc-wrapper">';
			
				echo '<div class="dtc-image-column">';	
					echo '<div class="dtc-image-wrapper">';	
						echo '<img class="dtc-image" src=""/>';
					echo '</div>';
				echo '</div>';

				echo '<div class="dtc-options-column">';
					echo '<div class="dtc-options-wrapper">';

						echo '<form class="ht-configurator" autocomplete="off">';
							foreach ( $option_groups as $options_group ) {
								echo '<fieldset>';
									echo '<legend>' . $options_group['label'] . '</legend>';

									if ( isset( $options_group['description'] ) && $options_group['description'] ) {
										echo '<p class="dtc-fieldset-description">' . $options_group['description'] . '</p>';
									}

									foreach ( $options_group['options'] as $option ) {
										echo sprintf(
											'<input id="%s" type="%s" name="%s" value="%s" %s>',
											$options_group['id'] . '-' . $option['id'],
											$options_group['type'],
											$options_group['id'],
											$option['id'],
											checked( 
												$default_variation[ $options_group['id'] ][0] ?? '',
												$option['id'] ?? '',
												false 
											)
										);
										echo '<label for="' . $options_group['id'] . '-' . $option['id'] . '">';
											// label
											echo '<div class="option-name">' . esc_html( $option['label'] ) . '</div>';

											// description
											if ( isset( $option['description'] ) && $option['description'] ) {
												echo '<div class="option-description">' . esc_html( $option['description'] ) . '</div>';
											}

											// price
											if ( ! empty( $option['option_price'] ) ) {
												$price_string = sprintf(
													'%s €%s',
													$option['option_price_prefix'] ?? '',
													number_format( $option['option_price'], 2, ',', '.' )
												);
												echo '<div class="option-price">' . esc_html( $price_string ) . '</div>';
											}

										echo '</label>';
									}

									if ( isset( $options_group['section_popup_text'] ) && $options_group['section_popup_text'] ) {
										echo '<a href="#" class="dtc-fieldset-popup-open">Weitere Infos</a>';
										echo '<div class="dtc-fieldset-popup">';
											echo '<div class="dtc-fieldset-popup-wrapper">';
												echo '<div class="dtc-fieldset-popup-heading">' . ( $options_group['section_popup_heading'] ?? '' ) . '</div>';
												echo '<div class="dtc-fieldset-popup-text"><p>' . nl2br( $options_group['section_popup_text'] ) . '</p></div>';
												echo '<button class="dtc-fieldset-popup-ok">Ok</button>';
											echo '</div>';
										echo '</div>';
									}

								echo '</fieldset>';
							}

							if ( current_user_can( 'manage_options' ) ) {
								echo '<fieldset class="htc-no-fieldset-styling">';
									echo '<legend>Admin only options</legend>';
									echo '<label class="htc-simple-checkbox">';
										echo '<input id="submit-to-woo-checkbox" type="checkbox" name="submit_to_woo" value="1">';
										echo '<span>Submit to WooCommerce</span>';
										echo '<p>* as draft</p>';
									echo '</label>';
								echo '</fieldset>';
							}
						echo '</form>';

						
						echo '<div class="dtc-options-footer">';
							echo '<div class="dtc-price-wrapper">';
								echo '<div class="dtc-total-text">Gesamtbetrag</div>';
								echo '<div class="dtc-total-price"></div>';
							echo '</div>';


							echo '<div class="dtc-submit-wrapper">';
								echo '<button>' . ( $settings['submit_button_text'] ?? 'Submit' ) . '</div>'; 
							echo '</div>';
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
		$form_fields   = $_POST['form_fields'] ?? [];
		$variations    = get_option( 'htc-variations' )['variation'] ?? [];
		$image_url     = $this->get_matching_variation_image_url( $form_fields, $variations );
		$option_groups = get_option( 'htc-options' )['options_group'] ?? [];
		$price         = 0;

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
				'price'     => number_format( $price, 2, ',', '.') . '€',
			)
		);
	}







	/**
	 * AJAX callback for form submit.
	 *
	 * Handles the AJAX request for form submit and returns the message.
	 */
	public function ajax_htc_form_submit() {
		$form_fields = $_POST['form_fields'] ?? [];
		$settings    = get_option( 'ht-configurator' );

		if ( 
			isset( $form_fields['submit_to_woo'] ) && 
			$settings['woo_endpoint']
		) {
			$product_publish = $this->submit_to_woocommerce( $form_fields, $settings );
		}

		if ( 
			$settings['request_email'] &&
			( $settings['request_email_live_mode'] ||
			   current_user_can( 'manage_options' )
			)
		) {
			$send_email = $this->send_request_email( $form_fields, $settings );
		}




		wp_send_json_success(
			array(
				'message' => 'Submitted!'
			)
		);
	}







	/**
	 * Submit form data to remote WooCommerce.
	 *
	 * Handles the AJAX request for form submit and returns the message.
	 */
	private function submit_to_woocommerce( $form_fields, $settings ) {

	}







	/**
	 * Submit form data to remote WooCommerce.
	 *
	 * Handles the AJAX request for form submit and returns the message.
	 */
	private function send_request_email( $form_fields, $settings ) {
		// to
		$to            = $settings['request_email'];
		$to            = explode( ',', $to );
		$valid_emails  = array();
		
		foreach ( $to as $email ) {
			 $sanitized_email = sanitize_email( $email );
			 if ( is_email( $sanitized_email ) ) {
				  $valid_emails[] = $sanitized_email;
			 } else {
				  error_log( "Invalid email address: $email" );
			 }
		}
		
		// subject
		$subject = $settings['request_email_subject'];

		// body
		$option_groups = get_option( 'htc-options' )['options_group'] ?? [];
		$body = '';
		foreach ( $option_groups as $group ) {
			if ( ! isset( $form_fields[ $group['id'] ] ) ) {
				continue;
			}

			$body .= '<b>' . $group['label'] . '</b>: ';

			$selected_option_labels = [];
			foreach ( $form_fields[ $group['id'] ] as $selected_option_id ) {
				foreach ( $group['options'] as $option ) {
					if ( $option['id'] == $selected_option_id ) {
						$selected_option_labels[] = $option['label'];
					}
				}
			}

			$body .= implode( ',', $selected_option_labels ) . '<br>';

		}


		// headers
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		
		if ( wp_mail( $valid_emails, $subject, $body, $headers ) ) {
			 error_log( 'Email successfully sent' );
		} else {
			 error_log( 'An unexpected error occurred' );
		}
	}
}

new HT_Configurator();