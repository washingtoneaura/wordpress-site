<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_General_Data_Protection
 *
 * @package Hustle
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Hustle_General_Data_Protection
 *
 * @since 4.0.2
 */
class Hustle_General_Data_Protection {

	/**
	 * Clean up interval in string
	 *
	 * @var string
	 */
	protected $cron_cleanup_interval;

	/**
	 * Privacy settings array
	 *
	 * @var array
	 */
	private static $privacy_settings = array();

	/**
	 * Constructor.
	 *
	 * @param string $cron_cleanup_interval Cron interval.
	 */
	public function __construct( $cron_cleanup_interval = 'hourly' ) {
		$this->cron_cleanup_interval = $cron_cleanup_interval;
		$this->init();
	}

	/**
	 * Init
	 */
	protected function init() {

		// for data removal / anonymize data.
		if ( ! wp_next_scheduled( 'hustle_general_data_protection_cleanup' ) ) {
			wp_schedule_event( time(), $this->get_cron_cleanup_interval(), 'hustle_general_data_protection_cleanup' );
		}

		add_action( 'hustle_general_data_protection_cleanup', array( $this, 'personal_data_cleanup' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ), 10 );
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ), 10 );

	}

	/**
	 * Append registered eraser to wp eraser
	 *
	 * @param array $erasers Erasers.
	 *
	 * @since   4.0.2
	 *
	 * @return array
	 */
	public function register_eraser( $erasers = array() ) {
		$erasers['hustle-module-submissions'] = array(
			/* translators: Plugin name */
			'eraser_friendly_name' => esc_html( sprintf( __( '%s Module Submissions', 'hustle' ), Opt_In_Utils::get_plugin_name() ) ),
			'callback'             => array( 'Hustle_General_Data_Protection', 'do_submissions_eraser' ),
		);
		return $erasers;
	}

	/**
	 * Append registered eraser to wp eraser
	 *
	 * @param array $exporter Exporter.
	 *
	 * @since   4.0.2
	 *
	 * @return array
	 */
	public function register_exporter( $exporter = array() ) {
		$exporter['hustle-module-submissions'] = array(
			'exporter_friendly_name' => /* translators: Plugin name */ esc_html( sprintf( __( '%s Module Submissions', 'hustle' ), Opt_In_Utils::get_plugin_name() ) ),
			'callback'               => array( 'Hustle_General_Data_Protection', 'do_submissions_exporter' ),
		);
		return $exporter;
	}

	/**
	 * Get Interval
	 *
	 * @since   4.0.2
	 *
	 * @return string
	 */
	public function get_cron_cleanup_interval() {
		$cron_cleanup_interval = $this->cron_cleanup_interval;

		/**
		 * Filter interval to be used for cleanup process
		 *
		 * @since  4.0.2
		 *
		 * @params string $cron_cleanup_interval interval in string (daily,hourly, etc)
		 */
		$cron_cleanup_interval = apply_filters( 'hustle_general_data_cleanup_interval', $cron_cleanup_interval );

		return $cron_cleanup_interval;
	}

	/**
	 * Eraser
	 *
	 * @since 4.0.2
	 *
	 * @param string $email Email.
	 * @param int    $page Page.
	 *
	 * @return array
	 */
	public static function do_submissions_eraser( $email, $page ) {

		$settings = self::get_privacy_settings();

		$erasure_disabled = '1' === $settings['retain_sub_on_erasure'];

		$response = array(
			'items_removed'  => false,
			'items_retained' => true,
			'messages'       => array(),
			'done'           => true,
		);

		if ( true === $erasure_disabled ) {
			/* translators: Plugin name */
			$response['messages'][] = esc_html( sprintf( __( '%s Module Submissions were retained.', 'hustle' ), Opt_In_Utils::get_plugin_name() ) );
			return $response;
		}

		$entry_ids = Hustle_Entry_Model::get_entries_by_email( $email );

		// using action instead of filter here to stop data manipulation.
		do_action( 'hustle_before_submission_eraser', $email, $page, $entry_ids );

		if ( ! empty( $entry_ids ) ) {
			foreach ( $entry_ids as $entry_id ) {
				$entry_model = new Hustle_Entry_Model( $entry_id );
				Hustle_Entry_Model::delete_by_entry( $entry_model->module_id, $entry_id );
				/* translators: 1. Plugin name 2. entry id */
				$response['messages'][] = esc_html( sprintf( __( '%1$ submission #%2$d was deleted.', 'hustle' ), Opt_In_Utils::get_plugin_name(), $entry_id ) );

			}
			$response['items_removed']  = true;
			$response['items_retained'] = false;
		} else {
			/* translators: Plugin name */
			$response['messages'][] = esc_html( sprintf( __( ' %s submissions not found.', 'hustle' ), Opt_In_Utils::get_plugin_name() ) );
		}

		// using action instead of filter here to stop data manipulation.
		do_action( 'hustle_after_submission_eraser', $email, $page, $entry_ids );

		return $response;
	}

	/**
	 * Export module submissions
	 *
	 * @since 4.0.2
	 *
	 * @param string $email Email.
	 * @param int    $page Page.
	 *
	 * @return array
	 */
	public static function do_submissions_exporter( $email, $page ) {
		$entry_ids      = Hustle_Entry_Model::get_entries_by_email( $email );
		$data_to_export = array();

		if ( ! empty( $entry_ids ) && is_array( $entry_ids ) ) {
			foreach ( $entry_ids as $entry_id ) {
				$entry_model = new Hustle_Entry_Model( $entry_id );

				$data = array();

				if ( is_object( $entry_model ) ) {
					$data = self::get_custom_form_export_mappers( $entry_model );
				}

				$data_to_export[] = array(
					'group_id'    => 'hustle_module_submissions',
					'group_label' => /* translators: Plugin name */ esc_html( sprintf( __( '%s Module Submissions', 'hustle' ), Opt_In_Utils::get_plugin_name() ) ),
					'item_id'     => 'entry-' . $entry_id,
					'data'        => $data,
				);
			}
		}

		/**
		 * Filter Export data for Custom form submission on tools.php?page=export_personal_data
		 *
		 * @since 4.0.2
		 *
		 * @param array  $data_to_export
		 * @param string $email
		 * @param array  $entry_ids
		 */
		$data_to_export = apply_filters( 'hustle_module_submissions_export_data', $data_to_export, $email, $entry_ids );

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * Get data mappers and their values
	 *
	 * @since   4.0.2
	 *
	 * @param Hustle_Entry_Model $model Model.
	 *
	 * @return array
	 */
	public static function get_custom_form_export_mappers( $model ) {

		$meta = $model->meta_data;

		$mappers = array(
			array(
				'name'  => __( 'Entry ID', 'hustle' ),
				'value' => $model->entry_id,
			),
			array(
				'name'  => __( 'Submission Date', 'hustle' ),
				'value' => $model->date_created_sql,
			),
		);

		if ( ! empty( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				// base mapper for every field.
				if ( is_array( $value['value'] ) ) {
					continue;
				}

				$mapper             = array();
				$mapper['meta_key'] = $key;// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$mapper['name']     = $key;
				$mapper['value']    = $value['value'];

				if ( ! empty( $mapper ) ) {
					$mappers[] = $mapper;
				}
			}
		}

		return $mappers;
	}

	/**
	 * Anonymizing data
	 *
	 * @since 4.0.2
	 *
	 * @return bool
	 */
	public function personal_data_cleanup() {

		$settings = self::get_privacy_settings();

		$this->cleanup_submissions( $settings );
		$this->cleanup_ip_address( $settings );
		$this->cleanup_tracking_data( $settings );

		return true;
	}

	/**
	 * Clean up form submissions
	 *
	 * @since 4.0.2
	 *
	 * @param array $settings privacy settings.
	 *
	 * @return bool
	 */
	private function cleanup_submissions( $settings ) {

		$retain_number = $settings['submissions_retention_number'];
		$retain_unit   = $settings['submissions_retention_number_unit'];

		if ( '1' === $settings['retain_submission_forever'] || 0 === $retain_number ) {
			return false;
		}

		$possible_units = array(
			'days',
			'weeks',
			'months',
			'years',
		);

		if ( ! in_array( $retain_unit, $possible_units, true ) ) {
			return false;
		}

		$retain_time = strtotime( '-' . $retain_number . ' ' . $retain_unit );
		$retain_time = date_i18n( 'Y-m-d H:i:s', $retain_time );

		$entry_ids = Hustle_Entry_Model::get_older_entry_ids( $retain_time );

		foreach ( $entry_ids as $entry_id ) {
			$entry_model = new Hustle_Entry_Model( $entry_id );
			Hustle_Entry_Model::delete_by_entry( $entry_model->module_id, $entry_id );
		}

		return true;
	}

	/**
	 * Cleanup IP Address based on settings
	 *
	 * @since 4.0.2
	 *
	 * @param array $settings privacy settings.
	 *
	 * @return bool
	 */
	private function cleanup_ip_address( $settings ) {

		$retain_number = $settings['ip_retention_number'];
		$retain_unit   = $settings['ip_retention_number_unit'];

		if ( '1' === $settings['retain_ip_forever'] || 0 === $retain_number ) {
			return false;
		}

		$possible_units = array(
			'days',
			'weeks',
			'months',
			'years',
		);

		if ( ! in_array( $retain_unit, $possible_units, true ) ) {
			return false;
		}

		$retain_time = strtotime( '-' . $retain_number . ' ' . $retain_unit );
		$retain_time = date_i18n( 'Y-m-d H:i:s', $retain_time );

		$entry_ids    = Hustle_Entry_Model::get_older_entry_ids( $retain_time );
		$tracking_ids = Hustle_Tracking_Model::get_older_tracking_ids( $retain_time );

		foreach ( $entry_ids as $entry_id ) {
			$entry_model = new Hustle_Entry_Model( $entry_id );
			$this->anonymize_entry_model( $entry_model );
		}

		foreach ( $tracking_ids as $tracking_id ) {
			$this->anonymize_tracking_model( $tracking_id );
		}

		return true;
	}

	/**
	 * Anon Entry model IP
	 *
	 * @since 4.0.2
	 *
	 * @param Hustle_Entry_Model $entry_model Entry model.
	 */
	private function anonymize_entry_model( Hustle_Entry_Model $entry_model ) {
		if ( isset( $entry_model->meta_data['hustle_ip'] ) ) {
			$meta_id    = $entry_model->meta_data['hustle_ip']['id'];
			$meta_value = $entry_model->meta_data['hustle_ip']['value'];

			if ( function_exists( 'wp_privacy_anonymize_ip' ) ) {
				$anon_value = wp_privacy_anonymize_ip( $meta_value );
			} else {
				$anon_value = '';
			}

			if ( $anon_value !== $meta_value ) {
				$entry_model->update_meta( $meta_id, 'hustle_ip', $anon_value );
			}
		}
	}

	/**
	 * Cleanup tracking data
	 *
	 * @since 4.0.2
	 * @param array $settings privacy settings.
	 * @return bool
	 */
	private function cleanup_tracking_data( $settings ) {

		$retain_number = $settings['tracking_retention_number'];
		$retain_unit   = $settings['tracking_retention_number_unit'];

		if ( '1' === $settings['retain_tracking_forever'] || 0 === $retain_number ) {
			return false;
		}

		$possible_units = array(
			'days',
			'weeks',
			'months',
			'years',
		);

		if ( ! in_array( $retain_unit, $possible_units, true ) ) {
			return false;
		}

		$retain_time = strtotime( '-' . $retain_number . ' ' . $retain_unit );
		$retain_time = date_i18n( 'Y-m-d H:i:s', $retain_time );

		$tracking_ids = Hustle_Tracking_Model::get_older_tracking_ids( $retain_time );

		foreach ( $tracking_ids as $tracking_id ) {
			Hustle_Tracking_Model::delete_data_by_tracking_id( $tracking_id );
		}

		return true;
	}

	/**
	 * Get privacy settings
	 *
	 * @since 4.0.2
	 *
	 * @return settings array()
	 */
	private static function get_privacy_settings() {
		if ( empty( self::$privacy_settings ) ) {
			self::$privacy_settings = Hustle_Settings_Admin::get_privacy_settings();
		}
		return self::$privacy_settings;
	}

	/**
	 * Anon Tracking model IP
	 *
	 * @since 4.0.2
	 *
	 * @param string $tracking tracking id.
	 */
	private function anonymize_tracking_model( $tracking ) {
		if ( ! empty( $tracking ) ) {

			$ip = Hustle_Tracking_Model::get_ip_from_tracking_id( $tracking );

			if ( ! empty( $ip ) ) {

				if ( function_exists( 'wp_privacy_anonymize_ip' ) ) {
					$anon_value = wp_privacy_anonymize_ip( $ip[0] );
				} else {
					$anon_value = '';
				}

				Hustle_Tracking_Model::anonymise_tracked_id( $tracking, $anon_value );
			}
		}
	}

}
