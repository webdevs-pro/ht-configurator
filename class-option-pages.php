<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class HT_Options {

	const OPTION_NAME = 'ht_options';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_options_page' ) );
		add_action( 'admin_init', array( $this, 'setup_sections_and_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ));
	}









	public function create_options_page() {
		// Main menu page
		add_menu_page(
			'Configurator',
			'Configurator',
			'manage_options',
			'ht_configurator',
			array( $this, 'options_page_content' ),
			'',
			4
		);

	}









	public function options_page_content() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
					<?php
					settings_fields( 'ht_options_group' );
					do_settings_sections( 'ht_options' );
					submit_button();
					?>
			</form>
			<script>
				jQuery(document).ready(function ($) {
					var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
					editorSettings.codemirror = _.extend(
						{},
						editorSettings.codemirror,
						{
							mode: 'application/json',
							indentWithTabs: false,
							tabSize: 3,
							lineNumbers: true,
							lineWrapping: true,
							autoCloseBrackets: true,
							matchBrackets: true,
							lint: true,
							styleActiveLine: true,
							// theme: '3024-night'
							theme: 'monokai'
						}
					);
					var editor = wp.codeEditor.initialize($('#json-textarea'), editorSettings);

					editor.codemirror.on('blur', function(){
						var totalLines = editor.codemirror.lineCount();
						var totalChars = editor.codemirror.getTextArea().value.length;
						editor.codemirror.autoFormatRange({line:0, ch:0}, {line:totalLines, ch:totalChars});
					});

					editor.codemirror.on('changes', function(cm){
						cm.setSize(null, cm.getScrollInfo().height);
					});

					editor.codemirror.setSize(null, editor.codemirror.getScrollInfo().height);
				});
			</script>
		</div>
		<?php
	}









	public function options_subpage_content() {
		// Display the content for the Options submenu page
		?>
		<div class="wrap">
			<h1>Options</h1>
			<p>This is the Options subpage. Here, you could display a form for configuring options, for example.</p>
		</div>
		<?php
	}









	public function variations_subpage_content() {
		// Display the content for the Variations submenu page
		?>
		<div class="wrap">
			<h1>Variations</h1>
			<p>This is the Variations subpage. Here, you could display a form for configuring variations, for example.</p>
		</div>
		<?php
	}









	public function setup_sections_and_fields() {
		register_setting( 'ht_options_group', self::OPTION_NAME, array( $this, 'sanitize_input' ) );

		add_settings_section(
			'ht_options_section',
			'Edit JSON String',
			array( $this, 'section_callback' ),
			'ht_options'
		);

		add_settings_field(
			'ht_options_field',
			'JSON String',
			array( $this, 'field_callback' ),
			'ht_options',
			'ht_options_section'
		);
	}









	public function section_callback( $arguments ) {
		echo 'You can edit your JSON string in the text area below:';
	}









	public function field_callback( $arguments ) {
		$option = get_option( self::OPTION_NAME );
		echo '<textarea name="' . esc_attr( self::OPTION_NAME ) . '" id="json-textarea" cols="100" rows="10">' . esc_textarea( $option ) . '</textarea>';
	}









	public function sanitize_input( $input ) {
		// TODO: Add sanitization, validation, and escaping here
		return $input;
	}









	public function enqueue_admin_scripts( $hook ) {
		if ( 'toplevel_page_ht_configurator' !== $hook ) {
			return;
		}

		$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'application/json' ) );
		wp_localize_script( 'jquery', 'cm_settings', $cm_settings );

		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_style( 'wp-codemirror' );

		// wp_enqueue_style( 'codemirror-theme-3024-night', plugin_dir_url( __FILE__ ) . 'assets/3024-night.css' );
		wp_enqueue_style( 'codemirror-theme-3024-night', plugin_dir_url( __FILE__ ) . 'assets/monokai.css' );
	}
}

new HT_Options();