<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Opt_In_Infusionsoft_XML_Res class
 *
 * @package Hustle
 */

if ( class_exists( 'Opt_In_Infusionsoft_XML_Res' ) ) {
	return;
}

/**
 * Class Opt_In_Infusionsoft_XML_Res
 */
class Opt_In_Infusionsoft_XML_Res extends  SimpleXMLElement {

	/**
	 * Returns value from xml like the template
	 *  <methodResponse>
	 *       <params>
	 *            <param>
	 *               <value><i4>contactIDNumber</i4></value>
	 *           </param>
	 *       </params>
	 *   </methodResponse>
	 *
	 * @param string $xml_structure XML structure.
	 * @return mixed
	 */
	public function get_value( $xml_structure = '' ) {
		$temp_array = (array) $this->params->param->value;
		$value      = reset( $temp_array );

		if ( ! empty( $xml_structure ) ) {
			$xml = explode( '.', $xml_structure );
			$xml = array_filter( $xml );

			foreach ( $xml as $key ) {
				if ( is_object( $value ) && isset( $value->$key ) ) {
					$value = $value->$key;
				}
			}
		}

		return $value;
	}

	/**
	 * Retrieves tag list from the query result
	 *
	 * @return array
	 */
	public function get_tags_list() {
		$lists = array();
		$count = count( $this->get_value()->data->value );

		for ( $i = 0; $i < $count; $i++ ) {
			$list  = $this->get_value()->data->value[ $i ];
			$label = (string) $list->struct->member[0]->value;
			if ( ! empty( $label ) ) {

				$temp_array   = (array) $list->struct->member[1]->value;
				$id           = (int) reset( $temp_array );
				$lists[ $id ] = $label;
			}
		}

		return $lists;
	}

	/**
	 * Response to array
	 *
	 * @return array
	 */
	public function response_to_array() {
		$array = array();

		foreach ( $this->get_value()->data->value as $list ) {
			foreach ( $list->struct->member as $info ) {
				if ( 'Name' === (string) $info->name ) {
					$label = (string) $info->value;
				} elseif ( 'Id' === (string) $info->name ) {
					$temp_array = (array) $info->value;
					$id         = (int) reset( $temp_array );
				}
				if ( isset( $label ) && isset( $id ) ) {
					$array[ $id ] = $label;
					unset( $label, $id );
				}
			}
			unset( $label, $id );
		}

		return $array;
	}

	/**
	 * Checks if responsive is faulty
	 *
	 * @return bool
	 */
	public function is_faulty() {
		return isset( $this->fault );
	}

	/**
	 * Returns bool false in case response is not faulty or a WP_Error with the fault code and message
	 *
	 * @return bool|WP_Error
	 */
	public function get_fault() {
		if ( ! $this->is_faulty() ) {
			return false;
		}

		$err = new WP_Error();
		$err->add( (int) $this->fault->value->struct->member[0]->value, (string) $this->fault->value->struct->member[1]->value );
		return $err;
	}
}
