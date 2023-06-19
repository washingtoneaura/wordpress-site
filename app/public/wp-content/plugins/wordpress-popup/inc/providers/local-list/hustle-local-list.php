<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Local_List class
 *
 * @package Hustle
 */

if ( ! class_exists( 'Hustle_Local_List' ) ) :

	/**
	 * Class Hustle_Local_List
	 */
	class Hustle_Local_List extends Hustle_Provider_Abstract {

		/**
		 * Provider Instance
		 *
		 * @since 3.0.5
		 *
		 * @var self|null
		 */
		protected static $instance = null;

		/**
		 * Slug
		 *
		 * @since 3.0.5
		 * @var string
		 */
		protected $slug = 'local_list';

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
		protected $title = 'Local List';

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
		protected $form_settings = 'Hustle_Local_List_Form_Settings';

		/**
		 * Class name of form hooks
		 *
		 * @var string
		 */
		protected $form_hooks = 'Hustle_Local_List_Form_Hooks';

		/**
		 * Array of options which should exist for confirming that settings are completed
		 *
		 * @since 4.0
		 * @var array
		 */
		protected $completion_options = array();

		/**
		 * Provider constructor.
		 */
		public function __construct() {
			$this->icon_2x = plugin_dir_url( __FILE__ ) . 'images/icon.png';
			$this->logo_2x = plugin_dir_url( __FILE__ ) . 'images/logo.png';
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
		 * Is active?
		 *
		 * @return boolean
		 */
		public function active() {
			return true;
		}

		/**
		 * Migrate 3.0
		 *
		 * @param object $module Module.
		 * @param object $old_module Old module.
		 */
		public function migrate_30( $module, $old_module ) {
			$save_local      = ! empty( $old_module->meta['content']['save_local_list'] );
			$local_list_name = ! empty( $old_module->meta['content']['local_list_name'] )
			? $old_module->meta['content']['local_list_name']
			: '';

			if ( $save_local ) {
				$module->set_provider_settings(
					$this->get_slug(),
					array(
						'local_list_name' => $local_list_name,
					)
				);

				// Activate the addon.
				Hustle_Providers::get_instance()->activate_addon( $this->get_slug() );
			}
		}

	}

endif;
