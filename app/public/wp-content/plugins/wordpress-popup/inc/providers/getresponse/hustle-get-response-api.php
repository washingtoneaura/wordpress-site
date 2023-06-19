<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Get_Response_Api class
 *
 * @package Hustle
 */

/**
 * GetResponse API implementation
 *
 * Class Hustle_Get_Response_Api
 */
class Hustle_Get_Response_Api {

	/**
	 * Api key
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Endpoint
	 *
	 * @var string
	 */
	private $endpoint = 'https://api.getresponse.com/v3/';

	/**
	 * Constructs class with required data
	 *
	 * Hustle_Get_Response_Api constructor.
	 *
	 * @param string $api_key Api key.
	 * @param array  $args Args.
	 */
	public function __construct( $api_key, $args = array() ) {
		$this->api_key = $api_key;

		if ( isset( $args['endpoint'] ) ) {
			$this->endpoint = $args['endpoint'];
		}
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

			if ( 'contacts' === $action ) {
				$url = rawurldecode( $url );
			}
		} elseif ( ! empty( $args['body'] ) ) {
			$_args['body'] = wp_json_encode( $args['body'] );
		}

		$res = wp_remote_request( $url, $_args );

		// logging data.
		$utils                     = Hustle_Provider_Utils::get_instance();
		$utils->last_url_request   = $url;
		$utils->last_data_sent     = $_args;
		$utils->last_data_received = $res;

		if ( ! is_wp_error( $res ) && is_array( $res ) && $res['response']['code'] <= 204 ) {
			return json_decode( wp_remote_retrieve_body( $res ) );
		}

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		$err      = new WP_Error();
		$message  = $res['response']['message'];
		$message .= wp_remote_retrieve_body( $res );

		$err->add( $res['response']['code'], $message );
		return $err;
	}

	/**
	 * Sends rest GET request
	 *
	 * @param string $action Actions.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function get( $action, $args = array() ) {
		return $this->request( $action, 'GET', $args );
	}

	/**
	 * Sends rest POST request
	 *
	 * @param string $action Actions.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function post( $action, $args = array() ) {
		return $this->request( $action, 'POST', $args );
	}

	/**
	 * Sends rest DELETE request
	 *
	 * @param string $action Actions.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function delete( $action, $args = array() ) {
		return $this->request( $action, 'DELETE', $args );
	}

	/**
	 * Retrieves campaigns as array of objects
	 *
	 * @return array|WP_Error
	 */
	public function get_campaigns() {
		return $this->get(
			'campaigns',
			array(
				'name'    => array( 'CONTAINS' => '%' ),
				'perPage' => 1000,
			)
		);
	}

	/**
	 * Retrieves contactID
	 *
	 * @since 4.0
	 * @param array $data Data.
	 * @return string
	 */
	public function get_contact( $data ) {
		$res        = $this->get(
			'contacts',
			array(
				'query[email]'      => rawurlencode( $data['email'] ),
				'query[campaignId]' => $data['list_id'],
			)
		);
		$contact_id = '';

		if ( ! empty( $res[0] ) && ! empty( $res[0]->contactId ) ) {
			$contact_id = $res[0]->contactId;
		}

		return $contact_id;
	}

	/**
	 * Add new contact
	 *
	 * @param array $data Data.
	 * @return array|mixed|object|WP_Error
	 */
	public function subscribe( $data ) {
		$url  = 'contacts';
		$args = array(
			'body' => $data,
		);
		$res  = $this->post( $url, $args );

		return empty( $res ) ? __( 'Successful subscription', 'hustle' ) : $res;
	}

	/**
	 * Update contact
	 *
	 * @param string $contact_id Contact ID.
	 * @param array  $data New data.
	 * @return array|mixed|object|WP_Error
	 */
	public function update_contact( $contact_id, $data ) {
		$url  = 'contacts/' . $contact_id;
		$args = array(
			'body' => $data,
		);
		$res  = $this->post( $url, $args );

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
		$args       = array(
			'email'   => $email,
			'list_id' => $list_id,
		);
		$contact_id = $this->get_contact( $args );

		if ( empty( $contact_id ) ) {
			return false;
		}
		// They don't have the ability to unsubscribe.
		// To remove is their official reply from their dev.
		$res = $this->delete( 'contacts/' . $contact_id );

		return ! is_wp_error( $res );
	}

	/**
	 * Get custom fields
	 *
	 * @return array
	 */
	public function get_custom_fields() {
		$args = array( 'fields' => 'name, type' );
		$res  = $this->get( 'custom-fields', $args );

		return $res;
	}

	/**
	 * Add custom field
	 *
	 * @param array $custom_field Custom field.
	 **/
	public function add_custom_field( $custom_field ) {
		$url  = 'custom-fields';
		$args = array(
			'body' => $custom_field,
		);
		$res  = $this->post( $url, $args );

		if ( is_wp_error( $res ) ) {
			return $res;
		}
		if ( ! empty( $res ) && ! empty( $res->customFieldId ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return $res->customFieldId;// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		return false;
	}
}
