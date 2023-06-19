<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Settings_Page
 *
 * @package Hustle
 */

/**
 * Class Hustle_Settings_Page
 */
class Hustle_Settings_Page extends Hustle_Admin_Page_Abstract {

	/**
	 * Key of the Hustle's settings in wp_options.
	 *
	 * @since 4.0
	 */
	const SETTINGS_OPTION_KEY = 'hustle_settings';

	/**
	 * Init
	 */
	public function init() {

		$this->page = 'hustle_settings';

		/* translators: Plugin name */
		$this->page_title = sprintf( __( '%s Settings', 'hustle' ), Opt_In_Utils::get_plugin_name() );

		$this->page_menu_title = __( 'Settings', 'hustle' );

		$this->page_capability = 'hustle_edit_settings';

		$this->page_template_path = 'admin/settings';
	}

	/**
	 * Actions to be performed on Settings page.
	 *
	 * @since 4.1.0
	 */
	public function current_page_loaded() {
		parent::current_page_loaded();

		// Set up all the filters and buttons for tinymce editors.
		$this->set_up_tinymce();

		add_filter( 'mce_external_plugins', array( $this, 'remove_all_mce_external_plugins' ), -1 );

		add_action( 'admin_enqueue_scripts', array( 'Hustle_Module_Front', 'add_hui_scripts' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue colorpicker scripts
	 *
	 * @since 4.2.0
	 */
	public function enqueue_scripts() {
		self::add_color_picker();
	}

	/**
	 * Get page template args
	 *
	 * @return array
	 */
	public function get_page_template_args() {
		$current_user     = wp_get_current_user();
		$general_settings = Hustle_Settings_Admin::get_general_settings();
		$migration        = Hustle_Migration::get_instance();

		return array(
			'user_name'               => ucfirst( $current_user->display_name ),
			'email_name'              => $general_settings['sender_email_name'],
			'email_address'           => $general_settings['sender_email_address'],
			'unsubscription_messages' => Hustle_Settings_Admin::get_unsubscribe_messages(),
			'unsubscription_email'    => Hustle_Settings_Admin::get_unsubscribe_email_settings(),
			'hustle_settings'         => Hustle_Settings_Admin::get_hustle_settings(),
			'section'                 => $this->get_current_section( 'general' ),
		);
	}

	/**
	 * Add data to the current json array.
	 *
	 * @since 4.3.1
	 *
	 * @return array
	 */
	protected function get_vars_to_localize() {
		$current_array = parent::get_vars_to_localize();

		// Error messages for 4.0.x restoring.
		$current_array['messages']['restricted_access']  = esc_html__( "You can't perform this action", 'hustle' );
		$current_array['messages']['restore_40x_failed'] = esc_html__( "The restore failed. It could be that there's no data to restore. Please check the logs.", 'hustle' );

		$current_array['settings_palettes_action_nonce'] = wp_create_nonce( 'hustle_palette_action' );

		$current_array['palettes'] = Hustle_Palettes_Helper::get_all_palettes();

		$saved_id = filter_input( INPUT_GET, 'saved-id', FILTER_SANITIZE_SPECIAL_CHARS );
		if ( $saved_id ) {

			$saved_palettes = Hustle_Palettes_Helper::get_all_palettes_slug_and_name();
			if ( ! empty( $saved_palettes[ $saved_id ] ) ) {

				$saved_name = '<span style="color:#333;"><strong>' . esc_html( $saved_palettes[ $saved_id ] ) . '</strong></span>';
				/* translators: %s: palette name */
				$current_array['messages']['palette_saved'] = sprintf( esc_html__( '%s - Palette saved successfully.', 'hustle' ), $saved_name );
			}
		}

		$deleted_name = filter_input( INPUT_GET, 'deleted-name', FILTER_SANITIZE_SPECIAL_CHARS );
		if ( $deleted_name ) {

			$deleted_name = '<span style="color:#333;"><strong>' . esc_html( $deleted_name ) . '</strong></span>';
			/* translators: %s: palette name */
			$current_array['messages']['palette_deleted'] = esc_html( sprintf( __( '%s - Palette deleted successfully.', 'hustle' ), $deleted_name ) );
		}

		$palettes = array();
		$args     = array( 'except_types' => array( Hustle_Module_Model::SOCIAL_SHARING_MODULE ) );
		$modules  = Hustle_Module_Collection::instance()->get_all( null, $args );

		foreach ( $modules as $module ) {
			$palettes[ $module->module_type ][ $module->module_id ] = esc_html( $module->module_name );
		}
		$current_array['current']                        = $palettes;
		$current_array['current']['save_settings_nonce'] = wp_create_nonce( 'hustle_settings_save' );

		$current_array['messages']['generic_ajax_error'] = esc_html__( 'Something went wrong with the request. Please reload the page and try again.', 'hustle' );
		$current_array['messages']['settings_saved']     = esc_html__( 'Settings saved.', 'hustle' );
		$current_array['messages']['settings_was_reset'] = '<label class="wpmudev-label--notice"><span>' . esc_html__( 'Plugin was successfully reset.', 'hustle' ) . '</span></label>';

		return $current_array;
	}

	/**
	 * Removing all MCE external plugins which often break our pages
	 *
	 * @since 3.0.8
	 * @param array $external_plugins External plugins.
	 * @return array
	 */
	public function remove_all_mce_external_plugins( $external_plugins ) {

		remove_all_filters( 'mce_external_plugins' );
		$external_plugins = array();

		return $external_plugins;
	}
}
