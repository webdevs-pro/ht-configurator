<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



class HT_Metabox {

	public function __construct() {
		add_filter( 'mb_settings_pages', array( $this, 'register_settings_pages' ) );
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_settings_meta_boxes' ), 10);
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_options_meta_boxes' ), 10);
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_variations_meta_boxes' ), 10);
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_coupons_meta_boxes' ), 10);
		add_action( 'admin_print_styles', array( $this, 'print_scripts_and_styles' ) );
		add_filter( 'rwmb_coupon_field_meta', array( $this, 'add_empty_row_to_clonable_group' ), 1, 3 );

	}













	public function print_scripts_and_styles() {
		global $pagenow;

		if ( $pagenow == 'admin.php' && $_GET['page'] == 'ht-configurator' ) {
			?>
			<style>
				/* always show sidebar */
				#htc-options-side {
					display: block !important;
				}
			</style>
			<?php
		}
	}










	public function add_empty_row_to_clonable_group( $value, $field, $object_id ) {
		if ( ! is_admin() ) {
				return $value;
		}

		$default_group_item_fields = array();
		$group_fields = array_column( $field['fields'], 'id' );
		foreach ( $group_fields as $field_id ) {
			$default_group_item_fields[$field_id] = '';
		}

		if ( is_array( $value) && count( $value ) >= 1 && ! empty( $value[0] ) ) {
			array_unshift( $value, $default_group_item_fields );
		}

		?>
		<style>
			.hidden-first-item-clonable-group .rwmb-group-clone:first-child {
				display: none;
			}
		</style>
		<?php

		return $value;
	}











	private function  get_pages_as_array( $parent_id = 0, $level = 0 ) {
		$args = array(
			'parent' => $parent_id,
			'sort_column' => 'menu_order',
		);

		$pages = get_pages( $args );
		$page_array = array();

		if ( $pages ) {
			foreach ( $pages as $page ) {
				// Adding dashes for subpages
				$prefix = str_repeat( '— ', $level );
				$page_array[$page->ID] = $prefix . $page->post_title;
				
				// Retrieve subpages if exist
				$subpages = $this->get_pages_as_array( $page->ID, $level + 1 );
				if ( ! empty( $subpages ) ) {
					$page_array += $subpages;
				}
			}
		}

		return $page_array;
	}












	public function register_settings_pages( $settings_pages ) {
		$settings_pages[] = [
			'menu_title'    => 'Configurator',
			'id'            => 'ht-configurator',
			'position'      => 4,
			'columns'       => 2,
			'submenu_title' => 'Settings',
			'page_title'    => 'Configurator settings',
			'icon_url'      => 'dashicons-admin-generic',
			'tabs'          => [
				'general'         => 'General',
				'email'           => 'Email',
				'woo-integration' => 'WooCommerce integration',
				'backup'          => 'Backup',
			],
		];

		$settings_pages[] = [
			'menu_title' => 'Options',
			'id'         => 'htc-options',
			'parent'     => 'ht-configurator',
			'columns'    => 1,
			'icon_url'   => 'dashicons-admin-generic',
			'tabs'       => [
				'options' => 'Options',
				'backup'  => 'Backup',
			],
		];
		$settings_pages[] = [
			'menu_title'    => 'Variations',
			'id'            => 'htc-variations',
			'parent'        => 'ht-configurator',
			'columns'       => 1,
			'icon_url'      => 'dashicons-admin-generic',
			'tabs'          => [
				'variations' => 'Variations',
				'backup'     => 'Backup',
			],
		];
		$settings_pages[] = [
			'menu_title'    => 'Coupons',
			'id'            => 'htc-coupons',
			'parent'        => 'ht-configurator',
			'columns'       => 1,
			'icon_url'      => 'dashicons-admin-generic',
			'tabs'          => [
				'coupons' => 'Coupons',
				'backup'     => 'Backup',
			],
		];

		return $settings_pages;
	}













	public function add_settings_meta_boxes( $meta_boxes ) {

		ob_start();
		?>

			<div class="alert alert-warning">This is a custom HTML content</div>

		<?php
		$help_html = ob_get_clean();

		$meta_boxes['htc-settings-support'] = [
			'title'          => 'Support',
			'id'             => 'htc-options-side',
			'settings_pages' => ['ht-configurator'],
			'context'        => 'side',
			'tab'            => 'general',
			'fields'         => [
				[
					'type' => 'custom_html',
					'std'  => $help_html,
				],
			],
		];
		

		$meta_boxes['htc-settings-general'] = [
			'title'          => 'Settings',
			'id'             => 'htc-settings-general',
			'settings_pages' => ['ht-configurator'],
			'tab'            => 'general',
			'fields'         => [
				[
					'name' => 'Coupon section heading',
					'id'   => 'coupon_section_heading',
					'type' => 'text',
				],
				[
					'name' => '"Apply Coupon" Button Label',
					'id'   => 'coupon_section_apply_button_label',
					'type' => 'text',
				],
				[
					'name' => 'Total label',
					'id'   => 'total_label',
					'type' => 'text',
				],
				[
					'type' => 'divider',
				],
				[
					'name' => 'Email section heading',
					'id'   => 'email_section_heading',
					'type' => 'text',
				],
				[
					'name' => 'Name field label',
					'id'   => 'name_field_label',
					'type' => 'text',
				],
				[
					'name' => 'Email field label',
					'id'   => 'email_field_label',
					'type' => 'text',
				],
				[
					'name' => 'Phone field label',
					'id'   => 'phone_field_label',
					'type' => 'text',
				],
				[
					'name' => 'Acceptance checkbox text',
					'id'   => 'acceptance_text',
					'type' => 'text',
				],
				[
					'name'    => 'Terms and condition page',
					'id'      => 'tac_page_id',
					'type'    => 'select',
					'placeholder' => 'Select page',
					'options' => $this->get_pages_as_array(),
				],
				[
					'name' => 'Submit button label',
					'id'   => 'submit_button_text',
					'type' => 'text',
				],
				[
					'name' => 'Message on form submit',
					'id'   => 'thankyou_text',
					'type' => 'text',
				],
			],
		];


		ob_start();
			echo '<p>List of available shortcodes to use in Email</p>';
			foreach ( HT_Notifications::get_email_shortcodes() as $shortcode => $description ) {
				echo '[' . $shortcode . '] - ' . $description . '<br>';
			}
		$email_help_html = ob_get_clean();

		$meta_boxes['htc-settings-email-help'] = [
			'title'          => 'Help',
			'id'             => 'htc-email-help-side',
			'settings_pages' => ['ht-configurator'],
			'context'        => 'side',
			'tab'            => 'email',
			'fields'         => [
				[
					'type' => 'custom_html',
					'std'  => $email_help_html,
				],
			],
		];

		$meta_boxes['htc-settings-email'] = [
			'title'          => 'Email',
			'id'             => 'htc-settings-email',
			'settings_pages' => ['ht-configurator'],
			'tab'            => 'email',
			'fields'         => [
				[
					'type' => 'heading',
					'name' => 'Admin Email',
				],
				[
					'name' => 'Live mode',
					'id'   => 'request_email_live_mode',
					'type' => 'checkbox',
					'desc' => 'If the checkbox is unchecked, only an administrator can send an email request.',
				],
				[
					'name' => 'Admin Email adress',
					'id'   => 'admin_email',
					'type' => 'text',
					'desc' => 'List of email addresses separated by commas',
				],
				[
					'name' => 'Admin Email subject',
					'id'   => 'admin_email_subject',
					'type' => 'text',
				],
				[
					'name' => 'Admin Email from name',
					'id'   => 'admin_email_from_name',
					'type' => 'text',
				],
				[
					'name' => 'Admin Email from email',
					'id'   => 'admin_email_from_email',
					'type' => 'text',
				],
				[
					'name'    => 'Admin Email body',
					'id'      => 'admin_email_body_template',
					'type'    => 'wysiwyg',
					'raw'     => false,
					'options' => [
						'textarea_rows' => 10,
						'teeny'         => false,
					],
				],
				[
					'type' => 'heading',
					'name' => 'Client Email',
				],
				[
					'name' => 'Client Email subject',
					'id'   => 'client_email_subject',
					'type' => 'text',
				],
				[
					'name' => 'Client Email from name',
					'id'   => 'client_email_from_name',
					'type' => 'text',
				],
				[
					'name' => 'Client Email from email',
					'id'   => 'client_email_from_email',
					'type' => 'text',
				],
				[
					'name'    => 'Client Email body',
					'id'      => 'client_email_body_template',
					'type'    => 'wysiwyg',
					'raw'     => false,
					'options' => [
						'textarea_rows' => 10,
						'teeny'         => false,
					],
				],
			],
		];


		$meta_boxes['htc-settings-woo'] = [
			'title'          => 'Settings',
			'id'             => 'htc-settings-woo',
			'settings_pages' => ['ht-configurator'],
			'tab'            => 'woo-integration',
			'fields'         => [
				[
					'name' => 'WooCommerce endpoint URL',
					'id'   => 'woo_endpoint',
					'type' => 'text',
				],
				[
					'name' => 'Secret',
					'id'   => 'woo_secret',
					'type' => 'text',
					'desc' => 'This field ONLY accepts these chars: a-z, 0-9, _ and - and the value remains the same for the sender and receiver.',
					'attributes' => [
						'oninput' => 'this.value = this.value.replace(/[^a-z0-9_-]/, \'\');',
					],
				],
			],
		];


		$meta_boxes['htc-settings-backup'] = [
			'title'          => 'Backup & restore options',
			'id'             => 'htc-settings-backup',
			'settings_pages' => ['ht-configurator'],
			'tab'            => 'backup',
			'fields'         => [
				[
					'name' => 'Backup & restore options',
					'type' => 'backup',
				],
			],
		];

		return $meta_boxes;
	}













	public function add_options_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'  => 'Options',
			'id'     => 'htc-options',
			'settings_pages' => ['htc-options'],
			'tab'            => 'options',
			'fields' => [
				[
					'name'          => '',
					'id'            => 'options_group',
					'type'          => 'group',
					'collapsible'   => true,
					'default_state' => 'collapsed',
					'group_title'   => 'Section: {label}',
					'clone'         => true,
					'sort_clone'    => true,
					'add_button'    => 'Add section',
					'fields'        => [
						[
							'name' => 'Section label',
							'id'   => 'label',
							'type' => 'text',
							'required' => true,
						],
						[
							'name' => 'Description',
							'id'   => 'description',
							'type' => 'text',
						],
						[
							'name'    => 'Type',
							'id'      => 'type',
							'type'    => 'select',
							'options' => [
								'radio'    => 'Radio button (single option)',
								'checkbox' => 'Checkbox (multiple options)',
							]
						],
						[
							'name' => 'Section ID',
							'id'   => 'id',
							'type' => 'text',
							'required' => true,
							'desc' => 'This field ONLY accepts these chars: a-z, 0-9, _ and - and the value must be unique. <br><b style="color: red;">Changing the section ID will break existing variations. Please don\'t touch it!</b>',
							'attributes' => [
								'oninput' => 'this.value = this.value.replace(/[^a-z0-9_-]/, \'\');',
								'onclick' => 'if (this.dataset.confirmed !== "true") { var confirmed = confirm("Changing the option ID will break existing variations. Please don\'t touch it!"); if (confirmed) { this.dataset.confirmed = "true"; this.focus(); } else { this.blur(); } } else { this.focus(); }',
							],
						],
						[
							'name'          => 'Options',
							'id'            => 'options',
							'type'          => 'group',
							'collapsible'   => true,
							'default_state' => 'collapsed',
							'group_title'   => '{label} | {option_price}',
							'clone'         => true,
							'sort_clone'    => true,
							'add_button'    => 'Add option',
							'fields'        => [
								[
									'name' => 'Option Label',
									'id'   => 'label',
									'type' => 'text',
									'required' => true,  
								],
								[
									'name' => 'Option Description',
									'id'   => 'description',
									'type' => 'text',
								],
								[
									'name' => 'Option ID',
									'id'   => 'id',
									'type' => 'text',
									'required' => true,
									'desc' => 'This field ONLY accepts these chars: a-z, 0-9, _ and - and the value must be unique within options group. <br><b style="color: red;">Changing the option ID will break existing variations. Please don\'t touch it!</b>',
									'attributes' => [
										'oninput' => 'this.value = this.value.replace(/[^a-z0-9_-]/, \'\');',
										'onclick' => 'if (this.dataset.confirmed !== "true") { var confirmed = confirm("Changing the option ID will break existing variations. Please don\'t touch it!"); if (confirmed) { this.dataset.confirmed = "true"; this.focus(); } else { this.blur(); } } else { this.focus(); }',
									],
								],
								[
									'name' => 'Price prefix',
									'id'   => 'option_price_prefix',
									'type' => 'text',
								],
								[
									'name' => 'Option price',
									'id'   => 'option_price',
									'type' => 'number',
									'step' => 0.01,
								],
							],
						],
						[
							'name' => 'Popup heading',
							'id'   => 'section_popup_heading',
							'type' => 'text',
						],
						[
							'name' => 'Popup text',
							'id'   => 'section_popup_text',
							'type' => 'textarea',
							'rows' => 8,
						],
					],
				],
			],
		];

		$meta_boxes[] = [
			'title'          => 'Backup & restore options',
			'id'             => 'htc-options-backup',
			'settings_pages' => ['htc-options'],
			'tab'            => 'backup',
			'fields'         => [
				[
					'name' => 'Backup & restore options',
					'type' => 'backup',
				],
			],
		];

		return $meta_boxes;
	}











	public function add_variations_meta_boxes( $meta_boxes ) {
		$meta_boxes['htc_variations'] = [
			'title'          => 'Variants Configurator',
			'id'             => 'htc-variations',
			'settings_pages' => ['htc-variations'],
			'tab'            => 'variations',
			'fields'         => [
				[
					'type' => 'heading',
					'name' => 'Default variation',
				],
				'default_variant' => [
					'name'         => '',
					'id'           => 'default_variation',
					'type'         => 'group',
					'fields'       => [],
				],
				[
					'type' => 'heading',
					'name' => 'Variations',
				],
				'variants' => [
					'name'          => '',
					'id'            => 'variation',
					'type'          => 'group',
					'collapsible'   => true,
					'default_state' => 'collapsed',
					'group_title'   => '{variation_internal_description}',
					'clone'         => true,
					'sort_clone'    => true,
					'add_button'    => 'Add variation',
					'fields'        => [
						[
							'name' => 'Variation internal description',
							'id'   => 'variation_internal_description',
							'type' => 'text',
						],
						[
							'name' => 'Variation Image',
							'id'   => 'variation_image',
							'type' => 'single_image',
						],
						[
							'name' => 'Variation price',
							'id'   => 'variation_price',
							'type' => 'number',
							'step' => 0.01,
							'size' => 8,
							'desc' => 'This will override all other options'
						],
					],
				],
			],
		];

		$options = get_option( 'htc-options' )['options_group'] ?? [];

		foreach ( $options as $options_group ) {
			$meta_boxes['htc_variations']['fields']['default_variant']['fields'][] = array(
				'name'    => $options_group['label'],
				'id'      => $options_group['id'],
				'type'    => 'checkbox_list',
				'options' => array_column( $options_group['options'], 'label', 'id' ),
			);
			$meta_boxes['htc_variations']['fields']['variants']['fields'][] = array(
				'name'    => $options_group['label'],
				'id'      => $options_group['id'],
				'type'    => 'checkbox_list',
				'options' => array_column( $options_group['options'], 'label', 'id' ),
			);
		}


		$meta_boxes[] = [
			'title'          => 'Backup & restore options',
			'id'             => 'htc-variations-backup',
			'settings_pages' => ['htc-variations'],
			'tab'            => 'backup',
			'fields'         => [
				[
					'name' => 'Backup & restore options',
					'type' => 'backup',
				],
			],
		];

		return $meta_boxes;
	}











	public function add_coupons_meta_boxes( $meta_boxes ) {
		$meta_boxes['htc_coupons'] = [
			'title'          => 'HT Variant Image Configurator',
			'id'             => 'htc-coupons',
			'settings_pages' => ['htc-coupons'],
			'tab'            => 'coupons',
			'class' => 'hidden-first-item-clonable-group',
			'fields'         => [
				'coupons' => [
					'name'          => '',
					'id'            => 'coupon',
					'type'          => 'group',
					'collapsible'   => true,
					'default_state' => 'collapsed',
					'group_title'   => '{coupon_name} | {coupon_code} | {coupon_type} | {coupon_amount}',
					'clone'         => true,
					'sort_clone'    => true,
					'add_button'    => 'Add coupon',
					'fields'        => [
						[
							'name' => 'Name',
							'id'   => 'coupon_name',
							'type' => 'text',
						],
						[
							'name' => 'Code',
							'id'   => 'coupon_code',
							'type' => 'text',
						],
						[
							'name'    => 'Type',
							'id'      => 'coupon_type',
							'type'    => 'select',
							'placeholder' => 'Select coupon type',
							'options' => [
								'percentage'    => 'Percentage',
								'flat' => 'Flat',
							]
						],
						[
							'name' => 'Amount',
							'id'   => 'coupon_amount',
							'type' => 'number',
							'step' => 0.01,
						],
						[
							'name' => 'Coupon applied message',
							'id'   => 'coupon_applied_message',
							'type' => 'text',
						],
					],
				],
			],
		];



		$meta_boxes[] = [
			'title'          => 'Backup & restore options',
			'id'             => 'htc-coupons-backup',
			'settings_pages' => ['htc-coupons'],
			'tab'            => 'backup',
			'fields'         => [
				[
					'name' => 'Backup & restore options',
					'type' => 'backup',
				],
			],
		];

		return $meta_boxes;
	}
}
new HT_Metabox();
