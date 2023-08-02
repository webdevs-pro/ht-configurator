<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



class HT_Notifications {

	public function __construct() {

	}



	/**
	 * Send admin email containing form data and configuration image.
	 *
	 * @param array  $form_fields Form field data.
	 * @param array  $settings    Notification settings.
	 * @param int    $image_id    Attachment ID of the configuration image.
	 * @return bool True if the email is sent successfully, false otherwise.
	 */
	public static function send_admin_email( $form_fields, $settings, $image_id ) {
		$to            = $settings['admin_email'];
		$to            = explode( ',', $to );
		$valid_emails  = array();
		
		foreach ( $to as $email ) {
			$sanitized_email = sanitize_email( $email );
			if ( is_email( $sanitized_email ) ) {
				$valid_emails[] = $sanitized_email;
			}
		}
		$subject = $settings['admin_email_subject'];
		$body    = self::get_admin_email_body( $form_fields, $settings, $image_id  );

		$headers = array();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		if ( isset( $settings['admin_email_from_name'] ) && $settings['admin_email_from_name'] ) {
			$from_name = $settings['admin_email_from_name'];
		} else {
			$from_name = 'WordPress';
		}

		if ( isset( $settings['admin_email_from_email'] ) && $settings['admin_email_from_email'] ) {
			$from_email = $settings['admin_email_from_email'];
		} else {
			$site_url = get_site_url();
			$domain = parse_url($site_url)['host'];
			$from_email = 'wordpress@' . $domain;
		}

		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';

		return wp_mail( $valid_emails, $subject, $body, $headers );
	}




	/**
	 * Send client email containing form data.
	 *
	 * @param array  $form_fields Form field data.
	 * @param array  $settings    Notification settings.
	 * @param int    $image_id    Attachment ID of the configuration image.
	 * @return bool True if the email is sent successfully, false otherwise.
	 */
	public static function send_client_email( $form_fields, $settings, $image_id ) {
		$to      = $form_fields['email'];
		$subject = $settings['client_email_subject'];
		$body    = self::get_client_email_body( $form_fields, $settings, $image_id  );
		
		$headers = array();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		if ( isset( $settings['client_email_from_name'] ) && $settings['client_email_from_name'] ) {
			$from_name = $settings['client_email_from_name'];
		} else {
			$from_name = 'WordPress';
		}

		if ( isset( $settings['client_email_from_email'] ) && $settings['client_email_from_email'] ) {
			$from_email = $settings['client_email_from_email'];
		} else {
			$site_url = get_site_url();
			$domain = parse_url($site_url)['host'];
			$from_email = 'wordpress@' . $domain;
		}
		
		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
		
		return wp_mail( $to, $subject, $body, $headers );
	}



	/**
	 * Get email shortcodes with their descriptions.
	 *
	 * @return array An array of shortcodes and their descriptions.
	 */
	public static function get_email_shortcodes() {
		return array(
			'image' => 'Configuration image',
			'config' => 'Configuration options',
			'price' => 'Configuration price',
			'coupon' => 'Coupon',
			'name' => 'Client name',
			'email' => 'Client Email',
			'phone' => 'Client phone',
		);
	}



	/**
	 * Generate the admin email body by replacing shortcodes with form data.
	 *
	 * @param array  $form_fields Form field data.
	 * @param array  $settings    Notification settings.
	 * @param int    $image_id    Attachment ID of the configuration image.
	 * @return string The parsed email body with shortcodes replaced.
	 */
	public static function get_admin_email_body( $form_fields, $settings, $image_id ) {
		$template = $settings['admin_email_body_template'];
		return self::parse_shortcodes( $template, $form_fields, $settings, $image_id );
	}



	/**
	 * Generate the client email body by replacing shortcodes with form data.
	 *
	 * @param array  $form_fields Form field data.
	 * @param array  $settings    Notification settings.
	 * @param int    $image_id    Attachment ID of the configuration image.
	 * @return string The parsed email body with shortcodes replaced.
	 */
	public static function get_client_email_body( $form_fields, $settings, $image_id ) {
		$template = $settings['client_email_body_template'];
		return self::parse_shortcodes( $template, $form_fields, $settings, $image_id );
	}



	/**
	 * Parse shortcodes in the given template and replace them with form data.
	 *
	 * @param string $template    The email template containing shortcodes.
	 * @param array  $form_fields Form field data.
	 * @param array  $settings    Notification settings.
	 * @param int    $image_id    Attachment ID of the configuration image.
	 * @return string The parsed template with shortcodes replaced by form data.
	 */
	private static function parse_shortcodes( $template, $form_fields, $settings, $image_id ) {
		$pattern  = '/\[(\w+)\]/';

		// Perform the replacement using preg_replace_callback() with an anonymous function.
		$parsed_string = preg_replace_callback( $pattern, function ( $matches ) use ( $form_fields, $settings, $image_id ) {
			$shortcode   = $matches[1];
			$replacement = '';

			switch ( $shortcode ) {
				case 'image':
					if ( $image_id ) {
						$image_attributes = wp_get_attachment_image_src( $image_id, 'large' );
						if ( isset( $image_attributes[0] ) ) {
							$replacement = '<img src="' . esc_url( $image_attributes[0] ) . '" alt="Configuration Image" style="width: 30%;"><br>';
						}
					}
					break;


				case 'config':
					$option_groups = get_option( 'htc-options' )['options_group'] ?? [];
					foreach ( $option_groups as $group ) {
						if ( ! isset( $form_fields[ $group['id'] ] ) ) {
							continue;
						}
			
						$replacement .= '<b>' . $group['label'] . '</b>: ';
			
						$selected_option_labels = [];
						foreach ( $form_fields[ $group['id'] ] as $selected_option_id ) {
							foreach ( $group['options'] as $option ) {
								if ( $option['id'] == $selected_option_id ) {
									$selected_option_labels[] = $option['label'];
								}
							}
						}
			
						$replacement .= implode( ',', $selected_option_labels ) . '<br>';
					}
					break;
				

				case 'name':
					$replacement = $form_fields['name'][0];
					break;
				

				case 'email':
					$replacement = $form_fields['email'][0];
					break;
				

				case 'phone':
					$replacement = $form_fields['phone'][0];
					break;
				

				case 'price':
					$replacement = $form_fields['price'][0];
					break;
				

				case 'coupon':
					$replacement = $form_fields['coupon_code'][0];
					break;
	
			}


			return $replacement;
		}, $template );

		return $parsed_string;
	}
}