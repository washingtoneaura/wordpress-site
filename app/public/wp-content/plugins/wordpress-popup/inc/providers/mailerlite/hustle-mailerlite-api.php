<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * MailerLite API Helper
 *
 * @package Hustle
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Hustle_MailerLite_Api' ) ) :

	/**
	 * Class Hustle_MailerLite_Api
	 */
	class Hustle_MailerLite_Api {

		/**
		 * Api Key
		 *
		 * @var String
		 */
		protected $api_key;

		/**
		 * End point
		 *
		 * @var String
		 */
		private $end_point = 'https://api.mailerlite.com/api/v2/';

		/**
		 * Constructor
		 *
		 * @param string $_api_key Api key.
		 */
		public function __construct( $_api_key ) {
			$this->api_key = $_api_key;
		}

		/**
		 * Perform API remote request
		 *
		 * @param String       $path - relative api path to the $end_point.
		 * @param String       $method - allowed methods are GET, POST, PUT, DELETE.
		 * @param String|Array $input - the post data.
		 *
		 * @return WP_Error|Array
		 */
		private function do_request( $path, $method, $input ) {

			$called_url = $this->end_point . $path;
			$ssl_verify = true;
			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
				// Windows only over-ride.
				$ssl_verify = false;
			}

			$args = array(
				'method'    => $method,
				'sslverify' => apply_filters( 'hustle_mailerlite_sslverify', $ssl_verify ),
				'headers'   => array(
					'X-MailerLite-ApiKey' => $this->api_key,
					'Content-Type'        => 'application/json',
				),
			);

			$args['body'] = $input;

			$response = wp_remote_request( $called_url, $args );

			$utils                     = Hustle_Provider_Utils::get_instance();
			$utils->last_url_request   = $called_url;
			$utils->last_data_received = $response;
			$utils->last_data_sent     = $args;

			$data = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $data ) ) {
				return $data;
			}

			return json_decode( $data, true );
		}

		/**
		 * GET Request
		 *
		 * @param string $path Path.
		 * @param array  $input Input.
		 * @return type
		 */
		private function get( $path, $input = array() ) {
			return $this->do_request( $path, 'GET', $input );
		}

		/**
		 * PUT Http request
		 *
		 * @param string $path Path.
		 * @param array  $input Input.
		 */
		private function put( $path, $input = array() ) {
			return $this->do_request( $path, 'PUT', wp_json_encode( $input ) );
		}

		/**
		 * POST Http request
		 *
		 * @param string $path Path.
		 * @param array  $input Input.
		 */
		private function post( $path, $input = array() ) {
			return $this->do_request( $path, 'POST', wp_json_encode( $input ) );
		}

		/**
		 * DELETE Http request
		 *
		 * @param string $path Path.
		 * @param array  $input Input.
		 */
		private function delete( $path, $input = array() ) {
			return $this->do_request( $path, 'DELETE', $input );
		}

		/**
		 * List Groups
		 *
		 * @param int $offset Offset.
		 * @return Array|WP_Error
		 */
		public function list_groups( $offset = 0 ) {
			return $this->get( 'groups?offset=' . intval( $offset ) );
		}

		/**
		 * Add Subscriber
		 *
		 * @param Integer $group_id - the group id.
		 * @param Array   $subscriber_data - An array containing the keys email and fields(name,value).
		 * @param int     $resubscribe Resubscribe.
		 */
		public function add_subscriber( $group_id, $subscriber_data, $resubscribe = 0 ) {
			$subscriber_data['resubscribe'] = $resubscribe;
			$path                           = 'groups/' . $group_id . '/subscribers';

			$res = $this->post( $path, $subscriber_data );

			return $res;
		}

		/**
		 * Update Subscriber
		 *
		 * @param string $email Email.
		 * @param Array  $subscriber_data - An array containing the keys email and fields(name,value).
		 */
		public function update_subscriber( $email, $subscriber_data ) {
			$path = 'subscribers/' . $email;

			$res = $this->put( $path, $subscriber_data );

			return $res;
		}

		/**
		 * Delete subscriber from the list
		 *
		 * @param string $list_id List ID.
		 * @param string $email Email.
		 *
		 * @return bool
		 */
		public function delete_email( $list_id, $email ) {
			$endpoint = 'groups/' . $list_id . '/subscribers/' . $email;
			$res      = $this->delete( $endpoint );

			return ! is_wp_error( $res );
		}

		/**
		 * Get Subscriber groups
		 *
		 * @param string $email Email.
		 */
		public function get_subscriber( $email ) {
			$path = 'subscribers/' . $email . '/groups';

			$res = $this->get( $path );

			return $res;
		}

		/**
		 * Add custom field
		 *
		 * @param Array $field_data (title, type).
		 */
		public function add_custom_field( $field_data ) {
			$path = 'fields';

			$res = $this->post( $path, $field_data );

			return $res;
		}

		/**
		 * Get custom field
		 */
		public function get_custom_field() {
			$path = 'fields';
			$res  = $this->get( $path );

			return $res;
		}
	}
endif;
