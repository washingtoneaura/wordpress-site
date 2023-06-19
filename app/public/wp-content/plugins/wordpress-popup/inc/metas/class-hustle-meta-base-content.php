<?php
/**
 * File for Hustle_Meta_Base_Content class.
 *
 * @package Hustle
 * @since 4.2.0
 */

/**
 * Hustle_Meta_Base_Content is the base class for the "content" meta of modules.
 * This class should handle what's related to the "content" meta.
 *
 * @since 4.2.0
 */
class Hustle_Meta_Base_Content extends Hustle_Meta {

	/**
	 * Get the defaults for this meta.
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_defaults() {
		$data = array(
			'module_name'         => '',
			'title'               => '',
			'sub_title'           => '',
			'main_content'        => '',
			'feature_image'       => '',
			'background_image'    => '',
			'show_never_see_link' => '0',
			'never_see_link_text' => __( 'Never see this message again.', 'hustle' ),
			'show_cta'            => '0',
			'cta_label'           => '',
			'cta_url'             => '',
			'cta_target'          => 'blank',
			'cta_two_label'       => 'Close',
			'cta_two_url'         => '',
			'cta_two_target'      => 'close',
			'cta_helper_show'     => '0',
			'cta_helper_text'     => '',
		);

		if ( ! empty( $this->module->module_type ) && 'embedded' === $this->module->module_type ) {
			$data['cta_two_label']  = '';
			$data['cta_two_target'] = 'blank';
		}

		return $data;
	}

	/**
	 * Returns whether the module has CTA active.
	 *
	 * @since 4.3.1
	 *
	 * @return boolean
	 */
	public function has_cta() {
		return '1' === $this->data['show_cta'];
	}
}
