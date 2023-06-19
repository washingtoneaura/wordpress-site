<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Sendy_API class
 *
 * @package Hustle
 */

/**
 * Class Hustle_Sendy_API
 */
class Hustle_Sendy_API {
	const SUBSCRIBE         = 'subscribe';
	const SUBSCRIBER_COUNT  = 'api/subscribers/active-subscriber-count.php';
	const SUBSCRIBER_STATUS = 'api/subscribers/subscription-status.php';

	/**
	 * Base URL
	 *
	 * @var string
	 */
	private $base_url;
	/**
	 * Api key
	 *
	 * @var string
	 */
	private $api_key;
	/**
	 * List ID
	 *
	 * @var string
	 */
	private $list_id;

	/**
	 * Constructor
	 *
	 * @param string $base_url Base URL.
	 * @param string $api_key Api key.
	 * @param string $list_id List ID.
	 */
	public function __construct( $base_url, $api_key, $list_id ) {
		$this->base_url = trim( strval( $base_url ) );
		$this->api_key  = trim( strval( $api_key ) );
		$this->list_id  = trim( strval( $list_id ) );
	}

	/**
	 * Get endpoint URL
	 *
	 * @param string $endpoint Endpoint.
	 * @return type
	 */
	private function get_endpoint_url( $endpoint ) {
		return sprintf( '%s%s', trailingslashit( $this->base_url ), $endpoint );
	}

	/**
	 * Make request
	 *
	 * @param string $endpoint Endpoint.
	 * @param array  $args Args.
	 * @param string $verb Verbs.
	 *
	 * @return string|WP_Error Response body or WP_Error
	 */
	private function make_request( $endpoint, $args = array(), $verb = 'POST' ) {
		$url = $this->get_endpoint_url( $endpoint );

		if ( 'GET' === $verb ) {
			$response = wp_remote_get(
				$url,
				array(
					'timeout' => 10,
					'body'    => array_merge(
						array(
							'api_key' => $this->api_key,
							'list_id' => $this->list_id,
						),
						$args
					),
				)
			);

		} else {
			$response = wp_remote_post(
				$url,
				array(
					'timeout' => 10,
					'body'    => array_merge(
						array(
							'api_key' => $this->api_key,
							'list_id' => $this->list_id,
						),
						$args
					),
				)
			);
		}

		// logging data.
		$utils                     = Hustle_Provider_Utils::get_instance();
		$utils->last_url_request   = $url;
		$utils->last_data_received = $response;
		$utils->last_data_sent     = $args;

		if (
			is_wp_error( $response )
			|| wp_remote_retrieve_response_code( $response ) > 200
		) {
			return new WP_Error(
				'remote_error',
				esc_html__( 'Could not talk to your Sendy installation. Please check the installation URL!', 'hustle' )
			);
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Get subscriber count
	 *
	 * @return \WP_Error
	 */
	public function get_subscriber_count() {
		$response = $this->make_request( self::SUBSCRIBER_COUNT );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! is_numeric( $response ) ) {
			$error = $this->error_string( $response, true );
			return new WP_Error( $error['code'], $error['message'] );
		}

		return intval( $response );
	}

	/**
	 * Subscribe
	 *
	 * @param array $data Data.
	 * @return \WP_Error|boolean
	 */
	public function subscribe( $data ) {

		if ( empty( $data ) || ! isset( $data['email'] ) ) {
			return new WP_Error( 'invalid_data', __( 'Invalid or empty data supplied', 'hustle' ) );
		}

		$data['list'] = $this->list_id;
		$response     = $this->make_request( self::SUBSCRIBE, array_filter( $data ) );

		if ( ! is_wp_error( $response ) ) {
			return true;
		}

		return new WP_Error( 'remote_error', $this->error_string( $response ) );
	}

	/**
	 * Delete subscriber from the list
	 *
	 * @param string $email Email.
	 *
	 * @return bool
	 */
	public function delete_email( $email ) {
		$res = $this->make_request(
			'unsubscribe',
			array(
				'list'  => $this->list_id,
				'email' => $email,
			)
		);

		return ! is_wp_error( $res );
	}

	/**
	 * Get subscriber status
	 *
	 * @param string $email Email.
	 * @return \WP_Error
	 */
	public function subscriber_status( $email ) {

		$response = $this->make_request(
			self::SUBSCRIBER_STATUS,
			array_filter(
				array(
					'email' => $email,
				)
			),
			'POST'
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return new WP_Error( 'remote_user_status', $response );
	}

	/**
	 * Get error
	 *
	 * @param string $string String.
	 * @param bool   $return_with_code Return with code.
	 * @return array
	 */
	private function error_string( $string, $return_with_code = false ) {
		$strings = array(
			// Subscribe.
			'Some fields are missing.' => esc_html__( 'Some fields are missing.', 'hustle' ),
			'Invalid email address.'   => esc_html__( 'Invalid email address.', 'hustle' ),
			'Invalid list ID.'         => esc_html__( 'Invalid list ID.', 'hustle' ),
			'Already subscribed.'      => esc_html__( 'This email address has already subscribed.', 'hustle' ),
			// Subscriber count.
			'No data passed'           => esc_html__( 'No data passed', 'hustle' ),
			'API key not passed'       => esc_html__( 'API key not passed', 'hustle' ),
			'Invalid API key'          => esc_html__( 'Invalid API key', 'hustle' ),
			'List ID not passed'       => esc_html__( 'List ID not passed', 'hustle' ),
			'List does not exist'      => esc_html__( 'List does not exist', 'hustle' ),
		);

		$message = empty( $strings[ $string ] ) ? $string : $strings[ $string ];

		if ( ! $return_with_code ) {
			return $message;
		}

		// We need the non-translated code sometimes.
		return array(
			'code'    => $string,
			'message' => $message,
		);
	}
}
