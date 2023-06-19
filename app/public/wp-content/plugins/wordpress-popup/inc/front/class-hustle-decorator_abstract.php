<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Hustle_Decorator_Abstract
 *
 * @package Hustle
 */

/**
 * Class Hustle_Decorator_Abstract
 */
abstract class Hustle_Decorator_Abstract {

	/**
	 * Module
	 *
	 * @var object
	 */
	protected $module;

	/**
	 * Instance of the design meta handler of the module.
	 *
	 * @since 4.3.0
	 * @var Hustle_Meta_Base_Design
	 */
	protected $design_meta;

	/**
	 * Design
	 *
	 * @var array
	 */
	protected $design;

	/**
	 * Desktop breakpoint
	 *
	 * @var int
	 */
	protected $bp_desktop;
	/**
	 * Mobile breakpoint
	 *
	 * @var int
	 */
	protected $bp_mobile;

	/**
	 * Gets the string with the module's styles.
	 * The meat of the class.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	abstract protected function get_styles();

	/**
	 * Constructor
	 *
	 * @param Hustle_Model $module Module.
	 */
	public function __construct( Hustle_Model $module ) {
		$this->module = $module;

		$this->bp_mobile  = Hustle_Settings_Admin::get_mobile_breakpoint();
		$this->bp_desktop = $this->bp_mobile + 1;
	}

	/**
	 * Get module styles
	 *
	 * @param type $module_type Module type.
	 * @return type
	 */
	public function get_module_styles( $module_type ) {

		$this->design_meta = $this->module->get_design();
		$this->design      = (array) $this->module->design; // Making it an array to avoic changing all the decorator files.

		$styles = $this->get_styles();

		return wp_strip_all_tags( $styles );
	}
}
