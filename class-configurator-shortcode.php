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
	 * Shortcode callback for the hot tube configurator.
	 *
	 * @return string Configurator HTML.
	 */
	public function hot_tube_configurator_shortcode() {
		ob_start();

			$settings          = get_option( 'ht-configurator' );
			$option_groups     = get_option( 'htc-options' )['options_group'] ?? [];
			$default_variation = get_option( 'htc-variations' )['default_variation'] ?? [];

			echo '<div class="htc-wrapper">';
			
				echo '<div class="htc-left-column">';	
					echo '<div class="htc-image-wrapper">';	
						echo '<img class="htc-image" src=""/>';
					echo '</div>';
				echo '</div>';

				echo '<div class="htc-right-column">';
					echo '<div class="htc-right-column-wrapper">';
						echo '<form class="ht-configurator" autocomplete="off">';
							echo '<div class="htc-options-wrapper">';
								foreach ( $option_groups as $options_group ) {
									echo '<fieldset>';
										echo '<legend>' . $options_group['label'] . '</legend>';

										if ( isset( $options_group['description'] ) && $options_group['description'] ) {
											echo '<p class="htc-fieldset-description">' . $options_group['description'] . '</p>';
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
											echo '<a href="#" class="htc-fieldset-popup-open">Weitere Infos</a>';
											echo '<div class="htc-fieldset-popup">';
												echo '<div class="htc-fieldset-popup-wrapper">';
													echo '<div class="htc-fieldset-popup-heading">' . ( $options_group['section_popup_heading'] ?? '' ) . '</div>';
													echo '<div class="htc-fieldset-popup-text"><p>' . nl2br( $options_group['section_popup_text'] ) . '</p></div>';
													echo '<button class="htc-fieldset-popup-ok">Ok</button>';
												echo '</div>';
											echo '</div>';
										}

									echo '</fieldset>';
								}



								
								
								$total_label = $settings['total_label'] ?? 'Total';
								$coupon_section_heading = $settings['coupon_section_heading'] ?? 'Have a coupon code?';
								$apply_button_label = $settings['coupon_section_apply_button_label'] ?? 'Apply';
								$email_section_heading = $settings['email_section_heading'] ?? 'Personal information';
								?>
								<div class="htc-options-price-section">
									<!-- total price section -->
									<div class="htc-price-wrapper">
										<div class="htc-section-heading"><?php echo $total_label; ?></div>
										<div class="htc-total-price"></div>
									</div>
								
									<!-- coupon section -->
									<div class="htc-coupon-wrapper">
										<div class="htc-section-heading"><?php echo $coupon_section_heading; ?></div>
										<div class="htc-coupon-field-wrapper">
											<input type="text" name="coupon_code">
											<button class="htc-apply-coupon"><?php echo $apply_button_label; ?></button>
										</div>
										<div class="htc-coupon-message"></div>
									</div>
								</div>

							</div> <!-- options wrapper -->
								
							<div class="htc-submit-wrapper">

								<div class="htc-options-submit-section">

									<div class="htc-section-heading"><?php echo $email_section_heading; ?></div>

									<label class="htc-name-field">
										<span class="htc-required-mark">*</span>
										<?php echo $settings['name_field_label'] ?? 'Name'; ?>
										<input type="text" name="name" autocomplete="on">
									</label>

									<label class="htc-email-field">
										<span class="htc-required-mark">*</span>
										<?php echo $settings['email_field_label'] ?? 'Email'; ?>
										<input type="email" name="email" autocomplete="on">
									</label>

									<label class="htc-phone-field">
										<span class="htc-required-mark">*</span>
										<?php echo $settings['phone_field_label'] ?? 'Phone'; ?>
										<input type="text" name="phone" autocomplete="on">
									</label>

									<label class="htc-simple-checkbox htc-aceptance-field">
										<input type="checkbox" name="aceptance" value="1" data-required="true">
										<span>* <?php echo $settings['acceptance_text'] ?? 'Acceptance text'; ?></span>
									</label>

									<input type="hidden" name="price">

									<p><?php echo '<a target="_blank" class="htc-tac-page-link" href="' . get_permalink( $settings['tac_page_id'] ) . '">' . get_the_title( $settings['tac_page_id'] ) . '</a>'; ?></p>

									<button class="htc-submit"><?= $settings['submit_button_text'] ?? 'Submit'; ?></button>

									<div class="htc-form-success-message"></div>
									<div class="htc-form-error-message"></div>


									<?php if ( current_user_can( 'manage_options' ) ) : ?>
										<!-- <div class="htc-section-heading">Admin only options</div> -->
										<label class="htc-simple-checkbox">
											<input type="checkbox" name="submit_to_woo" value="1">
											<span></span>
											<span>Create new product on RMSPA</span>
										</label>
									<?php endif; ?>
									

							
								
								</div>

							</div>
							<?php

						echo '</form>';

							

					echo '</div>'; // htc-options-wrapper
				echo '</div>'; // htc-right-column
			echo '</div>'; // htc-wrapper

		return ob_get_clean();
	}
}

new HT_Configurator();