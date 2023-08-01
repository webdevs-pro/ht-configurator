<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class HT_Configurator_Ajax {
   /**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_htc_form_change',  array( $this, 'ajax_htc_form_change' ) );
      add_action( 'wp_ajax_nopriv_htc_form_change',  array( $this, 'ajax_htc_form_change' ) );
		add_action( 'wp_ajax_htc_form_submit',  array( $this, 'ajax_htc_form_submit' ) );
      add_action( 'wp_ajax_nopriv_htc_form_submit',  array( $this, 'ajax_htc_form_submit' ) );
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
	 * AJAX callback for form change.
	 *
	 * Handles the AJAX request for form changes and returns the updated image URL.
	 */
	public function ajax_htc_form_change() {
		$form_fields   = $_POST['form_fields'] ?? [];
		$variations    = get_option( 'htc-variations' )['variation'] ?? [];
		$coupons       = get_option( 'htc-coupons' )['coupon'] ?? [];
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

		// maybe apply coupon
		if ( isset( $form_fields['coupon_code'][0] ) ) {
			foreach ( $coupons as $coupon ) {
				if ( $coupon['coupon_code'] == $form_fields['coupon_code'][0] ) {
					if ( $coupon['coupon_type'] == 'flat' ) {
						$price = $price - $coupon['coupon_amount'];
					}
					else if ( $coupon['coupon_type'] == 'percentage' ) {
						$discount = $price / 100 * $coupon['coupon_amount'];
						$price = $price - $discount;
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

		error_log( "form_fields\n" . print_r( $form_fields, true ) . "\n" );

		// form validation
		if ( 
			! $form_fields['name'][0] ||
			! $form_fields['email'][0] ||
			! $form_fields['phone'][0] ||
			! isset( $form_fields['aceptance'] )
		) {



			
			wp_send_json_error( array( 
				'message' => 'Bitte füllen Sie alle erforderlichen Formularfelder aus!',
			) );
		}


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


		if ( $send_email ) {
			$success_message = $settings['thankyou_text'] ?? 'Submitted!';
			wp_send_json_success(
				array(
					'message' => $success_message
				)
			);
		}

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
		

		return wp_mail( $valid_emails, $subject, $body, $headers );

	}
}
new HT_Configurator_Ajax();