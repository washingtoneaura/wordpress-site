<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Send In Blue API Helper
 *
 * Modification from https://github.com/mailin-api/mailin-api-php/tree/master/V2.0
 *
 * @package Hustle
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Hustle_SendinBlue_Api' ) ) :
	/**
	 * Hustle_SendinBlue_Api class
	 */
	class Hustle_SendinBlue_Api {


		/**
		 * SendinBlue API Url
		 *
		 * @since 4.0.2
		 * @var array
		 */
		private $endpoint = 'https://api.sendinblue.com/v3/';

		/**
		 * SendinBlue API Url
		 *
		 * @since 4.0.2
		 * @var array
		 */
		private $migrate_endpoint = 'https://api.sendinblue.com/v2.0/account/generateapiv3key';

		/**
		 * API Key
		 *
		 * @since 4.0.2
		 * @var string
		 */
		private $api_key = '';

		/**
		 * Instances of sendinblue
		 *
		 * @var array
		 */
		private static $instances = array();

		/**
		 * Version of sendinblue API Wrapper
		 *
		 * @var string
		 */
		const HUSTLE_PROVIDER_SENDINBLUE_VERSION = '1.0';

		/**
		 * Construct the class
		 *
		 * Here constructor is private becase we
		 * want to force the `boot()` method to
		 * initate the class and maitain instances
		 *
		 * @param string $api_key Api key.
		 * @throws Exception Missing required API Credential.
		 */
		private function __construct( $api_key ) {
			if ( ! $api_key ) {
				throw new Exception( __( 'Missing required API Credential', 'hustle' ) );
			}
			$this->api_key = $api_key;
		}

		/**
		 * Get singleton
		 *
		 * @since 4.0.2
		 *
		 * @param string $api_key Api key.
		 *
		 * @return Hustle_SendinBlue_Api|null
		 */
		public static function boot( $api_key ) {

			$instance_key = md5( $api_key );

			if ( ! isset( self::$instances[ $instance_key ] ) ) {
				self::$instances[ $instance_key ] = new static( $api_key );
			}

			return self::$instances[ $instance_key ];
		}

		/**
		 * Add custom user agent on request
		 *
		 * @since 4.0.1
		 *
		 * @param string $user_agent User agent.
		 * @return string
		 */
		public function filter_user_agent( $user_agent ) {
			$user_agent .= ' HustleSendinBlue/' . self::HUSTLE_PROVIDER_SENDINBLUE_VERSION;

			/**
			 * Filter user agent to be used by sendinblue api
			 *
			 * @since 1.1
			 *
			 * @param string $user_agent current user agent
			 */
			$user_agent = apply_filters( 'hustle_provider_sendinblue_api_user_agent', $user_agent );

			return $user_agent;
		}

		/**
		 * Send request to API
		 *
		 * @since 4.0.2
		 *
		 * @param string $action Action.
		 * @param array  $args Args.
		 * @param string $verb Verb.
		 * @param bool   $migrate Migrate.
		 * @return Mixed|Array|String|
		 * @throws Exception Failed to processing request.
		 */
		private function request( $action, $args, $verb = 'GET', $migrate = false ) {

			// Adding extra user agent for wp remote request.
			add_filter( 'http_headers_useragent', array( $this, 'filter_user_agent' ) );

			$url = esc_url( trailingslashit( $this->endpoint ) . $action );

			if ( true === $migrate ) {
				$url = esc_url( trailingslashit( $this->migrate_endpoint ) );
			}

			/**
			 * Filter sendinblue url to be used on sending api request
			 *
			 * @since 1.1
			 *
			 * @param string $url  full url with scheme
			 * @param string $verb `GET` `POST` `PUT` `DELETE` `PATCH`
			 * @param array  $args argument sent to this function
			 */
			$url = apply_filters( 'hustle_provider_sendinblue_api_url', $url, $verb, $args );

			$headers = array(
				'api-key'      => $this->api_key,
				'content-type' => 'application/json',
			);
			/**
			 * Filter sendinblue headers to sent on api request
			 *
			 * @since 1.1
			 *
			 * @param array  $headers
			 * @param string $verb `GET` `POST` `PUT` `DELETE` `PATCH`
			 * @param string $url  full url with scheme
			 * @param array  $args argument sent to this function
			 */
			$headers = apply_filters( 'hustle_provider_sendinblue_api_request_headers', $headers, $verb, $url, $args );

			$_args = array(
				'method'  => $verb,
				'headers' => $headers,
			);

			$request_data = $args;

			/**
			 * Filter sendinblue request data to be used on sending api request
			 *
			 * @since 1.1
			 *
			 * @param array  $request_data it will be `http_build_query`-ed when `GET` or `wp_json_encode`-ed otherwise
			 * @param string $verb         `GET` `POST` `PUT` `DELETE` `PATCH`
			 * @param string $url         requested path resource
			 */
			$args = apply_filters( 'hustle_provider_sendinblue_api_request_data', $request_data, $verb, $url );

			if ( 'GET' === $verb ) {
				$url .= ( '?' . http_build_query( $args ) );
			} else {
				$_args['body'] = wp_json_encode( $args );
			}

			$res = wp_remote_request( $url, $_args );

			// logging data.
			$utils                     = Hustle_Provider_Utils::get_instance();
			$utils->last_url_request   = $url;
			$utils->last_data_sent     = $_args;
			$utils->last_data_received = $res;

			$wp_response = $res;
			remove_filter( 'http_headers_useragent', array( $this, 'filter_user_agent' ) );

			if ( is_wp_error( $res ) || ! $res ) {
				throw new Exception(
					__( 'Failed to process request, make sure your Webhook URL is correct and your server has internet connection.', 'hustle' )
				);
			}

			$body = wp_remote_retrieve_body( $res );

			// probably silent mode.
			if ( ! empty( $body ) ) {
				$res = json_decode( $body );
			}

			if ( isset( $wp_response['response']['code'] ) ) {
				$status_code = $wp_response['response']['code'];
				$msg         = '';
				if ( $status_code >= 400 ) {
					if ( isset( $wp_response['response']['message'] ) ) {
						$msg = $wp_response['response']['message'];
					}

					if ( ! is_null( $res ) && is_object( $res ) && isset( $res->message ) ) {
						$msg = $res->message;
					}

					/* translators: error message */
					throw new Exception( sprintf( __( 'Failed to processing request : %s', 'hustle' ), $msg ) );
				}
			}

			$response = $res;

			/**
			 * Filter sendinblue api response returned to addon
			 *
			 * @since 4.0.2
			 *
			 * @param mixed          $response    original wp remote request response or decoded body if available
			 * @param string         $body        original content of http response's body
			 * @param array|WP_Error $wp_response original wp remote request response
			 */
			$res = apply_filters( 'hustle_sendinblue_api_response', $response, $body, $wp_response );

			return $res;
		}

		/**
		 * Prepare a get request
		 *
		 * @param string $endpoint Endpoint.
		 * @param array  $args Args.
		 * @since 4.0.2
		 */
		private function get( $endpoint, $args ) {
			return $this->request( $endpoint, $args, 'GET' );
		}

		/**
		 * Prepare a post request
		 *
		 * @param string $endpoint Endpoint.
		 * @param array  $args Args.
		 * @param bool   $migrate Migrate.
		 * @since 4.0.2
		 */
		private function post( $endpoint, $args, $migrate = false ) {
			return $this->request( $endpoint, $args, 'POST', $migrate );
		}

		/**
		 * Prepare a put request
		 *
		 * @param string $endpoint Endpoint.
		 * @param array  $args Args.
		 * @since 4.0.2
		 */
		private function put( $endpoint, $args ) {
			return $this->request( $endpoint, $args, 'PUT' );
		}

		/**
		 * Get account details
		 *
		 * @since 4.0.2
		 */
		public function get_account() {
			return $this->get( 'account', array() );
		}

		/**
		 * Get sendinblue lists
		 *
		 * @since 4.0.2
		 *
		 * @param array $args Args.
		 * @return array|mixed|object
		 */
		public function get_lists( $args ) {
			return $this->get( 'contacts/lists', $args );
		}

		/**
		 * Create contact
		 *
		 * @since 4.0.2
		 *
		 * @param array $data Data.
		 *
		 * @return array|mixed|object
		 */
		public function create_contact( $data ) {
			return $this->post( 'contacts', $data );
		}

		/**
		 * Update contact
		 *
		 * @since 4.0.2
		 *
		 * @param array $data Data.
		 * @return array|mixed|object
		 */
		public function update_contact( $data ) {
			return $this->put( 'contacts/' . $data['email'], $data );
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
			$endpoint = 'contacts/lists/' . $list_id . '/contacts/remove';
			$res      = $this->post( $endpoint, array( 'emails' => array( $email ) ) );

			return ! is_wp_error( $res ) && ! empty( $res->contacts->success );
		}

		/**
		 * Check if Contact exists
		 *
		 * @since 4.0.2
		 *
		 * @param string $email Email.
		 *
		 * @return array|mixed|object
		 */
		public function get_contact( $email ) {
			return $this->get(
				'contacts/' .
				rawurlencode( trim( $email ) ),
				array()
			);
		}

		/**
		 * Get custom fields
		 *
		 * @since 4.0.2
		 *
		 * @return array|mixed|object
		 */
		public function get_attributes() {
			return $this->get(
				'contacts/attributes',
				array()
			);
		}

		/**
		 * Add custom fields
		 *
		 * @since 4.0.2
		 *
		 * @param string $name Name.
		 * @param string $category Category.
		 * @param array  $data Data.
		 *
		 * @return array|mixed|object
		 */
		public function create_attributes( $name, $category = 'normal', $data = array() ) {
			return $this->post(
				'contacts/attributes/' .
				rawurlencode( trim( $category ) ) . '/' .
				rawurlencode( trim( $name ) ),
				$data
			);
		}

		/**
		 * Slient migration to V3
		 *
		 * @since 4.0.2
		 *
		 * @param string $name Name.
		 *
		 * @return array|mixed|object
		 */
		public function migrate_to_v3( $name ) {
			return $this->post( ' ', $name, true );
		}
	}
endif;


