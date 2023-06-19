<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_ConstantContact class
 *
 * @package Hustle
 */

if ( ! class_exists( 'Hustle_ConstantContact' ) ) :

	/**
	 * Class Hustle_ConstantContact
	 */
	class Hustle_ConstantContact extends Hustle_Provider_Abstract {

		const SLUG = 'constantcontact';

		/**
		 * Errors
		 *
		 * @var array
		 */
		protected static $errors;

		/**
		 * Constant Contact Provider Instance
		 *
		 * @since 3.0.5
		 *
		 * @var self|null
		 */
		protected static $instance = null;

		/**
		 * PHP min version
		 *
		 * @since 3.0.5
		 * @var string
		 */
		public static $min_php_version = '5.3';

		/**
		 * Slug
		 *
		 * @since 3.0.5
		 * @var string
		 */
		protected $slug = 'constantcontact';

		/**
		 * Version
		 *
		 * @since 3.0.5
		 * @var string
		 */
		protected $version = '1.0';

		/**
		 * Class
		 *
		 * @since 3.0.5
		 * @var string
		 */
		protected $class = __CLASS__;

		/**
		 * Title
		 *
		 * @since 3.0.5
		 * @var string
		 */
		protected $title = 'Constant Contact';

		/**
		 * Is multi on global
		 *
		 * @since 4.0
		 * @var boolean
		 */
		protected $is_multi_on_global = false;

		/**
		 * Class name of form settings
		 *
		 * @var string
		 */
		protected $form_settings = 'Hustle_ConstantContact_Form_Settings';

		/**
		 * Class name of form hooks
		 *
		 * @since 4.0
		 * @var string
		 */
		protected $form_hooks = 'Hustle_ConstantContact_Form_Hooks';

		/**
		 * Hustle_ConstantContact constructor.
		 */
		public function __construct() {
			$this->icon_2x = plugin_dir_url( __FILE__ ) . 'images/icon.png';
			$this->logo_2x = plugin_dir_url( __FILE__ ) . 'images/logo.png';

			if ( ! class_exists( 'Hustle_ConstantContact_Api' ) ) {
				require_once 'hustle-constantcontact-api.php';
			}
			// Instantiate the API on constructor because it's required after getting the authorization.
			$hustle_constantcontact = new Hustle_ConstantContact_Api();
		}

		/**
		 * Get Instance
		 *
		 * @return self|null
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Check if the settings are completed
		 *
		 * @since 4.0
		 * @param string $multi_id Multi ID.
		 * @return boolean
		 */
		protected function settings_are_completed( $multi_id = '' ) {
			$api = $this->api();

			return (bool) $api->get_token( 'access_token' );
		}

		/**
		 * Get api
		 *
		 * @return bool|Opt_In_ConstantContact_Api
		 */
		public function api() {
			return self::static_api();
		}

		/**
		 * Get api by static method
		 *
		 * @return \WP_Error|\Hustle_ConstantContact_Api
		 */
		public static function static_api() {
			if ( ! class_exists( 'Hustle_ConstantContact_Api' ) ) {
				require_once 'hustle-constantcontact-api.php';
			}

			if ( class_exists( 'Hustle_ConstantContact_Api' ) ) {
				$api = new Hustle_ConstantContact_Api();
				return $api;
			} else {
				return new WP_Error( 'error', __( 'API Class could not be initialized', 'hustle' ) );
			}

		}

		/**
		 * Get the wizard callbacks for the global settings.
		 *
		 * @since 4.0
		 *
		 * @return array
		 */
		public function settings_wizards() {
			return array(
				array(
					'callback'     => array( $this, 'configure_api_key' ),
					'is_completed' => array( $this, 'is_connected' ),
				),
			);
		}


		/**
		 * Configure the API key settings. Global settings.
		 *
		 * @since 4.0
		 *
		 * @param array $submitted_data Submitted data.
		 * @param bool  $is_submit Is submit.
		 * @param int   $module_id Module ID.
		 * @return array
		 */
		public function configure_api_key( $submitted_data, $is_submit, $module_id ) {

			$api = $this->api();

			if ( ! $module_id ) {
				$auth_url = $api->get_authorization_uri( 0, true, Hustle_Data::INTEGRATIONS_PAGE );

			} else {

				$module = new Hustle_Module_Model( $module_id );
				if ( ! is_wp_error( $module ) ) {
					$auth_url = $api->get_authorization_uri( $module_id, true, $module->get_wizard_page() );
				}
			}

			$is_connected = $this->is_connected();

			if ( $is_connected ) {

				$description = __( 'You are already connected to Constant Contact. You can disconnect your Constant Contact Integration (if you need to) using the button below.', 'hustle' );

				$buttons = array(
					'disconnect' => array(
						'markup' => Hustle_Provider_Utils::get_provider_button_markup(
							__( 'Disconnect', 'hustle' ),
							'sui-button-ghost sui-button-center',
							'disconnect',
							true
						),
					),
				);

			} else {
				/* translators: Plugin name */
				$description = sprintf( __( 'Connect the Constant Contact integration by authenticating it using the button below. Note that you’ll be taken to the Constant Contact website to grant access to %s and then redirected back.', 'hustle' ), Opt_In_Utils::get_plugin_name() );

				$buttons = array(
					'auth' => array(
						'markup' => Hustle_Provider_Utils::get_provider_button_markup(
							__( 'Authenticate', 'hustle' ),
							'sui-button-center',
							'',
							true,
							false,
							$auth_url
						),
					),
				);
			}

			$step_html = Hustle_Provider_Utils::get_integration_modal_title_markup( __( 'Connect Constant Contact', 'hustle' ), $description );

			if ( $is_connected ) {

				$account_details = $this->get_settings_values();

				$account_email = isset( $account_details['email'] ) ? $account_details['email'] : $this->save_account_email();

				$step_html .= Hustle_Provider_Utils::get_html_for_options(
					array(
						array(
							'type'  => 'notice',
							'icon'  => 'info',
							/* translators: email associated to the account */
							'value' => sprintf( esc_html__( 'You are connected to %s', 'hustle' ), '<strong>' . esc_html( $account_email ) . '</strong>' ),
							'class' => 'sui-notice-success',
						),
					)
				);

			}

			$response = array(
				'html'    => $step_html,
				'buttons' => $buttons,
			);

			return $response;
		}

		/**
		 * Get the current account's email.
		 * If not stored already, store it.
		 *
		 * @since 4.0.2
		 *
		 * @return string
		 */
		private function save_account_email() {

			try {
				$account_details = $this->get_settings_values();
				$account_info    = $this->api()->get_account_info();
				$account_email   = $account_info->email;

				$account_details['email'] = $account_email;

				$this->save_settings_values( $account_details );

			} catch ( Exception $e ) {
				$account_email = __( 'The associated email could not be retrieved', 'hustle' );
			}

			return $account_email;
		}

		/**
		 * Migrate 3.0
		 *
		 * @param object $module Module.
		 * @param object $old_module Old module.
		 * @return boolean
		 */
		public function migrate_30( $module, $old_module ) {
			$migrated = parent::migrate_30( $module, $old_module );
			if ( ! $migrated ) {
				return false;
			}

			/*
			 * Our regular migration would've saved the provider settings in a format that's incorrect for constant contact
			 *
			 * Let's fix that now.
			 */
			$module_provider_settings = $module->get_provider_settings( $this->get_slug() );
			if ( ! empty( $module_provider_settings ) ) {
				// At provider level don't store anything (at least not in the regular option).
				delete_option( $this->get_settings_options_name() );

				// selected_global_multi_id not needed at module level.
				unset( $module_provider_settings['selected_global_multi_id'] );
				$module->set_provider_settings( $this->get_slug(), $module_provider_settings );
			}

			return $migrated;
		}

		/**
		 * Process the request after coming from authentication.
		 *
		 * @since 4.0.2
		 * @return array
		 */
		public function process_external_redirect() {

			$response = array();

			$status = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS );

			$api           = $this->api();
			$is_authorized = (bool) $api->get_token( 'access_token' );

			// API Auth was successful.
			if ( 'success' === $status && $is_authorized ) {

				if ( ! $this->is_active() ) {

					$providers_instance = Hustle_Providers::get_instance();
					$activated          = $providers_instance->activate_addon( $this->slug );

					// Provider successfully activated.
					if ( $activated ) {

						$response = array(
							'action'  => 'notification',
							'status'  => 'success',
							/* translators: integration name */
							'message' => sprintf( esc_html__( '%s successfully connected.', 'hustle' ), '<strong>' . esc_html( $this->title ) . '</strong>' ),
						);

						$this->save_account_email();

					} else { // Provider couldn't be activated.

						$response = array(
							'action'  => 'notification',
							'status'  => 'error',
							'message' => wp_kses_post( $providers_instance->get_last_error_message() ),
						);
					}
				}
			} else { // API Auth failed.

				$response = array(
					'action'  => 'notification',
					'status'  => 'error',
					/* translators: integration name */
					'message' => sprintf( esc_html__( 'Authentication failed! Please check your %s credentials and try again.', 'hustle' ), esc_html( $this->title ) ),
				);

			}

			return $response;
		}

		/**
		 * Get 3.0 provider mappings
		 *
		 * @return array
		 */
		public function get_30_provider_mappings() {
			return array();
		}

		/**
		 * Remove wp_options rows
		 */
		public function remove_wp_options() {
			$api = $this->api();
			$api->remove_wp_options();
		}
	}
endif;
