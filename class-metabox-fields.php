<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



class HT_Metabox {

	public function __construct() {
		add_filter( 'mb_settings_pages', array( $this, 'register_settings_pages' ) );
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_options_meta_boxes' ), 10);
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_variations_meta_boxes' ), 10);
		add_action( 'admin_print_styles', array( $this, 'print_scripts_and_styles' ) );
	}













	public function print_scripts_and_styles() {
		global $pagenow;

		if ( $pagenow == 'admin.php' && $_GET['page'] == 'htc-options' ) {
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'application/json' ) );
			wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
	
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
	
			// wp_enqueue_style( 'codemirror-theme-3024-night', plugin_dir_url( __FILE__ ) . 'assets/3024-night.css' );
			wp_enqueue_style( 'codemirror-theme-3024-night', plugin_dir_url( __FILE__ ) . 'assets/monokai.css' );
		}
	}













	public function register_settings_pages( $settings_pages ) {
		$settings_pages[] = [
			'menu_title' => 'Options config',
			'id'         => 'htc-options',
			'position'   => 0,
			'parent'     => 'ht_configurator',
			'columns'    => 1,
			'icon_url'   => 'dashicons-admin-generic',
			'tabs'       => [
            'options'  => 'Options',
            'backup' => 'Backup',
        ],
		];
		$settings_pages[] = [
			'menu_title' => 'Variations',
			'id'         => 'htc-variations',
			'position'   => 0,
			'parent'     => 'ht_configurator',
			'columns'    => 1,
			'icon_url'   => 'dashicons-admin-generic',
		];

		return $settings_pages;
	}













	public function add_options_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'      => 'Options',
			'id'         => 'htc-options',
			'settings_pages' => ['htc-options'],
			'tab'            => 'options',
			'fields'     => [
				[
					'name'          => 'Options Group',
					'id'            => 'options_group',
					'type'          => 'group',
					'collapsible'   => true,
					'default_state' => 'collapsed',
					'group_title'   => 'Section: {label}',
					'clone'         => true,
					'add_button'    => 'Add section',
					'fields'        => [
						[
							'name' => 'Section label',
							'id'   => 'label',
							'type' => 'text',
						],
						[
							'name' => 'Description',
							'id'   => 'description',
							'type' => 'text',
						],
						[
							'name' => 'Type',
							'id'   => 'type',
							'type' => 'select',
							'options' => [
								'radio' => 'Radio button (single option)',
								'checkbox' => 'Checkbox (multiple options)',
							]
						],
						[
							'name' => 'Section ID',
							'id'   => 'id',
							'type' => 'text',
							'desc' => 'This field ONLY accepts these chars: a-z, 0-9, _ and - and the value must be unique. <br><b style="color: red;">Changing the section ID will break existing variations. Please don\'t touch it!</b>',
							'attributes' => [
								'oninput' => 'this.value = this.value.replace(/[^a-z0-9_-]/, \'\');',
								'onclick' => 'if (this.dataset.confirmed !== "true") { var confirmed = confirm("Changing the option ID will break existing variations. Please don\'t touch it!"); if (confirmed) { this.dataset.confirmed = "true"; this.focus(); } else { this.blur(); } } else { this.focus(); }',
							],
						],
						[
							'name'          => 'Group',
							'id'            => 'options',
							'type'          => 'group',
							'collapsible'   => true,
							'default_state' => 'collapsed',
							'group_title'   => '{label}',
							'clone'         => true,
							'sort_clone'    => true,
							'add_button'    => 'Add option',
							'fields'        => [
								[
									'name' => 'Option Label',
									'id'   => 'label',
									'type' => 'text',
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
									'desc' => 'This field ONLY accepts these chars: a-z, 0-9, _ and - and the value must be unique within options group. <br><b style="color: red;">Changing the option ID will break existing variations. Please don\'t touch it!</b>',
									'attributes' => [
										'oninput' => 'this.value = this.value.replace(/[^a-z0-9_-]/, \'\');',
										'onclick' => 'if (this.dataset.confirmed !== "true") { var confirmed = confirm("Changing the option ID will break existing variations. Please don\'t touch it!"); if (confirmed) { this.dataset.confirmed = "true"; this.focus(); } else { this.blur(); } } else { this.focus(); }',
									],
								],
								[
									'name' => 'Amount to add',
									'id'   => 'amount',
									'type' => 'number',
									'desc' => 'Amount to be added to the current price',
									'step' => 0.01,
								],
							],
						],
					],
				],
			],
		];

		$meta_boxes[] = [
			'title'      => 'Backup & restore options',
			'id'         => 'htc-options-backup',
			'settings_pages' => ['htc-options'],
			'tab'            => 'backup',
			'fields'     => [
				[
					'name' => 'Backup & restore options',
					'type' => 'backup',
				],
			],
		];

		return $meta_boxes;
	}











	public function add_variations_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'          => 'HT Variant Image Configurator',
			'id'             => 'htc-variations',
			'settings_pages' => ['htc-variations'],
			'fields'         => [
				[
					'name'          => 'Group',
					'id'            => 'variation',
					'type'          => 'group',
					'collapsible'   => true,
					'default_state' => 'collapsed',
					'group_title'   => '{internal_color} - {external_color} | {variation_internal_description}',
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
							'name' => 'Variation amount',
							'id'   => 'variation_amount',
							'type' => 'number',
							'step' => 0.01,
							'size' => 8,
						],
						[
							'type' => 'heading',
							'name' => 'Grundausstattung',
						],
						[
							'name'        => 'Wannen Farbe',
							'id'          => 'internal_color',
							'type'        => 'select',
							'options'     => [
								1      => 'Himmel Blau (Standard)',
								2      => 'Ozeanblau',
								3      => 'Creme',
								4      => 'Grau',
								'Weis' => 'Weis',
							],
							'placeholder' => 'Select inner color',
						],
						[
							'name'        => 'Außenfarben',
							'id'          => 'external_color',
							'type'        => 'select',
							'options'     => [
								1 => 'Grau WPC',
								2 => 'Braun WPC',
								3 => 'Thermo Holz (Hell)',
								4 => 'Fichtenholz (dunkel)',
							],
							'placeholder' => 'Select external color',
						],
						[
							'name'    => 'Ofen Art',
							'id'      => 'ofen_art',
							'type'    => 'checkbox_list',
							'options' => [
								1 => 'Ohne Ofen (Nur Tub)',
								2 => 'Externer Ofen Model: AISI430 mit Schornsteinsystem',
								3 => 'Externer Ofen Model: AISI304 mit Schornsteinsystem',
								4 => 'Externer Ofen Model: AISI316 mit Schornsteinsystem Geeignet für Chemikalien (chlor)',
							],
						],
						[
							'name'        => 'Deckel',
							'id'          => 'deckel',
							'type'        => 'select',
							'options'     => [
								1 => 'Ohne Tub Deckel',
								2 => 'Kunstoff Deckel Durchsichtig',
								3 => 'Thermo leder Deckel',
							],
							'placeholder' => 'Select cover color',
						],
						[
							'name'        => 'Treppen Art',
							'id'          => 'treppen_art',
							'type'        => 'select',
							'options'     => [
								1 => 'Ohne Treppe',
								2 => 'Offene 2 Stufige Treppe',
								3 => 'Geschlossene 2 Stufige Truppe',
							],
							'placeholder' => 'Select stairs color',
						],
						[
							'type' => 'heading',
							'name' => 'Ausstattungsvarianten',
						],
						[
							'name'        => 'LED IM TUB',
							'id'          => 'led_im_tub',
							'type'        => 'select',
							'options'     => [
								1 => 'Ohne LED´s',
								2 => '1 LED 65mm Unterwasserlampe',
								3 => '2 LED 65mm Unterwasserlampe',
								4 => '3 LED 65mm Unterwasserlampe',
								5 => '4. LED 65mm Unterwasserlampe',
							],
							'placeholder' => 'Select LED type',
						],
					],
				],
			],
		];

		return $meta_boxes;
	}
}
new HT_Metabox();
