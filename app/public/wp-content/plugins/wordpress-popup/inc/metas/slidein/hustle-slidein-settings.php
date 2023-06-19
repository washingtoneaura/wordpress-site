<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Slidein_Settings class
 *
 * @package Hustle
 */

/**
 * Class Hustle_Slidein_Settings
 */
class Hustle_Slidein_Settings extends Hustle_Meta_Base_Settings {

	/**
	 * Get the default settings.
	 *
	 * @return array
	 */
	public function get_defaults() {
		$base = parent::get_defaults();

		// Specific for slidein.
		$settings = array_merge(
			$base,
			array(
				'display_position' => 's',
				'auto_hide'        => '0',
				'auto_hide_unit'   => 'seconds',
				'auto_hide_time'   => '5',
			)
		);

		return $settings;
	}
}
