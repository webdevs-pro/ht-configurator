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

		return array(
			'image_id' => $image_id,
			'image_url' => $image_url,
		);
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

			if ( $match_count >= $best_match_count ) {
				$best_match       = $variation;
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
		$image         = $this->get_matching_variation_image_url( $form_fields, $variations );
		$image_id      = $image['image_id'];
		$image_url     = $image['image_url'];
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
		$coupon_message = array(
			'status' => '',
			'message' => ''
		);
		if ( isset( $form_fields['coupon_code'][0] ) && $form_fields['coupon_code'][0] ) {
			$coupon_message['message'] = 'Falscher Gutscheincode!';
			$coupon_message['status'] = 'error';
			foreach ( $coupons as $coupon ) {
				if ( $coupon['coupon_code'] == $form_fields['coupon_code'][0] ) {
					if ( $coupon['coupon_type'] == 'flat' ) {
						$price = $price - $coupon['coupon_amount'];
					}
					else if ( $coupon['coupon_type'] == 'percentage' ) {
						$discount = $price / 100 * $coupon['coupon_amount'];
						$price = $price - $discount;
					}

					$coupon_message['status'] = 'ok';
					if ( isset( $coupon['coupon_applied_message'] ) && $coupon['coupon_applied_message'] ) {
						$coupon_message['message'] = $coupon['coupon_applied_message'];
					} else {
						$coupon_message['message'] = 'Gutschein angewendet!';
					}
				}
			}
		}

		wp_send_json_success(
			array(
				'image_url'      => $image_url,
				'image_id'       => $image_id,
				'price'          => number_format( $price, 2, ',', '.') . '€',
				'coupon_message' => $coupon_message,
			)
		);
	}







	/**
	 * AJAX callback for form submit.
	 *
	 * Handles the AJAX request for form submit and returns the message.
	 */
	public function ajax_htc_form_submit() {

		// error_log( "_POST\n" . print_r( $_POST, true ) . "\n" );

		$form_fields = $_POST['form_fields'] ?? [];
		$image_id = $_POST['image_id'] ?? [];
		$settings    = get_option( 'ht-configurator' );


		// form validation
		if ( 
			! $form_fields['name'][0] ||
			! $form_fields['email'][0] ||
			! $form_fields['phone'][0] ||
			! isset( $form_fields['aceptance'] )
		) {
			wp_send_json_error( array( 
				'message' => 'Bitte füllen Sie alle erforderlichen Formularfelder aus',
			) );
		}

		if ( ! is_email( $form_fields['email'][0] ) ) {
			wp_send_json_error( array( 
				'message' => 'Bitte geben Sie die richtige E-Mail-Adresse ein',
			) );
		}


		if ( 
			isset( $form_fields['submit_to_woo'] ) && 
			$settings['woo_endpoint']
		) {
			$product_publish = $this->submit_to_woocommerce( $form_fields, $settings );
		}

		if ( 
			$settings['admin_email'] &&
			( $settings['request_email_live_mode'] ||
			   current_user_can( 'manage_options' )
			)
		) {
			$send_admin_email = HT_Notifications::send_admin_email( $form_fields, $settings, $image_id );
			$send_client_email = HT_Notifications::send_client_email( $form_fields, $settings, $image_id );
		}


		if ( $send_admin_email && $send_client_email ) {
			$success_message = $settings['thankyou_text'] ?? 'Submitted!';
			wp_send_json_success(
				array(
					'message' => $success_message
				)
			);
		} else {
			wp_send_json_error( array( 
				'message' => 'Email not sent',
			) );
		}

	}







	/**
	 * Submit form data to remote WooCommerce.
	 *
	 * Handles the AJAX request for form submit and returns the message.
	 */
	private function submit_to_woocommerce( $form_fields, $settings ) {

	}

}
new HT_Configurator_Ajax();