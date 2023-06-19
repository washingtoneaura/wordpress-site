<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Mailchimp_Api class
 *
 * @package Hustle
 */

/**
 * Mailchimp API
 *
 * @class Hustle_Mailchimp_Api
 **/
class Hustle_Mailchimp_Api {

	/**
	 * Api key
	 *
	 * @var string
	 */
	private $api_key;
	/**
	 * Data center
	 *
	 * @var string
	 */
	private $data_center;
	/**
	 * User
	 *
	 * @var string
	 */
	private $user;

	/**
	 * The <dc> part of the URL corresponds to the data center for your account. For example, if the last part of your Mailchimp API key is us6, all API endpoints for your account are available at https://us6.api.mailchimp.com/3.0/.
	 *
	 * @var string
	 */
	private $endpoint = 'https://<dc>.api.mailchimp.com/3.0/';

	/**
	 * Constructs class with required data
	 *
	 * Hustle_Mailchimp_Api constructor.
	 *
	 * @param string $api_key Api key.
	 * @param string $data_center Data center.
	 */
	public function __construct( $api_key, $data_center ) {
		$this->api_key     = $api_key;
		$this->data_center = $data_center;
		$this->endpoint    = str_replace( '<dc>', $data_center, $this->endpoint );
		$this->user        = wp_get_current_user()->display_name;
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
				'Authorization' => 'apikey ' . $this->api_key,
				'Content-Type'  => 'application/json;charset=utf-8',
			),
		);

		if ( 'GET' === $verb ) {
			$url .= ( '?' . http_build_query( $args ) );
		} elseif ( ! empty( $args['body'] ) ) {
			$_args['body'] = wp_json_encode( $args['body'] );
		}

		$res = wp_remote_request( $url, $_args );

		$utils                     = Hustle_Provider_Utils::get_instance();
		$utils->last_url_request   = $url;
		$utils->last_data_received = $res;
		$utils->last_data_sent     = $_args;

		if ( ! is_wp_error( $res ) && is_array( $res ) ) {
			if ( $res['response']['code'] <= 204 ) {
				return json_decode( wp_remote_retrieve_body( $res ) );
			}

			$err = new WP_Error();
			$err->add( $res['response']['code'], $res['response']['message'], $res['body'] );
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
	 * Sends rest GET request
	 *
	 * @param string $action Action.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function delete( $action, $args = array() ) {
		return $this->request( $action, 'DELETE', $args );
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
	 * Sends rest PATCH request
	 *
	 * @param string $action Action.
	 * @param array  $args Args.
	 * @return array|mixed|object|WP_Error
	 */
	private function patch( $action, $args = array() ) {
		return $this->request( $action, 'PATCH', $args );
	}

	/**
	 * Get User Info for the current API KEY
	 *
	 * @param array $fields Fields.
	 * @return array|mixed|object|WP_Error
	 */
	public function get_info( $fields = array() ) {
		if ( empty( $fields ) ) {
			$fields = array( 'account_id', 'account_name', 'email' );
		}

		return $this->request(
			'',
			'GET',
			array(
				'fields' => implode( ',', $fields ),
			)
		);
	}

	/**
	 * Gets all the lists
	 *
	 * @param int $offset - current total lists to show.
	 * @param int $count - current total lists to show.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_lists( $offset = 50, $count = 10 ) {
		return $this->get(
			'lists',
			array(
				'user'   => $this->user . ':' . $this->api_key,
				'offset' => $offset,
				'count'  => $count,
			)
		);
	}

	/**
	 * Gets all the groups under a list
	 *
	 * @param string $list_id List ID.
	 * @param int    $total Total.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_interest_categories( $list_id, $total = 10 ) {
		return $this->get(
			'lists/' . $list_id . '/interest-categories',
			array(
				'user'  => $this->user . ':' . $this->api_key,
				'count' => $total,
			)
		);
	}

	/**
	 * Gets all the GDPR fields under a list
	 *
	 * @param string $list_id List ID.
	 *
	 * @return array
	 */
	public function get_gdpr_fields( $list_id ) {
		$gdpr_fieds = array();
		$members    = $this->get_members( $list_id );
		if ( ! $members ) {
			$email = 'dummy@incsub.com';
			$args  = array(
				'email_address' => $email,
				'status'        => 'unsubscribed',
			);
			$this->subscribe( $list_id, $args );
			$members = $this->get_members( $list_id );
			$this->delete_email( $list_id, $email );
		}

		if ( empty( $members ) || ! is_array( $members ) || empty( $members[0]->marketing_permissions ) || ! is_array( $members[0]->marketing_permissions ) ) {
			return $gdpr_fieds;
		}

		foreach ( $members[0]->marketing_permissions as $value ) {
			if ( ! isset( $value->marketing_permission_id ) || ! isset( $value->text ) ) {
				continue;
			}
			$gdpr_fieds[ $value->marketing_permission_id ] = $value->text;
		}

		return $gdpr_fieds;
	}

	/**
	 * Get members by list ID
	 *
	 * @param string $list_id List ID.
	 * @return array
	 */
	private function get_members( $list_id ) {
		$data = $this->get(
			'lists/' . $list_id . '/members',
			array(
				'user' => $this->user . ':' . $this->api_key,
			)
		);

		return $data && is_object( $data ) && ! empty( $data->members ) ? $data->members : array();
	}

	/**
	 * Gets all the interests under a group list
	 *
	 * @param string $list_id List ID.
	 * @param string $category_id Category ID.
	 * @param int    $total Total.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_interests( $list_id, $category_id, $total = 10 ) {
		return $this->get(
			'lists/' . $list_id . '/interest-categories/' . $category_id . '/interests',
			array(
				'user'  => $this->user . ':' . $this->api_key,
				'count' => $total,
			)
		);
	}

	/**
	 * Gets all the tags/static segments on a list
	 *
	 * @param string $list_id List ID.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_tags( $list_id ) {
		return $this->get(
			'lists/' . $list_id . '/segments',
			array(
				'count' => 1000,
				'user'  => $this->user . ':' . $this->api_key,
				'type'  => 'static',
			)
		);
	}

	/**
	 * Check member email address if already existing
	 *
	 * @param string $list_id List ID.
	 * @param string $email Email.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function check_email( $list_id, $email ) {
		$md5_email = md5( strtolower( $email ) );
		return $this->get(
			'lists/' . $list_id . '/members/' . $md5_email,
			array(
				'user' => $this->user . ':' . $this->api_key,
			)
		);
	}

	/**
	 * Delete detail of member
	 *
	 * @param string $list_id List ID.
	 * @param string $email Email.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function delete_email( $list_id, $email ) {
		$md5_email = md5( strtolower( $email ) );
		$this->update_subscription_patch( $list_id, $email, array( 'status' => 'unsubscribed' ) );
		return $this->delete( 'lists/' . $list_id . '/members/' . $md5_email );
	}

	/**
	 * Add custom field for list
	 *
	 * @param string $list_id List ID.
	 * @param array  $field_data Field data.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function add_custom_field( $list_id, $field_data ) {
		return $this->post(
			'lists/' . $list_id . '/merge-fields',
			array(
				'body' => $field_data,
			)
		);
	}

	/**
	 * Get custom fields for list
	 *
	 * @param string $list_id List ID.
	 * @param int    $count Count.
	 * @param int    $offset Offset.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_custom_fields( $list_id, $count = PHP_INT_MAX, $offset = 0 ) {
		return $this->get(
			'lists/' . $list_id . '/merge-fields',
			array(
				'user'   => $this->user . ':' . $this->api_key,
				'offset' => $offset,
				'count'  => $count,
			)
		);
	}

	/**
	 * Add new subscriber
	 *
	 * @param string $list_id List ID.
	 * @param array  $data Data.
	 * @return array|mixed|object|WP_Error
	 * @throws Exception Something went wrong.
	 */
	public function subscribe( $list_id, $data ) {
		$res = $this->post(
			'lists/' . $list_id . '/members',
			array(
				'body' => $data,
			)
		);

		if ( ! is_wp_error( $res ) ) {
			return $res;
		} else {
			if ( strpos( $res->get_error_data(), '"Forgotten Email Not Subscribed"' ) ) {
				$error  = __( "This contact was previously removed from this list via Mailchimp dashboard. To rejoin, they'll need to sign up using a Mailchimp native form.", 'hustle' );
				$error .= ' ' . __( 'Subscriber email: ', 'hustle' ) . $data['email_address'];
			} else {
				$error      = implode( ', ', $res->get_error_messages() );
				$error     .= __( 'Something went wrong.', 'hustle' );
				$error_data = $res->get_error_data();
				if ( ! empty( $error_data ) ) {
					$error .= ' ' . $error_data;
				}
			}
			throw new Exception( $error );
		}
	}

	/**
	 * Update subscription
	 *
	 * @param string $list_id - the list id.
	 * @param string $email - the email.
	 * @param array  $data - data.
	 *
	 * @return array|mixed|object|WP_Error
	 * @throws Exception Something went wrong.
	 */
	public function update_subscription( $list_id, $email, $data ) {
		$md5_email = md5( strtolower( $email ) );
		$res       = $this->put(
			'lists/' . $list_id . '/members/' . $md5_email,
			array(
				'body' => $data,
			)
		);

		if ( ! is_wp_error( $res ) ) {
			// returns object on success @since 4.0.2 as we need it for GDPR.
			return $res;
		}
	}

	/**
	 * Update subscription
	 *
	 * @param string $list_id - the list id.
	 * @param string $email - the email.
	 * @param array  $data - array.
	 *
	 * @return array|mixed|object|WP_Error
	 * @throws Exception Something went wrong.
	 */
	public function update_subscription_patch( $list_id, $email, $data ) {
		$md5_email = md5( strtolower( $email ) );
		if ( ! empty( $data['tags'] ) && is_array( $data['tags'] ) ) {
			foreach ( $data['tags'] as $tag_id ) {
				$res = $this->post(
					'lists/' . $list_id . '/segments/' . $tag_id . '/members/',
					array(
						'body' => array(
							'email_address' => strtolower( $email ),
						),
					)
				);
			}
			unset( $data['tags'] );
		}
		$res = $this->patch(
			'lists/' . $list_id . '/members/' . $md5_email,
			array(
				'body' => $data,
			)
		);

		$error = __( "Couldn't update the user", 'hustle' );
		if ( ! is_wp_error( $res ) ) {
			return __( 'User updated', 'hustle' );
		} else {
			throw new Exception( $error );
		}
	}

}
