<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



class HT_Metabox {

	public function __construct() {
		add_filter( 'rwmb_meta_boxes', array( $this, 'add_options_meta_boxes' ) );
	}

	public function add_options_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'      => 'HT Options Configurator',
			'id'         => 'ht-configurator',
			'post_types' => ['page'],
			'context'    => 'after_title',
			'fields'     => [
					[
						'type' => 'heading',
						'name' => 'Initial settings',
					],
					[
						'name' => 'Initial price',
						'id'   => 'initial_price',
						'type' => 'number',
						'step' => 0.01,
						'size' => 10,
					],
					[
						'name' => 'Initial image',
						'id'   => 'initial_image',
						'type' => 'single_image',
					],
					[
						'type' => 'heading',
						'name' => 'Configurator sections',
					],
					[
						'name'          => 'Options Group',
						'id'            => 'options_group',
						'type'          => 'group',
						'collapsible'   => true,
						'default_state' => 'collapsed',
						'group_title'   => 'Section: {section_label}',
						'clone'         => true,
						'add_button'    => 'Add section',
						'fields'        => [
							[
								'name' => 'Section label',
								'id'   => 'label',
								'type' => 'text',
							],
							[
								'name'          => 'Group',
								'id'            => 'options',
								'type'          => 'group',
								'collapsible'   => true,
								'default_state' => 'collapsed',
								'group_title'   => 'Option: {option_name}',
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
										'id'   => 'option_amount',
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

		return $meta_boxes;
	}
}
new HT_Metabox();
