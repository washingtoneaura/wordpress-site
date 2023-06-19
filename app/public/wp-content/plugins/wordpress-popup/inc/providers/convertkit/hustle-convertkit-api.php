<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_ConvertKit_Api class
 *
 * @package Hustle
 */

/**
 * ConvertKit API
 *
 * @class Hustle_ConvertKit_Api
 **/
class Hustle_ConvertKit_Api {

	/**
	 * Api key
	 *
	 * @var string
	 */
	private $api_key;
	/**
	 * Api secret
	 *
	 * @var string
	 */
	private $api_secret;
	/**
	 * Endpoint
	 *
	 * @var string
	 */
	private $endpoint = 'https://api.convertkit.com/v3/';

	/**
	 * Constructs class with required data
	 *
	 * Hustle_ConvertKit_Api constructor.
	 *
	 * @param string $api_key Api key.
	 * @param string $api_secret Api secret.
	 */
	public function __construct( $api_key, $api_secret = '' ) {
		$this->api_key    = $api_key;
		$this->api_secret = $api_secret;
	}

	/**
	 * Sends request to the endpoint url with the provided $action
	 *
	 * @param string $action rest action.
	 * @param string $verb Verb.
	 * @param array  $args Args.
	 * @return object|WP_Error
	 */
	private function request( $action, $verb = 'GET', $args = array() ) {
		$url = trailingslashit( $this->endpoint ) . $action;

		$_args = array(
			'method'  => $verb,
			'headers' => array(
				'X-Auth-Token' => 'api-key ' . $this->api_key,
				'Content-Type' => 'application/json;charset=utf-8',
			),
		);

		if ( 'GET' === $verb ) {
			$url .= ( '?' . http_build_query( $args ) );
		} else {
			$_args['body'] = wp_json_encode( $args['body'] );
		}

		$res = wp_remote_request( $url, $_args );

		// logging data.
		$utils                     = Hustle_Provider_Utils::get_instance();
		$utils->last_url_request   = $url;
		$utils->last_data_sent     = $_args;
		$utils->last_data_received = $res;

		if ( ! is_wp_error( $res ) && is_array( $res ) ) {

			if ( $res['response']['code'] <= 204 ) {
				return json_decode( wp_remote_retrieve_body( $res ) );
			}

			$err = new WP_Error();
			$err->add( $res['response']['code'], $res['response']['message'] );
			return $err;
		}

		return $res;
	}

	/**
	 * Sends rest GET request
	 *
	 * @param string $action Action.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function get( $action, $args = array() ) {
		return $this->request( $action, 'GET', $args );
	}

	/**
	 * Sends rest POST request
	 *
	 * @param string $action Action.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function post( $action, $args = array() ) {
		return $this->request( $action, 'POST', $args );
	}

	/**
	 * Sends rest PUT request
	 *
	 * @param string $action Action.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function put( $action, $args = array() ) {
		return $this->request( $action, 'PUT', $args );
	}

	/**
	 * Retrieves ConvertKit forms as array of objects
	 *
	 * @return array|WP_Error
	 */
	public function get_forms() {
		$forms = $this->get(
			'forms',
			array(
				'api_key' => $this->api_key,
			)
		);
		if ( is_wp_error( $forms ) ) {
			return $forms;
		}
		if ( ! isset( $forms->forms ) ) {
			return new WP_Error( 'forms_not_found', __( 'Not found forms with this api key.', 'hustle' ) );
		}
		return $forms->forms;
	}

	/**
	 * Retrieves ConvertKit subscribers as array of objects
	 *
	 * @return array|WP_Error
	 */
	public function get_subscribers() {
		$subscribers = $this->get(
			'subscribers',
			array(
				'api_secret' => $this->api_secret,
			)
		);
		if ( is_wp_error( $subscribers ) ) {
			return $subscribers;
		}
		return $subscribers->subscribers;
	}

	/**
	 * Retrieves ConvertKit form's custom fields as array of objects
	 *
	 * @return array|WP_Error
	 */
	public function get_form_custom_fields() {
		return $this->get(
			'custom_fields',
			array(
				'api_key' => $this->api_key,
			)
		)->custom_fields;
	}

	/**
	 * Add new custom fields to subscription
	 *
	 * @param array $field_data Fields data.
	 * @return array|mixed|object|WP_Error
	 */
	public function create_custom_fields( $field_data ) {
		$url  = 'custom_fields';
		$args = array(
			'body' => $field_data,
		);
		$res  = $this->post( $url, $args );

		return empty( $res ) ? __( 'Successfully added custom field', 'hustle' ) : $res;
	}

	/**
	 * Add new subscriber
	 *
	 * @param string $form_id Form ID.
	 * @param array  $data Data.
	 * @return array|mixed|object|WP_Error
	 */
	public function subscribe( $form_id, $data ) {
		$url  = 'forms/' . $form_id . '/subscribe';
		$args = array(
			'body' => $data,
		);
		$res  = $this->post( $url, $args );

		return empty( $res ) ? __( 'Successful subscription', 'hustle' ) : $res;
	}

	/**
	 * Update subscriber
	 *
	 * @since 4.0
	 *
	 * @param string $id ID.
	 * @param array  $data Data.
	 * @return array|mixed|object|WP_Error
	 */
	public function update_subscriber( $id, $data ) {
		$url                = 'subscribers/' . $id;
		$data['api_secret'] = $this->api_secret;
		$args               = array(
			'body' => $data,
		);
		$res                = $this->put( $url, $args );

		return empty( $res ) ? __( 'Successful subscription', 'hustle' ) : $res;
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
		$args = array(
			'body' => array(
				'email'      => $email,
				'form'       => $list_id, // Actually they don't support this argument and have ability only cancel subscriptions to all forms.
				'api_secret' => $this->api_secret,
			),
		);
		$res  = $this->put( 'unsubscribe', $args );

		return ! is_wp_error( $res );
	}

	/**
	 * Verify if an email is already a subscriber.
	 *
	 * @param (string) $email Email.
	 *
	 * @return (object) Returns data of existing subscriber if exist otherwise false.
	 **/
	public function is_subscriber( $email ) {
		$url  = 'subscribers';
		$args = array(
			'api_key'       => $this->api_key,
			'api_secret'    => $this->api_secret,
			'email_address' => $email,
		);

		$res = $this->get( $url, $args );

		return ! is_wp_error( $res ) && ! empty( $res->subscribers ) ? array_shift( $res->subscribers ) : false;
	}

	/**
	 * Verify if an email is already a subscriber in a form.
	 *
	 * @param string  $email Email.
	 * @param integer $form_id Form ID.
	 *
	 * @return boolean True if the subscriber exists, otherwise false.
	 **/
	public function is_form_subscriber( $email, $form_id ) {
		$url   = 'forms/' . $form_id . '/subscriptions';
		$args  = array(
			'api_secret' => $this->api_secret,
		);
		$exist = false;
		$res   = $this->get( $url, $args );

		$utils                     = Hustle_Provider_Utils::get_instance();
		$utils->last_data_received = $res;
		$utils->last_url_request   = trailingslashit( $this->endpoint ) . $url;
		$utils->last_data_sent     = $args;

		if ( is_wp_error( $res ) ) {
			Hustle_Provider_Utils::maybe_log( 'There was an error retrieving the subscribers from Convertkit: ' . $res->get_error_message() );
			return false;
		} elseif ( empty( $res->subscriptions ) ) {
			return false;
		} else {
			$subscriptions  = wp_list_pluck( $res->subscriptions, 'subscriber' );
			$subscribers    = wp_list_pluck( $subscriptions, 'email_address' );
			$subscribers_id = wp_list_pluck( $subscriptions, 'id' );
			$exist          = in_array( $email, $subscribers, true ) ? $subscribers_id[ array_search( $email, $subscribers, true ) ] : false;
			if ( false === $exist && $res->total_pages > 1 ) {
				for ( $i = 2; $i <= $res->total_pages; $i++ ) {

					$url                       = 'forms/' . $form_id . '/subscriptions';
					$args                      = array(
						'api_secret' => $this->api_secret,
						'page'       => $i,
					);
					$res                       = $this->get( $url, $args );
					$utils                     = Hustle_Provider_Utils::get_instance();
					$utils->last_data_received = $res;
					$utils->last_url_request   = trailingslashit( $this->endpoint ) . $url;
					$utils->last_data_sent     = $args;

					$subscriptions  = wp_list_pluck( $res->subscriptions, 'subscriber' );
					$subscribers    = wp_list_pluck( $subscriptions, 'email_address' );
					$subscribers_id = wp_list_pluck( $subscriptions, 'id' );

					if ( in_array( $email, $subscribers, true ) ) {
						$exist = $subscribers_id[ array_search( $email, $subscribers, true ) ];
						return $exist;
					}
				}
			} else {
				return $exist;
			}
		}

		return $exist;
	}
}
