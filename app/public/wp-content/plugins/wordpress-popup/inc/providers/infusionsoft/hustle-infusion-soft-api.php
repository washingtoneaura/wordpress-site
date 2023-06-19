<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Opt_In_Infusionsoft_Api class
 *
 * @package Hustle
 */

if ( class_exists( 'Opt_In_Infusionsoft_Api' ) ) {
	return;
}

/**
 * Class Opt_In_Infusionsoft_Api
 */
class Opt_In_Infusionsoft_Api {

	/**
	 * Api key
	 *
	 * @var string $api_key
	 */
	private $api_key;

	/**
	 * App name
	 *
	 * @var string $app_name
	 */
	private $app_name;

	/**
	 * Class instance
	 *
	 * @var object $xml SimpleXMLElement
	 **/
	public $xml;

	/**
	 * Params node
	 *
	 * @var object $params SimpleXMLElement
	 **/
	public $params;

	/**
	 * Struct node
	 *
	 * @var object $struct SimpleXMLElement
	 **/
	public $struct;

	/**
	 * Store the values getting from custom field request.
	 *
	 * @var array
	 */
	public $custom_fields_with_data_type;

	/**
	 * Opt_In_Infusionsoft_Api constructor.
	 *
	 * @param string $api_key Api key.
	 * @param string $app_name App name.
	 */
	public function __construct( $api_key, $app_name ) {
		$this->api_key  = $api_key;
		$this->app_name = $app_name;
		return $this;
	}

	/**
	 * Set method
	 *
	 * @param string $method_name Method name.
	 */
	public function set_method( $method_name ) {
		$xml       = '<?xml version="1.0" encoding="UTF-8"?><methodCall></methodCall>';
		$this->xml = new SimpleXMLElement( $xml );
		$this->xml->addChild( 'methodName', $method_name );
		$this->params = $this->xml->addChild( 'params' );
		$this->set_param( $this->api_key );
		$this->struct = false;
	}

	/**
	 * Set param
	 *
	 * @param mixed  $value Value.
	 * @param string $type Type.
	 * @return object
	 */
	public function set_param( $value, $type = 'string' ) {
		$param = $this->params->addChild( 'param' );
		return $param->addChild( 'value' )->addChild( $type, $value );
	}

	/**
	 * Set member
	 *
	 * @param string $name Name.
	 * @param string $value Value.
	 * @param string $type Type.
	 */
	public function set_member( $name, $value = '', $type = 'string' ) {
		if ( ! $this->struct ) {
			$this->struct = $this->params->addChild( 'param' )->addChild( 'value' )->addChild( 'struct' );
		}

		$member = $this->struct->addChild( 'member' );
		$member->addChild( 'name', $name );
		if ( ! empty( $value ) ) {
			$member->addChild( 'value' )->addChild( $type, $value );
		}
	}

	/**
	 * Contains the list of built-in custom fields.
	 **/
	public function builtin_custom_fields() {
		$custom_fields = array(
			'Anniversary',
			'AssistantName',
			'AssistantPhone',
			'Birthday',
			'City',
			'City2',
			'City3',
			'Company',
			'CompanyID',
			'ContactNotes',
			'ContactType',
			'Country',
			'Country2',
			'Country3',
			'Email',
			'EmailAddress2',
			'EmailAddress3',
			'Fax1',
			'Fax1Type',
			'Fax2',
			'Tax2Type',
			'FirstName',
			'JobTitle',
			'Language',
			'LastName',
			'MiddleName',
			'Nickname',
			'Password',
			'Phone1',
			'Phone1Ext',
			'Phone1Type',
			'Phone2',
			'Phone2Ext',
			'Phone2Type',
			'PostalCode',
			'PostalCode2',
			'ReferralCode',
			'SpouseName',
			'State',
			'State2',
			'StreetAddress1',
			'StreetAddress2',
			'Suffix',
			'TimeZone',
			'Title',
			'Website',
			'ZipFour1',
			'ZipFour2',
		);

		return $custom_fields;
	}

	/**
	 * Get the custom fields at InfusionSoft account.
	 **/
	public function get_custom_fields() {
		$this->set_method( 'DataService.query' );
		$this->set_param( 'DataFormField' );
		$this->set_param( 1000, 'int' );
		$this->set_param( 0, 'int' );
		$this->set_member( 'FormId', '-1' );

		$data = $this->params->addChild( 'param' )->addChild( 'value' )->addChild( 'array' )->addChild( 'data' );
		$data->addChild( 'value' )->addChild( 'string', 'Name' );

		$res = $this->request( $this->xml->asXML() );
		if ( is_wp_error( $res ) ) {
			return $res;
		}

		$builtin_custom_fields              = $this->builtin_custom_fields();
		$extra_custom_fields                = array();
		$this->custom_fields_with_data_type = array();

		foreach ( $res->get_value()->data->value as $custom_field ) {
			$name  = '';
			$value = '';

			foreach ( $custom_field->struct->member as $info ) {
				if ( 'Name' === (string) $info->name ) {
					$extra_custom_fields[] = (string) $info->value;
					$name                  = (string) $info->value;
				}

				if ( 'DataType' === (string) $info->name ) {
					$value = (int) $info->value->i4;
				}
			}

			$this->custom_fields_with_data_type[ $name ] = $value;
		}

		$custom_fields = array_merge( $builtin_custom_fields, $extra_custom_fields );

		return $custom_fields;
	}

	/**
	 * Get Custom Field Groups for getting HeaderId for creating new Custom Field
	 *
	 * @return type
	 */
	private function get_custom_field_groups() {
		$this->set_method( 'DataService.query' );
		$this->set_param( 'DataFormGroup' );
		$this->set_param( 1000, 'int' );
		$this->set_param( 0, 'int' );

		$this->set_member( 'Id', '%' );

		$data = $this->params->addChild( 'param' )->addChild( 'value' )->addChild( 'array' )->addChild( 'data' );
		$data->addChild( 'value' )->addChild( 'string', 'Id' );
		$data->addChild( 'value' )->addChild( 'string', 'Name' );

		$res = $this->request( $this->xml->asXML() );
		if ( is_wp_error( $res ) ) {
			return $res;
		}

		return $res->response_to_array();
	}

	/**
	 * Create custom field at InfusionSoft account.
	 *
	 * @param string $name Name.
	 **/
	public function add_custom_field( $name ) {
		$headers = $this->get_custom_field_groups();
		if ( is_wp_error( $headers ) ) {
			return $headers;
		}
		$cf_group_id = array_search( 'Custom Fields', $headers, true );
		$header_id   = false !== $cf_group_id ? $cf_group_id : array_keys( $headers )[0];
		$this->set_method( 'DataService.addCustomField' );
		$this->set_param( 'Contact' );
		$this->set_param( $name );
		$this->set_param( 'Text' );
		$this->set_param( $header_id, 'int' );

		$res = $this->request( $this->xml->asXML() );
		if ( is_wp_error( $res ) ) {
			return $res;
		}

		return $res->get_value();
	}

	/**
	 * Add new contact to infusionsoft and return contact ID on success or WP_Error.
	 *
	 * @param array $contact            An array of contact details.
	 **/
	public function add_contact( $contact ) {
		if ( false === $this->email_exist( $contact['Email'] ) ) {
			$this->opt_in_email( $contact['Email'] ); // First optin the email.

			$this->set_method( 'ContactService.add' );

			// According to their documentations custom fields should be prefixed with "_".
			foreach ( $contact as $key => $value ) {
				if ( ! in_array( $key, $this->builtin_custom_fields(), true ) ) {
					$key = '_' . $key;
				}

				$this->set_member( $key, $value );
			}

			$res = $this->request( $this->xml->asXML() );

			if ( is_wp_error( $res ) ) {
				return $res;
			}

			// make email marketable.
			$this->set_method( 'APIEmailService.optIn' );
			$this->set_param( $contact['Email'] );
			$this->set_param( 'Customer opted-in through webform' );
			$optin = $this->request( $this->xml->asXML() );

			return $res->get_value( 'i4' );
		} else {
			$err = new WP_Error();
			$err->add( 'email_exist', __( 'This email address has already subscribed.', 'hustle' ) );
			return $err;
		}
	}

	/**
	 * Updates an existing contact.
	 *
	 * @since 3.0.7
	 *
	 * @param array $contact Array of contact details to be updated.
	 * @return integer|WP_Error Contact ID if everything went well, WP_Error otherwise.
	 */
	public function update_contact( $contact ) {

		$this->opt_in_email( $contact['Email'] ); // First optin the email.

		$contact_id = $this->get_contact_id( $contact['Email'] );

		if ( ! $contact_id ) {
			return new WP_Error( 'contact_not_found', __( 'The existing contact could not be updated.', 'hustle' ) );
		}

		$this->set_method( 'ContactService.update' );

		$this->set_param( $contact_id, 'int' );
		foreach ( $contact as $key => $value ) {
			$this->set_member( $key, $value );
		}

		$res = $this->request( $this->xml->asXML() );

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		return $res->get_value( 'i4' );

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

		$contact_id = $this->get_contact_id( $email );

		if ( ! $contact_id ) {
			return false;
		}

		$xml = "<?xml version='1.0' encoding='UTF-8'?>
				<methodCall>
				  <methodName>ContactService.removeFromGroup</methodName>
				  <params>
					<param>
					  <value>
						<string>{$this->api_key}</string>
					  </value>
					</param>
					<param>
					  <value>
						<int>$contact_id</int>
					  </value>
					</param>
					<param>
					  <value>
						<int>$list_id</int>
					  </value>
					</param>
				  </params>
				</methodCall>";

		$res = $this->request( $xml );

		if ( is_wp_error( $res ) ) {
			return false;
		}

		return (bool) $res->get_value();
	}

	/**
	 * Email exists?
	 *
	 * @param string $email Email.
	 * @return boolean
	 */
	public function email_exist( $email ) {
		$this->set_method( 'ContactService.findByEmail' );
		$this->set_param( $email );
		$data = $this->params->addChild( 'param' )->addChild( 'value' )->addChild( 'array' )->addChild( 'data' );
		$data->addChild( 'value' )->addChild( 'string', 'Id' );

		$res = $this->request( $this->xml->asXML() );

		if ( ! is_wp_error( $res ) ) {
			$subscriber_id = $res->get_value( 'array.data.value.struct.member.value.i4' );

			return (int) $subscriber_id > 0;
		}

		return false;
	}

	/**
	 * Get the ID of an existing contact
	 *
	 * @param string $email Email.
	 * @return integer|boolean The ID of the existing contact, false on error. An ID of 0 or less means the contact does not exist.
	 */
	public function get_contact_id( $email ) {
		$this->set_method( 'ContactService.findByEmail' );
		$this->set_param( $email );
		$data = $this->params->addChild( 'param' )->addChild( 'value' )->addChild( 'array' )->addChild( 'data' );
		$data->addChild( 'value' )->addChild( 'string', 'Id' );

		$res = $this->request( $this->xml->asXML() );

		if ( ! is_wp_error( $res ) ) {
			$subscriber_id = $res->get_value( 'array.data.value.struct.member.value.i4' );

			return (int) $subscriber_id;
		}

		return false;
	}

	/**
	 * Opt-in email
	 * This allows the email to be marketable
	 *
	 * @param String $email Email.
	 *
	 * @return WP_Error|Xml
	 */
	private function opt_in_email( $email ) {
		$site_name = get_bloginfo( 'name' );
		$this->set_method( 'ContactService.findByEmail' );
		$this->set_param( $email );
		$this->set_param( $site_name );
		$res = $this->request( $this->xml->asXML() );
		return $res;
	}

	/**
	 * Adds contact with $contact_id to group with $group_id
	 *
	 * @param string $contact_id Contact ID.
	 * @param string $tag_id Tag ID.
	 * @return Opt_In_Infusionsoft_XML_Res|WP_Error
	 */
	public function add_tag_to_contact( $contact_id, $tag_id ) {
		$xml = "<?xml version='1.0' encoding='UTF-8'?>
				<methodCall>
				  <methodName>ContactService.addToGroup</methodName>
				  <params>
					<param>
					  <value>
						<string>{$this->api_key}</string>
					  </value>
					</param>
					<param>
					  <value>
						<int>$contact_id</int>
					  </value>
					</param>
					<param>
					  <value>
						<int>$tag_id</int>
					  </value>
					</param>
				  </params>
				</methodCall>";

		$res = $this->request( $xml );

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		return $res->get_value();

	}

	/**
	 * Get lists
	 *
	 * @return type
	 */
	public function get_lists() {
		$page = 0;
		$xml  = "<?xml version='1.0' encoding='UTF-8'?>
				<methodCall>
				  <methodName>DataService.query</methodName>
				  <params>
					<param>
					  <value>
						<string>{$this->api_key}</string>
					  </value>
					</param>
					<param>
					  <value>
						<string>ContactGroup</string>
					  </value>
					</param>
					<param>
					  <value>
						<int>1000</int>
					   </value>
					</param>
					<param>
					  <value>
						<int>$page</int>
					  </value>
					</param>
					<param>
					  <value><struct>
						<member>
							  <name>Id</name>
							  <value>
								<string>%</string>
							  </value>
						</member>
					  </struct></value>
					</param>
					<param>
					  <value><array>
						<data>
						  <value><string>Id</string></value>
						  <value><string>GroupName</string></value>
						</data>
					  </array></value>
					</param>
				  </params>
				</methodCall>";

		$res = $this->request( $xml );

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		return $res->get_tags_list();
	}

	/**
	 * Dispatches the request to the Infusionsoft server
	 *
	 * @param string $query_str Query string.
	 * @return Opt_In_Infusionsoft_XML_Res|WP_Error
	 */
	private function request( $query_str ) {
		$url = esc_url_raw( 'https://' . $this->app_name . '.infusionsoft.com/api/xmlrpc' );

		$headers = array(
			'Content-Type'   => 'text/xml',
			'Accept-Charset' => 'UTF-8,ISO-8859-1,US-ASCII',
		);

		$res = wp_remote_post(
			$url,
			array(
				'sslverify' => false,
				'headers'   => $headers,
				'body'      => $query_str,
			)
		);

		$utils                     = Hustle_Provider_Utils::get_instance();
		$utils->last_url_request   = $url;
		$utils->last_data_received = $res;
		$utils->last_data_sent     = $query_str;

		$code    = wp_remote_retrieve_response_code( $res );
		$message = wp_remote_retrieve_response_message( $res );
		$err     = new WP_Error();

		if ( $code < 204 ) {
			$xml = simplexml_load_string( wp_remote_retrieve_body( $res ), 'Opt_In_Infusionsoft_XML_Res' );

			if ( empty( $xml ) ) {
				$err->add( 'Invalid_app_name', __( 'Invalid app name, please check app name and try again', 'hustle' ) );
				return $err;
			}

			if ( $xml->is_faulty() ) {
				return $xml->get_fault();
			}

			return $xml;
		}

		$err->add( $code, $message );
		return $err;
	}
}
