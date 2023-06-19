<?php
/**
 * Hustle_Layout_Helper class.
 *
 * @package Hustle
 * @since 4.2.0
 */

/**
 * Helper class for rendering markup on admin side.
 * This is used along admin pages to standardize certain elements markup.
 *
 * @since 4.2.0
 */
class Hustle_Layout_Helper {

	/**
	 * Instance of the class that controls the template.
	 *
	 * @since 4.2.0
	 * @var Object
	 */
	private $admin;

	/**
	 * White labeling based on Dash Plugin Settings.
	 *
	 * @since 4.2.0
	 * @var boolean
	 */
	private $is_branding_hidden = false;

	/**
	 * White labeling branding image.
	 *
	 * @since 4.4.7
	 * @var string
	 */
	private $branding_image;

	/** Array list of quicktags for tinymce editor.
	 *
	 * @since 4.4.7
	 * @var array
	 */
	private $tinymce_quicktags;

	/**
	 * To be removed.
	 *
	 * @var string something.
	 */
	public static $plugin_url;

	/**
	 * Flag for letting SUI doesn't run auto init selects to suiSelect.
	 *
	 * @var bool
	 */
	private static $dont_init_selects;

	/**
	 * Hustle_Layout_Helper class constructor.
	 *
	 * @since 4.2.0
	 * @param object $referer The class that has the properties to access from within templates.
	 */
	public function __construct( $referer = null ) {

		self::$plugin_url = Opt_In::$plugin_url;

		$this->is_branding_hidden = apply_filters( 'wpmudev_branding_hide_branding', $this->is_branding_hidden );

		// White label custom branding image.
		$this->branding_image = apply_filters( 'wpmudev_branding_hero_image', null );

		// init common config for tinymce editor.
		$this->tinymce_init();
		/**
		 * Sets the referer class as a property.
		 * This allows us to access the referer class' properties if needed
		 * from the template files.
		 */
		// TODO maybe check if the referer has the two allowed classes.
		if ( $referer ) {
			$this->admin = $referer;
		}
	}

	/**
	 * Gets the previously set referer.
	 *
	 * @since 4.2.0
	 * @return object
	 */
	public function get_referer() {
		if ( ! $this->admin ) {
			return false;
		}
		return $this->admin;
	}

	/**
	 * Returns or echoes markup from the given $options array.
	 * Uses the file 'admin/commons/options' as the markup template.
	 *
	 * @since 4.2.0
	 *
	 * @param  array   $options Array with the options that define the markup to be returned.
	 * @param  boolean $return Whether to echo or return the markup.
	 * @return string
	 */
	public function get_html_for_options( $options, $return = false ) {
		$html = '';
		foreach ( $options as $key => $option ) {
			$html .= $this->render( 'admin/commons/options', $option, $return );
		}
		return $html;
	}

	/**
	 * Renders a view file with static call.
	 *
	 * @since 1.0
	 * @since 4.2.0 Moved from Opt_In to this class.
	 *
	 * @param string     $file Path to the view file.
	 * @param array      $params Array whose keys will be variable names when within the view file.
	 * @param bool|false $return Whether to echo or return the contents.
	 * @return string
	 */
	public function render( $file, $params = array(), $return = false ) {

		// Assign $file to a variable which is unlikely to be used by users of the method.
		$opt_in_to_be_file_name = $file;
		extract( $params, EXTR_OVERWRITE ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		if ( $return ) {
			ob_start();
		}

		$template_file = trailingslashit( Opt_In::$plugin_path ) . Opt_In::VIEWS_FOLDER . '/' . $opt_in_to_be_file_name . '.php';
		if ( file_exists( $template_file ) ) {
			include $template_file;

		} else {
			$template_path = Opt_In::$template_path . $opt_in_to_be_file_name . '.php';

			// Render file located outside the plugin's folder. Useful when adding third-party integrations.
			$external_path = $opt_in_to_be_file_name . '.php';

			if ( file_exists( $template_path ) ) {
				include $template_path;
			} elseif ( file_exists( $external_path ) ) {
				include $external_path;
			} elseif ( file_exists( $opt_in_to_be_file_name ) ) {
				include $opt_in_to_be_file_name;
			}
		}

		if ( $return ) {
			return ob_get_clean();
		}

		if ( ! empty( $params ) ) {
			foreach ( $params as $param ) {
				unset( $param );
			}
		}
	}

	/**
	 * Renders html.
	 *
	 * @param string $content Content - HTML.
	 */
	public function render_html( $content ) {
		$common_arrts = array(
			'id'               => true,
			'data-*'           => true,
			'title'            => true,
			'sandbox'          => true,
			'class'            => true,
			'aria-hidden'      => true,
			'aria-labelledby'  => true,
			'aria-describedby' => true,
			'role'             => true,
			'xmlns'            => true,
			'xmlns:xlink'      => true,
			'width'            => true,
			'height'           => true,
			'viewbox'          => true,
			'type'             => true,
			'name'             => true,
			'value'            => true,
			'checked'          => true,
			'selected'         => true,
			'placeholder'      => true,
			'disabled'         => true,
			'method'           => true,

		);
		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_tags = array_merge(
			$allowed_html,
			array(
				'iframe' => $common_arrts,
				'form'   => $common_arrts,
				'svg'    => $common_arrts,
				'defs'   => true,
				'g'      => array(
					'fill'      => true,
					'fill-rule' => true,
					'clip-rule' => true,
					'd'         => true,
				),
				'path'   => array(
					'd'         => true,
					'id'        => true,
					'fill'      => true,
					'fill-rule' => true,
				),
				'input'  => $common_arrts,
				'select' => $common_arrts,
				'option' => $common_arrts,
			)
		);

		$allowed_tags = apply_filters( 'hustle_content_allowed_tags', $allowed_tags );

		echo wp_kses( $content, $allowed_tags );
	}

	/**
	 * Renders custom attributes within views templates.
	 *
	 * @since 1.0.0
	 * @since 4.2.0 Moved from Opt_In to this class.
	 * @since 4.3.0 Removed the $echo parameter.
	 *
	 * @param array $html_options Attributes as an array to be renderd.
	 * @return string
	 */
	public function render_attributes( $html_options ) {

		if ( array() === $html_options ) {
			return '';
		}

		$special_attributes = array(
			'async',
			'autofocus',
			'autoplay',
			'checked',
			'controls',
			'declare',
			'default',
			'defer',
			'disabled',
			'formnovalidate',
			'hidden',
			'ismap',
			'loop',
			'multiple',
			'muted',
			'nohref',
			'noresize',
			'novalidate',
			'open',
			'readonly',
			'required',
			'reversed',
			'scoped',
			'seamless',
			'selected',
			'typemustmatch',
		);

		foreach ( $html_options as $name => $value ) {
			if ( in_array( $name, $special_attributes, true ) ) {
				if ( $value ) {
					echo ' ' . esc_attr( $name ) . '="' . esc_attr( $name ) . '"';
				}
			} elseif ( null !== $value ) {
				echo ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
			}
		}
	}

	/**
	 * Renders a basic modal with the passed attributes.
	 *
	 * @since 4.2.0
	 * @param array $arguments Arguments for the modal. Documented in the template file.
	 */
	private function render_modal( $arguments ) {
		$this->render( '/admin/commons/modal-template', $arguments );
	}

	/**
	 * Image function
	 * Return image element with 2x and 1x support.
	 *
	 * @since 4.3.1
	 *
	 * @param string      $image_path URL for the given image.
	 * @param string      $image_suffix Image format, like png, jpg, etc.
	 * @param string      $image_class Class for the image HTML element.
	 * @param string|bool $support Whether the image has retina support.
	 */
	private function hustle_image( $image_path, $image_suffix, $image_class, $support ) {
		/* translators: Plugin name */
		$image_name = esc_html( sprintf( __( '%s image', 'hustle' ), Opt_In_Utils::get_plugin_name() ) );

		echo '<img src="' . esc_url( $image_path . '.' . $image_suffix ) . '" alt="' . esc_attr( $image_name ) . '"';
		if ( true === $support || '2x' === $support ) {
			echo ' srcset="' . esc_attr( $image_path . '.' . $image_suffix ) . ' 1x, ' . esc_attr( $image_path . '@2x.' . $image_suffix ) . ' 2x"';
		}
		if ( '' !== $image_class ) {
			echo ' class="' . esc_attr( $image_class ) . '"';
		}
		echo ' aria-hidden="true">';
	}

	/**
	 * Color Picker
	 *
	 * Return the correct color picker markup that's compatible with Shared UI 2.0
	 *
	 * @since 4.3.1
	 *
	 * @param string $id "id" attribute of the input.
	 * @param string $name "name" attribute of the input.
	 * @param string $alpha "false"/"true". Enables or disables the alpha selector in the colorpicker.
	 * @param bool   $is_js_template whether this colorpicker will be filled via js templating.
	 * @param string $value Value to be used when js templating isn't used.
	 */
	private function sui_colorpicker( $id, $name, $alpha = 'false', $is_js_template = true, $value = false ) {

		$value = ( ! $is_js_template && $value ) ? $value : '{{ ' . $name . ' }}';

		echo '<div class="sui-colorpicker-wrap">

			<div class="sui-colorpicker" aria-hidden="true">
				<div class="sui-colorpicker-value">
					<span role="button">
						<span style="background-color: ' . esc_attr( $value ) . '"></span>
					</span>
					<input class="hustle-colorpicker-input" type="text" value="' . esc_attr( $value ) . '"/>
					<button><span class="sui-icon-close" aria-hidden="true"></span></button>
				</div>
				<button class="sui-button">' . esc_html__( 'Select', 'hustle' ) . '</button>
			</div>

			<input type="text"
				name="' . esc_attr( $name ) . '"
				value="' . esc_attr( $value ) . '"
				id="' . esc_attr( $id ) . '"
				class="sui-colorpicker-input"
				data-alpha-enabled="' . esc_attr( $alpha ) . '"
				data-attribute="' . esc_attr( $name ) . '" />

		</div>';

	}

	/**
	 * Common init config for tinymce editor.
	 *
	 * @since 4.4.7
	 * @return void
	 */
	private function tinymce_init() {
		// remove add more tag from visual tab.
		add_filter(
			'mce_buttons',
			function( $mce_buttons ) {
				$remove = array( 'wp_more' );
				return array_diff( $mce_buttons, $remove );
			}
		);
		// remove more tag from text tab.
		$this->tinymce_quicktags = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
	}
}
