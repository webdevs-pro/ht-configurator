<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



class HT_Notifications {

	public function __construct() {

	}

	public static function send_admin_email( $form_fields, $settings, $image_id ) {
		// to
		$to            = $settings['admin_email'];
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
		$subject = $settings['admin_email_subject'];

		// body
		$body = self::get_admin_email_body( $form_fields, $settings, $image_id  );

		// headers
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		
		return wp_mail( $valid_emails, $subject, $body, $headers );
	}



	public static function get_email_shortcodes() {
		return array(
			'image' => 'Configuration image',
			'config' => 'Configuration options',
			'name' => 'Client name',
			'email' => 'Client Email',
			'phone' => 'Client phone',
		);
	}



	public static function get_admin_email_body( $form_fields, $settings, $image_id ) {
		$template = $settings['admin_email_body_template'];
		return self::parse_shortcodes( $template, $form_fields, $settings, $image_id );
	}


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
					$replacement = $form_fields['name'];
					break;
				

				case 'email':
					$replacement = $form_fields['email'];
					break;
				

				case 'phone':
					$replacement = $form_fields['phone'];
					break;
	
			}


			return $replacement;
		}, $template );

		return $parsed_string;
	}
}